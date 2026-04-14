<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Produk: ') . $product->name }}
        </h2>
    </x-slot>

    <style>
        /* Hilangkan tanda panah spinner pada semua input number */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] { -moz-appearance: textfield; }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- ===== SECTION 1: INFORMASI DASAR ===== --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 border-b pb-3 mb-5">Informasi Dasar</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Nama --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                            <select name="category_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Brand --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                            <select name="brand_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Pilih Brand (Opsional)</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Produk</label>
                            <textarea name="description" rows="4"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $product->description) }}</textarea>
                        </div>

                        {{-- Berat --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Berat Produk (gram)</label>
                            <input type="number" name="weight" value="{{ old('weight', $product->weight) }}" min="1"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                {{-- ===== SECTION 2: GALERI FOTO ===== --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 border-b pb-3 mb-5">Galeri Foto</h3>

                    {{-- Foto Lama --}}
                    @if($product->images->count() > 0)
                        <p class="text-sm text-gray-500 mb-3">Foto saat ini (hover untuk hapus):</p>
                        <div class="grid grid-cols-4 gap-3 mb-5" id="existing-images-grid">
                            @foreach($product->images as $img)
                            <div class="relative group border rounded overflow-hidden" data-image-id="{{ $img->id }}">
                                <img src="{{ Storage::url($img->image_path) }}" class="w-full h-24 object-cover">
                                @if($img->is_primary)
                                    <span class="absolute bottom-1 left-1 bg-indigo-500 text-white text-xs px-1.5 rounded">Cover</span>
                                @endif
                                <button type="submit" form="delete-img-{{ $img->id }}"
                                    class="absolute inset-0 bg-red-500 bg-opacity-80 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition text-xs font-medium"
                                    onclick="return confirm('Yakin hapus foto ini?')">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </button>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic mb-4" id="no-image-msg">Belum ada foto produk.</p>
                        <div id="existing-images-grid" class="grid grid-cols-4 gap-3 mb-5 hidden"></div>
                    @endif

                    {{-- Upload Foto Baru --}}
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-indigo-400 transition"
                        onclick="document.getElementById('new_images').click()">
                        <i class="fas fa-plus text-2xl text-gray-400"></i>
                        <p class="text-xs text-gray-500 mt-1">Tambah Foto Baru (bisa pilih banyak)</p>
                        <input type="file" name="new_images[]" id="new_images" class="hidden" multiple accept="image/*" onchange="previewNewImages(this)">
                    </div>
                    <div id="new-image-preview" class="grid grid-cols-4 gap-2 mt-3"></div>
                </div>

                {{-- ===== SECTION 3: VARIAN ===== --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center border-b pb-3 mb-5">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Varian Produk</h3>
                            <p class="text-xs text-gray-400 mt-1">Setiap varian dapat dikaitkan dengan foto produk yang berbeda.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            {{-- Tampilan total stok otomatis dari semua varian --}}
                            <div class="flex items-center gap-2 bg-indigo-50 border border-indigo-200 rounded-lg px-3 py-1.5">
                                <span class="text-xs font-medium text-indigo-500 uppercase tracking-wide">Total Stok</span>
                                <span id="total-stock-display" class="text-sm font-bold text-indigo-700">{{ $product->variants->sum('stock') }}</span>
                            </div>
                            <button type="button" onclick="addNewVariant()"
                                class="text-sm bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded hover:bg-indigo-100 transition font-medium">
                                + Varian Baru
                            </button>
                        </div>
                    </div>
                    {{-- Hidden: total stok dikirim ke controller saat form submit --}}
                    <input type="hidden" name="stock" id="total-stock-input" value="{{ $product->variants->sum('stock') }}">

                    {{-- Varian Lama --}}
                    <div class="space-y-4" id="existing-variants-container">
                        @foreach($product->variants as $variant)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                            <div class="grid grid-cols-1 md:grid-cols-7 gap-3 items-end">

                                {{-- Foto Varian --}}
                                <div class="md:col-span-1">
                                    <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Foto Varian</label>
                                    <div class="relative">
                                        {{-- Preview Foto Terpilih --}}
                                        <div class="w-full h-20 border-2 border-dashed border-gray-200 rounded-lg overflow-hidden cursor-pointer hover:border-indigo-400 transition flex items-center justify-center bg-gray-50"
                                             onclick="openImagePicker('picker-existing-{{ $variant->id }}')"
                                             id="preview-existing-{{ $variant->id }}">
                                            @if($variant->image)
                                                <img src="{{ Storage::url($variant->image->image_path) }}"
                                                     class="w-full h-full object-cover"
                                                     id="img-preview-existing-{{ $variant->id }}">
                                            @else
                                                <div id="img-preview-existing-{{ $variant->id }}" class="flex flex-col items-center text-gray-300">
                                                    <i class="fas fa-image text-2xl"></i>
                                                    <span class="text-xs mt-1">Pilih Foto</span>
                                                </div>
                                            @endif
                                        </div>
                                        <input type="hidden"
                                               name="existing_variants[{{ $variant->id }}][product_image_id]"
                                               id="hidden-img-existing-{{ $variant->id }}"
                                               value="{{ $variant->product_image_id ?? '' }}">
                                    </div>
                                </div>

                                {{-- Ukuran --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Ukuran</label>
                                    <select name="existing_variants[{{ $variant->id }}][size]" class="w-full text-sm border-gray-300 rounded-md">
                                        @foreach(['S','M','L','XL','XXL','XXXL'] as $s)
                                            <option value="{{ $s }}" {{ $variant->size == $s ? 'selected' : '' }}>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Warna --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Warna</label>
                                    <input type="text" name="existing_variants[{{ $variant->id }}][color]" value="{{ $variant->color }}"
                                        id="color-name-ex-{{ $variant->id }}"
                                        oninput="syncColorFromNameEx('{{ $variant->id }}')"
                                        class="w-full text-sm border-gray-300 rounded-md">
                                </div>

                                {{-- Kode Warna --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Kode Warna</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="existing_variants[{{ $variant->id }}][color_code]"
                                            value="{{ $variant->color_code ?? '#6b7280' }}"
                                            id="color-picker-ex-{{ $variant->id }}"
                                            oninput="syncNameFromColorEx('{{ $variant->id }}')"
                                            class="w-9 h-9 border border-gray-300 rounded cursor-pointer flex-shrink-0">
                                        <input type="text" id="color-hex-ex-{{ $variant->id }}"
                                            value="{{ $variant->color_code ?? '#6b7280' }}" maxlength="7"
                                            oninput="syncHexTextEx('{{ $variant->id }}')"
                                            placeholder="#rrggbb"
                                            class="w-full text-xs border-gray-300 rounded-md font-mono">
                                    </div>
                                </div>

                                {{-- Harga --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Harga (Rp)</label>
                                    <input type="number"
                                           name="existing_variants[{{ $variant->id }}][price]"
                                           value="{{ $variant->additional_price }}"
                                           min="0"
                                           class="w-full text-sm border-gray-300 rounded-md">
                                </div>

                                {{-- Stok --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Stok</label>
                                    <input type="number" name="existing_variants[{{ $variant->id }}][stock]"
                                        value="{{ $variant->stock }}" min="0"
                                        oninput="recalcStock()"
                                        class="w-full text-sm border-gray-300 rounded-md">
                                </div>

                                {{-- SKU & Delete --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">SKU Varian</label>
                                    <div class="flex items-center gap-2">
                                        <input type="text"
                                               name="existing_variants[{{ $variant->id }}][sku]"
                                               value="{{ $variant->sku_variant }}"
                                               class="w-full text-sm border-gray-300 rounded-md"
                                               placeholder="SKU Varian"
                                               required>
                                        <button type="submit" form="delete-variant-{{ $variant->id }}"
                                            class="flex-shrink-0 text-red-400 hover:text-red-600 transition"
                                            onclick="return confirm('Yakin hapus varian {{ $variant->size }} - {{ $variant->color }}?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Image Picker Popup untuk varian lama --}}
                            <div id="picker-existing-{{ $variant->id }}"
                                 class="hidden mt-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                <p class="text-xs font-medium text-gray-600 mb-2">Pilih foto untuk varian ini:</p>
                                <div class="flex flex-wrap gap-2">
                                    {{-- Opsi: Tidak ada foto --}}
                                    <div class="cursor-pointer border-2 rounded overflow-hidden w-16 h-16 flex items-center justify-center bg-white text-gray-300 hover:border-indigo-400 transition"
                                         onclick="selectImage('existing', {{ $variant->id }}, '', '')">
                                        <i class="fas fa-times text-lg"></i>
                                    </div>
                                    {{-- Foto tersedia --}}
                                    @foreach($product->images as $img)
                                    <div class="cursor-pointer border-2 rounded overflow-hidden w-16 h-16 hover:border-indigo-400 transition {{ $variant->product_image_id == $img->id ? 'border-indigo-500' : 'border-gray-200' }}"
                                         onclick="selectImage('existing', {{ $variant->id }}, {{ $img->id }}, '{{ Storage::url($img->image_path) }}')">
                                        <img src="{{ Storage::url($img->image_path) }}" class="w-full h-full object-cover">
                                        @if($img->is_primary)
                                            <span class="text-xs text-indigo-500 font-medium block text-center leading-tight">Cover</span>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($product->variants->count() == 0)
                        <p class="text-sm text-gray-400 italic mt-3 text-center">Belum ada varian. Klik "+ Varian Baru".</p>
                    @endif

                    {{-- Varian Baru --}}
                    <div id="new-variant-container" class="space-y-4 mt-4"></div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.products.index') }}" class="bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg hover:bg-gray-300 transition">Batal</a>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 transition font-medium">
                        Update Produk
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- Form DELETE VARIAN --}}
    @foreach($product->variants as $variant)
        <form id="delete-variant-{{ $variant->id }}"
              action="{{ route('admin.products.variant.destroy', $variant->id) }}"
              method="POST" style="display:none;">
            @csrf @method('DELETE')
        </form>
    @endforeach

    {{-- Form DELETE GAMBAR --}}
    @foreach($product->images as $img)
        <form id="delete-img-{{ $img->id }}"
              action="{{ route('admin.products.image.destroy', $img->id) }}"
              method="POST" style="display:none;">
            @csrf @method('DELETE')
        </form>
    @endforeach

    {{-- Data gambar existing untuk JS --}}
    @php
        $imagesForJs = $product->images->map(function($img) {
            return [
                'id'      => $img->id,
                'url'     => Storage::url($img->image_path),
                'primary' => $img->is_primary,
            ];
        })->values()->toArray();
    @endphp
    <script>
        const existingImages = @json($imagesForJs);

        // ============================================================
        // PREVIEW GAMBAR BARU & update semua picker varian baru
        // ============================================================
        let newUploadedImages = []; // [{dataUrl, tempIndex}]

        function previewNewImages(input) {
            const container = document.getElementById('new-image-preview');
            container.innerHTML = '';
            newUploadedImages = [];
            if (input.files) {
                const files = Array.from(input.files);
                const results = new Array(files.length);
                let loaded = 0;
                files.forEach((file, i) => {
                    const reader = new FileReader();
                    reader.onload = e => {
                        results[i] = e.target.result;
                        loaded++;
                        if (loaded === files.length) {
                            results.forEach((dataUrl, idx) => {
                                newUploadedImages.push({ dataUrl, tempIndex: idx });
                                container.innerHTML += `<div id="new-photo-card-${idx}" style="position:relative;border-radius:8px;border:2px solid #fbbf24;overflow:hidden;aspect-ratio:1;">
                                    <img src="${dataUrl}" style="width:100%;height:100%;object-fit:cover;display:block;">
                                    <span id="new-photo-label-${idx}" style="position:absolute;bottom:0;left:0;right:0;background:#f59e0b;color:#fff;font-size:10px;text-align:center;padding:2px 0;font-weight:700;">
                                        📷 Foto Baru ${idx+1}
                                    </span>
                                </div>`;
                            });
                            // Update semua picker varian baru yang sudah ada
                            refreshNewVariantPickers();
                            autoAssignNewImages();
                        }
                    };
                    reader.readAsDataURL(file);
                });
            }
        }

        // Auto-assign foto baru ke varian baru yang belum punya foto
        function autoAssignNewImages() {
            let imgIdx = 0;
            for (let i = 0; i < newVarIndex; i++) {
                if (!document.getElementById(`new-variant-row-${i}`)) continue;
                const hw = document.getElementById(`hidden-new-img-index-${i}`);
                const pw = document.getElementById(`img-preview-new-${i}`);
                if (!hw || !pw) continue;
                // Hanya assign jika belum ada foto
                if (hw.value === '' && newUploadedImages[imgIdx]) {
                    const img = newUploadedImages[imgIdx];
                    hw.value = img.tempIndex;
                    pw.outerHTML = `<img src="${img.dataUrl}" class="w-full h-full object-cover" id="img-preview-new-${i}">`;
                    imgIdx++;
                } else if (hw.value !== '') {
                    imgIdx++;
                }
            }
        }

        // Refresh picker pada semua varian baru yang sudah di-render
        function refreshNewVariantPickers() {
            for (let i = 0; i < newVarIndex; i++) {
                const pickerEl = document.getElementById(`picker-new-${i}`);
                if (!pickerEl) continue;
                // Bagian foto baru
                let newSection = pickerEl.querySelector('.new-uploads-section');
                const newOpts = newUploadedImages.map((img, ti) => `
                    <div class="cursor-pointer border-2 border-yellow-300 rounded overflow-hidden w-16 h-16 hover:border-indigo-400 transition" title="Foto Baru ${ti+1}"
                         onclick="selectNewVariantByNewImage(${i}, ${ti})">
                        <img src="${img.dataUrl}" class="w-full h-full object-cover">
                        <span style="position:absolute;bottom:0;left:0;right:0;background:#f59e0b;color:#fff;font-size:9px;text-align:center;">Baru ${ti+1}</span>
                    </div>`).join('');
                if (!newSection) {
                    if (newUploadedImages.length > 0) {
                        newSection = document.createElement('div');
                        newSection.className = 'new-uploads-section mt-2';
                        newSection.innerHTML = '<p class="text-xs font-medium text-yellow-600 mb-1">📷 Foto baru yang diupload:</p><div class="flex flex-wrap gap-2" style="position:relative;">' + newOpts + '</div>';
                        pickerEl.appendChild(newSection);
                    }
                } else {
                    newSection.innerHTML = newUploadedImages.length > 0
                        ? '<p class="text-xs font-medium text-yellow-600 mb-1">📷 Foto baru yang diupload:</p><div class="flex flex-wrap gap-2" style="position:relative;">' + newOpts + '</div>'
                        : '';
                }
            }
        }

        // ============================================================
        // BUKA / TUTUP IMAGE PICKER
        // ============================================================
        function openImagePicker(pickerId) {
            // Tutup semua picker yang terbuka
            document.querySelectorAll('[id^="picker-"]').forEach(el => {
                if (el.id !== pickerId) el.classList.add('hidden');
            });
            // Toggle picker yang diklik
            document.getElementById(pickerId).classList.toggle('hidden');
        }

        // ============================================================
        // PILIH GAMBAR UNTUK VARIAN LAMA
        // ============================================================
        function selectImage(type, variantId, imageId, imageUrl) {
            const previewEl = document.getElementById(`img-preview-${type}-${variantId}`);
            const hiddenEl  = document.getElementById(`hidden-img-${type}-${variantId}`);

            hiddenEl.value = imageId;

            if (imageUrl) {
                previewEl.outerHTML = `<img src="${imageUrl}" class="w-full h-full object-cover" id="img-preview-${type}-${variantId}">`;
            } else {
                previewEl.outerHTML = `<div id="img-preview-${type}-${variantId}" class="flex flex-col items-center text-gray-300">
                    <i class="fas fa-image text-2xl"></i>
                    <span class="text-xs mt-1">Pilih Foto</span>
                </div>`;
            }

            // Tutup picker setelah memilih
            document.getElementById(`picker-${type}-${variantId}`).classList.add('hidden');
        }

        // ============================================================
        // TAMBAH VARIAN BARU
        // ============================================================
        let newVarIndex = 0;

        function addNewVariant() {
            const sizes = ['S','M','L','XL','XXL','XXXL'];
            const sizeOptions = sizes.map(s => `<option value="${s}">${s}</option>`).join('');

            // Build image picker options dari existing images
            let imgPickerOptions = `
                <div class="cursor-pointer border-2 rounded overflow-hidden w-16 h-16 flex items-center justify-center bg-white text-gray-300 hover:border-indigo-400 transition"
                     onclick="selectNewVariantImage(${newVarIndex}, '', '')">
                    <i class="fas fa-times text-lg"></i>
                </div>`;

            existingImages.forEach(img => {
                imgPickerOptions += `
                    <div class="cursor-pointer border-2 border-gray-200 rounded overflow-hidden w-16 h-16 hover:border-indigo-400 transition"
                         onclick="selectNewVariantImage(${newVarIndex}, ${img.id}, '${img.url}')">
                        <img src="${img.url}" class="w-full h-full object-cover">
                    </div>`;
            });

            const ni = newVarIndex;
            const html = `
                <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-4" id="new-variant-row-${ni}">
                    <div class="grid grid-cols-1 md:grid-cols-7 gap-3 items-end">

                        <div class="md:col-span-1">
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Foto Varian</label>
                            <div class="w-full h-20 border-2 border-dashed border-gray-200 rounded-lg overflow-hidden cursor-pointer hover:border-indigo-400 transition flex items-center justify-center bg-white"
                                 onclick="openImagePicker('picker-new-${ni}')">
                                <div id="img-preview-new-${ni}" class="flex flex-col items-center text-gray-300">
                                    <i class="fas fa-image text-2xl"></i>
                                    <span class="text-xs mt-1">Pilih Foto</span>
                                </div>
                            </div>
                            {{-- existing product_image_id (foto lama) --}}
                            <input type="hidden" name="new_variants[${ni}][product_image_id]"
                                   id="hidden-img-new-${ni}" value="">
                            {{-- new_image_index (foto baru yang diupload) --}}
                            <input type="hidden" name="new_variants[${ni}][new_image_index]"
                                   id="hidden-new-img-index-${ni}" value="">
                        </div>

                        {{-- Ukuran --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Ukuran</label>
                            <select name="new_variants[${ni}][size]" class="w-full text-sm border-gray-300 rounded-md" required>
                                <option value="">Pilih</option>${sizeOptions}
                            </select>
                        </div>

                        {{-- Warna --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Warna</label>
                            <input type="text" name="new_variants[${ni}][color]" placeholder="Mis: Merah, Hitam"
                                id="color-name-new-${ni}"
                                oninput="syncColorFromNameNew(${ni})"
                                class="w-full text-sm border-gray-300 rounded-md" required>
                        </div>

                        {{-- Kode Warna --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Kode Warna</label>
                            <div class="flex items-center gap-2">
                                <input type="color" name="new_variants[${ni}][color_code]" value="#6b7280"
                                    id="color-picker-new-${ni}"
                                    oninput="syncNameFromColorNew(${ni})"
                                    class="w-9 h-9 border border-gray-300 rounded cursor-pointer flex-shrink-0">
                                <input type="text" id="color-hex-new-${ni}" value="#6b7280" maxlength="7"
                                    oninput="syncHexTextNew(${ni})"
                                    placeholder="#rrggbb"
                                    class="w-full text-xs border-gray-300 rounded-md font-mono">
                            </div>
                        </div>

                        {{-- Harga --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Harga (Rp)</label>
                            <input type="number" name="new_variants[${ni}][price]" placeholder="0"
                                min="0" class="w-full text-sm border-gray-300 rounded-md" required>
                        </div>

                        {{-- Stok --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Stok</label>
                            <input type="number" name="new_variants[${ni}][stock]" placeholder="0"
                                min="0" oninput="recalcStock()" class="w-full text-sm border-gray-300 rounded-md" required>
                        </div>

                        {{-- SKU & Delete --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">SKU Varian</label>
                            <div class="flex items-center gap-2">
                                <input type="text" name="new_variants[${ni}][sku]" placeholder="SKU"
                                    class="w-full text-sm border-gray-300 rounded-md">
                                <button type="button" onclick="document.getElementById('new-variant-row-${ni}').remove(); recalcStock();"
                                    class="flex-shrink-0 text-red-400 hover:text-red-600 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Image Picker untuk varian baru --}}
                    <div id="picker-new-${ni}" class="hidden mt-3 p-3 bg-white border border-gray-200 rounded-lg">
                        <p class="text-xs font-medium text-gray-600 mb-2">Pilih foto untuk varian ini:</p>
                        <div class="flex flex-wrap gap-2">
                            ${imgPickerOptions}
                        </div>
                        ${existingImages.length === 0 ? '<p class="text-xs text-gray-400 italic">Upload foto produk terlebih dahulu.</p>' : ''}
                    </div>
                </div>
            `;

            document.getElementById('new-variant-container').insertAdjacentHTML('beforeend', html);

            // Auto-assign: foto baru yang belum terpakai ke varian baru ini
            if (newUploadedImages.length > 0) {
                const usedTempIndices = [];
                for (let j = 0; j < ni; j++) {
                    const h = document.getElementById(`hidden-new-img-index-${j}`);
                    if (h && h.value !== '') usedTempIndices.push(parseInt(h.value));
                }
                const freeImg = newUploadedImages.find(img => !usedTempIndices.includes(img.tempIndex));
                if (freeImg) {
                    const hw = document.getElementById(`hidden-new-img-index-${ni}`);
                    const pw = document.getElementById(`img-preview-new-${ni}`);
                    if (hw) hw.value = freeImg.tempIndex;
                    if (pw) pw.outerHTML = `<img src="${freeImg.dataUrl}" class="w-full h-full object-cover" id="img-preview-new-${ni}">`;
                }
            }

            newVarIndex++;
        }

        // Pilih gambar untuk varian baru
        function selectNewVariantImage(idx, imageId, imageUrl) {
            const previewEl = document.getElementById(`img-preview-new-${idx}`);
            const hiddenEl  = document.getElementById(`hidden-img-new-${idx}`);
            const hiddenNewEl = document.getElementById(`hidden-new-img-index-${idx}`);

            hiddenEl.value = imageId; // existing image id
            if (hiddenNewEl) hiddenNewEl.value = ''; // clear new_image_index

            if (imageUrl) {
                previewEl.outerHTML = `<img src="${imageUrl}" class="w-full h-full object-cover" id="img-preview-new-${idx}">`;
            } else {
                previewEl.outerHTML = `<div id="img-preview-new-${idx}" class="flex flex-col items-center text-gray-300">
                    <i class="fas fa-image text-2xl"></i>
                    <span class="text-xs mt-1">Pilih Foto</span>
                </div>`;
            }

            document.getElementById(`picker-new-${idx}`).classList.add('hidden');
        }

        // Pilih foto BARU (yang baru diupload) untuk varian baru
        function selectNewVariantByNewImage(idx, tempIndex) {
            const img = newUploadedImages[tempIndex];
            if (!img) return;
            const previewEl = document.getElementById(`img-preview-new-${idx}`);
            const hiddenEl  = document.getElementById(`hidden-img-new-${idx}`);
            const hiddenNewEl = document.getElementById(`hidden-new-img-index-${idx}`);

            if (hiddenEl) hiddenEl.value = ''; // clear existing id
            if (hiddenNewEl) hiddenNewEl.value = tempIndex; // set new image index

            if (previewEl) {
                previewEl.outerHTML = `<img src="${img.dataUrl}" class="w-full h-full object-cover" id="img-preview-new-${idx}">`;
            }
            document.getElementById(`picker-new-${idx}`).classList.add('hidden');
        }

        function recalcStock() {
            const existing = document.querySelectorAll('#existing-variants-container input[name*="[stock]"]');
            const newVars  = document.querySelectorAll('#new-variant-container input[name*="[stock]"]');
            let total = 0;
            existing.forEach(inp => { total += parseInt(inp.value) || 0; });
            newVars.forEach(inp  => { total += parseInt(inp.value) || 0; });
            document.getElementById('total-stock-display').textContent = total.toLocaleString('id-ID');
            document.getElementById('total-stock-input').value = total;
        }

        // ============================================================
        // COLOR SYNC: Nama Warna <-> Color Picker <-> Hex Text
        // ============================================================
        const colorNameMap = {
            'merah':'#ef4444','merah muda':'#ec4899','pink':'#ec4899','pink tua':'#db2777',
            'hitam':'#111827','putih':'#f9fafb','abu':'#9ca3af','abu-abu':'#9ca3af','grey':'#9ca3af','gray':'#9ca3af',
            'biru':'#3b82f6','biru tua':'#1d4ed8','biru muda':'#93c5fd','navy':'#1e3a5f','biru navy':'#1e3a5f',
            'hijau':'#22c55e','hijau tua':'#15803d','hijau muda':'#86efac','olive':'#6b7c11',
            'kuning':'#eab308','oranye':'#f97316','orange':'#f97316',
            'ungu':'#a855f7','violet':'#7c3aed','lavender':'#c4b5fd',
            'coklat':'#92400e','cokelat':'#92400e','krem':'#fde68a','cream':'#fde68a','mocca':'#7c5c3b',
            'maroon':'#7f1d1d','marun':'#7f1d1d','burgundy':'#7f1d1d',
            'emas':'#f59e0b','gold':'#d97706','silver':'#c0c0c0','perak':'#c0c0c0',
            'red':'#ef4444','blue':'#3b82f6','green':'#22c55e','yellow':'#eab308',
            'black':'#111827','white':'#f9fafb','purple':'#a855f7','brown':'#92400e',
            'teal':'#14b8a6','cyan':'#06b6d4','indigo':'#6366f1','lime':'#84cc16',
        };
        function nameToHex(name) { return colorNameMap[name.trim().toLowerCase()] || null; }

        // --- Varian LAMA (Blade rendered) ---
        function syncColorFromNameEx(id) {
            const hex = nameToHex(document.getElementById(`color-name-ex-${id}`).value);
            if (hex) {
                document.getElementById(`color-picker-ex-${id}`).value = hex;
                document.getElementById(`color-hex-ex-${id}`).value    = hex;
            }
        }
        function syncNameFromColorEx(id) {
            const hex = document.getElementById(`color-picker-ex-${id}`).value.toLowerCase();
            document.getElementById(`color-hex-ex-${id}`).value = hex;
            const matched = Object.entries(colorNameMap).find(([,v]) => v === hex);
            if (matched) document.getElementById(`color-name-ex-${id}`).value = matched[0].charAt(0).toUpperCase()+matched[0].slice(1);
        }
        function syncHexTextEx(id) {
            const val = document.getElementById(`color-hex-ex-${id}`).value.trim();
            if (/^#[0-9a-fA-F]{6}$/.test(val)) document.getElementById(`color-picker-ex-${id}`).value = val;
        }

        // --- Varian BARU (JS rendered) ---
        function syncColorFromNameNew(idx) {
            const hex = nameToHex(document.getElementById(`color-name-new-${idx}`).value);
            if (hex) {
                document.getElementById(`color-picker-new-${idx}`).value = hex;
                document.getElementById(`color-hex-new-${idx}`).value    = hex;
            }
        }
        function syncNameFromColorNew(idx) {
            const hex = document.getElementById(`color-picker-new-${idx}`).value.toLowerCase();
            document.getElementById(`color-hex-new-${idx}`).value = hex;
            const matched = Object.entries(colorNameMap).find(([,v]) => v === hex);
            if (matched) document.getElementById(`color-name-new-${idx}`).value = matched[0].charAt(0).toUpperCase()+matched[0].slice(1);
        }
        function syncHexTextNew(idx) {
            const val = document.getElementById(`color-hex-new-${idx}`).value.trim();
            if (/^#[0-9a-fA-F]{6}$/.test(val)) document.getElementById(`color-picker-new-${idx}`).value = val;
        }
    </script>
</x-app-layout>