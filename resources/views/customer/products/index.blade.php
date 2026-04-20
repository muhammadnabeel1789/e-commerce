<x-app-layout>
    <x-slot name="header">Katalog Produk</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4">
            
            {{-- Info Hasil Pencarian --}}
            @if(request('search'))
                <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 flex justify-between items-center rounded-r shadow-sm">
                    <div class="flex items-center">
                        <p class="text-sm text-blue-700">
                            Menampilkan hasil pencarian untuk: <strong>"{{ request('search') }}"</strong>
                        </p>
                    </div>
                    <a href="{{ route('products.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 hover:underline">
                        &times; Hapus Pencarian
                    </a>
                </div>
            @endif

            {{-- Grid Produk --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($products as $product)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden group">
                    <a href="{{ route('products.show', $product->id) }}">
                        <div class="relative h-64 overflow-hidden">
                            @if($product->images && $product->images->count() > 0)
                                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                            @else
                                <img src="https://via.placeholder.com/400x300?text=No+Image" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                            @endif
                            
                            @if($product->stock == 0)
                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center text-white font-bold">HABIS</div>
                            @endif
                        </div>
                    </a>

                    <div class="p-4">
                       
                        @if($product->category)
                            <span>{{ $product->category->name }}</span>
                        @endif
                        @if($product->brand && $product->category)
                            <span class="mx-1">•</span>
                        @endif
                        @if($product->brand)
                            <span class="font-semibold">{{ $product->brand->name }}</span>
                        @endif
                    
                        
                        <h3 class="text-lg font-bold text-gray-900 truncate">
                            <a href="{{ route('products.show', $product->id) }}" class="hover:text-indigo-600 transition">
                                {{ $product->name }}
                            </a>
                        </h3>
                        
                        {{-- Harga --}}
                        <div class="flex items-center justify-between mt-3">
                            <div>
                                @php
                                    $minPrice = $product->variants->min('additional_price') ?? 0;
                                    $maxPrice = $product->variants->max('additional_price') ?? 0;
                                @endphp
                                
                                @if($minPrice == $maxPrice)
                                    <span class="text-indigo-600 font-bold">
                                        Rp {{ number_format($minPrice, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-indigo-600 font-bold">
                                        Rp {{ number_format($minPrice, 0, ',', '.') }}
                                    </span>
                                    <span class="text-xs text-gray-400"> - {{ number_format($maxPrice, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            
                            {{-- Info Stok --}}
                            <span class="text-xs {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $product->stock }} item
                            </span>
                            
                           
                        </div>
                    </div>
                </div>

                @empty
                <div class="col-span-4 text-center text-gray-500 py-10">
                    <div class="text-gray-400 mb-3">
                        <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                    <p class="text-lg font-medium">Produk belum tersedia.</p>
                    <p class="text-sm text-gray-400 mt-1">Silakan cek kembali nanti.</p>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $products->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>