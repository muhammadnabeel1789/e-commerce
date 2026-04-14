<?php

namespace App\Services;

class ShippingService
{
    // ── Koordinat toko (Depok, Jawa Barat) ──
    const STORE_LAT  = -6.4025;
    const STORE_LNG  = 106.7942;

    // Tarif: Rp 200 per km per kg, minimal Rp 5.000
   
    const MIN_COST            = 5000;

    /**
     * Hitung ongkos kirim berdasarkan jarak (Haversine) + berat.
     *
     * @param  float  $destLat   Latitude kota tujuan
     * @param  float  $destLng   Longitude kota tujuan
     * @param  int    $weightGram Berat barang dalam gram
     * @return array  ['cost' => int, 'distance_km' => float, 'weight_kg' => float]
     */
   public static function calculate(float $destLat, float $destLng, int $weightGram): array
    {
        $distanceKm = self::haversine(self::STORE_LAT, self::STORE_LNG, $destLat, $destLng);

        // Minimal jarak 1 km (untuk dalam kota/kelurahan yang sama)
        $distanceKm = max($distanceKm, 1);

        // Ambil dari pengaturan database
        $ongkirPerKm = (int) (\App\Models\Setting::where('key', 'ongkir_per_km')->value('value') ?? 200);
        $ongkirPerGram = (int) (\App\Models\Setting::where('key', 'ongkir_per_gram')->value('value') ?? 100);

        // Hitung Ongkir: (Jarak * Tarif per KM) + (Berat Gram * Tarif per Gram)
        $cost = (int) ceil(($distanceKm * $ongkirPerKm) + ($weightGram * $ongkirPerGram));

        $cost = max($cost, self::MIN_COST);

        // Bulatkan ke ratusan terdekat supaya rapih
        $cost = (int) (ceil($cost / 100) * 100);

        // Hitung estimasi hari (ETA) berdasarkan jarak
        if ($distanceKm <= 50) {
            $eta = '1-2 Hari';
        } elseif ($distanceKm <= 200) {
            $eta = '2-3 Hari';
        } elseif ($distanceKm <= 500) {
            $eta = '3-5 Hari';
        } else {
            $eta = '5-7 Hari';
        }

        return [
            'cost'          => $cost,
            'distance_km'   => round($distanceKm, 1),
            'weight_kg'     => round($weightGram / 1000, 2),
            'weight_gram'   => $weightGram,
            'eta'           => $eta,
            'ongkir_per_km' => $ongkirPerKm,
            'ongkir_per_gram' => $ongkirPerGram,
            'min_cost'      => self::MIN_COST,
        ];
    }

    /**
     * Formula Haversine — hitung jarak dua titik koordinat (km).
     */
    public static function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Ambil koordinat kota berdasarkan nama kota (dari tabel indoregion).
     * Indoregion tidak menyertakan lat/lng, jadi kita gunakan
     * koordinat ibu kota provinsi sebagai fallback.
     *
     * Untuk akurasi lebih baik, Anda bisa tambah kolom lat/lng
     * ke tabel cities dengan migration sendiri.
     */
    public static function getCityCoordinates(string $cityName, string $provinceName = ''): array
    {
        // ── Koordinat per ibu kota provinsi (34 provinsi Indonesia) ──
        $provinceCoords = [
            // Sumatera
            'Aceh'                         => [-5.5483, 95.3238],
            'Sumatera Utara'               => [3.5952,  98.6722],
            'Sumatera Barat'               => [-0.9471, 100.4172],
            'Riau'                         => [0.5071,  101.4478],
            'Kepulauan Riau'               => [0.9170,  104.4580],
            'Jambi'                        => [-1.6101, 103.6131],
            'Sumatera Selatan'             => [-2.9761, 104.7754],
            'Kepulauan Bangka Belitung'    => [-2.7410, 106.4406],
            'Bengkulu'                     => [-3.8004, 102.2655],
            'Lampung'                      => [-5.4294, 105.2610],
            // Jawa
            'DKI Jakarta'                  => [-6.2088, 106.8456],
            'Jawa Barat'                   => [-6.9175, 107.6191],
            'Banten'                       => [-6.4058, 106.0640],
            'Jawa Tengah'                  => [-7.1500, 110.1403],
            'DI Yogyakarta'                => [-7.7956, 110.3695],
            'Jawa Timur'                   => [-7.5361, 112.2384],
            // Kalimantan
            'Kalimantan Barat'             => [-0.0236, 109.3425],
            'Kalimantan Tengah'            => [-1.6814, 113.3824],
            'Kalimantan Selatan'           => [-3.3194, 114.5908],
            'Kalimantan Timur'             => [0.5387,  116.4194],
            'Kalimantan Utara'             => [3.0731,  116.0413],
            // Sulawesi
            'Sulawesi Utara'               => [1.4931,  124.8413],
            'Gorontalo'                    => [0.5435,  123.0568],
            'Sulawesi Tengah'              => [-0.9003, 119.8779],
            'Sulawesi Barat'               => [-2.8441, 119.2321],
            'Sulawesi Selatan'             => [-5.1477, 119.4327],
            'Sulawesi Tenggara'            => [-4.0145, 122.5150],
            // Nusa Tenggara
            'Bali'                         => [-8.4095, 115.1889],
            'Nusa Tenggara Barat'          => [-8.6529, 117.3616],
            'Nusa Tenggara Timur'          => [-8.6574, 121.0794],
            // Maluku & Papua
            'Maluku'                       => [-3.2385, 130.1453],
            'Maluku Utara'                 => [1.5709,  127.8088],
            'Papua Barat'                  => [-1.3361, 133.1747],
            'Papua Barat Daya'             => [-1.3633, 132.3478],
            'Papua'                        => [-4.2699, 138.0804],
            'Papua Tengah'                 => [-3.9968, 136.3706],
            'Papua Pegunungan'             => [-4.0158, 138.9464],
            'Papua Selatan'                => [-7.2997, 138.3947],
        ];

        // ── Koordinat kota-kota besar (override lebih akurat) ──
        $cityCoords = [
            // DKI Jakarta
            'Jakarta Pusat'    => [-6.1862, 106.8346],
            'Jakarta Utara'    => [-6.1384, 106.8616],
            'Jakarta Barat'    => [-6.1682, 106.7641],
            'Jakarta Selatan'  => [-6.2615, 106.8106],
            'Jakarta Timur'    => [-6.2250, 106.9004],
            'Kepulauan Seribu' => [-5.6139, 106.5297],

            // Jawa Barat
            'Bandung'          => [-6.9175, 107.6191],
            'Bekasi'           => [-6.2383, 106.9756],
            'Depok'            => [-6.4025, 106.7942],
            'Bogor'            => [-6.5971, 106.8060],
            'Tangerang'        => [-6.1702, 106.6402],
            'Tangerang Selatan'=> [-6.2877, 106.7159],
            'Sukabumi'         => [-6.9184, 106.9280],
            'Cirebon'          => [-6.7320, 108.5523],
            'Garut'            => [-7.2119, 107.9063],
            'Tasikmalaya'      => [-7.3274, 108.2207],
            'Karawang'         => [-6.3214, 107.3381],
            'Purwakarta'       => [-6.5571, 107.4393],
            'Subang'           => [-6.5727, 107.7620],
            'Indramayu'        => [-6.3286, 108.3190],
            'Majalengka'       => [-6.8364, 108.2277],
            'Kuningan'         => [-6.9757, 108.4852],
            'Ciamis'           => [-7.3326, 108.3524],
            'Banjar'           => [-7.3730, 108.5350],
            'Pangandaran'      => [-7.6883, 108.6508],

            // Banten
            'Serang'           => [-6.1201, 106.1500],
            'Cilegon'          => [-6.0025, 106.0018],
            'Lebak'            => [-6.5582, 106.2489],
            'Pandeglang'       => [-6.3083, 106.1061],

            // Jawa Tengah
            'Semarang'         => [-6.9932, 110.4203],
            'Solo'             => [-7.5561, 110.8315],
            'Surakarta'        => [-7.5561, 110.8315],
            'Yogyakarta'       => [-7.7956, 110.3695],
            'Magelang'         => [-7.4797, 110.2176],
            'Salatiga'         => [-7.3305, 110.5084],
            'Pekalongan'       => [-6.8886, 109.6753],
            'Tegal'            => [-6.8797, 109.1256],
            'Brebes'           => [-6.8699, 108.9602],
            'Purwokerto'       => [-7.4306, 109.2325],
            'Banyumas'         => [-7.5159, 109.2960],
            'Cilacap'          => [-7.7331, 109.0159],
            'Kebumen'          => [-7.6778, 109.6500],
            'Purworejo'        => [-7.7136, 110.0161],
            'Klaten'           => [-7.7059, 110.6010],
            'Boyolali'         => [-7.5322, 110.5982],
            'Sukoharjo'        => [-7.6864, 110.8387],
            'Wonogiri'         => [-7.8159, 110.9246],
            'Karanganyar'      => [-7.5985, 111.0180],
            'Sragen'           => [-7.4263, 111.0286],
            'Grobogan'         => [-7.0039, 110.9150],
            'Blora'            => [-6.9621, 111.4135],
            'Rembang'          => [-6.7092, 111.3431],
            'Pati'             => [-6.7535, 111.0299],
            'Kudus'            => [-6.8042, 110.8401],
            'Jepara'           => [-6.5877, 110.6679],
            'Demak'            => [-6.8943, 110.6390],
            'Kendal'           => [-6.9229, 110.2025],
            'Batang'           => [-6.9136, 109.7290],
            'Pemalang'         => [-6.8893, 109.3807],
            'Wonosobo'         => [-7.3614, 109.9078],
            'Temanggung'       => [-7.3168, 110.1677],
            'Banjarnegara'     => [-7.3900, 109.6957],
            'Purbalingga'      => [-7.3909, 109.3657],

            // Jawa Timur
            'Surabaya'         => [-7.2575, 112.7521],
            'Malang'           => [-7.9797, 112.6304],
            'Sidoarjo'         => [-7.4478, 112.7183],
            'Gresik'           => [-7.1574, 112.6524],
            'Mojokerto'        => [-7.4727, 111.5234],
            'Jombang'          => [-7.5499, 112.2384],
            'Pasuruan'         => [-7.6456, 112.9076],
            'Probolinggo'      => [-7.7543, 113.2159],
            'Batu'             => [-7.8709, 122.5154],
            'Kediri'           => [-7.8168, 112.0112],
            'Blitar'           => [-8.0983, 112.1686],
            'Tulungagung'      => [-8.0644, 111.9024],
            'Nganjuk'          => [-7.6043, 111.9041],
            'Madiun'           => [-7.6297, 111.5234],
            'Ngawi'            => [-7.4038, 111.4464],
            'Bojonegoro'       => [-7.1610, 111.8807],
            'Tuban'            => [-6.8972, 112.0527],
            'Lamongan'         => [-7.1173, 112.4176],
            'Bangkalan'        => [-7.0416, 112.7344],
            'Sampang'          => [-7.1796, 113.2467],
            'Pamekasan'        => [-7.1579, 113.4728],
            'Sumenep'          => [-6.9883, 113.9607],
            'Jember'           => [-8.1684, 113.7003],
            'Bondowoso'        => [-7.9174, 113.8222],
            'Situbondo'        => [-7.7061, 114.0080],
            'Banyuwangi'       => [-8.2193, 114.3691],
            'Lumajang'         => [-8.1321, 113.2226],
            'Trenggalek'       => [-8.0500, 111.7093],
            'Pacitan'          => [-8.1845, 111.1020],
            'Ponorogo'         => [-7.8601, 111.4634],
            'Magetan'          => [-7.6517, 111.3309],

            // Bali
            'Denpasar'         => [-8.6705, 115.2126],
            'Badung'           => [-8.5731, 115.1879],
            'Gianyar'          => [-8.5313, 115.3267],
            'Tabanan'          => [-8.5429, 115.0843],
            'Klungkung'        => [-8.5356, 115.4026],
            'Bangli'           => [-8.4502, 115.3593],
            'Karangasem'       => [-8.4460, 115.6155],
            'Buleleng'         => [-8.1122, 115.0892],
            'Jembrana'         => [-8.3695, 114.6524],

            // Sumatera Utara
            'Medan'            => [3.5952,  98.6722],
            'Binjai'           => [3.5923,  98.4851],
            'Tebing Tinggi'    => [3.3289,  99.1622],
            'Pematangsiantar'  => [2.9595,  99.0687],
            'Tanjungbalai'     => [2.9666,  99.7990],
            'Sibolga'          => [1.7390,  98.7754],
            'Padangsidimpuan'  => [1.3790,  99.2732],
            'Gunungsitoli'     => [1.2922,  97.6178],

            // Sumatera Barat
            'Padang'           => [-0.9471, 100.4172],
            'Bukittinggi'      => [-0.3081, 100.3691],
            'Payakumbuh'       => [-0.2270, 100.6367],
            'Solok'            => [-0.7979, 100.6554],
            'Sawahlunto'       => [-0.6817, 100.7804],
            'Padang Panjang'   => [-0.4583, 100.4072],
            'Pariaman'         => [-0.6254, 100.1162],

            // Riau
            'Pekanbaru'        => [0.5071,  101.4478],
            'Dumai'            => [1.6773,  101.4491],

            // Kepulauan Riau
            'Batam'            => [1.1301,  104.0529],
            'Tanjungpinang'    => [0.9170,  104.4580],

            // Jambi
            'Jambi'            => [-1.6101, 103.6131],
            'Sungai Penuh'     => [-2.0627, 101.3942],

            // Sumatera Selatan
            'Palembang'        => [-2.9761, 104.7754],
            'Prabumulih'       => [-3.4349, 104.2365],
            'Pagar Alam'       => [-4.0283, 103.2598],
            'Lubuklinggau'     => [-3.3005, 102.8619],

            // Bengkulu
            'Bengkulu'         => [-3.8004, 102.2655],

            // Lampung
            'Bandar Lampung'   => [-5.4294, 105.2610],
            'Metro'            => [-5.1131, 105.3068],

            // Bangka Belitung
            'Pangkalpinang'    => [-2.1316, 106.1161],

            // Kalimantan Barat
            'Pontianak'        => [-0.0236, 109.3425],
            'Singkawang'       => [0.9010,  108.9770],

            // Kalimantan Tengah
            'Palangka Raya'    => [-2.2161, 113.9135],

            // Kalimantan Selatan
            'Banjarmasin'      => [-3.3194, 114.5908],
            'Banjarbaru'       => [-3.4423, 114.8302],

            // Kalimantan Timur
            'Samarinda'        => [0.5022,  117.1536],
            'Balikpapan'       => [-1.2654, 116.8312],
            'Bontang'          => [0.1280,  117.4999],

            // Kalimantan Utara
            'Tarakan'          => [3.2970,  117.6335],

            // Sulawesi Utara
            'Manado'           => [1.4931,  124.8413],
            'Bitung'           => [1.4406,  125.1983],
            'Tomohon'          => [1.3242,  124.8348],
            'Kotamobagu'       => [0.7292,  124.3155],

            // Sulawesi Tengah
            'Palu'             => [-0.9003, 119.8779],

            // Sulawesi Selatan
            'Makassar'         => [-5.1477, 119.4327],
            'Parepare'         => [-4.0068, 119.6308],
            'Palopo'           => [-3.0025, 120.1965],

            // Sulawesi Tenggara
            'Kendari'          => [-3.9985, 122.5129],
            'Baubau'           => [-5.4783, 122.6137],

            // Gorontalo
            'Gorontalo'        => [0.5435,  123.0568],

            // Sulawesi Barat
            'Mamuju'           => [-2.6814, 118.8868],

            // NTB
            'Mataram'          => [-8.5833, 116.1167],
            'Bima'             => [-8.4714, 118.7266],

            // NTT
            'Kupang'           => [-10.1771, 123.6070],

            // Maluku
            'Ambon'            => [-3.6953, 128.1814],
            'Tual'             => [-5.6536, 132.7503],

            // Maluku Utara
            'Ternate'          => [0.7897,  127.3688],
            'Tidore Kepulauan' => [0.7172,  127.4175],

            // Papua
            'Jayapura'         => [-2.5333, 140.7167],

            // Papua Barat
            'Manokwari'        => [-0.8619, 134.0619],
            'Sorong'           => [-0.8833, 131.2500],
        ];

        // Cari di data kota dulu
        $cityName = trim($cityName);
        foreach ($cityCoords as $key => $coords) {
            if (stripos($cityName, $key) !== false || stripos($key, $cityName) !== false) {
                return ['lat' => $coords[0], 'lng' => $coords[1]];
            }
        }

        // Fallback ke koordinat provinsi
        $provinceName = trim($provinceName);
        foreach ($provinceCoords as $key => $coords) {
            if (stripos($provinceName, $key) !== false || stripos($key, $provinceName) !== false) {
                return ['lat' => $coords[0], 'lng' => $coords[1]];
            }
        }

        // Jika tidak ditemukan, kembalikan koordinat Jakarta sebagai default
        return ['lat' => -6.2088, 'lng' => 106.8456];
    }
}