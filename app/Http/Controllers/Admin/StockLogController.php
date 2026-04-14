<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockLog;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockLogController extends Controller
{
    public function index()
    {
        $stockLogs = StockLog::with(['product', 'variant', 'user'])
            ->latest()
            ->paginate(15); // ✅ Pagination: 15 baris per halaman

        $products = Product::with('variants')->orderBy('name')->get();

        return view('admin.stock-logs.index', compact('stockLogs', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'type'               => 'required|in:in,out',
            'quantity_change'    => 'required|integer|min:1',
            'notes'              => 'nullable|string|max:255',
        ], [
            'product_variant_id.required' => 'Varian produk harus dipilih!',
            'quantity_change.min'         => 'Jumlah minimal adalah 1',
        ]);

        DB::beginTransaction();
        try {
            $variant        = ProductVariant::findOrFail($request->product_variant_id);
            $product        = $variant->product;
            $quantityChange = (int) $request->quantity_change;

            // Simpan stok awal varian
            $previousStock = $variant->stock;

            // Cek stok cukup saat keluar
            if ($request->type === 'out' && $variant->stock < $quantityChange) {
                DB::rollBack();
                return back()->with('error', 'Stok tidak cukup! Sisa stok varian ini: ' . $variant->stock . ' pcs.');
            }

            // Update stok varian
            $variant->stock = $request->type === 'in'
                ? $variant->stock + $quantityChange
                : $variant->stock - $quantityChange;
            $variant->save();

            // Sisa stok saat ini
            $currentStock = $variant->stock;

            // Sinkron total stok produk induk dari jumlah semua varian
            $product->stock = $product->variants()->sum('stock');
            $product->save();

            // Catat log
            StockLog::create([
                'product_id'         => $variant->product_id,
                'product_variant_id' => $variant->id,
                'user_id'            => Auth::id(),
                'quantity_change'    => $request->type === 'in' ? $quantityChange : -$quantityChange,
                'previous_stock'     => $previousStock,
                'current_stock'      => $currentStock,
                'type'               => $request->type,
                'notes'              => $request->notes,
            ]);

            DB::commit();
            return back()->with('success', 'Stok berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}