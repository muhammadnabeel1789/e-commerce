<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Stock Log') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .sl-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Hilangkan spinner semua input number */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }

        .sl-wrap { background: #F0F2F5; min-height: 100vh; padding: 32px 0 60px; }
        .sl-container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

        /* ── Alert ── */
        .sl-alert {
            padding: 12px 16px; border-radius: 10px; margin-bottom: 20px;
            font-size: 0.825rem; font-weight: 600; display: flex; align-items: center; gap: 8px;
        }
        .sl-alert.success { background: #F0FDF4; border-left: 4px solid #22C55E; color: #15803D; }
        .sl-alert.error   { background: #FFF1F2; border-left: 4px solid #E11D48; color: #BE123C; }

        /* ── Layout ── */
        .sl-layout { display: grid; grid-template-columns: 340px 1fr; gap: 20px; align-items: start; }

        /* ── Card ── */
        .sl-card {
            background: #fff; border-radius: 16px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.07); overflow: hidden;
        }
        .sl-card-header {
            padding: 16px 20px; border-bottom: 1px solid #F3F4F6;
            display: flex; align-items: center; gap: 10px;
        }
        .sl-card-icon {
            width: 34px; height: 34px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .sl-card-title { font-size: 0.9rem; font-weight: 800; color: #1A1A2E; text-transform: uppercase; letter-spacing: 0.04em; }
        .sl-card-body { padding: 20px; }

        /* ── Form ── */
        .sl-field { margin-bottom: 16px; }
        .sl-label { display: block; font-size: 0.75rem; font-weight: 700; color: #6B7280; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 6px; }
        .sl-input, .sl-select {
            width: 100%; padding: 9px 12px; border: 1.5px solid #E5E7EB; border-radius: 8px;
            font-size: 0.85rem; font-weight: 500; color: #1A1A2E;
            background: #fff; outline: none; transition: border-color 0.15s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .sl-input:focus, .sl-select:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.08); }
        .sl-input.disabled, .sl-select:disabled { background: #F9FAFB; color: #9CA3AF; cursor: not-allowed; }
        .sl-err { font-size: 0.7rem; color: #DC2626; margin-top: 4px; }

        /* Type toggle */
        .sl-type-toggle { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .sl-type-btn {
            padding: 9px; border: 2px solid #E5E7EB; border-radius: 8px;
            font-size: 0.8rem; font-weight: 700; cursor: pointer;
            text-align: center; transition: all 0.15s; background: #fff;
            display: flex; align-items: center; justify-content: center; gap: 5px;
        }
        .sl-type-btn.in  { color: #15803D; }
        .sl-type-btn.out { color: #BE123C; }
        .sl-type-btn.in.active  { background: #F0FDF4; border-color: #22C55E; }
        .sl-type-btn.out.active { background: #FFF1F2; border-color: #E11D48; }
        .sl-type-btn:hover.in  { background: #F0FDF4; border-color: #86EFAC; }
        .sl-type-btn:hover.out { background: #FFF1F2; border-color: #FECDD3; }

        /* Qty display */
        .sl-qty-wrap {
            display: flex; align-items: center; border: 1.5px solid #E5E7EB;
            border-radius: 8px; overflow: hidden;
        }
        .sl-qty-wrap:focus-within { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.08); }
        .sl-qty-btn {
            width: 38px; height: 38px; background: #F9FAFB; border: none;
            font-size: 1.1rem; font-weight: 700; color: #4F46E5;
            cursor: pointer; transition: background 0.15s; flex-shrink: 0;
        }
        .sl-qty-btn:hover { background: #EEF2FF; }
        .sl-qty-input {
            flex: 1; border: none; outline: none; text-align: center;
            font-size: 0.95rem; font-weight: 800; color: #1A1A2E;
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 0; background: #fff;
        }

        /* Submit btn */
        .sl-submit-btn {
            width: 100%; padding: 11px; background: #4F46E5; color: #fff;
            border: none; border-radius: 10px; font-size: 0.875rem; font-weight: 800;
            cursor: pointer; transition: background 0.15s; font-family: 'Plus Jakarta Sans', sans-serif;
            display: flex; align-items: center; justify-content: center; gap: 6px;
            margin-top: 20px;
        }
        .sl-submit-btn:hover { background: #4338CA; }

        /* ── Tabel Riwayat ── */
        .sl-tbl-head {
            display: grid; grid-template-columns: 1.2fr 2fr 0.7fr 0.9fr 1.5fr 1fr;
            padding: 10px 16px; background: #F8F9FE; border-bottom: 1px solid #EDEEF5;
        }
        .sl-th { font-size: 0.67rem; font-weight: 700; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.07em; }
        .sl-th.center { text-align: center; }

        .sl-row {
            display: grid; grid-template-columns: 1.2fr 2fr 0.7fr 0.9fr 1.5fr 1fr;
            padding: 13px 16px; border-bottom: 1px solid #F5F5F8;
            align-items: center; transition: background 0.12s;
        }
        .sl-row:last-child { border-bottom: none; }
        .sl-row:hover { background: #FAFBFF; }

        .sl-time { font-size: 0.75rem; font-weight: 600; color: #374151; }
        .sl-time-sub { font-size: 0.68rem; color: #AAA; margin-top: 2px; }

        .sl-prod-name { font-size: 0.82rem; font-weight: 700; color: #1A1A2E; }
        .sl-prod-var  { font-size: 0.72rem; color: #9CA3AF; margin-top: 2px; }
        .sl-prod-var.deleted { color: #EF4444; font-style: italic; }

        .sl-badge {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 3px 10px; border-radius: 100px;
            font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;
        }
        .sl-badge.in  { background: #F0FDF4; color: #15803D; border: 1px solid #86EFAC; }
        .sl-badge.out { background: #FFF1F2; color: #BE123C; border: 1px solid #FECDD3; }

        .sl-qty-val { font-size: 0.9rem; font-weight: 800; text-align: center; }
        .sl-qty-val.in  { color: #16A34A; }
        .sl-qty-val.out { color: #DC2626; }

        .sl-notes { font-size: 0.75rem; color: #6B7280; }
        .sl-user  { font-size: 0.75rem; color: #6B7280; }

        .sl-empty { text-align: center; padding: 60px 20px; }
        .sl-empty-icon {
            width: 60px; height: 60px; background: #EEF2FF; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;
        }
        .sl-empty p { font-size: 0.85rem; color: #AAA; font-weight: 500; }

        @media(max-width: 900px) {
            .sl-layout { grid-template-columns: 1fr; }
            .sl-tbl-head { display: none; }
            .sl-row { grid-template-columns: 1fr; gap: 6px; padding: 12px 16px; }
        }
    </style>

    <div class="sl-wrap">
        <div class="sl-container">

            {{-- Alert --}}
            @if(session('success'))
                <div class="sl-alert success">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="sl-alert error">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="sl-layout">

                {{-- ── KIRI: FORM ── --}}
                <div class="sl-card">
                    <div class="sl-card-header">
                        <div class="sl-card-icon" style="background:#EEF2FF;">
                            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="#4F46E5">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <span class="sl-card-title">Tambah / Kurang Stok</span>
                    </div>
                    <div class="sl-card-body">
                        <form action="{{ route('admin.stock-logs.store') }}" method="POST" id="stock-form">
                            @csrf

                            {{-- Produk --}}
                            <div class="sl-field">
                                <label class="sl-label">Pilih Produk</label>
                                <select id="product_id" class="sl-select" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Varian --}}
                            <div class="sl-field">
                                <label class="sl-label">Varian (Ukuran & Warna)</label>
                                <select name="product_variant_id" id="product_variant_id" class="sl-select" required disabled>
                                    <option value="">-- Pilih Produk Dulu --</option>
                                </select>
                                @error('product_variant_id')
                                    <div class="sl-err">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Stok saat ini --}}
                            <div class="sl-field" id="current-stock-wrap" style="display:none;">
                                <label class="sl-label">Stok Saat Ini</label>
                                <div style="display:flex; align-items:center; gap:8px; background:#F9FAFB; border:1.5px solid #E5E7EB; border-radius:8px; padding:9px 12px;">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#6B7280">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                    </svg>
                                    <span id="current-stock-val" class="text-sm font-bold text-indigo-700">0</span>
                                    <span class="text-xs text-gray-400">pcs tersedia</span>
                                </div>
                            </div>

                            {{-- Tipe Transaksi --}}
                            <div class="sl-field">
                                <label class="sl-label">Tipe Transaksi</label>
                                <div class="sl-type-toggle">
                                    <button type="button" class="sl-type-btn in active" id="btn-in" onclick="setType('in')">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                        </svg>
                                        Stok Masuk
                                    </button>
                                    <button type="button" class="sl-type-btn out" id="btn-out" onclick="setType('out')">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                        </svg>
                                        Stok Keluar
                                    </button>
                                </div>
                                <input type="hidden" name="type" id="type-input" value="in">
                            </div>

                            {{-- Jumlah dengan tombol +/- --}}
                            <div class="sl-field">
                                <label class="sl-label">Jumlah (Pcs)</label>
                                <div class="sl-qty-wrap">
                                    <button type="button" class="sl-qty-btn" onclick="changeQty(-1)">−</button>
                                    <input type="number" name="quantity_change" id="qty-input"
                                           value="{{ old('quantity_change', 1) }}" min="1"
                                           class="sl-qty-input" required>
                                    <button type="button" class="sl-qty-btn" onclick="changeQty(1)">+</button>
                                </div>
                                @error('quantity_change')
                                    <div class="sl-err">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Catatan --}}
                            <div class="sl-field" style="margin-bottom:0;">
                                <label class="sl-label">Catatan</label>
                                <select name="notes" class="sl-select">
                                    <option value="">Pilih alasan...</option>
                                    <option value="Pembelian dari supplier">Pembelian dari supplier</option>
                                    <option value="Produk rusak/cacat">Produk rusak/cacat</option>
                                    <option value="Kehilangan">Barang Hilang</option>
                                </select>
                            </div>

                            <button type="submit" class="sl-submit-btn">
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan Perubahan Stok
                            </button>
                        </form>
                    </div>
                </div>

                {{-- ── KANAN: RIWAYAT ── --}}
                <div class="sl-card">
                    <div class="sl-card-header">
                        <div class="sl-card-icon" style="background:#FFF7ED;">
                            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="#EA580C">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <span class="sl-card-title">Riwayat Pergerakan Stok</span>
                    </div>

                    {{-- Table Head --}}
                    <div class="sl-tbl-head">
                        <div class="sl-th"> Tanggal & Waktu</div>
                        <div class="sl-th">Produk & Varian</div>
                        <div class="sl-th center">Tipe</div>
                        <div class="sl-th center">Qty</div>
                        <div class="sl-th">Catatan</div>
                        <div class="sl-th">User</div>
                    </div>

                    @forelse($stockLogs as $log)
                        <div class="sl-row">
                            <div>
                                <div class="sl-time">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="sl-time-sub">{{ $log->created_at->format('H:i') }} WIB</div>
                            </div>
                            <div>
                                <div class="sl-prod-name">{{ $log->product->name ?? 'Produk Dihapus' }}</div>
                                <div class="sl-prod-var {{ !$log->variant ? 'deleted' : '' }}">
                                    @if($log->variant)
                                        Size: {{ $log->variant->size }} · {{ $log->variant->color }}
                                    @else
                                        Varian dihapus
                                    @endif
                                </div>
                            </div>
                            <div style="text-align:center;">
                                <span class="sl-badge {{ $log->type }}">
                                    {{ $log->type == 'in' ? 'MASUK' : 'KELUAR' }}
                                </span>
                            </div>
                            <div style="text-align:center;">
                                <div class="sl-qty-val {{ $log->type }}">
                                    {{ $log->type == 'in' ? '+' : '−' }}{{ abs($log->quantity_change) }}
                                </div>
                                <div style="font-size:0.68rem; color:#9CA3AF; margin-top:2px; font-weight:700;">
                                    {{ $log->previous_stock ?? 0 }} <span style="font-size:0.5rem;">➔</span> <span style="color:#374151;">{{ $log->current_stock ?? 0 }}</span>
                                </div>
                            </div>
                            <div class="sl-notes">{{ $log->notes ?? '—' }}</div>
                            <div class="sl-user">{{ $log->user->name ?? 'Sistem' }}</div>
                        </div>
                    @empty
                        <div class="sl-empty">
                            <div class="sl-empty-icon">
                                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="#4F46E5">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p>Belum ada riwayat pergerakan stok.</p>
                        </div>
                    @endforelse

                    {{-- Pagination --}}
                    @if($stockLogs->hasPages())
                        <div style="padding: 14px 20px; border-top: 1px solid #F3F4F6;">
                            {{ $stockLogs->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productsData  = @json($products->load('variants'));
            const productSelect = document.getElementById('product_id');
            const variantSelect = document.getElementById('product_variant_id');

            // ── Pilih Produk → populate Varian ──
            productSelect.addEventListener('change', function () {
                const selectedId = parseInt(this.value);
                variantSelect.innerHTML = '<option value="">-- Pilih Varian --</option>';
                document.getElementById('current-stock-wrap').style.display = 'none';

                if (selectedId) {
                    const prod = productsData.find(p => p.id === selectedId);
                    if (prod && prod.variants && prod.variants.length > 0) {
                        variantSelect.disabled = false;
                        prod.variants.forEach(v => {
                            const opt = document.createElement('option');
                            opt.value = v.id;
                            opt.dataset.stock = v.stock;
                            opt.textContent = `Size: ${v.size} — ${v.color} (Stok: ${v.stock} pcs)`;
                            variantSelect.appendChild(opt);
                        });
                    } else {
                        variantSelect.disabled = true;
                        variantSelect.innerHTML = '<option value="">Tidak ada varian tersedia</option>';
                    }
                } else {
                    variantSelect.disabled = true;
                    variantSelect.innerHTML = '<option value="">-- Pilih Produk Dulu --</option>';
                }
            });

            // ── Pilih Varian → tampilkan stok saat ini ──
            variantSelect.addEventListener('change', function () {
                const selected = this.options[this.selectedIndex];
                const stock    = selected ? selected.dataset.stock : null;
                const wrap     = document.getElementById('current-stock-wrap');
                if (stock !== undefined && stock !== null && this.value !== '') {
                    document.getElementById('current-stock-val').textContent = parseInt(stock).toLocaleString('id-ID');
                    wrap.style.display = 'block';
                } else {
                    wrap.style.display = 'none';
                }
            });
        });

        // ── Toggle Tipe Transaksi ──
        function setType(type) {
            document.getElementById('type-input').value = type;
            document.getElementById('btn-in').classList.toggle('active', type === 'in');
            document.getElementById('btn-out').classList.toggle('active', type === 'out');
        }

        // ── Tombol +/- Qty ──
        function changeQty(delta) {
            const input = document.getElementById('qty-input');
            const val   = parseInt(input.value) || 1;
            input.value = Math.max(1, val + delta);
        }
    </script>
</x-app-layout>