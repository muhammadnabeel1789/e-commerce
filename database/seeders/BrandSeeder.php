<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        // Masukkan Nama Brand dan Link Logo (URL) yang Anda mau di sini
        $brands = [
            [
                'name' => 'Zara',
                'url' => 'https://ia600409.us.archive.org/21/items/3146173327_202604/3146173327.jpg'
            ],
            [
                'name' => 'Adidas',
                'url' => 'https://ia601404.us.archive.org/12/items/download-1_20260221/download%20%281%29.png'
            ],
            [
                'name' => 'Uniqlo',
                'url' => 'https://ia601004.us.archive.org/29/items/download-2_20260221/download%20%282%29.png'
            ],
            [
                'name' => 'Gucci',
                'url' => 'https://ia600700.us.archive.org/0/items/download-3_20260221_0446/download%20%283%29.png'
            ],
            [
                'name' => 'Nike',
                'url' => 'https://ia600705.us.archive.org/9/items/images-1_20260408/images%20%281%29.png'
            ],
            [
                'name' => 'Erigo',
                'url' => 'https://ia601506.us.archive.org/19/items/id-11134216-81ztl-meplxo4fk2dgbc_202604/id-11134216-81ztl-meplxo4fk2dgbc.jpg'
            ]
        ];

        // Buat folder jika belum ada
        if (!Storage::disk('public')->exists('brands')) {
            Storage::disk('public')->makeDirectory('brands');
        }

        foreach ($brands as $brand) {

            $fileName = 'brands/' . Str::slug($brand['name']) . '.png';

            // LOGIKA: Jika ada URL, pakai URL itu. Jika tidak ada, pakai UI Avatars (Inisial)
            if (!empty($brand['url'])) {
                $imageUrl = $brand['url'];
            } else {
                $imageUrl = 'https://ui-avatars.com/api/?name=' . urlencode($brand['name']) . '&background=random&color=fff&size=512';
            }

            try {
                // Proses Download Gambar dari Link
                $imageContent = file_get_contents($imageUrl);

                if ($imageContent) {
                    Storage::disk('public')->put($fileName, $imageContent);
                }
            } catch (\Exception $e) {
                // Jika link mati/error, set null
                $fileName = null;
            }

            // Simpan ke Database
            Brand::create([
                'name' => $brand['name'],
                'description' => 'Produk original kualitas terbaik dari ' . $brand['name'],
                'logo' => $fileName, // Yang masuk database tetap path lokal (brands/nama.png)
                'is_active' => true,
            ]);
        }
    }
}