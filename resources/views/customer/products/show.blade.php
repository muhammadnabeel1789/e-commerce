<x-app-layout>
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Header Produk --}}
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
               <h3 class="text-lg font-bold text-gray-900 truncate">  <p class="text-gray-500 mt-1">Kategori: {{ $product->category->name ?? 'Uncategorized' }}</p></h3>
                 <h3 class="text-lg font-bold text-gray-900 truncate"> <p class="text-gray-500 mt-1">Brand: {{ $product->brand->name ?? 'Uncategorized' }}</p></h3>
                <div class="flex items-center mt-2">
                    <div class="flex text-yellow-400">
                        @php $avgRating = $product->reviews->avg('rating') ?? 0; @endphp
                        @for($i=1; $i<=5; $i++)
                            <i class="fas fa-star {{ $i <= round($avgRating) ? '' : 'text-gray-300' }}"></i>
                        @endfor
                    </div>
                    <span class="text-gray-600 ml-2 text-sm">({{ $product->reviews->count() }} Ulasan)</span>
                </div>
            </div>

            {{-- ===== GAMBAR PRODUK ===== --}}
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                <div class="md:flex">
                    <div class="md:w-1/2 p-4">
                        @php
                            $defaultImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                            $defaultSrc   = $defaultImage ? asset('storage/' . $defaultImage->image_path) : 'https://via.placeholder.com/600x400?text=No+Image';
                        @endphp

                        {{-- Carousel Gambar Utama --}}
                        <div class="relative w-full mb-4 group overflow-hidden rounded-lg border border-gray-200">
                            <div id="image-carousel" class="flex overflow-x-auto snap-x snap-mandatory no-scrollbar scroll-smooth">
                                @if($product->images && $product->images->count() > 0)
                                    @foreach($product->images as $index => $image)
                                        <div class="w-full flex-shrink-0 snap-center relative" id="slide-[{{ $image->id }}]">
                                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                                 alt="{{ $product->name }}"
                                                 class="w-full aspect-square object-cover md:aspect-auto md:h-[400px]">
                                        </div>
                                    @endforeach
                                @else
                                    <div class="w-full flex-shrink-0 snap-center">
                                        <img src="{{ $defaultSrc }}"
                                             alt="{{ $product->name }}"
                                             class="w-full aspect-square object-cover md:aspect-auto md:h-[400px]">
                                    </div>
                                @endif
                            </div>

                            @if($product->images && $product->images->count() > 1)
                                <button onclick="slideCarousel(-1)" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full w-8 h-8 flex items-center justify-center shadow hidden sm:group-hover:flex transition z-10">
                                    <i class="fas fa-chevron-left text-sm"></i>
                                </button>
                                <button onclick="slideCarousel(1)" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full w-8 h-8 flex items-center justify-center shadow hidden sm:group-hover:flex transition z-10">
                                    <i class="fas fa-chevron-right text-sm"></i>
                                </button>
                                <div class="absolute bottom-3 right-3 bg-black/50 text-white text-xs px-3 py-1 rounded-full z-10">
                                    <span id="slide-indicator">1</span> / {{ $product->images->count() }}
                                </div>
                            @endif
                        </div>

                        {{-- Thumbnail Bawah --}}
                        @if($product->images && $product->images->count() > 1)
                        <div class="flex gap-2 overflow-x-auto no-scrollbar pb-2" id="thumbnail-grid">
                            @foreach($product->images as $index => $image)
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                     id="thumb-[{{ $image->id }}]"
                                     class="thumbnail w-16 h-16 flex-shrink-0 object-cover rounded cursor-pointer border-2 transition
                                            {{ $index === 0 ? 'border-indigo-500' : 'border-transparent hover:border-indigo-300' }}"
                                     onclick="goToSlide('{{ $image->id }}')">
                            @endforeach
                        </div>
                        @endif
                    </div>

                    

                    <div class="md:w-1/2 p-8">
                        <div class="prose max-w-none text-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 truncate"> <p>Deskripsi Produk<p></h3>
                            <p>{{ $product->description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== DAFTAR VARIAN ===== --}}
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Pilih Varian Produk</h2>

            @if($product->variants && $product->variants->count() > 0)

                @php
                    $variantImageMap = [];
                    foreach ($product->variants as $v) {
                        $variantImageMap[$v->id] = $v->image
                            ? asset('storage/' . $v->image->image_path)
                            : $defaultSrc;
                    }
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($product->variants as $variant)
                        @php
                            $stokVarian   = $variant->stock;
                            $hargaJual    = $variant->additional_price ?? 0;
                            $variantImgSrc = $variant->image
                                ? asset('storage/' . $variant->image->image_path)
                                : $defaultSrc;
                        @endphp

                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition border cursor-pointer
                                    {{ $stokVarian > 0 ? 'border-gray-200 hover:border-indigo-400' : 'border-red-200 bg-red-50' }}"
                             onclick="selectVariantCard(this, {{ $variant->id }})">

                            {{-- Foto Varian --}}
                            <div class="h-48 bg-gray-100 relative overflow-hidden">
                                <img src="{{ $variantImgSrc }}"
                                     alt="{{ $product->name }} - {{ $variant->size }} {{ $variant->color }}"
                                     class="w-full h-full object-cover transition duration-300 hover:scale-105">

                                @if($stokVarian <= 0)
                                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                        <span class="bg-red-600 text-white font-bold px-4 py-2 rounded-lg transform -rotate-12">STOK HABIS</span>
                                    </div>
                                @endif

                                <div class="selected-badge hidden absolute top-2 right-2 bg-indigo-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                                    ✓ Dipilih
                                </div>
                            </div>

                            <div class="p-5">
                                {{-- Info Ukuran & Warna --}}
                                <div class="flex items-center justify-between mb-3">
                                    <div class="bg-gray-100 px-3 py-1 rounded-lg">
                                        <span class="text-xs text-gray-500 block">Ukuran</span>
                                        <span class="font-bold text-lg text-gray-900">{{ $variant->size ?? '-' }}</span>
                                    </div>
                                    <div class="bg-gray-100 px-3 py-1 rounded-lg">
                                        <span class="text-xs text-gray-500 block">Warna</span>
                                        <div class="flex items-center gap-2">
                                            @if($variant->color_code)
                                                <span class="w-4 h-4 rounded-full border border-gray-300" style="background-color: {{ $variant->color_code }};"></span>
                                            @endif
                                            <span class="font-bold text-gray-900">{{ $variant->color ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- SKU Varian — tampil jika ada --}}
                                @if($variant->sku_variant)
                                    <p class="text-xs text-gray-400 mb-3">SKU: {{ $variant->sku_variant }}</p>
                                @endif

                                {{-- Harga --}}
                                <div class="mb-4">
                                    <span class="text-2xl font-bold text-indigo-600">
                                        Rp {{ number_format($hargaJual, 0, ',', '.') }}
                                    </span>
                                </div>

                                {{-- Stok --}}
                                <div class="flex items-center justify-between mb-4 p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-600">Stok tersedia:</span>
                                    <span class="font-bold {{ $stokVarian > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $stokVarian }} item
                                    </span>
                                </div>

                                {{-- Tombol Aksi --}}
                                @if($stokVarian > 0)
                                    <div class="space-y-3" onclick="event.stopPropagation()">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jumlah</label>
                                            <input type="number"
                                                   id="qty-{{ $variant->id }}"
                                                   class="w-full border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500"
                                                   value="1"
                                                   min="1"
                                                   max="{{ $stokVarian }}">
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <form action="{{ route('cart.add', $product->id) }}" method="POST"
                                                  onsubmit="return setQty('{{ $variant->id }}', this)">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="variant_id" value="{{ $variant->id }}">
                                                <input type="hidden" name="quantity" class="qty-input" value="1">
                                                <input type="hidden" name="action" value="add_to_cart">
                                                <button type="submit"
                                                        class="w-full bg-gray-200 text-gray-800 font-bold py-2 px-3 rounded-lg hover:bg-gray-300 transition flex items-center justify-center gap-1">
                                                    <i class="fas fa-shopping-cart"></i>
                                                    <span>Keranjang</span>
                                                </button>
                                            </form>

                                            <form action="{{ route('cart.add', $product->id) }}" method="POST"
                                                  onsubmit="return setQty('{{ $variant->id }}', this)">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="variant_id" value="{{ $variant->id }}">
                                                <input type="hidden" name="quantity" class="qty-input" value="1">
                                                <input type="hidden" name="action" value="buy_now">
                                                <button type="submit"
                                                        class="w-full bg-indigo-600 text-white font-bold py-2 px-3 rounded-lg hover:bg-indigo-700 transition flex items-center justify-center gap-1">
                                                    <i class="fas fa-bolt"></i>
                                                    <span>Beli</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <button disabled
                                            class="w-full bg-gray-300 text-gray-500 py-3 px-4 rounded-lg font-bold cursor-not-allowed">
                                        <i class="fas fa-times-circle"></i>
                                        <span>Stok Habis</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

            @else
                {{-- Produk tanpa varian --}}
                <div class="bg-white rounded-xl shadow-md overflow-hidden p-8 text-center">
                    <p class="text-gray-500 mb-4">Produk ini tidak memiliki varian</p>

                    @if($product->stock > 0)
                        <div class="max-w-md mx-auto">
                            <div class="mb-4">
                                <span class="text-3xl font-bold text-indigo-600">
                                    Rp {{ number_format($product->variants->first()->additional_price ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg mb-4">
                                <span class="text-gray-600">Stok: <strong class="text-green-600">{{ $product->stock }} item</strong></span>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm text-gray-600 mb-1">Jumlah</label>
                                <input type="number" id="qty-main"
                                       class="w-full border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500"
                                       value="1" min="1" max="{{ $product->stock }}">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" onsubmit="return setMainQty(this)">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="variant_id" value="">
                                    <input type="hidden" name="quantity" class="qty-input" value="1">
                                    <input type="hidden" name="action" value="add_to_cart">
                                    <button type="submit" class="w-full bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-lg hover:bg-gray-300 transition">
                                        <i class="fas fa-shopping-cart"></i> Keranjang
                                    </button>
                                </form>
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" onsubmit="return setMainQty(this)">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="variant_id" value="">
                                    <input type="hidden" name="quantity" class="qty-input" value="1">
                                    <input type="hidden" name="action" value="buy_now">
                                    <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700 transition">
                                        <i class="fas fa-bolt"></i> Beli Sekarang
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <button disabled class="bg-gray-300 text-gray-500 py-2 px-6 rounded-lg font-bold cursor-not-allowed">
                            Stok Habis
                        </button>
                    @endif
                </div>
            @endif

            {{-- ===== ULASAN ===== --}}
            <div class="mt-12 bg-white rounded-lg shadow-lg overflow-hidden p-6">
                <h3 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-4">Ulasan Pelanggan</h3>

                @forelse($product->reviews as $review)
                    <div class="border-b border-gray-100 pb-6 mb-6 last:border-0 last:pb-0 last:mb-0">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold mr-3 uppercase">
                                    {{ substr($review->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800">{{ $review->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex text-yellow-400 mb-2 text-sm">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>

                        <p class="text-gray-700 mb-3 leading-relaxed">{{ $review->comment }}</p>

                        @if($review->images->count() > 0)
                            <div class="flex space-x-2 mt-2">
                                @foreach($review->images as $img)
                                    <img src="{{ asset('storage/' . $img->image_path) }}"
                                         class="w-20 h-20 object-cover rounded cursor-pointer border border-gray-200 hover:opacity-75 transition"
                                         onclick="window.open(this.src)">
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-10 bg-gray-50 rounded-lg">
                        <i class="far fa-comment-dots text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-500">Belum ada ulasan untuk produk ini.</p>
                    </div>
                @endforelse
            </div>

            {{-- ===== PRODUK TERKAIT ===== --}}
            @if($relatedProducts->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Produk Terkait</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $related)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300 group">
                        <a href="{{ route('products.show', $related->id) }}" class="block relative overflow-hidden h-48">
                            @if($related->images && $related->images->count() > 0)
                                <img src="{{ asset('storage/' . $related->images->first()->image_path) }}"
                                     alt="{{ $related->name }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                            @else
                                <img src="https://via.placeholder.com/300x200?text=No+Image"
                                     class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                            @endif
                        </a>
                        <div class="p-4">
                            <h3 class="text-sm font-bold text-gray-900 truncate mb-1">
                                <a href="{{ route('products.show', $related->id) }}" class="hover:text-indigo-600">
                                    {{ $related->name }}
                                </a>
                            </h3>
                            @php
                                $relatedMinPrice = $related->variants->min('additional_price') ?? 0;
                                $relatedMaxPrice = $related->variants->max('additional_price') ?? 0;
                            @endphp
                            @if($relatedMinPrice == $relatedMaxPrice)
                                <p class="text-indigo-600 font-bold">Rp {{ number_format($relatedMinPrice, 0, ',', '.') }}</p>
                            @else
                                <p class="text-indigo-600 font-bold">Rp {{ number_format($relatedMinPrice, 0, ',', '.') }} - {{ number_format($relatedMaxPrice, 0, ',', '.') }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

    <script>
        const variantImageMap = @json($variantImageMap ?? []);
        const carousel = document.getElementById('image-carousel');
        const indicator = document.getElementById('slide-indicator');

        // Update indikator & thumbnail saat carousel digeser
        if(carousel) {
            carousel.addEventListener('scroll', () => {
                const currentSlideIndex = Math.round(carousel.scrollLeft / carousel.clientWidth);
                if(indicator) indicator.innerText = currentSlideIndex + 1;
                document.querySelectorAll('.thumbnail').forEach((t, index) => {
                    if (index === currentSlideIndex) {
                        t.classList.add('border-indigo-500');
                        t.classList.remove('border-transparent', 'border-gray-200');
                        t.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    } else {
                        t.classList.remove('border-indigo-500');
                        t.classList.add('border-transparent');
                    }
                });
            });
        }

        function slideCarousel(direction) {
            if(!carousel) return;
            carousel.scrollBy({ left: direction * carousel.clientWidth, behavior: 'smooth' });
        }

        function goToSlide(imageId) {
            const slide = document.getElementById('slide-[' + imageId + ']');
            if(slide && carousel) {
                carousel.scrollTo({ left: slide.offsetLeft - carousel.offsetLeft, behavior: 'smooth' });
            }
        }

        function selectVariantCard(cardEl, variantId) {
            document.querySelectorAll('[onclick^="selectVariantCard"]').forEach(c => {
                c.classList.remove('border-indigo-500', 'ring-2', 'ring-indigo-400');
                c.classList.add('border-gray-200');
                c.querySelector('.selected-badge').classList.add('hidden');
            });
            cardEl.classList.add('border-indigo-500', 'ring-2', 'ring-indigo-400');
            cardEl.classList.remove('border-gray-200');
            cardEl.querySelector('.selected-badge').classList.remove('hidden');

            if (variantImageMap[variantId] && carousel) {
                const targetSrc = variantImageMap[variantId];
                const targetImg = Array.from(carousel.querySelectorAll('img')).find(img => img.src === targetSrc);
                if(targetImg) {
                    const slide = targetImg.closest('.snap-center');
                    carousel.scrollTo({ left: slide.offsetLeft - carousel.offsetLeft, behavior: 'smooth' });
                }
            }
        }

        function setQty(variantId, form) {
            const qtyInput = document.getElementById('qty-' + variantId);
            const qty = parseInt(qtyInput.value);
            if (qty < 1) { alert('Jumlah minimal 1'); return false; }
            if (qty > parseInt(qtyInput.max)) { alert('Jumlah melebihi stok (maks: ' + qtyInput.max + ')'); return false; }
            form.querySelector('.qty-input').value = qty;
            return true;
        }

        function setMainQty(form) {
            const qtyInput = document.getElementById('qty-main');
            const qty = parseInt(qtyInput.value);
            if (qty < 1) { alert('Jumlah minimal 1'); return false; }
            if (qty > parseInt(qtyInput.max)) { alert('Jumlah melebihi stok (maks: ' + qtyInput.max + ')'); return false; }
            form.querySelector('.qty-input').value = qty;
            return true;
        }

        function changeImage(src) {
            const targetImg = Array.from(carousel.querySelectorAll('img')).find(img => img.src === src);
            if(targetImg) {
                const slide = targetImg.closest('.snap-center');
                carousel.scrollTo({ left: slide.offsetLeft - carousel.offsetLeft, behavior: 'smooth' });
            }
        }
    </script>
</x-app-layout>