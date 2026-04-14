<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order', 'asc')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:3072', // Wajib ada gambar, max 3MB
            'title' => 'nullable|string|max:255',
            'link'  => 'nullable|string|max:255',
        ]);

        $path = $request->file('image')->store('banners', 'public');

        Banner::create([
            'title' => $request->title,
            'image' => $path,
            'link' => $request->link,
            'position' => 'hero', // Default posisi hero slider
            'is_active' => true,
            'sort_order' => 0,
        ]);

        return redirect()->back()->with('success', 'Banner berhasil ditambahkan!');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        $banner->delete();
        return redirect()->back()->with('success', 'Banner dihapus.');
    }
}