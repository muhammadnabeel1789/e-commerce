<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Daftar semua ulasan — bisa filter by status
     */
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'pending'); // pending | approved | all

        $query = Review::with(['user', 'product.images', 'images'])
            ->latest();

        if ($filter === 'pending') {
            $query->where('is_approved', false);
        } elseif ($filter === 'approved') {
            $query->where('is_approved', true);
        }
        // 'all' → tidak ada filter tambahan

        $reviews       = $query->paginate(15)->withQueryString();
        $pendingCount  = Review::where('is_approved', false)->count();
        $approvedCount = Review::where('is_approved', true)->count();

        return view('admin.reviews.index', compact('reviews', 'filter', 'pendingCount', 'approvedCount'));
    }

    /**
     * Approve ulasan → is_approved = true → langsung tampil di produk
     */
    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        return redirect()->back()
            ->with('success', "Ulasan dari {$review->user->name} berhasil disetujui dan sekarang tampil di halaman produk.");
    }

    /**
     * Reject / hapus ulasan (beserta gambar-gambarnya)
     */
    public function destroy(Review $review)
    {
        // Hapus file gambar dari storage
        foreach ($review->images as $img) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($img->image_path);
            $img->delete();
        }

        $reviewerName = $review->user->name ?? 'Pengguna';
        $review->delete();

        return redirect()->back()
            ->with('success', "Ulasan dari {$reviewerName} berhasil dihapus.");
    }
}