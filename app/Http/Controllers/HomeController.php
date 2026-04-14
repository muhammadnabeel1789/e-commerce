<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            $role = \Illuminate\Support\Facades\Auth::user()->role;
            if ($role === 'kurir') {
                return redirect()->route('kurir.dashboard');
            }
        }
        // 1. Ambil Banner
        $banners = Banner::orderBy('sort_order')->get();

        // 2. Ambil Kategori Utama
        $categories = Category::take(6)->get();

        // 3. Produk TERBAIK — hanya yang SUDAH ADA RATING (reviews_count > 0)
        $featuredProducts = Product::where('is_active', true)
            ->with(['category', 'brand', 'images', 'variants'])
            ->withAvg(['reviews' => fn($q) => $q->where('is_approved', true)], 'rating')
            ->withCount(['reviews' => fn($q) => $q->where('is_approved', true)])
            ->having('reviews_count', '>', 0)  // hanya produk yang sudah ada ulasan
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('reviews_count')
            ->take(8)
            ->get();

        // 4. Produk Terbaru
        $newProducts = Product::where('is_active', true)
            ->latest()
            ->with(['category', 'brand', 'images', 'variants'])
            ->withAvg(['reviews' => fn($q) => $q->where('is_approved', true)], 'rating')
            ->withCount(['reviews' => fn($q) => $q->where('is_approved', true)])
            ->take(8)
            ->get();

        // 5. Brand Partner
        $brands = Brand::take(12)->get();

        return view('home.index', compact(
            'banners',
            'categories',
            'featuredProducts',
            'newProducts',
            'brands'
        ));
    }
}