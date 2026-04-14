<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'images', 'variants'])->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                    => 'required|string|max:255',
            'category_id'             => 'required|exists:categories,id',
            'brand_id'                => 'nullable|exists:brands,id',
            'description'             => 'nullable|string',
            'weight'                  => 'nullable|integer|min:0',
            'is_featured'             => 'nullable|boolean',
            'is_active'               => 'nullable|boolean',
            'images'                  => 'nullable|array',
            'images.*'                => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'variants'                => 'required|array|min:1',
            'variants.*.size'         => 'required|string',
            'variants.*.color'        => 'required|string',
            'variants.*.price'        => 'required|numeric|min:0',
            'variants.*.stock'        => 'required|integer|min:0',
            'variants.*.sku_variant'  => 'required|string|max:255|distinct|unique:product_variants,sku_variant',
        ]);

        try {
            DB::beginTransaction();

            $totalStock = collect($request->variants)->sum('stock');
            $mainSku = 'PRD-' . strtoupper(Str::random(8));

            $product = Product::create([
                'name'        => $request->name,
                'category_id' => $request->category_id,
                'brand_id'    => $request->brand_id,
                'description' => $request->description,
                'stock'       => $totalStock,
                'sku'         => $mainSku,
                'weight'      => $request->weight ?? 10,
                'is_featured' => $request->boolean('is_featured'),
                'is_active'   => $request->boolean('is_active', true),
            ]);

            // Simpan Gambar
            $uploadedImages = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('products', 'public');
                    $img = ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => ($index === 0),
                        'sort_order' => $index,
                    ]);
                    $uploadedImages[$index] = $img;
                }
            }

            // Simpan Varian — SKU diambil dari input admin
            foreach ($request->variants as $index => $v) {
                $imageId = null;
                if (isset($v['image_index']) && isset($uploadedImages[(int)$v['image_index']])) {
                    $imageId = $uploadedImages[(int)$v['image_index']]->id;
                }

                ProductVariant::create([
                    'product_id'       => $product->id,
                    'product_image_id' => $imageId,
                    'size'             => $v['size'],
                    'color'            => $v['color'],
                    'color_code'       => $v['color_code'] ?? null,
                    'stock'            => $v['stock'],
                    'additional_price' => $v['price'],
                    'sku_variant'      => $v['sku_variant'],
                ]);
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing product: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $product    = Product::with(['images', 'variants.image'])->findOrFail($id);
        $categories = Category::all();
        $brands     = Brand::all();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'             => 'required|string|max:255',
            'category_id'      => 'required|exists:categories,id',
            'brand_id'         => 'nullable|exists:brands,id',
            'description'      => 'nullable|string',
            'weight'           => 'nullable|integer|min:0',
            'is_featured'      => 'nullable|boolean',
            'is_active'        => 'nullable|boolean',
            'new_images'       => 'nullable|array',
            'new_images.*'     => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'existing_variants'                    => 'nullable|array',
            'existing_variants.*.size'             => 'required|string',
            'existing_variants.*.color'            => 'required|string',
            'existing_variants.*.price'            => 'required|numeric|min:0',
            'existing_variants.*.stock'            => 'required|integer|min:0',
            'existing_variants.*.sku'              => 'nullable|string|max:255',
            'existing_variants.*.product_image_id' => 'sometimes|nullable|exists:product_images,id',
            'new_variants'                         => 'nullable|array',
            'new_variants.*.size'                  => 'required|string',
            'new_variants.*.color'                 => 'required|string',
            'new_variants.*.price'                 => 'required|numeric|min:0',
            'new_variants.*.stock'                 => 'required|integer|min:0',
            'new_variants.*.sku'                   => 'nullable|string|max:255',
            'new_variants.*.product_image_id'      => 'sometimes|nullable|exists:product_images,id',
            'new_variants.*.new_image_index'       => 'nullable|integer|min:0',
        ]);

        // Validasi manual: hapus product_image_id kosong (string '')
        // agar tidak gagal validasi exists ketika kosong
        if ($request->has('new_variants')) {
            $newVars = $request->input('new_variants', []);
            foreach ($newVars as $i => $v) {
                if (isset($v['product_image_id']) && $v['product_image_id'] === '') {
                    $newVars[$i]['product_image_id'] = null;
                }
            }
            $request->merge(['new_variants' => $newVars]);
        }

        try {
            DB::beginTransaction();

            // Update varian lama
            if ($request->has('existing_variants')) {
                foreach ($request->existing_variants as $varId => $data) {
                    $variant = ProductVariant::find($varId);
                    if ($variant && $variant->product_id == $product->id) {
                        $updateData = [
                            'size'             => $data['size'],
                            'color'            => $data['color'],
                            'color_code'       => $data['color_code'] ?? $variant->color_code,
                            'stock'            => $data['stock'],
                            'additional_price' => $data['price'],
                            'product_image_id' => !empty($data['product_image_id']) ? $data['product_image_id'] : null,
                        ];
                        // Update SKU hanya jika diisi
                        if (!empty($data['sku'])) {
                            $updateData['sku_variant'] = $data['sku'];
                        }
                        $variant->update($updateData);
                    }
                }
            }

            // Upload gambar baru DULU (sebelum create varian baru)
            $newlyUploadedImages = [];
            if ($request->hasFile('new_images')) {
                $sortStart = $product->images()->count();
                foreach ($request->file('new_images') as $index => $file) {
                    $path = $file->store('products', 'public');
                    $img = ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => false,
                        'sort_order' => $sortStart + $index,
                    ]);
                    $newlyUploadedImages[$index] = $img;
                }
            }

            // Tambah varian baru
            if ($request->has('new_variants')) {
                foreach ($request->new_variants as $index => $data) {
                    $skuVariant = !empty($data['sku'])
                        ? $data['sku']
                        : $product->sku . '-VN-' . strtoupper(Str::random(4));

                    // Tentukan product_image_id:
                    // Prioritas 1: new_image_index (foto baru yang baru diupload)
                    // Prioritas 2: product_image_id (foto existing yang dipilih dari picker)
                    $imgId = null;
                    if (isset($data['new_image_index']) && $data['new_image_index'] !== '') {
                        $ni = (int) $data['new_image_index'];
                        $imgId = $newlyUploadedImages[$ni]->id ?? null;
                    } elseif (!empty($data['product_image_id'])) {
                        $imgId = $data['product_image_id'];
                    }

                    ProductVariant::create([
                        'product_id'       => $product->id,
                        'product_image_id' => $imgId,
                        'size'             => $data['size'],
                        'color'            => $data['color'],
                        'color_code'       => $data['color_code'] ?? null,
                        'stock'            => $data['stock'],
                        'additional_price' => $data['price'],
                        'sku_variant'      => $skuVariant,
                    ]);
                }
            }

            // Hitung ulang total stok
            $totalStock = $product->variants()->sum('stock');

            $product->update([
                'name'        => $request->name,
                'category_id' => $request->category_id,
                'brand_id'    => $request->brand_id,
                'description' => $request->description,
                'stock'       => $totalStock,
                'weight'      => $request->weight ?? $product->weight,
                'is_featured' => $request->boolean('is_featured'),
                'is_active'   => $request->boolean('is_active', true),
            ]);

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyImage($id)
    {
        try {
            $image = ProductImage::findOrFail($id);
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
            return back()->with('success', 'Foto berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal hapus foto: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    public function destroyVariant($id)
    {
        try {
            $variant = ProductVariant::findOrFail($id);
            $product = $variant->product;
            $variant->delete();

            $newStock = $product->variants()->sum('stock');
            $product->update(['stock' => $newStock]);

            return back()->with('success', 'Varian berhasil dihapus dan stok diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus varian: ' . $e->getMessage());
        }
    }
}