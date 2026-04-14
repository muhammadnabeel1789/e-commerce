<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // Menampilkan form edit tarif & Informasi Toko
    public function index()
    {
        // Ambil nilai dari database, berikan nilai default jika belum ada
        $settings = [
            'ongkir_per_km'   => Setting::where('key', 'ongkir_per_km')->value('value') ?? 200,
            'ongkir_per_gram' => Setting::where('key', 'ongkir_per_gram')->value('value') ?? 100,
            'shop_name'       => Setting::where('key', 'shop_name')->value('value') ?? 'Fashion Store',
            'shop_phone'      => Setting::where('key', 'shop_phone')->value('value') ?? '-',
            'shop_email'      => Setting::where('key', 'shop_email')->value('value') ?? '-',
            'shop_address'    => Setting::where('key', 'shop_address')->value('value') ?? '-',
            'shop_logo'       => Setting::where('key', 'shop_logo')->value('value'),
        ];
        
        return view('admin.settings.index', $settings);
    }

    // Menyimpan perubahan tarif & Informasi Toko
    public function update(Request $request)
    {
        // Validasi input
        $request->validate([
            'ongkir_per_km'   => 'required|numeric|min:0',
            'ongkir_per_gram' => 'required|numeric|min:0',
            'shop_name'       => 'required|string|max:255',
            'shop_phone'      => 'nullable|string|max:50',
            'shop_email'      => 'nullable|email|max:100',
            'shop_address'    => 'nullable|string|max:500',
            'shop_logo'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $keys = ['ongkir_per_km', 'ongkir_per_gram', 'shop_name', 'shop_phone', 'shop_email', 'shop_address'];

        foreach ($keys as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($key)]
            );
        }

        if ($request->hasFile('shop_logo')) {
            $file = $request->file('shop_logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/logo'), $filename);
            Setting::updateOrCreate(
                ['key' => 'shop_logo'],
                ['value' => 'images/logo/' . $filename]
            );
        }

        // Hapus cache agar layout langsung terupdate
        \Illuminate\Support\Facades\Cache::forget('shop_settings');

        return redirect()->back()->with('success', 'Konfigurasi Toko & Tarif berhasil diperbarui!');
    }
}