<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('kurir.orders.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800">
                Detail Pesanan <span class="text-indigo-600">#{{ $order->order_number }}</span>
            </h2>
        </div>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap');
        .ks-wrap * { font-family: 'DM Sans', sans-serif; }
        .ks-wrap { background: #F0F2F5; min-height: 100vh; padding: 24px 0 60px; }
        .ks-container { max-width: 860px; margin: 0 auto; padding: 0 24px; }

        .ks-card { background: #fff; border-radius: 14px; box-shadow: 0 1px 5px rgba(0,0,0,0.07); margin-bottom: 16px; overflow: hidden; }
        .ks-card-hd { padding: 14px 20px; border-bottom: 1px solid #F3F4F6; display: flex; align-items: center; gap: 10px; }
        .ks-card-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .ks-card-title { font-size: 0.8rem; font-weight: 800; color: #1A1A2E; text-transform: uppercase; letter-spacing: 0.06em; }
        .ks-card-bd { padding: 18px 20px; }

        /* ── STATUS TUGAS ── */
        .ks-task-bar { display: flex; align-items: center; padding: 18px 20px; gap: 0; }
        .ks-task-step { display: flex; flex-direction: column; align-items: center; gap: 6px; flex: 1; }
        .ks-task-dot { width: 38px; height: 38px; border-radius: 50%; background: #F3F4F6; border: 3px solid #E5E7EB;
                       display: flex; align-items: center; justify-content: center; font-size: 0.9rem; transition: all 0.3s; position: relative; z-index: 2; }
        .ks-task-step.done .ks-task-dot   { background: #059669; border-color: #059669; color: #fff; }
        .ks-task-step.active .ks-task-dot { background: #0284C7; border-color: #0284C7; color: #fff;
                                            box-shadow: 0 0 0 5px rgba(2,132,199,0.18); }
        .ks-task-label { font-size: 0.68rem; font-weight: 700; color: #9CA3AF; text-align: center; }
        .ks-task-step.done .ks-task-label { color: #059669; }
        .ks-task-step.active .ks-task-label { color: #0284C7; font-weight: 800; }
        .ks-task-line { flex: 1; height: 3px; background: #E5E7EB; margin-top: -18px; border-radius: 2px; }
        .ks-task-line.done { background: #059669; }

        /* ── ESTIMASI ── */
        .ks-estimasi { border-radius: 12px; padding: 16px 20px; margin: 0 20px 16px; color: #fff; }
        .ks-est-waiting  { background: linear-gradient(135deg, #F97316, #EA580C); }
        .ks-est-onway    { background: linear-gradient(135deg, #0284C7, #0369A1); }
        .ks-est-done     { background: linear-gradient(135deg, #059669, #047857); }
        .ks-est-title { font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; opacity: 0.85; margin-bottom: 4px; }
        .ks-est-main  { font-size: 1.05rem; font-weight: 800; }
        .ks-est-sub   { font-size: 0.75rem; opacity: 0.8; margin-top: 3px; }

        /* ── INFO ROWS ── */
        .ks-info-row { display: flex; justify-content: space-between; padding: 9px 0;
                       border-bottom: 1px solid #F9FAFB; font-size: 0.825rem; }
        .ks-info-row:last-child { border-bottom: none; }
        .ks-info-label { color: #9CA3AF; font-weight: 500; }
        .ks-info-val { font-weight: 700; color: #1A1A2E; text-align: right; }

        /* ── UPLOAD ZONE ── */
        .ks-upload-zone { border: 2.5px dashed #C7D2FE; border-radius: 12px; padding: 26px 20px; text-align: center;
                          cursor: pointer; background: #FAFBFF; position: relative; transition: all 0.2s; }
        .ks-upload-zone:hover { border-color: #4F46E5; background: #F0F1FF; }
        .ks-upload-zone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
        .ks-upload-icon { font-size: 2rem; margin-bottom: 6px; }
        .ks-upload-text { font-size: 0.875rem; font-weight: 700; color: #4F46E5; }
        .ks-upload-sub  { font-size: 0.72rem; color: #9CA3AF; margin-top: 3px; }
        .ks-preview { display: none; margin-top: 12px; position: relative; }
        .ks-preview img { width: 100%; max-height: 240px; object-fit: cover; border-radius: 10px; border: 2px solid #E0E7FF; }

        .ks-notes-input { width: 100%; border: 1.5px solid #E5E7EB; border-radius: 8px; padding: 10px 12px;
                          font-family: inherit; font-size: 0.875rem; resize: vertical; min-height: 70px;
                          outline: none; box-sizing: border-box; margin-top: 10px; }
        .ks-notes-input:focus { border-color: #4F46E5; }

        .ks-submit-btn { width: 100%; padding: 14px; border: none; border-radius: 12px; font-size: 0.9rem; font-weight: 800;
                         cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
                         margin-top: 14px; transition: all 0.15s; }
        .btn-pickup    { background: linear-gradient(135deg, #F97316, #EA580C); color: #fff; }
        .btn-pickup:hover  { opacity: 0.9; transform: translateY(-1px); }
        .btn-delivered { background: linear-gradient(135deg, #059669, #047857); color: #fff; }
        .btn-delivered:hover { opacity: 0.9; transform: translateY(-1px); }

        /* ── FOTO BUKTI ── */
        .ks-proof-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .ks-proof-item { border-radius: 10px; overflow: hidden; border: 1.5px solid #E5E7EB; }
        .ks-proof-img { width: 100%; height: 180px; object-fit: cover; display: block; cursor: zoom-in; transition: opacity 0.15s; }
        .ks-proof-img:hover { opacity: 0.9; }
        .ks-proof-meta { padding: 10px 12px; background: #FAFAFA; }
        .ks-proof-type-pickup { font-size: 0.7rem; font-weight: 800; color: #C2410C; text-transform: uppercase; }
        .ks-proof-type-del    { font-size: 0.7rem; font-weight: 800; color: #15803D; text-transform: uppercase; }
        .ks-proof-time  { font-size: 0.7rem; color: #9CA3AF; margin-top: 2px; }
        .ks-proof-notes { font-size: 0.72rem; color: #555; margin-top: 3px; font-style: italic; }

        /* ── ADDRESS BOX ── */
        .ks-addr-box { background: #F8F9FE; border-radius: 10px; padding: 14px 16px;
                       font-size: 0.875rem; color: #444; line-height: 1.7; }
        .ks-addr-box strong { font-size: 0.95rem; color: #1A1A2E; display: block; margin-bottom: 2px; }

        /* ── PICKUP PREVIEW INLINE ── */
        .ks-prev-pickup { margin-bottom: 14px; padding: 10px 12px; background: #F0FDF4; border-radius: 8px;
                          border: 1.5px solid #86EFAC; }
        .ks-prev-pickup-label { font-size: 0.7rem; font-weight: 800; color: #15803D; margin-bottom: 6px; }
        .ks-prev-pickup img { width: 100%; max-height: 130px; object-fit: cover; border-radius: 6px; }

        /* Lightbox */
        .ks-lightbox { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.88); z-index: 9999;
                       align-items: center; justify-content: center; }
        .ks-lightbox.active { display: flex; }
        .ks-lightbox img { max-width: 90vw; max-height: 88vh; border-radius: 8px; object-fit: contain; }
        .ks-lb-close { position: absolute; top: 20px; right: 24px; color: #fff; font-size: 2rem; cursor: pointer;
                       background: none; border: none; line-height: 1; }

        @media(max-width: 600px) {
            .ks-proof-grid { grid-template-columns: 1fr; }
        }
    </style>

    @php
        $taskRank = ['assigned'=>1, 'picked_up'=>2, 'delivered'=>3];
        $curTask  = $taskRank[$order->courier_task_status ?? ''] ?? 0;

        // Estimasi — menggunakan data yang disimpan di database
        $etaText   = $order->shipping_eta ?? '2–4 hari kerja';
        $distText  = $order->shipping_distance ? $order->shipping_distance . ' KM' : '-';

        $estimasiInfo = null;
        $estimasiClass = 'ks-est-waiting';

        if ($order->courier_task_status === 'assigned') {
            $estimasiInfo = [
                'title' => '⏳ Langkah Berikutnya',
                'main'  => 'Ambil paket dari toko / gudang',
                'sub'   => 'Estimasi tiba: ' . $etaText . ' · Jarak: ' . $distText,
            ];
            $estimasiClass = 'ks-est-waiting';
        } elseif ($order->courier_task_status === 'picked_up' && $order->picked_up_at) {
            $estimasiInfo = [
                'title' => '🚚 Paket Dalam Perjalanan',
                'main'  => 'Sedang menuju alamat customer',
                'sub'   => 'Estimasi tiba: ' . $etaText . ' · Jarak: ' . $distText,
            ];
            $estimasiClass = 'ks-est-onway';
        } elseif ($order->courier_task_status === 'delivered') {
            $estimasiInfo = [
                'title' => '✅ Pengiriman Selesai',
                'main'  => 'Paket sudah diterima penerima',
                'sub'   => 'Terkirim: ' . ($order->delivered_at ? $order->delivered_at->format('d M Y, H:i') . ' WIB' : '-'),
            ];
            $estimasiClass = 'ks-est-done';
        }
    @endphp

    <div class="ks-wrap">
        <div class="ks-container">

            {{-- ALERTS --}}
            @if(session('success'))
            <div style="margin-bottom:16px;background:#F0FDF4;border-left:4px solid #22C55E;padding:12px 16px;border-radius:10px;font-size:0.825rem;font-weight:700;color:#15803D;">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div style="margin-bottom:16px;background:#FFF1F2;border-left:4px solid #F43F5E;padding:12px 16px;border-radius:10px;font-size:0.825rem;font-weight:700;color:#BE123C;">
                {{ session('error') }}
            </div>
            @endif

            {{-- ══ PROGRESS STEPS ══ --}}
            <div class="ks-card">
                <div class="ks-task-bar">
                    {{-- Step 1 --}}
                    <div class="ks-task-step {{ $curTask >= 1 ? 'done' : '' }}">
                        <div class="ks-task-dot">
                            @if($curTask >= 1) <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#fff" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @else 📋 @endif
                        </div>
                        <div class="ks-task-label">Ditugaskan</div>
                    </div>
                    <div class="ks-task-line {{ $curTask >= 2 ? 'done' : '' }}"></div>
                    {{-- Step 2 --}}
                    <div class="ks-task-step {{ $curTask >= 2 ? 'done' : ($curTask >= 1 ? 'active' : '') }}">
                        <div class="ks-task-dot">
                            @if($curTask >= 2) <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#fff" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @else 📦 @endif
                        </div>
                        <div class="ks-task-label">Paket Diambil</div>
                    </div>
                    <div class="ks-task-line {{ $curTask >= 3 ? 'done' : '' }}"></div>
                    {{-- Step 3 --}}
                    <div class="ks-task-step {{ $curTask >= 3 ? 'done' : ($curTask >= 2 ? 'active' : '') }}">
                        <div class="ks-task-dot">
                            @if($curTask >= 3) <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#fff" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @else 🚚 @endif
                        </div>
                        <div class="ks-task-label">Dalam Perjalanan</div>
                    </div>
                    <div class="ks-task-line {{ $curTask >= 3 ? 'done' : '' }}"></div>
                    {{-- Step 4 --}}
                    <div class="ks-task-step {{ $curTask >= 3 ? 'done' : '' }}">
                        <div class="ks-task-dot">
                            @if($curTask >= 3) <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#fff" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @else ✅ @endif
                        </div>
                        <div class="ks-task-label">Terkirim</div>
                    </div>
                </div>

                {{-- Estimasi Banner --}}
                @if($estimasiInfo)
                <div class="ks-estimasi {{ $estimasiClass }}">
                    <div class="ks-est-title">{{ $estimasiInfo['title'] }}</div>
                    <div class="ks-est-main">{{ $estimasiInfo['main'] }}</div>
                    <div class="ks-est-sub">{{ $estimasiInfo['sub'] }}</div>
                </div>
                @endif
            </div>

            {{-- ══ ALAMAT (PALING PENTING UNTUK KURIR) ══ --}}
            <div class="ks-card">
                <div class="ks-card-hd">
                    <div class="ks-card-icon" style="background:#FFF1F2;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#E11D48">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="ks-card-title">📍 Alamat Tujuan</span>
                </div>
                <div class="ks-card-bd">
                    <div class="ks-addr-box">
                        <strong>{{ $order->recipient_name }} &nbsp;·&nbsp; {{ $order->recipient_phone }}</strong>
                        {{ $order->address_detail ?? $order->shipping_address }},
                        Kel. {{ $order->village ?? '' }}, Kec. {{ $order->district ?? '-' }}<br>
                        {{ $order->city ?? '-' }}, {{ $order->province ?? '-' }} {{ $order->postal_code ?? '' }}
                    </div>
                    @if($order->notes ?? false)
                    <div style="margin-top:10px;padding:10px 14px;background:#FFFBEB;border-radius:8px;font-size:0.825rem;color:#92400E;">
                        📝 <strong>Catatan:</strong> {{ $order->notes }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- ══ NOMOR RESI (prominent jika sudah ada) ══ --}}
            @if($order->tracking_number)
            <div style="background:linear-gradient(135deg,#7C3AED,#5B21B6);border-radius:14px;padding:18px 20px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div>
                    <div style="font-size:0.68rem;font-weight:800;color:rgba(255,255,255,0.65);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:4px;">📦 Nomor Resi Pengiriman</div>
                    <div style="font-size:1.15rem;font-weight:900;color:#fff;font-family:monospace;letter-spacing:2px;">{{ $order->tracking_number }}</div>
                    @if($order->courier_name)
                    <div style="font-size:0.72rem;color:rgba(255,255,255,0.7);margin-top:3px;">{{ strtoupper($order->courier_name) }} · {{ $order->shipped_at ? $order->shipped_at->format('d M Y') : $order->created_at->format('d M Y') }}</div>
                    @endif
                </div>
                <button onclick="navigator.clipboard.writeText('{{ $order->tracking_number }}');this.textContent='✅ Disalin!';setTimeout(()=>this.textContent='📋 Salin Resi',2000)"
                        style="padding:8px 18px;background:rgba(255,255,255,0.18);color:#fff;border:1.5px solid rgba(255,255,255,0.3);border-radius:8px;font-size:0.8rem;font-weight:800;cursor:pointer;white-space:nowrap;transition:background 0.15s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.28)'" onmouseout="this.style.background='rgba(255,255,255,0.18)'">
                    📋 Salin Resi
                </button>
            </div>
            @endif

            {{-- ══ INFO PESANAN ══ --}}
            <div class="ks-card">
                <div class="ks-card-hd">
                    <div class="ks-card-icon" style="background:#EEF2FF;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#4F46E5">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="ks-card-title">Info Pesanan</span>
                </div>
                <div style="padding:0 20px;">
                    <div class="ks-info-row">
                        <span class="ks-info-label">No. Pesanan</span>
                        <span class="ks-info-val">#{{ $order->order_number }}</span>
                    </div>
                    <div class="ks-info-row">
                        <span class="ks-info-label">Tanggal</span>
                        <span class="ks-info-val">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                    </div>
                    <div class="ks-info-row">
                        <span class="ks-info-label">Metode Bayar</span>
                        <span class="ks-info-val" style="text-transform:uppercase;">{{ $order->payment_method ?? '-' }}</span>
                    </div>
                    <div class="ks-info-row">
                        <span class="ks-info-label">Layanan Kurir</span>
                        <span class="ks-info-val" style="text-transform:uppercase;">{{ $order->courier_name ?? '-' }}</span>
                    </div>
                    @if($order->tracking_number)
                    <div class="ks-info-row">
                        <span class="ks-info-label">No. Resi</span>
                        <span class="ks-info-val" style="color:#7C3AED;font-family:monospace;font-size:0.8rem;">{{ $order->tracking_number }}</span>
                    </div>
                    @endif
                    @if($order->picked_up_at)
                    <div class="ks-info-row">
                        <span class="ks-info-label">Waktu Pickup</span>
                        <span class="ks-info-val">{{ $order->picked_up_at->format('d M Y, H:i') }} WIB</span>
                    </div>
                    @endif
                    @if($order->delivered_at)
                    <div class="ks-info-row">
                        <span class="ks-info-label">Waktu Terkirim</span>
                        <span class="ks-info-val" style="color:#15803D;">{{ $order->delivered_at->format('d M Y, H:i') }} WIB</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ══ [1] FORM KONFIRMASI PICKUP ══ --}}
            @if($order->courier_task_status === 'assigned')
            <div class="ks-card">
                <div class="ks-card-hd" style="background:#FFF7ED;">
                    <div class="ks-card-icon" style="background:#FED7AA;">
                        <span style="font-size:0.9rem;">📦</span>
                    </div>
                    <span class="ks-card-title" style="color:#C2410C;">Konfirmasi Ambil Paket</span>
                </div>
                <div class="ks-card-bd">
                    <p style="font-size:0.825rem;color:#555;margin-bottom:16px;line-height:1.6;">
                        Upload foto paket saat Anda mengambil dari toko/gudang. Pastikan foto menampilkan label pengiriman dengan jelas.
                    </p>

                    <form action="{{ route('kurir.orders.confirm-pickup', $order->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="ks-upload-zone" id="pickupZone">
                            <input type="file" name="photo" accept="image/*" capture="environment" required
                                   onchange="previewPhoto(this, 'pickupPreview', 'pickupZoneInner')">
                            <div id="pickupZoneInner">
                                <div class="ks-upload-icon">📷</div>
                                <div class="ks-upload-text">Tap untuk Foto / Pilih Gambar</div>
                                <div class="ks-upload-sub">JPG, PNG, WEBP · Maks 5MB</div>
                            </div>
                            <div class="ks-preview" id="pickupPreview">
                                <img src="" id="pickupPreviewImg" alt="Preview">
                            </div>
                        </div>
                        @error('photo') <p style="color:#DC2626;font-size:0.75rem;margin-top:6px;">{{ $message }}</p> @enderror

                        <textarea name="notes" class="ks-notes-input" placeholder="Catatan opsional (misal: paket di-wrap ulang, kondisi barang...)"></textarea>


                        <button type="submit" class="ks-submit-btn btn-pickup"
                                onclick="return confirm('Konfirmasi sudah ambil paket dari toko?')">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            ✅ Konfirmasi Pengambilan Paket
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- ══ [2] FORM KONFIRMASI DELIVERED ══ --}}
            @if($order->courier_task_status === 'picked_up')
            <div class="ks-card">
                <div class="ks-card-hd" style="background:#F0FDF4;">
                    <div class="ks-card-icon" style="background:#A7F3D0;">
                        <span style="font-size:0.9rem;">🤝</span>
                    </div>
                    <span class="ks-card-title" style="color:#065F46;">Konfirmasi Paket Diterima</span>
                </div>
                <div class="ks-card-bd">

                    {{-- Tampilkan foto pickup sebelumnya --}}
                    @if($pickUpProof)
                    <div class="ks-prev-pickup">
                        <div class="ks-prev-pickup-label">✅ Foto Pickup Sudah Ada</div>
                        <img src="{{ asset('storage/' . $pickUpProof->photo_path) }}" alt="Foto Pickup">
                        <div style="font-size:0.7rem;color:#16A34A;margin-top:4px;">{{ $pickUpProof->created_at->format('d M Y, H:i') }} WIB</div>
                    </div>
                    @endif

                    <p style="font-size:0.825rem;color:#555;margin-bottom:16px;line-height:1.6;">
                        Upload foto serah terima paket kepada penerima. Foto harus jelas menampilkan paket dan/atau penerima.
                    </p>

                    <form action="{{ route('kurir.orders.confirm-delivered', $order->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="ks-upload-zone">
                            <input type="file" name="photo" accept="image/*" capture="environment" required
                                   onchange="previewPhoto(this, 'deliveredPreview', 'deliveredZoneInner')">
                            <div id="deliveredZoneInner">
                                <div class="ks-upload-icon">🤝</div>
                                <div class="ks-upload-text">Foto Serah Terima ke Penerima</div>
                                <div class="ks-upload-sub">JPG, PNG, WEBP · Maks 5MB</div>
                            </div>
                            <div class="ks-preview" id="deliveredPreview">
                                <img src="" id="deliveredPreviewImg" alt="Preview">
                            </div>
                        </div>
                        @error('photo') <p style="color:#DC2626;font-size:0.75rem;margin-top:6px;">{{ $message }}</p> @enderror

                        <textarea name="notes" class="ks-notes-input" placeholder="Catatan opsional (misal: diterima satpam, penerima tidak di rumah...)"></textarea>


                        <button type="submit" class="ks-submit-btn btn-delivered"
                                onclick="return confirm('Konfirmasi paket sudah diserahkan ke penerima?')">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            🎉 Konfirmasi Paket Sudah Diterima
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- ══ [3] FOTO BUKTI (jika sudah delivered) ══ --}}
            @if($order->courier_task_status === 'delivered')
            <div class="ks-card">
                <div class="ks-card-hd">
                    <div class="ks-card-icon" style="background:#F0FDF4;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#15803D">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="ks-card-title">📸 Foto Bukti Pengiriman</span>
                </div>
                <div class="ks-card-bd">
                    <div class="ks-proof-grid">
                        @if($pickUpProof)
                        <div class="ks-proof-item">
                            <img src="{{ asset('storage/' . $pickUpProof->photo_path) }}" class="ks-proof-img"
                                 onclick="openLb(this.src)" alt="Foto Pickup">
                            <div class="ks-proof-meta">
                                <div class="ks-proof-type-pickup">📦 Pengambilan Paket</div>
                                <div class="ks-proof-time">{{ $pickUpProof->created_at->format('d M Y, H:i') }} WIB</div>
                                @if($pickUpProof->notes)<div class="ks-proof-notes">{{ $pickUpProof->notes }}</div>@endif
                            </div>
                        </div>
                        @endif
                        @if($deliveredProof)
                        <div class="ks-proof-item">
                            <img src="{{ asset('storage/' . $deliveredProof->photo_path) }}" class="ks-proof-img"
                                 onclick="openLb(this.src)" alt="Foto Delivered">
                            <div class="ks-proof-meta">
                                <div class="ks-proof-type-del">✅ Serah Terima</div>
                                <div class="ks-proof-time">{{ $deliveredProof->created_at->format('d M Y, H:i') }} WIB</div>
                                @if($deliveredProof->notes)<div class="ks-proof-notes">{{ $deliveredProof->notes }}</div>@endif
                            </div>
                        </div>
                        @endif
                    </div>

                    <div style="margin-top:14px;padding:12px 16px;background:#F0FDF4;border-radius:10px;font-size:0.825rem;font-weight:700;color:#15803D;display:flex;align-items:center;gap:8px;">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pengiriman selesai! Customer akan mengkonfirmasi penerimaan.
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- Lightbox --}}
    <div class="ks-lightbox" id="lb" onclick="closeLb()">
        <button class="ks-lb-close" onclick="closeLb()">✕</button>
        <img src="" id="lbImg" alt="Foto Bukti">
    </div>

    <script>
        function previewPhoto(input, previewId, contentId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    const preview = document.getElementById(previewId);
                    const img = preview.querySelector('img');
                    img.src = e.target.result;
                    preview.style.display = 'block';
                    document.getElementById(contentId).style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        function openLb(src) {
            document.getElementById('lbImg').src = src;
            document.getElementById('lb').classList.add('active');
        }
        function closeLb() {
            document.getElementById('lb').classList.remove('active');
        }
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLb(); });
    </script>
</x-app-layout>