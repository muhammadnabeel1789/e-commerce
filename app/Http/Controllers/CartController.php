<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Tampilkan halaman keranjang
     */
    public function index()
    {
        $cart = Cart::with(['items.product.images', 'items.variant.image'])
            ->where('user_id', Auth::id())
            ->first();

        return view('customer.cart.index', compact('cart'));
    }

    /**
     * Tambah item ke keranjang
     */
/**
     * Tambah item ke keranjang
     */
    public function addToCart(Request $request, $productId)
    {
        $request->validate([
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1',
            'action'     => 'required|in:add_to_cart,buy_now',
        ]);

        $product = Product::findOrFail($productId);
        
        // Cek varian
        $variant = null;
        $price = 0;
        
        if ($request->variant_id) {
            $variant = ProductVariant::findOrFail($request->variant_id);
            
            // Validasi stok varian
            if ($variant->stock < $request->quantity) {
                return redirect()->back()->with('error', 'Stok varian tidak mencukupi!');
            }
            
            // Harga dari varian
            $price = $variant->additional_price;
        } else {
            // Jika produk tanpa varian, ambil harga dari varian pertama
            $firstVariant = $product->variants()->first();
            if (!$firstVariant) {
                return redirect()->back()->with('error', 'Produk tidak memiliki varian!');
            }
            
            $price = $firstVariant->additional_price;
            
            // Validasi stok produk (menggunakan stok varian pertama)
            if ($firstVariant->stock < $request->quantity) {
                return redirect()->back()->with('error', 'Stok produk tidak mencukupi!');
            }
        }

        // Jika action = buy_now, JANGAN tambah ke cart
        // Langsung simpan ke session dan redirect ke checkout
        if ($request->action === 'buy_now') {
            Session::put('direct_buy', [
                'product_id' => $product->id,
                'variant_id' => $request->variant_id,
                'quantity'   => $request->quantity,
            ]);
            // BERSIHKAN SESSION KERANJANG AGAR TIDAK BENTROK
            Session::forget('selected_cart_items'); 
            return redirect()->route('checkout.index');
        }

        // BERSIHKAN SESSION DIRECT BUY KETIKA ADD TO CART
        Session::forget('direct_buy');

        // Hanya untuk add_to_cart: tambah ke keranjang
        $cart = Cart::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        // Cek apakah item sudah ada di cart
        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->where('product_variant_id', $request->variant_id)
            ->first();

        if ($existingItem) {
            // Update quantity jika sudah ada
            $newQty = $existingItem->quantity + $request->quantity;
            
            // Validasi stok
            if ($variant) {
                if ($variant->stock < $newQty) {
                    return redirect()->back()->with('error', 'Stok varian tidak mencukupi untuk jumlah yang diminta!');
                }
            } else {
                $firstVariant = $product->variants()->first();
                if ($firstVariant && $firstVariant->stock < $newQty) {
                    return redirect()->back()->with('error', 'Stok produk tidak mencukupi untuk jumlah yang diminta!');
                }
            }
            
            $existingItem->quantity = $newQty;
            $existingItem->save();
        } else {
            // Buat item baru
            CartItem::create([
                'cart_id'            => $cart->id,
                'product_id'         => $product->id,
                'product_variant_id' => $request->variant_id,
                'quantity'           => $request->quantity,
                'price'              => $price,
            ]);
        }

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    /**
     * Update quantity item di keranjang
     */
    public function updateCart(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $item = CartItem::findOrFail($id);
        
        // Validasi stok
        if ($item->variant) {
            if ($item->variant->stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok varian tidak mencukupi! Maksimal: ' . $item->variant->stock
                ], 400);
            }
        } else {
            // Ambil varian pertama untuk cek stok
            $firstVariant = $item->product->variants()->first();
            if ($firstVariant && $firstVariant->stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok produk tidak mencukupi! Maksimal: ' . $firstVariant->stock
                ], 400);
            }
        }

        $item->update([
            'quantity' => $request->quantity
        ]);

        // Hitung ulang total
        $cart = Cart::with('items')->where('user_id', Auth::id())->first();
        $subtotal = 0;
        foreach ($cart->items as $cartItem) {
            $subtotal += $cartItem->price * $cartItem->quantity;
        }

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil diperbarui!',
            'subtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
            'item_subtotal' => 'Rp ' . number_format($item->price * $item->quantity, 0, ',', '.')
        ]);
    }

    /**
     * Hapus item dari keranjang
     */
    public function destroy($id)
    {
        $item = CartItem::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'Item berhasil dihapus dari keranjang!');
    }
}