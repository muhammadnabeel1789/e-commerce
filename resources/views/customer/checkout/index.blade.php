<x-app-layout>
    <x-slot name="header">Checkout</x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ===== KIRI: FORM ALAMAT & KURIR ===== --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Alamat Pengiriman --}}
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Alamat Pengiriman
                        </h2>

                        @if($addresses->isEmpty())
                            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 p-4 rounded-lg text-sm">
                                Belum ada alamat tersimpan.
                                <button type="button" onclick="toggleAddressForm()" class="font-bold underline ml-1 text-indigo-600">Tambah Alamat Baru</button>
                            </div>
                        @else
                            <div class="space-y-3" id="address-list">
                                @foreach($addresses as $address)
                                <label class="flex items-start gap-3 p-4 border-2 rounded-lg cursor-pointer transition
                                    {{ $loop->first ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300' }}"
                                    id="address-label-{{ $address->id }}">
                                    <input type="radio" name="address_id" value="{{ $address->id }}" form="checkout-form"
                                        {{ $loop->first ? 'checked' : '' }}
                                        class="mt-1 text-indigo-600"
                                        onchange="highlightAddress({{ $address->id }})">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-gray-900 text-sm">{{ $address->recipient_name }}</span>
                                            <span class="text-xs text-gray-500">· {{ $address->phone }}</span>
                                            @if($address->is_primary || $address->is_default)
                                                <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded font-semibold">Utama</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $address->address }}</p>
                                        <p class="text-sm text-gray-500">Kel. {{ $address->village }}, Kec. {{ $address->district }}, {{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>
                                        <button type="button" onclick="openEditModal({{ $address->id }})" class="mt-2 text-xs font-semibold text-indigo-600 hover:underline">
                                            Edit Alamat Ini
                                        </button>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            <button type="button" id="btn-toggle-add-address" onclick="toggleAddressForm()" class="mt-3 inline-flex items-center gap-1 text-sm text-indigo-600 hover:underline font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Tambah Alamat Baru
                            </button>
                        @endif

                        {{-- ✅ FORM TAMBAH ALAMAT — BERDIRI SENDIRI (bukan nested di checkout-form) --}}
                        <div id="inline-add-address" class="hidden mt-4 border-2 border-indigo-200 rounded-xl p-5 bg-indigo-50">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-bold text-indigo-700">Tambah Alamat Baru</h3>
                                <button type="button" onclick="toggleAddressForm()" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            {{-- ✅ form ini BERDIRI SENDIRI, id unik, TIDAK di dalam checkout-form --}}
                            <form id="add-address-form"
                                  action="{{ route('addresses.store') }}"
                                  method="POST">
                                @csrf
                                <input type="hidden" name="redirect_back" value="{{ url()->current() }}">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Label Alamat</label>
                                        <input type="text" name="label" placeholder="Rumah / Kantor" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Penerima</label>
                                        <input type="text" name="recipient_name" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500 bg-white">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor HP</label>
                                    <input type="text" name="phone" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500 bg-white">
                                </div>
                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Provinsi</label>
                                        <select id="inline_select_province" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500 bg-white">
                                            <option value="">Pilih Provinsi...</option>
                                        </select>
                                        <input type="hidden" name="province" id="inline_input_province">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Kota/Kabupaten</label>
                                        <select id="inline_select_city" disabled required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500 bg-white disabled:bg-gray-100">
                                            <option value="">Pilih Kota...</option>
                                        </select>
                                        <input type="hidden" name="city" id="inline_input_city">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Kecamatan</label>
                                        <select id="inline_select_district" disabled required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500 bg-white disabled:bg-gray-100">
                                            <option value="">Pilih Kecamatan...</option>
                                        </select>
                                        <input type="hidden" name="district" id="inline_input_district">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Kelurahan/Desa</label>
                                        <select id="inline_select_village" disabled required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500 bg-white disabled:bg-gray-100">
                                            <option value="">Pilih Kelurahan...</option>
                                        </select>
                                        <input type="hidden" name="village" id="inline_input_village">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Pos</label>
                                    <input type="number" name="postal_code" required
                                        class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500 bg-white">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Lengkap</label>
                                    <textarea name="address" rows="2" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500 bg-white"></textarea>
                                </div>
                                <div class="flex gap-3 justify-end">
                                    <button type="button" onclick="toggleAddressForm()"
                                        class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="px-5 py-2 text-sm font-bold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                        Simpan Alamat
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Daftar Produk yang Dipesan --}}
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            Produk Dipesan
                        </h2>
                        <div class="space-y-4">
                            @foreach($cartItems as $item)
                            @php
                                $prod = $item->product;
                                $variant = $item->variant ?? null;
                                $harga = $item->final_price ?? $item->price ?? 0;
                                $qty = $item->quantity;
                                $itemImgPath = null;
                                if ($variant && $variant->image) {
                                    $itemImgPath = $variant->image->image_path;
                                } elseif ($prod->images && $prod->images->count() > 0) {
                                    $cover = $prod->images->firstWhere('is_primary', true) ?? $prod->images->first();
                                    $itemImgPath = $cover->image_path;
                                }
                            @endphp
                            <div class="flex items-center gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                                @if($itemImgPath)
                                    <img src="{{ asset('storage/' . $itemImgPath) }}" class="w-16 h-16 rounded-lg object-cover border border-gray-100 flex-shrink-0">
                                @else
                                    <div class="w-16 h-16 rounded-lg bg-gray-100 flex-shrink-0 flex items-center justify-center text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-gray-900 text-sm">{{ $prod->name }}</div>
                                    @if($variant)
                                        <div class="text-xs text-gray-500 mt-1">{{ $variant->size ?? '' }} {{ $variant->color ?? '' }}</div>
                                    @endif
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-sm font-bold text-indigo-600">Rp {{ number_format($harga, 0, ',', '.') }}</span>
                                        <span class="text-xs text-gray-500 ml-2">× {{ $qty }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                {{-- ===== KANAN: RINGKASAN PEMBAYARAN ===== --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                        <h2 class="text-base font-bold text-gray-900 mb-5">Ringkasan Pembayaran</h2>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal Produk</span>
                                <span id="display-subtotal">Rp {{ number_format($checkoutSubtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600 items-start">
                                <div>
                                    <span>Ongkos Kirim</span>
                                    <div class="text-[10px] text-gray-400 mt-0.5" id="display-shipping-eta"></div>
                                    <div class="text-[10px] text-gray-500 mt-1 leading-relaxed bg-gray-50 p-2 rounded border border-gray-100 hidden" id="display-shipping-detail"></div>
                                </div>
                                <span id="display-shipping">Memuat...</span>
                            </div>
                            <hr class="border-gray-200">
                            <div class="flex justify-between font-bold text-gray-900 text-base pt-1">
                                <span>Total Bayar</span>
                                <span id="display-total" class="text-indigo-600">
                                    Memuat...
                                </span>
                            </div>
                        </div>
                        <button type="submit" form="checkout-form"
                            class="mt-6 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl transition duration-200 flex items-center justify-center gap-2 text-sm"
                            {{ $addresses->isEmpty() ? 'disabled' : '' }}>
                            Pesan & Bayar Sekarang
                        </button>
                    </div>
                </div>

            </div>

            {{-- ✅ CHECKOUT FORM — di luar grid, hanya penampung hidden inputs & submit handler --}}
            <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="courier_name" id="courier_name" value="Reguler">
                <input type="hidden" name="shipping_cost" id="shipping_cost" value="0">
            </form>

        </div>
    </div>

    {{-- MODAL EDIT ALAMAT --}}
    <x-modal name="edit-address-modal" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Edit Alamat</h2>
            <form id="edit-address-form" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-input-label value="Label Alamat" />
                        <x-text-input id="edit_label" class="block mt-1 w-full" type="text" name="label" required />
                    </div>
                    <div>
                        <x-input-label value="Nama Penerima" />
                        <x-text-input id="edit_recipient_name" class="block mt-1 w-full" type="text" name="recipient_name" required />
                    </div>
                </div>
                <div class="mb-4">
                    <x-input-label value="Nomor HP" />
                    <x-text-input id="edit_phone" class="block mt-1 w-full" type="text" name="phone" required />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-input-label value="Provinsi" />
                        <select id="edit_select_province" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required></select>
                        <input type="hidden" name="province" id="edit_input_province">
                    </div>
                    <div>
                        <x-input-label value="Kota/Kabupaten" />
                        <select id="edit_select_city" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required></select>
                        <input type="hidden" name="city" id="edit_input_city">
                    </div>
                    <div>
                        <x-input-label value="Kecamatan" />
                        <select id="edit_select_district" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required></select>
                        <input type="hidden" name="district" id="edit_input_district">
                    </div>
                    <div>
                        <x-input-label value="Kelurahan/Desa" />
                        <select id="edit_select_village" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required></select>
                        <input type="hidden" name="village" id="edit_input_village">
                    </div>
                </div>
                <div class="mb-4">
                    <x-input-label value="Kode Pos" />
                    <x-text-input id="edit_postal_code" class="block mt-1 w-full md:w-1/2" type="number" name="postal_code" required />
                </div>
                <div class="mb-6">
                    <x-input-label value="Alamat Lengkap" />
                    <textarea id="edit_address" name="address" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required></textarea>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md text-sm hover:bg-gray-300">Batal</button>
                    <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        const userAddresses = @json($addresses);
        const subtotalAfterDiscount = {{ $checkoutSubtotal }};

        function formatRp(num) {
            return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        async function fetchShippingCost(addressId) {
            const summaryDisplay = document.getElementById('display-shipping');
            const totalDisplay = document.getElementById('display-total');
            const shippingInput = document.getElementById('shipping_cost');
            const submitBtn = document.querySelector('[form="checkout-form"][type="submit"]');
            
            summaryDisplay.textContent = 'Memuat...';
            totalDisplay.textContent = 'Memuat...';
            document.getElementById('display-shipping-eta').textContent = '';
            if (submitBtn) submitBtn.disabled = true;
            
            try {
                const response = await fetch('{{ route('checkout.shipping.calculate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ address_id: addressId })
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    const cost = data.cost;
                    shippingInput.value = cost;
                    summaryDisplay.textContent = data.formatted_cost;
                    totalDisplay.textContent = formatRp(subtotalAfterDiscount + cost);
                    document.getElementById('display-shipping-eta').textContent = `Est: ${data.eta} | Jarak: ${data.distance_km} KM | Berat: ${data.weight_gram} gr`;
                    
                    const distCost = data.distance_km * data.ongkir_per_km;
                    const weightCost = data.weight_gram * data.ongkir_per_gram;
                    const calculatedTotal = distCost + weightCost;
                    const appliedTotal = Math.ceil(calculatedTotal / 100) * 100;
                    const isMinCost = appliedTotal < data.min_cost;
                    
                    const detailContainer = document.getElementById('display-shipping-detail');
                    detailContainer.classList.remove('hidden');
                    detailContainer.innerHTML = `
                        <b>Rincian Perhitungan:</b><br>
                        • Jarak: <b>${data.distance_km}</b> km × ${formatRp(data.ongkir_per_km)} = ${formatRp(distCost)}<br>
                        • Berat: <b>${data.weight_gram}</b> gr × ${formatRp(data.ongkir_per_gram)} = ${formatRp(weightCost)}<br>
                        <div class="mt-1 pt-1 border-t border-gray-200">
                            Total Awal: <b>${formatRp(calculatedTotal)}</b>
                            ${isMinCost ? `<br><span class="text-indigo-600 font-semibold">* Minimum ongkir yang berlaku: ${formatRp(data.min_cost)}</span>` : `<br><span class="text-gray-400">* Dibulatkan ke: ${formatRp(data.cost)}</span>`}
                        </div>
                    `;
                    
                    if (submitBtn) submitBtn.disabled = false;
                } else {
                    summaryDisplay.textContent = 'Gagal';
                }
            } catch (error) {
                console.error(error);
                summaryDisplay.textContent = 'Error';
            }
        }

        function highlightAddress(id) {
            document.querySelectorAll('[id^="address-label-"]').forEach(el => {
                el.classList.remove('border-indigo-500', 'bg-indigo-50');
                el.classList.add('border-gray-200');
            });
            const target = document.getElementById('address-label-' + id);
            if (target) {
                target.classList.add('border-indigo-500', 'bg-indigo-50');
                target.classList.remove('border-gray-200');
            }
            
            // Hitung ongkir ketika alamat diganti
            fetchShippingCost(id);
        }

        function toggleAddressForm() {
            const box = document.getElementById('inline-add-address');
            const btn = document.getElementById('btn-toggle-add-address');
            const hidden = box.classList.contains('hidden');
            box.classList.toggle('hidden');
            if (btn) {
                btn.innerHTML = hidden
                    ? `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Batal`
                    : `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Tambah Alamat Baru`;
            }
            if (hidden) box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // Dropdown untuk form tambah alamat inline
        function setupInlineDropdowns() {
            const provSelect = document.getElementById('inline_select_province');
            const citySelect = document.getElementById('inline_select_city');
            const distSelect = document.getElementById('inline_select_district');
            const villSelect = document.getElementById('inline_select_village');
            const provInput  = document.getElementById('inline_input_province');
            const cityInput  = document.getElementById('inline_input_city');
            const distInput  = document.getElementById('inline_input_district');
            const villInput  = document.getElementById('inline_input_village');

            fetch('/api/regions/provinces').then(r => r.json()).then(data => {
                data.forEach(p => provSelect.insertAdjacentHTML('beforeend', `<option value="${p.code}" data-name="${p.name}">${p.name}</option>`));
            });
            provSelect.addEventListener('change', function() {
                provInput.value = this.options[this.selectedIndex]?.getAttribute('data-name') || '';
                citySelect.innerHTML = '<option value="">Pilih Kota...</option>';
                distSelect.innerHTML = '<option value="">Pilih Kecamatan...</option>';
                villSelect.innerHTML = '<option value="">Pilih Kelurahan...</option>';
                citySelect.disabled = !this.value; distSelect.disabled = true; villSelect.disabled = true;
                cityInput.value = ''; distInput.value = ''; villInput.value = '';
                if (this.value) fetch('/api/regions/cities/' + this.value).then(r => r.json()).then(data => {
                    data.forEach(c => citySelect.insertAdjacentHTML('beforeend', `<option value="${c.code}" data-name="${c.name}">${c.name}</option>`));
                });
            });
            citySelect.addEventListener('change', function() {
                cityInput.value = this.options[this.selectedIndex]?.getAttribute('data-name') || '';
                distSelect.innerHTML = '<option value="">Pilih Kecamatan...</option>';
                villSelect.innerHTML = '<option value="">Pilih Kelurahan...</option>';
                distSelect.disabled = !this.value; villSelect.disabled = true;
                distInput.value = ''; villInput.value = '';
                if (this.value) fetch('/api/regions/districts/' + this.value).then(r => r.json()).then(data => {
                    data.forEach(d => distSelect.insertAdjacentHTML('beforeend', `<option value="${d.code}" data-name="${d.name}">${d.name}</option>`));
                });
            });
            distSelect.addEventListener('change', function() {
                distInput.value = this.options[this.selectedIndex]?.getAttribute('data-name') || '';
                villSelect.innerHTML = '<option value="">Pilih Kelurahan...</option>';
                villSelect.disabled = !this.value; villInput.value = '';
                if (this.value) fetch('/api/regions/villages/' + this.value).then(r => r.json()).then(data => {
                    data.forEach(v => villSelect.insertAdjacentHTML('beforeend', `<option value="${v.code}" data-name="${v.name}">${v.name}</option>`));
                });
            });
            villSelect.addEventListener('change', function() {
                villInput.value = this.options[this.selectedIndex]?.getAttribute('data-name') || '';
            });
        }

        // Dropdown untuk modal edit alamat
        function setupRegionDropdowns(prefix) {
            const provSelect = document.getElementById(`${prefix}_select_province`);
            const citySelect = document.getElementById(`${prefix}_select_city`);
            const distSelect = document.getElementById(`${prefix}_select_district`);
            const villSelect = document.getElementById(`${prefix}_select_village`);
            const provInput  = document.getElementById(`${prefix}_input_province`);
            const cityInput  = document.getElementById(`${prefix}_input_city`);
            const distInput  = document.getElementById(`${prefix}_input_district`);
            const villInput  = document.getElementById(`${prefix}_input_village`);

            fetch('/api/regions/provinces').then(r => r.json()).then(data => {
                provSelect.innerHTML = '<option value="">Pilih Provinsi...</option>';
                data.forEach(p => provSelect.insertAdjacentHTML('beforeend', `<option value="${p.code}" data-name="${p.name}">${p.name}</option>`));
            });
            provSelect.addEventListener('change', function() {
                provInput.value = this.options[this.selectedIndex]?.getAttribute('data-name') || '';
                citySelect.innerHTML = '<option value="">Pilih Kota...</option>';
                distSelect.innerHTML = '<option value="">Pilih Kecamatan...</option>';
                villSelect.innerHTML = '<option value="">Pilih Kelurahan...</option>';
                citySelect.disabled = !this.value; distSelect.disabled = true; villSelect.disabled = true;
                cityInput.value = ''; distInput.value = ''; villInput.value = '';
                if (this.value) fetch('/api/regions/cities/' + this.value).then(r => r.json()).then(data => {
                    data.forEach(c => citySelect.insertAdjacentHTML('beforeend', `<option value="${c.code}" data-name="${c.name}">${c.name}</option>`));
                });
            });
            citySelect.addEventListener('change', function() {
                cityInput.value = this.options[this.selectedIndex]?.getAttribute('data-name') || '';
                distSelect.innerHTML = '<option value="">Pilih Kecamatan...</option>';
                villSelect.innerHTML = '<option value="">Pilih Kelurahan...</option>';
                distSelect.disabled = !this.value; villSelect.disabled = true;
                distInput.value = ''; villInput.value = '';
                if (this.value) fetch('/api/regions/districts/' + this.value).then(r => r.json()).then(data => {
                    data.forEach(d => distSelect.insertAdjacentHTML('beforeend', `<option value="${d.code}" data-name="${d.name}">${d.name}</option>`));
                });
            });
            distSelect.addEventListener('change', function() {
                distInput.value = this.options[this.selectedIndex]?.getAttribute('data-name') || '';
                villSelect.innerHTML = '<option value="">Pilih Kelurahan...</option>';
                villSelect.disabled = !this.value; villInput.value = '';
                if (this.value) fetch('/api/regions/villages/' + this.value).then(r => r.json()).then(data => {
                    data.forEach(v => villSelect.insertAdjacentHTML('beforeend', `<option value="${v.code}" data-name="${v.name}">${v.name}</option>`));
                });
            });
            villSelect.addEventListener('change', function() {
                villInput.value = this.options[this.selectedIndex]?.getAttribute('data-name') || '';
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            setupInlineDropdowns();
            setupRegionDropdowns('edit');
            
            // Auto hitung ongkir saat pertama kali dimuat
            const defaultAddress = document.querySelector('input[name="address_id"]:checked');
            if (defaultAddress) {
                fetchShippingCost(defaultAddress.value);
            }
        });

        // Modal edit alamat
        window.openEditModal = function(addressId) {
            const addr = userAddresses.find(a => a.id === addressId);
            if (!addr) return;
            document.getElementById('edit-address-form').action = `/addresses/${addr.id}`;
            document.getElementById('edit_label').value = addr.label || '';
            document.getElementById('edit_recipient_name').value = addr.recipient_name;
            document.getElementById('edit_phone').value = addr.phone;
            document.getElementById('edit_postal_code').value = addr.postal_code;
            document.getElementById('edit_address').value = addr.address;
            const provSelect = document.getElementById('edit_select_province');
            setTimeout(() => {
                Array.from(provSelect.options).forEach(opt => {
                    if (opt.getAttribute('data-name') === addr.province) {
                        opt.selected = true;
                        document.getElementById('edit_input_province').value = addr.province;
                        provSelect.dispatchEvent(new Event('change'));
                        setTimeout(() => {
                            Array.from(document.getElementById('edit_select_city').options).forEach(cOpt => {
                                if (cOpt.getAttribute('data-name') === addr.city) {
                                    cOpt.selected = true;
                                    document.getElementById('edit_input_city').value = addr.city;
                                    document.getElementById('edit_select_city').dispatchEvent(new Event('change'));
                                    setTimeout(() => {
                                        Array.from(document.getElementById('edit_select_district').options).forEach(dOpt => {
                                            if (dOpt.getAttribute('data-name') === addr.district) {
                                                dOpt.selected = true;
                                                document.getElementById('edit_input_district').value = addr.district;
                                                document.getElementById('edit_select_district').dispatchEvent(new Event('change'));
                                                setTimeout(() => {
                                                    Array.from(document.getElementById('edit_select_village').options).forEach(vOpt => {
                                                        if (vOpt.getAttribute('data-name') === addr.village) {
                                                            vOpt.selected = true;
                                                            document.getElementById('edit_input_village').value = addr.village;
                                                        }
                                                    });
                                                }, 400);
                                            }
                                        });
                                    }, 400);
                                }
                            });
                        }, 400);
                    }
                });
            }, 300);
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-address-modal' }));
        }

        // Submit checkout
        let savedSnapToken = null;
        let savedRedirectUrl = null;

        async function updateOrderResult(result) {
            try {
                await fetch('{{ route('checkout.update_snap_result') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(result)
                });
            } catch (e) {
                console.error('Failed to update result', e);
            }
        }

        function openSnapPopup(snapToken, redirectUrl, submitBtn) {
            window.snap.pay(snapToken, {
                onSuccess: async (result) => { 
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = 'Memproses...';
                    await updateOrderResult(result);
                    window.location.href = redirectUrl; 
                },
                onPending: async (result) => { 
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = 'Memproses...';
                    await updateOrderResult(result);
                    window.location.href = redirectUrl; 
                },
                onError: async (result) => { 
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = 'Memproses...';
                    await updateOrderResult(result);
                    window.location.href = redirectUrl; 
                },
                onClose: () => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Pesan & Bayar Sekarang';
                    // Cancel order to prevent zombie order since user stayed on checkout
                    if (savedSnapToken) {
                        fetch('{{ route('checkout.cancel_snap') }}', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json', 
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                            },
                            body: JSON.stringify({ snap_token: savedSnapToken })
                        });
                        savedSnapToken = null;
                        savedRedirectUrl = null;
                    }
                }
            });
        }

        document.getElementById('checkout-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const submitBtn = document.querySelector('[form="checkout-form"][type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Memproses...';

            if (savedSnapToken && savedRedirectUrl) {
                openSnapPopup(savedSnapToken, savedRedirectUrl, submitBtn);
                return;
            }

            // Kumpulkan data dari seluruh halaman (address_id, courier_name, shipping_cost)
            const data = new FormData();
            data.append('_token', '{{ csrf_token() }}');
            const addressId = document.querySelector('input[name="address_id"]:checked')?.value;
            const courierName = document.getElementById('courier_name')?.value;
            const shippingCost = document.getElementById('shipping_cost')?.value;
            if (addressId)    data.append('address_id',    addressId);
            if (courierName)  data.append('courier_name',  courierName);
            if (shippingCost) data.append('shipping_cost', shippingCost);

            try {
                const response = await fetch('{{ route('checkout.store') }}', {
                    method: 'POST',
                    body: data,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const result = await response.json();
                if (response.ok && result.snap_token) {
                    savedSnapToken   = result.snap_token;
                    savedRedirectUrl = result.redirect_url;
                    openSnapPopup(savedSnapToken, savedRedirectUrl, submitBtn);
                } else {
                    alert(result.message || 'Gagal memproses pesanan.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Pesan & Bayar Sekarang';
                }
            } catch (error) {
                alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Pesan & Bayar Sekarang';
            }
        });
    </script>
</x-app-layout>