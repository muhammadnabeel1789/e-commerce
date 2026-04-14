<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Keranjang Belanja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Alert Messages --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Keranjang Kosong --}}
            @if(!$cart || $cart->items->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-10 text-center">
                        <div class="mb-4">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Keranjang Anda kosong</h3>
                        <p class="mt-1 text-gray-500">Sepertinya Anda belum menambahkan item apa pun.</p>
                        <div class="mt-6">
                            <a href="{{ route('products.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Mulai Belanja
                            </a>
                        </div>
                    </div>
                </div>

            @else
                <div id="cart-wrapper">
                    <div class="flex flex-col md:flex-row gap-6">

                        {{-- KIRI: Daftar Item --}}
                        <div class="md:w-3/4 space-y-3">

                            {{-- Header Pilih Semua --}}
                            <div class="bg-white rounded-lg shadow-sm px-6 py-3 flex items-center gap-3 border-b">
                                <input type="checkbox" id="check-all"
                                    class="w-5 h-5 rounded border-gray-300 text-indigo-600 cursor-pointer">
                                <label for="check-all" class="text-sm font-semibold text-gray-700 cursor-pointer select-none">
                                    Pilih Semua
                                </label>
                            </div>

                            {{-- Daftar Item --}}
                            @foreach($cart->items as $item)
                                <div class="bg-white rounded-lg shadow-sm px-6 py-4 flex items-center gap-4 hover:shadow-md transition"
                                    data-item-id="{{ $item->id }}"
                                    data-item-price="{{ $item->price }}"
                                    data-item-qty="{{ $item->quantity }}">

                                    {{-- Checkbox --}}
                                    <input type="checkbox"
                                        class="item-checkbox w-5 h-5 rounded border-gray-300 text-indigo-600 cursor-pointer flex-shrink-0"
                                        value="{{ $item->id }}"
                                        data-price="{{ $item->price * $item->quantity }}">

                                    {{-- Gambar --}}
                                    @php
                                        $cartImgPath = null;
                                        if ($item->variant && $item->variant->image) {
                                            $cartImgPath = $item->variant->image->image_path;
                                        } elseif ($item->product->images && $item->product->images->count() > 0) {
                                            $cover = $item->product->images->firstWhere('is_primary', true) ?? $item->product->images->first();
                                            $cartImgPath = $cover->image_path;
                                        }
                                    @endphp
                                    <div class="flex-shrink-0 h-20 w-20">
                                        @if($cartImgPath)
                                            <img class="h-20 w-20 rounded-lg object-cover"
                                                src="{{ asset('storage/' . $cartImgPath) }}"
                                                alt="{{ $item->product->name }}">
                                        @else
                                            <img class="h-20 w-20 rounded-lg object-cover"
                                                src="https://via.placeholder.com/150" alt="No Image">
                                        @endif
                                    </div>

                                    {{-- Info Produk --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $item->product->name }}</p>
                                        @if($item->variant)
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $item->variant->size ?? '' }} / {{ $item->variant->color ?? '' }}
                                            </p>
                                        @endif
                                        <p class="text-indigo-600 font-bold mt-1">
                                            Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    {{-- Qty Update --}}
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                            <button type="button" onclick="adjustQty(this, -1, '{{ $item->id }}')"
                                                class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-lg leading-none transition">−</button>
                                            <input type="number" id="qty-{{ $item->id }}"
                                                value="{{ $item->quantity }}" min="1"
                                                max="{{ $item->variant ? $item->variant->stock : ($item->product->variants->first()->stock ?? 999) }}"
                                                class="w-12 text-center border-0 text-sm focus:ring-0 qty-input"
                                                data-item-id="{{ $item->id }}"
                                                onchange="updateQuantity(this)">
                                            <button type="button" onclick="adjustQty(this, 1, '{{ $item->id }}')"
                                                class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-lg leading-none transition">+</button>
                                        </div>
                                    </div>

                                    {{-- Subtotal Item --}}
                                    <div class="flex-shrink-0 text-right min-w-[100px]">
                                        <p class="text-sm font-bold text-gray-900 item-subtotal" id="subtotal-{{ $item->id }}">
                                            Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    {{-- Hapus --}}
                                    <div class="flex-shrink-0">
                                        <form action="{{ route('cart.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus item ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Kembali Belanja --}}
                            <div class="bg-white rounded-lg shadow-sm px-6 py-3">
                                <a href="{{ route('products.index') }}"
                                    class="text-sm text-indigo-600 hover:text-indigo-900 font-medium flex items-center w-fit">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    Lanjut Belanja
                                </a>
                            </div>
                        </div>

                        {{-- KANAN: Ringkasan Pesanan --}}
                        <div class="md:w-1/4">
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 border-b pb-4 mb-4">Ringkasan Pesanan</h3>

                                    <div class="flex justify-between mb-2 text-sm text-gray-600">
                                        <span>Item Dipilih</span>
                                        <span id="summary-count" class="font-semibold">0 item</span>
                                    </div>

                                    <div class="flex justify-between mb-4 text-sm text-gray-600">
                                        <span>Subtotal</span>
                                        <span id="summary-subtotal" class="font-semibold">Rp 0</span>
                                    </div>

                                    <div class="border-t pt-4 flex justify-between items-center mb-6">
                                        <span class="text-base font-bold text-gray-900">Total</span>
                                        <span id="summary-total" class="text-lg font-bold text-indigo-600">Rp 0</span>
                                    </div>

                                    @auth
                                        <button type="button" id="btn-checkout"
                                            onclick="submitCheckout()"
                                            disabled
                                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                                            Checkout (<span id="btn-count">0</span>)
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}"
                                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700">
                                            Login untuk Checkout
                                        </a>
                                    @endauth

                                    <p class="mt-4 text-xs text-center text-gray-500">
                                        Pilih item yang ingin di-checkout.<br>Ongkir dihitung saat checkout.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @endif
        </div>
    </div>

<script>
    // =============================================
    // LOGIKA CHECKBOX & SUMMARY
    // =============================================
    const checkAll        = document.getElementById('check-all');
    const btnCheckout     = document.getElementById('btn-checkout');
    const btnCount        = document.getElementById('btn-count');
    const summaryCount    = document.getElementById('summary-count');
    const summarySubtotal = document.getElementById('summary-subtotal');
    const summaryTotal    = document.getElementById('summary-total');

    function getChecked() {
        return document.querySelectorAll('.item-checkbox:checked');
    }

    function formatRupiah(num) {
        return 'Rp ' + num.toLocaleString('id-ID');
    }

    function updateSummary() {
        const checked = getChecked();
        let total = 0;

        checked.forEach(cb => {
            const row   = cb.closest('[data-item-id]');
            const price = parseInt(row.dataset.itemPrice);
            const qty   = parseInt(row.querySelector('.qty-input').value);
            total += price * qty;
        });

        const count = checked.length;

        summaryCount.textContent    = count + ' item';
        summarySubtotal.textContent = formatRupiah(total);
        summaryTotal.textContent    = formatRupiah(total);

        if (btnCount) btnCount.textContent = count;
        if (btnCheckout) btnCheckout.disabled = count === 0;

        const allBoxes = document.querySelectorAll('.item-checkbox');
        if (checkAll) {
            checkAll.checked       = allBoxes.length > 0 && count === allBoxes.length;
            checkAll.indeterminate = count > 0 && count < allBoxes.length;
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            document.querySelectorAll('.item-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            updateSummary();
        });
    }

    document.querySelectorAll('.item-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSummary);
    });

    // =============================================
    // UPDATE QUANTITY VIA AJAX
    // =============================================
    function adjustQty(btn, delta, itemId) {
        const input = document.getElementById('qty-' + itemId);
        if (!input) return;
        
        const min = parseInt(input.min) || 1;
        const max = parseInt(input.max) || 999;
        let val = parseInt(input.value) + delta;

        if (val < min) val = min;
        if (val > max) val = max;

        input.value = val;
        updateQuantity(input);
    }

    function updateQuantity(input) {
        const itemId = input.dataset.itemId;
        const qty = parseInt(input.value);
        
        fetch(`/cart/${itemId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ quantity: qty })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update subtotal item
                const subtotalEl = document.getElementById('subtotal-' + itemId);
                if (subtotalEl) subtotalEl.textContent = data.item_subtotal;
                
                // Update row dataset
                const row = document.querySelector(`[data-item-id="${itemId}"]`);
                if (row) {
                    row.dataset.itemQty = qty;
                }
                
                // Update ringkasan
                updateSummary();
            } else {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            location.reload();
        });
    }

    // =============================================
    // SUBMIT CHECKOUT - HANYA ITEM YANG DIPILIH
    // =============================================
    function submitCheckout() {
        const checked = getChecked();
        if (checked.length === 0) {
            alert('Pilih minimal 1 produk untuk di-checkout!');
            return;
        }

        // Buat form baru
        const form = document.createElement('form');
        form.method = 'GET';
        form.action = '{{ route('checkout.index') }}';
        
        // Tambahkan CSRF token
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        
        // Tambahkan selected items (HANYA YANG DICENTANG)
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_items[]';
            input.value = cb.value; // Ini adalah cart_item_id
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }

    // Inisialisasi awal
    updateSummary();
</script>
</x-app-layout>