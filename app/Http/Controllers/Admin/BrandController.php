<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::latest()->paginate(10);
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string', // Validasi baru
            'is_active' => 'required|boolean',    // Validasi baru
            'logo' => 'nullable|image|max:2048'
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('brands', 'public');
        }

        // Simpan Data
        Brand::create([
            'name' => $request->name,
            'description' => $request->description, // Simpan deskripsi
            'is_active' => $request->is_active,     // Simpan status
            'logo' => $logoPath,
        ]);

        return redirect()->route('admin.brands.index')->with('success', 'Brand berhasil ditambahkan!');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'logo' => 'nullable|image|max:2048'
        ]);

        // 1. Update data teks
        $brand->name = $request->name;
        $brand->description = $request->description;
        $brand->is_active = $request->is_active;

        // 2. Cek jika ada upload logo baru
        if ($request->hasFile('logo')) {
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }
            $brand->logo = $request->file('logo')->store('brands', 'public');
        }

        // 3. Simpan
        $brand->save();

        return redirect()->route('admin.brands.index')->with('success', 'Brand berhasil diperbarui!');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
            Storage::disk('public')->delete($brand->logo);
        }
        
        $brand->delete();
        return redirect()->back()->with('success', 'Brand dihapus.');
    }
}