<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Daftar ulasan milik user (read-only, tidak ada edit/hapus)
     */
    public function index()
    {
        $reviews = Review::with(['product.images', 'images'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('customer.reviews.index', compact('reviews'));
    }

    /**
     * Form tulis ulasan
     */
    public function create($orderId, $productId, Request $request)
    {
        $variantId = $request->query('variant');
        $order   = Order::findOrFail($orderId);
        $product = Product::with(['images', 'variants.image'])->findOrFail($productId);
        
        $variant = null;
        if ($variantId) {
            $variant = $product->variants->find($variantId);
        }

        if ($order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        // Cegah akses form jika sudah pernah review (termasuk varian tertentu)
        $alreadyReviewed = Review::where('user_id', Auth::id())
            ->where('order_id', $orderId)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->exists();

        if ($alreadyReviewed) {
            return redirect()->route('reviews.index')
                ->with('info', 'Anda sudah memberikan ulasan untuk produk ini.');
        }

        return view('customer.reviews.create', compact('order', 'product', 'variant'));
    }

    /**
     * Simpan ulasan — status PENDING (is_approved = false)
     * Admin harus approve sebelum muncul di halaman produk
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'order_id'   => 'required|exists:orders,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'required|string|min:5',
            'images.*'   => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Pastikan pesanan milik user yang login dan sudah completed
        $isValidOrder = Order::where('id', $request->order_id)
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->exists();

        if (!$isValidOrder) {
            return redirect()->back()
                ->with('error', 'Anda belum membeli produk ini atau pesanan belum selesai.');
        }

        // Cegah review duplikat (cek kombinasi produk + varian)
        $existingReview = Review::where('user_id', Auth::id())
            ->where('order_id', $request->order_id)
            ->where('product_id', $request->product_id)
            ->where('product_variant_id', $request->product_variant_id)
            ->exists();

        if ($existingReview) {
            return redirect()->route('reviews.index')
                ->with('info', 'Anda sudah memberikan ulasan untuk produk ini.');
        }

        DB::transaction(function () use ($request) {
            // ✅ is_approved = FALSE → masuk antrian admin
            $review = Review::create([
                'user_id'            => Auth::id(),
                'product_id'         => $request->product_id,
                'product_variant_id' => $request->product_variant_id,
                'order_id'           => $request->order_id,
                'rating'             => $request->rating,
                'comment'            => $request->comment,
                'is_approved'        => false,
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('reviews', 'public');
                    ReviewImage::create([
                        'review_id'  => $review->id,
                        'image_path' => $path,
                    ]);
                }
            }
        });

        return redirect()->route('reviews.index')
            ->with('success', 'Ulasan berhasil dikirim! Akan ditampilkan setelah disetujui admin.');
    }
}