<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Produk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Header Section --}}
            <div class="mb-6 sm:flex sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-bold leading-6 text-gray-900">Daftar Produk</h3>
                    <p class="mt-1 text-sm text-gray-500">Kelola semua produk yang tersedia di toko Anda.</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('admin.products.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Produk
                    </a>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Gambar</th>
                                <th class="px-6 py-3">Nama Produk</th>
                                <th class="px-6 py-3">Kategori</th>
                                <th class="px-6 py-3">Harga</th>
                                <th class="px-6 py-3">Stok per Varian</th>
                                <th class="px-6 py-3">Total Stok</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    @if($product->images->where('is_primary', true)->first())
                                        <img src="{{ Storage::url($product->images->where('is_primary', true)->first()->image_path) }}" class="w-12 h-12 object-cover rounded">
                                    @elseif($product->images->first())
                                        <img src="{{ Storage::url($product->images->first()->image_path) }}" class="w-12 h-12 object-cover rounded">
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center text-gray-400">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $product->name }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $product->category->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($product->variants && $product->variants->count() > 0)
                                        @php
                                            $hargaList = $product->variants->pluck('additional_price')->filter();
                                            $minPrice = $hargaList->min();
                                            $maxPrice = $hargaList->max();
                                        @endphp
                                        
                                        @if($minPrice == $maxPrice)
                                            <span class="font-semibold text-indigo-600">
                                                Rp {{ number_format($minPrice, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="font-semibold text-indigo-600">
                                                Rp {{ number_format($minPrice, 0, ',', '.') }}
                                            </span>
                                            <span class="text-xs text-gray-400"> - {{ number_format($maxPrice, 0, ',', '.') }}</span>
                                        @endif
                                        
                                        @if($product->discount_price)
                                            <div class="text-xs text-red-600 mt-1">
                                                Diskon: Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-red-500 text-xs">Belum ada varian</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($product->variants && $product->variants->count() > 0)
                                        <div class="space-y-1">
                                            @foreach($product->variants as $variant)
                                                <div class="flex items-center gap-2 text-xs">
                                                    <span class="inline-block w-16 font-medium">{{ $variant->size ?? '-' }}</span>
                                                    <span class="inline-flex items-center gap-1">
                                                        @if($variant->color_code)
                                                            <span class="w-3 h-3 rounded-full" style="background-color: {{ $variant->color_code }};"></span>
                                                        @endif
                                                        <span>{{ $variant->color ?? '-' }}</span>
                                                    </span>
                                                    <span class="ml-auto font-bold {{ $variant->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $variant->stock }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($product->stock > 0)
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                            {{ $product->stock }} Item
                                        </span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                            Habis
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded text-xs px-3 py-2 text-center transition">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-white bg-red-500 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded text-xs px-3 py-2 text-center transition">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-box-open text-4xl mb-3 text-gray-300"></i>
                                        <p>Belum ada data produk.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $products->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>