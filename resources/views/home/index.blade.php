<x-app-layout>
    {{-- Hero Section --}}
    <section class="bg-gradient-to-r from-blue-100 to-blue-50 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                {{-- Left Content --}}
                <div>
                    <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                  Tampil Percaya Diri dengan Koleksi Terbaru
                    </h1>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                      Destinasi belanja terbaik untuk kebutuhan Anda. Kualitas terjamin dengan pengiriman aman dan cepat ke seluruh Indonesia.
                    </p>
                @if(!\Illuminate\Support\Facades\Auth::check() || \Illuminate\Support\Facades\Auth::user()->role !== 'admin')
                <a href="{{ route('products.index') }}" 
                   class="inline-block bg-blue-600 text-white px-8 py-3 rounded hover:bg-blue-700 transition font-medium">
                    Mulai Untuk Belanja
                </a>
                @endif

                    {{-- Category Cards --}}
                    <div class="grid grid-cols-2 gap-4 mt-12">
                        {{-- Men Shopping --}}
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR7fZ54UvLBjwiBX7ZMu59A84VMobSRrPbVBA&s" 
                                 alt="Men Shopping" 
                                 class="w-full h-40 object-cover">
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900">Pakaian Pria</h3>
                            </div>
                        </div>

                        {{-- Women Shopping --}}
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                            <img src="https://media.istockphoto.com/id/1338894509/id/foto/wanita-memilih-gaya-baru-untuk-dirinya-sendiri.jpg?s=612x612&w=0&k=20&c=Y0S_n1xKB70qx3TNDxnByFT15bJXpePRidB7zOtMIIc=" 
                                 alt="Women Shopping" 
                                 class="w-full h-40 object-cover">
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900">Pakaian Wanita</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Image --}}
                <div class="relative">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5?w=800" 
                             alt="Shopping" 
                             class="rounded-lg shadow-2xl w-full">

                        {{-- Promo Badge --}}
                        <div class="absolute bottom-8 left-8 bg-white px-6 py-4 rounded-lg shadow-lg">
                            <p class="text-sm text-gray-600 mb-1">Dapatkan koleksi pakaian pria dan wanita terbaik dari merek ternama dengan kualitas terjamin.</p>
                            @if(!\Illuminate\Support\Facades\Auth::check() || \Illuminate\Support\Facades\Auth::user()->role !== 'admin')
                            <a href="{{ route('products.index') }}" class="text-blue-600 font-semibold text-sm hover:underline">
                                Cek Sekarang →
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Cultural Best Sellers Section --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="flex justify-between items-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900">Rekomendasi Produk</h2>
        </div>
      
        {{-- Products Grid --}}
        @if(isset($featuredProducts) && $featuredProducts->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($featuredProducts->take(4) as $product)
            <div class="group bg-white rounded-lg overflow-hidden hover:shadow-xl transition-all duration-300 border border-gray-200">
                {{-- Product Image --}}
                
                    @if($product->images->count() > 0)
                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                             alt="{{ $product->name }}"
                             class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-5xl"></i>
                        </div>
                    @endif
                    
                    {{-- Stock Badge --}}
                    @if($product->stock <= 0)
                        <div class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 text-xs font-bold rounded">
                            HABIS
                        </div>
                    @elseif($product->stock <= 5)
                        <div class="absolute top-4 right-4 bg-orange-500 text-white px-3 py-1 text-xs font-bold rounded">
                            STOK TERBATAS
                        </div>
                    @endif
                

                {{-- Product Info --}}
                <div class="p-6">
                    {{-- Category & Brand --}}
                    <div class="text-sm text-gray-500 mb-2 text-center">
                        @if($product->category)
                            <span>{{ $product->category->name }}</span>
                        @endif
                        @if($product->brand && $product->category)
                            <span class="mx-1">•</span>
                        @endif
                        @if($product->brand)
                            <span class="font-semibold">{{ $product->brand->name }}</span>
                        @endif
                    </div>

                    {{-- Product Name --}}
                    <a class="block text-center hover:text-blue-600 transition">
                        <h3 class="font-bold text-lg mb-3 text-gray-900 line-clamp-2 min-h-[3.5rem]">
                            {{ $product->name }}
                        </h3>
                    </a>

                    {{-- Rating --}}
                    <div class="flex items-center justify-center gap-1 mb-3">
                        @php
                            $avgRating = $product->reviews_avg_rating ?? 0;
                            $reviewCount = $product->reviews_count ?? 0;
                        @endphp
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($avgRating))
                                <i class="fas fa-star text-yellow-400 text-sm"></i>
                            @elseif($i - $avgRating < 1 && $i - $avgRating > 0)
                                <i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>
                            @else
                                <i class="far fa-star text-gray-300 text-sm"></i>
                            @endif
                        @endfor
                        <span class="text-sm text-gray-500 ml-1">({{ $reviewCount }})</span>
                    </div>

                    {{-- Price --}}
                   {{-- Price --}}
<div class="text-center mb-4">
    @php
        $harga = $product->variants->min('additional_price') ?? 0;
    @endphp
    
    <span class="text-2xl font-bold text-blue-600">
        Rp {{ number_format($harga, 0, ',', '.') }}
    </span>
</div>

    {{-- CEK STOK --}}
    @if($product->stock > 0)
        @if(!\Illuminate\Support\Facades\Auth::check() || \Illuminate\Support\Facades\Auth::user()->role !== 'admin')
            <a href="{{ route('products.show', $product->id) }}"
               class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition shadow-sm shadow-blue-100">
                <i class="fas fa-shopping-bag mr-1"></i> Lihat Produk
            </a>
        @endif
    @else
        {{-- STOK HABIS --}}
        <button disabled 
                class="w-full bg-gray-300 text-gray-500 py-3 rounded-lg font-semibold cursor-not-allowed">
            <i class="fas fa-times-circle"></i>
            <span>Stok Habis</span>
        </button>
    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
    

    {{-- Brands Section --}}
    @if(isset($brands) && $brands->count() > 0)
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Brand Yang Berkualitas</h2>
                <p class="text-gray-600">Brand dan Merek Unggulan Kami:</p>
            </div>

            {{-- Brands Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8">
                @foreach($brands->take(12) as $brand)
                <div class="group bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-all duration-300 flex items-center justify-center">
                    @if($brand->logo)
                        <img src="{{ asset('storage/' . $brand->logo) }}" 
                             alt="{{ $brand->name }}"
                             class="max-h-16 w-auto object-contain grayscale group-hover:grayscale-0 transition-all duration-300">
                    @else
                        <span class="text-gray-700 font-bold text-lg group-hover:text-blue-600 transition">
                            {{ $brand->name }}
                        </span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

{{-- Features Section --}}
<section class="py-16 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            {{-- Timely Delivery (Pengiriman Tepat Waktu) --}}
            <div class="text-center p-8">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    {{-- Menggunakan ikon stopwatch/truck --}}
                    <i class="fas fa-shipping-fast text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Pengiriman Tepat Waktu</h3>
                <p class="text-gray-600">Garansi barang sampai di tangan Anda sesuai estimasi</p>
            </div>

            {{-- Branded/Original Products (Produk Brand) --}}
            <div class="text-center p-8">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    {{-- Menggunakan ikon medali/bintang untuk kesan Original --}}
                    <i class="fas fa-medal text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Produk 100% Original</h3>
                <p class="text-gray-600">Jaminan keaslian produk dari brand ternama</p>
            </div>

            {{-- Secure Payment (Tetap) --}}
            <div class="text-center p-8">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-lock text-purple-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Pembayaran Aman</h3>
                <p class="text-gray-600">100% perlindungan pembayaran aman</p>
            </div>

        </div>
    </div>
</section>

    @push('styles')
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
    @endpush
</x-app-layout>