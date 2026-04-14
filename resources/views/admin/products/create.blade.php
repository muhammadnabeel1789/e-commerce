<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Produk Baru') }}
        </h2>
    </x-slot>

    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }

        .picker-thumb {
            position: relative;
            width: 64px; height: 64px; flex-shrink: 0;
            border: 2px solid #e5e7eb; border-radius: 6px;
            overflow: hidden; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            background: #fff; transition: border-color .15s;
        }
        .picker-thumb:hover { border-color: #6366f1; }
        .picker-thumb.selected { border-color: #6366f1; box-shadow: 0 0 0 2px #a5b4fc; }
        .picker-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .picker-thumb .cover-badge {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: #6366f1; color: #fff; font-size: 9px;
            text-align: center; padding: 1px 0;
        }
        .picker-wrap { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <strong class="font-bold">Terdapat kesalahan:</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- ===== SECTION 1: INFORMASI DASAR ===== --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 border-b pb-3 mb-5">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                            <select name="category_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                            <select name="brand_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Pilih Brand (Opsional)</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Berat Produk (gram)</label>
                            <input type="number" name="weight" value="{{ old('weight', 1000) }}" min="1"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Produk</label>
                            <textarea name="description" rows="4"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Tuliskan deskripsi produk...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- ===== SECTION 2: FOTO PRODUK ===== --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between border-b pb-3 mb-5">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Foto Produk</h3>
                            <p class="text-xs text-gray-400 mt-1">Upload foto sesuai jumlah varian — setiap foto akan otomatis dipasangkan ke masing-masing varian.</p>
                        </div>
                        <button type="button" onclick="autoAssignPhotos()" id="btn-auto-assign"
                            class="hidden text-xs bg-indigo-50 text-indigo-600 border border-indigo-200 px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition font-medium">
                            ⚡ Tautkan Foto ke Varian
                        </button>
                    </div>

                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-400 transition cursor-pointer"
                        onclick="document.getElementById('images').click()">
                        <i class="fas fa-images text-4xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-500">Klik untuk upload banyak foto sekaligus</p>
                        <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, WEBP. Maks 5MB per foto. Foto ke-1 = Cover, foto berikutnya = foto tiap varian.</p>
                        <input type="file" name="images[]" id="images" class="hidden" accept="image/*" multiple onchange="previewImages(this)">
                    </div>

                    <div id="image-preview-container" class="mt-4" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap:8px;"></div>
                </div>

                {{-- ===== SECTION 3: VARIAN ===== --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center border-b pb-3 mb-5">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Varian Produk</h3>
                            <p class="text-xs text-gray-400 mt-1">Setiap varian dapat dikaitkan dengan foto produk yang berbeda.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2 bg-indigo-50 border border-indigo-200 rounded-lg px-3 py-1.5">
                                <span class="text-xs font-medium text-indigo-500 uppercase tracking-wide">Total Stok</span>
                                <span id="total-stock-display" class="text-sm font-bold text-indigo-700">0</span>
                            </div>
                            <button type="button" onclick="addVariant()"
                                class="text-sm bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded hover:bg-indigo-100 transition font-medium">
                                + Tambah Varian
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="stock" id="total-stock-input" value="0">
                    <div class="space-y-4" id="variant-container"></div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.products.index') }}" class="bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg hover:bg-gray-300 transition">Batal</a>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 transition font-medium">
                        Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let uploadedImages = []; // [{ index, dataUrl, name }]

        // ============================================================
        // PREVIEW FOTO YANG DIUPLOAD
        // ============================================================
        function previewImages(input) {
            const container = document.getElementById('image-preview-container');
            container.innerHTML = '';
            uploadedImages = [];

            if (!input.files || input.files.length === 0) {
                document.getElementById('btn-auto-assign').classList.add('hidden');
                return;
            }

            const files   = Array.from(input.files);
            const results = new Array(files.length);
            let loaded    = 0;

            files.forEach((file, i) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    results[i] = e.target.result;
                    loaded++;
                    if (loaded === files.length) {
                        results.forEach((dataUrl, idx) => {
                            uploadedImages[idx] = { index: idx, dataUrl, name: files[idx].name };
                            container.insertAdjacentHTML('beforeend', `
                                <div id="photo-card-${idx}" style="position:relative; border-radius:8px; border:2px solid #e5e7eb; overflow:hidden; aspect-ratio:1;">
                                    <img src="${dataUrl}" style="width:100%; height:100%; object-fit:cover; display:block;">
                                    <span id="photo-label-${idx}" style="position:absolute;bottom:0;left:0;right:0;font-size:10px;text-align:center;padding:3px 0;font-weight:700;
                                        background:${idx===0?'#6366f1':'#64748b'};color:#fff;">
                                        ${idx === 0 ? '📌 Cover' : `Foto ${idx+1}`}
                                    </span>
                                </div>
                            `);
                        });
                        document.getElementById('btn-auto-assign').classList.remove('hidden');
                        refreshAllVariantPickers();
                        autoAssignPhotos(); // auto-assign saat upload
                    }
                };
                reader.readAsDataURL(file);
            });
        }

        // ============================================================
        // AUTO-ASSIGN: Pasangkan foto ke varian secara otomatis
        // ============================================================
        function autoAssignPhotos() {
            const pickers = document.querySelectorAll('.variant-picker');
            pickers.forEach((pickerEl, order) => {
                const variantIdx = pickerEl.dataset.variantidx;
                // Foto 0 = Cover, varian ke-1 pakai foto 0, varian ke-2 pakai foto 1, dst
                // Jadi: varian order ke-0 → foto index 0, order ke-1 → foto index 1, dst
                const imgData = uploadedImages[order] || null;
                const hiddenInput = document.getElementById(`hidden-img-index-${variantIdx}`);
                const previewWrap = document.getElementById(`img-preview-wrap-${variantIdx}`);
                if (!hiddenInput || !previewWrap) return;

                if (imgData) {
                    hiddenInput.value = imgData.index;
                    previewWrap.innerHTML = `<img src="${imgData.dataUrl}" style="width:100%;height:100%;object-fit:cover;">`;
                    // Tandai selected di picker
                    pickerEl.querySelectorAll('.picker-thumb').forEach(t => t.classList.remove('selected'));
                    const thumb = pickerEl.querySelector(`.picker-thumb[data-imgindex="${imgData.index}"]`);
                    if (thumb) thumb.classList.add('selected');
                }
            });
            updatePhotoLabels();
        }

        // ============================================================
        // UPDATE LABEL FOTO: Tampilkan "Varian X" pada preview foto
        // ============================================================
        function updatePhotoLabels() {
            // Reset semua label
            uploadedImages.forEach((img) => {
                const label = document.getElementById(`photo-label-${img.index}`);
                const card  = document.getElementById(`photo-card-${img.index}`);
                if (label) {
                    label.textContent = img.index === 0 ? '📌 Cover' : `Foto ${img.index+1}`;
                    label.style.background = img.index === 0 ? '#6366f1' : '#64748b';
                }
                if (card) card.style.borderColor = '#e5e7eb';
            });
            // Tandai foto yang sedang dipakai varian
            document.querySelectorAll('.variant-picker').forEach((pickerEl, order) => {
                const variantIdx  = pickerEl.dataset.variantidx;
                const hiddenInput = document.getElementById(`hidden-img-index-${variantIdx}`);
                if (!hiddenInput || hiddenInput.value === '') return;
                const imgIdx = parseInt(hiddenInput.value);
                const label  = document.getElementById(`photo-label-${imgIdx}`);
                const card   = document.getElementById(`photo-card-${imgIdx}`);
                if (label) {
                    label.textContent = `✅ Varian ${order+1}`;
                    label.style.background = '#16a34a';
                }
                if (card) card.style.borderColor = '#16a34a';
            });
        }

        // ============================================================
        // BUILD KONTEN PICKER
        // ============================================================
        function buildPickerContent(variantIdx) {
            let html = `<p class="text-xs font-medium text-gray-600 mb-1">Pilih foto untuk varian ini:</p>
                        <div class="picker-wrap">
                            <div class="picker-thumb" data-imgindex="" title="Tidak ada foto">
                                <i class="fas fa-times text-gray-400 text-lg"></i>
                            </div>`;

            uploadedImages.forEach(img => {
                html += `
                    <div class="picker-thumb" data-imgindex="${img.index}" title="Foto ${img.index + 1}${img.index === 0 ? ' (Cover)' : ''}">
                        <img src="${img.dataUrl}" alt="">
                        ${img.index === 0 ? '<span class="cover-badge">Cover</span>' : ''}
                    </div>`;
            });

            html += `</div>`;

            if (uploadedImages.length === 0) {
                html += `<p class="text-xs text-gray-400 italic mt-1">Upload foto produk terlebih dahulu.</p>`;
            }

            return html;
        }

        function refreshAllVariantPickers() {
            document.querySelectorAll('.variant-picker').forEach(pickerEl => {
                const variantIdx = pickerEl.dataset.variantidx;
                pickerEl.innerHTML = buildPickerContent(variantIdx);
                bindPickerEvents(pickerEl, variantIdx);
                const hiddenInput = document.getElementById(`hidden-img-index-${variantIdx}`);
                if (hiddenInput && hiddenInput.value !== '') {
                    const selected = pickerEl.querySelector(`.picker-thumb[data-imgindex="${hiddenInput.value}"]`);
                    if (selected) selected.classList.add('selected');
                }
            });
        }

        // ============================================================
        // BIND EVENT KLIK PICKER
        // ============================================================
        function bindPickerEvents(pickerEl, variantIdx) {
            pickerEl.querySelectorAll('.picker-thumb').forEach(thumb => {
                thumb.addEventListener('click', function () {
                    const imgIndex    = this.dataset.imgindex;
                    const imgData     = (imgIndex !== '') ? uploadedImages[parseInt(imgIndex)] : null;
                    const previewWrap = document.getElementById(`img-preview-wrap-${variantIdx}`);
                    const hiddenInput = document.getElementById(`hidden-img-index-${variantIdx}`);

                    hiddenInput.value = imgIndex;

                    if (imgData) {
                        previewWrap.innerHTML = `<img src="${imgData.dataUrl}" style="width:100%;height:100%;object-fit:cover;">` ;
                    } else {
                        previewWrap.innerHTML = `
                            <div style="display:flex;flex-direction:column;align-items:center;color:#d1d5db;">
                                <i class="fas fa-image" style="font-size:1.5rem;"></i>
                                <span style="font-size:0.75rem;margin-top:4px;">Pilih Foto</span>
                            </div>`;
                    }

                    pickerEl.querySelectorAll('.picker-thumb').forEach(t => t.classList.remove('selected'));
                    this.classList.add('selected');
                    pickerEl.classList.add('hidden');
                    updatePhotoLabels();
                });
            });
        }

        // ============================================================
        // TOGGLE PICKER
        // ============================================================
        function openImagePicker(variantIdx) {
            document.querySelectorAll('.variant-picker').forEach(el => {
                if (el.dataset.variantidx !== String(variantIdx)) el.classList.add('hidden');
            });
            document.getElementById(`picker-v${variantIdx}`).classList.toggle('hidden');
        }

        // ============================================================
        // TAMBAH BARIS VARIAN
        // ============================================================
        let variantIndex = 0;

        function addVariant() {
            const idx = variantIndex;

            const html = `
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition" id="variant-row-${idx}">
                    <div class="grid grid-cols-1 md:grid-cols-7 gap-3 items-end">

                        <!-- Foto Varian -->
                        <div class="md:col-span-1">
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Foto Varian</label>
                            <div id="img-preview-wrap-${idx}"
                                 class="w-full h-20 border-2 border-dashed border-gray-200 rounded-lg overflow-hidden cursor-pointer hover:border-indigo-400 transition flex items-center justify-center bg-gray-50"
                                 onclick="openImagePicker(${idx})">
                                <div style="display:flex;flex-direction:column;align-items:center;color:#d1d5db;">
                                    <i class="fas fa-image" style="font-size:1.5rem;"></i>
                                    <span style="font-size:0.75rem;margin-top:4px;">Pilih Foto</span>
                                </div>
                            </div>
                            <input type="hidden" name="variants[${idx}][image_index]" id="hidden-img-index-${idx}" value="">
                        </div>

                        <!-- Ukuran -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Ukuran <span class="text-red-500">*</span></label>
                            <select name="variants[${idx}][size]" class="w-full text-sm border-gray-300 rounded-md" required>
                                <option value="">Pilih</option>
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                                <option value="XXL">XXL</option>
                                <option value="XXXL">XXXL</option>
                            </select>
                        </div>

                        <!-- Warna -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Warna <span class="text-red-500">*</span></label>
                            <input type="text" name="variants[${idx}][color]" placeholder="Mis: Merah, Hitam"
                                id="color-name-${idx}"
                                oninput="syncColorFromName(${idx})"
                                class="w-full text-sm border-gray-300 rounded-md" required>
                        </div>

                        <!-- Kode Warna -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Kode Warna</label>
                            <div class="flex items-center gap-2">
                                <input type="color" name="variants[${idx}][color_code]" value="#6b7280"
                                    id="color-picker-${idx}"
                                    oninput="syncNameFromColor(${idx})"
                                    class="w-9 h-9 border border-gray-300 rounded cursor-pointer flex-shrink-0">
                                <input type="text" id="color-hex-${idx}" value="#6b7280" maxlength="7"
                                    oninput="syncHexInputFromText(${idx})"
                                    placeholder="#rrggbb"
                                    class="w-full text-xs border-gray-300 rounded-md font-mono">
                            </div>
                        </div>

                        <!-- Harga -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Harga (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="variants[${idx}][price]" placeholder="0"
                                min="0" class="w-full text-sm border-gray-300 rounded-md" required>
                        </div>

                        <!-- Stok -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Stok <span class="text-red-500">*</span></label>
                            <input type="number" name="variants[${idx}][stock]" placeholder="0"
                                min="0" oninput="recalcStock()" class="w-full text-sm border-gray-300 rounded-md" required>
                        </div>

                        <!-- SKU & Delete -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">SKU Varian <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-2">
                                <input type="text" name="variants[${idx}][sku_variant]" placeholder="Contoh: PRD-ABC-V1"
                                    class="w-full text-sm border-gray-300 rounded-md" required>
                                <button type="button"
                                    onclick="document.getElementById('variant-row-${idx}').remove(); recalcStock();"
                                    class="flex-shrink-0 text-red-400 hover:text-red-600 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Image Picker Popup -->
                    <div id="picker-v${idx}"
                         class="variant-picker hidden mt-3 p-3 bg-gray-50 border border-gray-200 rounded-lg"
                         data-variantidx="${idx}">
                    </div>
                </div>
            `;

            document.getElementById('variant-container').insertAdjacentHTML('beforeend', html);

            const pickerEl = document.getElementById(`picker-v${idx}`);
            pickerEl.innerHTML = buildPickerContent(idx);
            bindPickerEvents(pickerEl, idx);

            // Auto-assign: cari foto yang belum dipakai varian lain
            const usedIndices = [];
            document.querySelectorAll('.variant-picker').forEach(pEl => {
                const vi = pEl.dataset.variantidx;
                const h  = document.getElementById(`hidden-img-index-${vi}`);
                if (h && h.value !== '') usedIndices.push(parseInt(h.value));
            });
            const nextFreeImg = uploadedImages.find(img => !usedIndices.includes(img.index));
            if (nextFreeImg) {
                const hw = document.getElementById(`hidden-img-index-${idx}`);
                const pw = document.getElementById(`img-preview-wrap-${idx}`);
                hw.value = nextFreeImg.index;
                pw.innerHTML = `<img src="${nextFreeImg.dataUrl}" style="width:100%;height:100%;object-fit:cover;">`;
                const thumb = pickerEl.querySelector(`.picker-thumb[data-imgindex="${nextFreeImg.index}"]`);
                if (thumb) { pickerEl.querySelectorAll('.picker-thumb').forEach(t=>t.classList.remove('selected')); thumb.classList.add('selected'); }
            }

            variantIndex++;
            updatePhotoLabels();
        }

        // ============================================================
        // HITUNG TOTAL STOK
        // ============================================================
        function recalcStock() {
            let total = 0;
            document.querySelectorAll('#variant-container input[name*="[stock]"]').forEach(inp => {
                total += parseInt(inp.value) || 0;
            });
            document.getElementById('total-stock-display').textContent = total.toLocaleString('id-ID');
            document.getElementById('total-stock-input').value = total;
        }

        // ============================================================
        // COLOR SYNC: Nama Warna ↔ Color Picker ↔ Hex Text
        // ============================================================
        const colorNameMap = {
            // ID
            'merah':'#ef4444','merah muda':'#ec4899','pink':'#ec4899','pink tua':'#db2777',
            'hitam':'#111827','putih':'#f9fafb','abu':'#9ca3af','abu-abu':'#9ca3af','grey':'#9ca3af','gray':'#9ca3af',
            'biru':'#3b82f6','biru tua':'#1d4ed8','biru muda':'#93c5fd','navy':'#1e3a5f','biru navy':'#1e3a5f',
            'hijau':'#22c55e','hijau tua':'#15803d','hijau muda':'#86efac','olive':'#6b7c11',
            'kuning':'#eab308','oranye':'#f97316','orange':'#f97316',
            'ungu':'#a855f7','violet':'#7c3aed','lavender':'#c4b5fd',
            'coklat':'#92400e','cokelat':'#92400e','krem':'#fde68a','cream':'#fde68a','mocca':'#7c5c3b',
            'maroon':'#7f1d1d','marun':'#7f1d1d','burgundy':'#7f1d1d',
            'emas':'#f59e0b','gold':'#d97706','silver':'#c0c0c0','perak':'#c0c0c0',
            // EN common
            'red':'#ef4444','blue':'#3b82f6','green':'#22c55e','yellow':'#eab308',
            'black':'#111827','white':'#f9fafb','purple':'#a855f7','brown':'#92400e',
            'teal':'#14b8a6','cyan':'#06b6d4','indigo':'#6366f1','lime':'#84cc16',
        };

        function nameToHex(name) {
            return colorNameMap[name.trim().toLowerCase()] || null;
        }

        function syncColorFromName(idx) {
            const nameInput   = document.getElementById(`color-name-${idx}`);
            const pickerInput = document.getElementById(`color-picker-${idx}`);
            const hexInput    = document.getElementById(`color-hex-${idx}`);
            const hex = nameToHex(nameInput.value);
            if (hex) {
                pickerInput.value = hex;
                hexInput.value    = hex;
            }
        }

        function syncNameFromColor(idx) {
            const pickerInput = document.getElementById(`color-picker-${idx}`);
            const hexInput    = document.getElementById(`color-hex-${idx}`);
            hexInput.value = pickerInput.value;
            // Cari nama warna yang cocok dengan hex ini
            const hex = pickerInput.value.toLowerCase();
            const nameInput = document.getElementById(`color-name-${idx}`);
            const matched = Object.entries(colorNameMap).find(([,v]) => v === hex);
            if (matched) nameInput.value = matched[0].charAt(0).toUpperCase() + matched[0].slice(1);
        }

        function syncHexInputFromText(idx) {
            const hexInput    = document.getElementById(`color-hex-${idx}`);
            const pickerInput = document.getElementById(`color-picker-${idx}`);
            const val = hexInput.value.trim();
            if (/^#[0-9a-fA-F]{6}$/.test(val)) {
                pickerInput.value = val;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            addVariant();
            addVariant();
        });
    </script>
</x-app-layout>