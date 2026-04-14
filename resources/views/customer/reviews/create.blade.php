<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tulis Ulasan') }}
        </h2>
    </x-slot>
    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
               <div class="flex items-center mb-6 border-b pb-4">
                    @php 
                        // Prioritas: Gambar varian, kemudian gambar utama produk
                        $imgUrl = null;
                        if (isset($variant) && $variant->image) {
                            $imgUrl = $variant->image->image_path;
                        } else {
                            $imgUrl = $product->images->first()?->image_path;
                        }
                    @endphp

                    @if($imgUrl)
                        <img src="{{ asset('storage/' . $imgUrl) }}" class="w-16 h-16 object-cover rounded mr-4">
                    @else
                        {{-- Jika produk tidak punya gambar, tampilkan ikon kardus --}}
                        <div class="w-16 h-16 bg-gray-100 flex items-center justify-center rounded mr-4 text-2xl">
                            📦
                        </div>
                    @endif
                    
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">{{ $product->name }}</h3>
                        @if(isset($variant))
                            <p class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded inline-block mb-1">{{ $variant->name }}</p>
                        @endif
                        <p class="text-sm text-gray-500">Order ID: #{{ $order->id }}</p>
                    </div>
                </div>
                <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="product_variant_id" value="{{ $variant?->id }}">
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

                    <div class="mb-4" x-data="{ rating: 0, hover: 0 }">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Berikan Rating</label>
                        <div class="flex space-x-1">
                            <template x-for="star in 5">
                                <button type="button" 
                                    @click="rating = star" 
                                    @mouseover="hover = star" 
                                    @mouseleave="hover = 0"
                                    class="focus:outline-none transition-colors duration-200">
                                    <i class="fas fa-star text-2xl" 
                                       :class="(hover >= star || rating >= star) ? 'text-yellow-400' : 'text-gray-300'"></i>
                                </button>
                            </template>
                        </div>
                        <input type="hidden" name="rating" :value="rating" required>
                        @error('rating') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="comment" class="block text-gray-700 text-sm font-bold mb-2">Ulasan Anda</label>
                        <textarea name="comment" id="comment" rows="4" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            placeholder="Bagaimana kualitas produk ini?"></textarea>
                        @error('comment') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Foto Produk (Opsional)</label>
                        <input type="file" name="images[]" multiple accept="image/*"
                            class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100">
                        @error('images.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ url()->previous() }}" class="mr-4 px-4 py-2 text-gray-600 hover:text-gray-800">Batal</a>
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 transition">
                            Kirim Ulasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>