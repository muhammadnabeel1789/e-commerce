<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('customer.orders.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800">
                Detail Pesanan <span class="text-indigo-600">#{{ $order->order_number }}</span>
            </h2>
        </div>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap');
        .od-wrap * { font-family: 'DM Sans', sans-serif; }
        .od-wrap { background: #F1F3F7; min-height: 100vh; padding: 24px 0 60px; }
        .od-container { max-width: 1100px; margin: 0 auto; padding: 0 24px; }
        .od-layout { display: grid; grid-template-columns: 1fr 320px; gap: 20px; align-items: start; }
        .od-left { display: flex; flex-direction: column; gap: 18px; }
        .od-right { display: flex; flex-direction: column; gap: 18px; }

        .od-card { background: #fff; border-radius: 14px; box-shadow: 0 1px 4px rgba(0,0,0,0.07); overflow: hidden; }
        .od-card-hd { padding: 14px 20px; border-bottom: 1px solid #F3F4F6; display: flex; align-items: center; gap: 10px; }
        .od-card-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .od-card-title { font-size: 0.8rem; font-weight: 800; color: #1A1A2E; text-transform: uppercase; letter-spacing: 0.05em; }
        .od-card-bd { padding: 18px 20px; }

        /* ── PROGRESS STEPS ── */
        .od-steps { display: flex; align-items: center; padding: 20px 24px; overflow-x: auto; }
        .od-step { display: flex; flex-direction: column; align-items: center; gap: 8px; flex: 1; min-width: 70px; }
        .od-step-dot { width: 36px; height: 36px; border-radius: 50%; border: 3px solid #E5E7EB; background: #F9FAFB;
                       display: flex; align-items: center; justify-content: center; font-size: 0.85rem; transition: all 0.3s; }
        .od-step.done .od-step-dot   { background: #4F46E5; border-color: #4F46E5; }
        .od-step.active .od-step-dot { background: #4F46E5; border-color: #4F46E5; box-shadow: 0 0 0 5px rgba(79,70,229,0.15); }
        .od-step-line { flex: 1; height: 3px; background: #E5E7EB; margin-top: -18px; border-radius: 2px; transition: background 0.3s; }
        .od-step-line.done { background: #4F46E5; }
        .od-step-label { font-size: 0.68rem; font-weight: 700; color: #9CA3AF; text-align: center; }
        .od-step.done .od-step-label { color: #4F46E5; }
        .od-step.active .od-step-label { color: #4F46E5; font-weight: 800; }

        /* STATUS PILL */
        .od-pill { display: inline-flex; align-items: center; gap: 5px; padding: 5px 14px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; }
        .p-pending    { background:#FFF7ED; color:#C2410C; border:1.5px solid #FED7AA; }
        .p-paid       { background:#EFF6FF; color:#1D4ED8; border:1.5px solid #BFDBFE; }
        .p-processing { background:#F5F3FF; color:#6D28D9; border:1.5px solid #DDD6FE; }
        .p-shipped    { background:#ECFDF5; color:#065F46; border:1.5px solid #A7F3D0; }
        .p-completed  { background:#F0FDF4; color:#14532D; border:1.5px solid #86EFAC; }
        .p-cancelled  { background:#FFF1F2; color:#BE123C; border:1.5px solid #FECDD3; }

        /* INFO ROWS */
        .od-info-row { display: flex; justify-content: space-between; padding: 9px 0; border-bottom: 1px solid #F9FAFB; font-size: 0.825rem; }
        .od-info-row:last-child { border-bottom: none; }
        .od-info-label { color: #9CA3AF; font-weight: 500; }
        .od-info-val { font-weight: 700; color: #1A1A2E; text-align: right; }

        /* ITEM TABLE */
        .od-item-row { display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid #F9FAFB; }
        .od-item-row:last-child { border-bottom: none; }
        .od-item-img { width: 50px; height: 50px; border-radius: 8px; object-fit: cover; flex-shrink: 0; border: 1px solid #F0F0F0; }
        .od-item-ph  { width: 50px; height: 50px; border-radius: 8px; background: #F5F5F5; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #CCC; }
        .od-item-name  { font-size: 0.875rem; font-weight: 700; color: #1A1A2E; }
        .od-item-var   { font-size: 0.72rem; color: #9CA3AF; margin-top: 2px; }
        .od-item-price { font-size: 0.875rem; font-weight: 800; color: #4F46E5; margin-left: auto; white-space: nowrap; }

        /* TOMBOL ULASAN */
        .od-review-btn { display: inline-flex; align-items: center; gap: 5px; margin-top: 6px; padding: 5px 12px;
                         background: #FFF7ED; color: #EA580C; border: 1.5px solid #FED7AA; border-radius: 8px;
                         font-size: 0.72rem; font-weight: 700; text-decoration: none; transition: all 0.15s; }
        .od-review-btn:hover { background: #EA580C; color: #fff; border-color: #EA580C; }
        .od-reviewed-badge { display: inline-flex; align-items: center; gap: 4px; margin-top: 6px; padding: 4px 10px;
                             background: #F0FDF4; color: #15803D; border-radius: 8px; font-size: 0.72rem; font-weight: 700; }

        /* TOTALS */
        .od-totals { padding: 14px 20px; background: #F9FAFB; border-top: 1px dashed #E5E7EB; }
        .od-total-row { display: flex; justify-content: space-between; font-size: 0.825rem; padding: 4px 0; }
        .od-total-label { color: #9CA3AF; }
        .od-total-val   { font-weight: 600; color: #374151; }
        .od-grand-total { display: flex; justify-content: space-between; padding: 10px 0 0; border-top: 1px solid #E5E7EB; margin-top: 6px; }
        .od-grand-label { font-size: 0.9rem; font-weight: 800; color: #1A1A2E; }
        .od-grand-val   { font-size: 1rem; font-weight: 800; color: #4F46E5; }

        /* KURIR STATUS */
        .od-kurir-steps { display: flex; gap: 0; align-items: center; padding: 14px 20px 0; }
        .od-ks { display: flex; flex-direction: column; align-items: center; gap: 5px; flex: 1; }
        .od-ks-dot { width: 28px; height: 28px; border-radius: 50%; border: 2.5px solid #E5E7EB; background: #F9FAFB;
                     display: flex; align-items: center; justify-content: center; font-size: 0.75rem; }
        .od-ks.done .od-ks-dot { background: #059669; border-color: #059669; }
        .od-ks.active .od-ks-dot { background: #0284C7; border-color: #0284C7; box-shadow: 0 0 0 4px rgba(2,132,199,0.15); }
        .od-ks-line { flex: 1; height: 2px; background: #E5E7EB; margin-top: -13px; }
        .od-ks.done + .od-ks-line { background: #059669; }
        .od-ks-label { font-size: 0.6rem; font-weight: 700; color: #9CA3AF; text-align: center; }
        .od-ks.done .od-ks-label { color: #059669; }
        .od-ks.active .od-ks-label { color: #0284C7; font-weight: 800; }

        /* TOMBOL AKSI */
        .od-btn-confirm { width: 100%; padding: 13px; background: linear-gradient(135deg, #10B981, #059669); color: #fff;
                          border: none; border-radius: 10px; font-size: 0.9rem; font-weight: 800; cursor: pointer; transition: opacity 0.15s; }
        .od-btn-confirm:hover { opacity: 0.9; }
        .od-btn-pay { width: 100%; padding: 13px; background: #4F46E5; color: #fff; border: none;
                      border-radius: 10px; font-size: 0.9rem; font-weight: 800; cursor: pointer; transition: background 0.15s; }
        .od-btn-pay:hover { background: #4338CA; }
        .od-btn-cancel { width: 100%; padding: 10px; background: #fff; color: #DC2626; border: 2px solid #FCA5A5;
                         border-radius: 10px; font-size: 0.85rem; font-weight: 700; cursor: pointer; transition: all 0.15s; }
        .od-btn-cancel:hover { background: #FFF1F2; }

        /* Resi box */
        .od-resi-box { background: #F5F3FF; border-radius: 10px; padding: 12px 16px; margin-top: 10px; }
        .od-resi-label { font-size: 0.68rem; font-weight: 700; color: #7C3AED; text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 4px; }
        .od-resi-num { font-size: 0.9rem; font-weight: 800; color: #5B21B6; font-family: monospace; }

        /* Modal */
        .od-modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .od-modal.active { display: flex; }
        .od-modal-box { background: #fff; border-radius: 16px; padding: 28px; width: 90%; max-width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
        .od-modal-title { font-size: 1rem; font-weight: 800; margin-bottom: 6px; }
        .od-modal-sub   { font-size: 0.8rem; color: #888; margin-bottom: 14px; }
        .od-modal-textarea { width: 100%; border: 1.5px solid #E5E7EB; border-radius: 8px; padding: 10px;
                             font-family: inherit; font-size: 0.875rem; resize: vertical; min-height: 90px; outline: none; box-sizing: border-box; }
        .od-modal-textarea:focus { border-color: #4F46E5; }
        .od-modal-actions { display: flex; gap: 10px; margin-top: 14px; }
        .od-modal-submit { flex: 1; padding: 11px; background: #DC2626; color: #fff; border: none; border-radius: 8px; font-weight: 800; cursor: pointer; }
        .od-modal-close  { flex: 1; padding: 11px; background: #F3F4F6; color: #555; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; }

        .od-alert { padding: 12px 16px; border-radius: 8px; font-size: 0.825rem; font-weight: 600; margin-bottom: 16px; }
        .od-alert-success { background: #F0FDF4; color: #15803D; border-left: 4px solid #22C55E; }
        .od-alert-error   { background: #FFF1F2; color: #BE123C; border-left: 4px solid #F43F5E; }
        .od-alert-info    { background: #EFF6FF; color: #1D4ED8; border-left: 4px solid #3B82F6; }

        @media(max-width: 900px) {
            .od-layout { grid-template-columns: 1fr; }
        }
    </style>

    @php
        $statusRank = ['pending'=>0,'paid'=>1,'processing'=>2,'shipped'=>3,'completed'=>4,'cancelled'=>-1];
        $curRank    = $statusRank[$order->status] ?? 0;

        $pillMap = [
            'pending'=>'p-pending','paid'=>'p-paid','processing'=>'p-processing',
            'shipped'=>'p-shipped','completed'=>'p-completed','cancelled'=>'p-cancelled',
        ];
        $statusLabel = [
            'pending'=>'Menunggu Bayar','paid'=>'Dibayar','processing'=>'Sedang Diproses',
            'shipped'=>'Sedang Dikirim','completed'=>'Selesai','cancelled'=>'Dibatalkan',
        ];

        $taskRank = ['assigned'=>1,'picked_up'=>2,'delivered'=>3];
        $curTask  = $taskRank[$order->courier_task_status ?? ''] ?? 0;

        $canConfirm      = $order->status === 'shipped' && $curTask >= 3;
        $canPay          = in_array($order->status, ['pending', 'unpaid']) && $order->payment_status != 'paid' && !empty($order->snap_token);
        $cancellable     = in_array($order->status, ['pending','paid','processing']) && $order->cancel_request_status !== 'pending';
        $isPaid          = in_array($order->status, ['paid','processing','shipped','completed']) || $order->payment_status === 'paid';
    @endphp
    
    <!-- DEV DEBUG:
    payment_status: {{ $order->payment_status }}
    status: {{ $order->status }}
    snap_token_exists: {{ !empty($order->snap_token) ? 'YES' : 'NO' }}
    canPay: {{ $canPay ? 'TRUE' : 'FALSE' }}
    -->

    <div class="od-wrap">
        <div class="od-container">

            {{-- HEADER ROW --}}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
                <div>
                    <div style="font-size:1.1rem;font-weight:800;color:#1A1A2E;">Pesanan #{{ $order->order_number }}</div>
                    <div style="font-size:0.78rem;color:#9CA3AF;margin-top:2px;">{{ $order->created_at->format('d M Y, H:i') }} WIB</div>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="od-pill {{ $pillMap[$order->status] ?? '' }}">{{ $statusLabel[$order->status] ?? ucfirst($order->status) }}</span>
                    @if(in_array($order->status, ['paid','processing','shipped','completed']))
                        <a href="{{ route('customer.orders.invoice', $order->id) }}" target="_blank"
                           style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;background:#4F46E5;color:#fff;text-decoration:none;border-radius:8px;font-size:0.78rem;font-weight:700;">
                            🖨️ Invoice
                        </a>
                    @endif
                </div>
            </div>

            {{-- ALERTS --}}
            @if(session('success'))
                <div class="od-alert od-alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="od-alert od-alert-error">{{ session('error') }}</div>
            @endif
            @if(session('info'))
                <div class="od-alert od-alert-info">{{ session('info') }}</div>
            @endif

            {{-- ══ PROGRESS BAR ══ --}}
            <div class="od-card" style="margin-bottom:18px;">
                @if($order->status === 'cancelled')
                    <div style="padding:20px 24px;display:flex;align-items:center;gap:10px;color:#BE123C;">
                        <span style="font-size:1.5rem;">❌</span>
                        <div>
                            <div style="font-weight:800;">Pesanan Dibatalkan</div>
                            <div style="font-size:0.78rem;opacity:0.8;">Pesanan ini telah dibatalkan.</div>
                        </div>
                    </div>
                @else
                    <div class="od-steps">
                        @php
                            $steps = [
                                ['key'=>'pending',    'emoji'=>'🕐','label'=>'Menunggu Bayar'],
                                ['key'=>'paid',       'emoji'=>'💳','label'=>'Dibayar'],
                                ['key'=>'processing', 'emoji'=>'📦','label'=>'Diproses'],
                                ['key'=>'shipped',    'emoji'=>'🚚','label'=>'Dikirim'],
                                ['key'=>'completed',  'emoji'=>'✅','label'=>'Selesai'],
                            ];
                            $stepRanks = ['pending'=>0,'paid'=>1,'processing'=>2,'shipped'=>3,'completed'=>4];
                        @endphp
                        @foreach($steps as $i => $step)
                            @if($i > 0)<div class="od-step-line {{ $curRank > $stepRanks[$step['key']]-1 ? 'done' : '' }}"></div>@endif
                            @php
                                $r = $stepRanks[$step['key']];
                                $cls = $curRank > $r ? 'done' : ($curRank === $r ? 'active' : '');
                            @endphp
                            <div class="od-step {{ $cls }}">
                                <div class="od-step-dot">
                                    @if($curRank > $r)
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#fff" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        {{ $step['emoji'] }}
                                    @endif
                                </div>
                                <div class="od-step-label">{{ $step['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="od-layout">

                {{-- ══ KOLOM KIRI ══ --}}
                <div class="od-left">

                    {{-- STATUS KURIR (jika ada) --}}
                    @if($order->courier_id)
                    <div class="od-card">
                        <div class="od-card-hd">
                            <div class="od-card-icon" style="background:#EFF6FF;">🚚</div>
                            <span class="od-card-title">Status Pengiriman Kurir</span>
                            @php
                                $taskLabels = ['assigned'=>'Menuju Toko','picked_up'=>'Dalam Perjalanan','delivered'=>'Sudah Tiba'];
                                $taskPills  = ['assigned'=>'background:#FFF7ED;color:#C2410C;','picked_up'=>'background:#EFF6FF;color:#0369A1;','delivered'=>'background:#F0FDF4;color:#15803D;'];
                            @endphp
                            @if($order->courier_task_status)
                                <span style="margin-left:auto;font-size:0.72rem;font-weight:800;padding:4px 12px;border-radius:100px;{{ $taskPills[$order->courier_task_status] ?? '' }}">
                                    {{ $taskLabels[$order->courier_task_status] ?? '' }}
                                </span>
                            @endif
                        </div>

                        {{-- Steps kurir --}}
                        <div class="od-kurir-steps">
                            @php
                                $ks = [
                                    ['emoji'=>'📋','label'=>'Ditugaskan','rank'=>1],
                                    ['emoji'=>'📦','label'=>'Paket Diambil','rank'=>2],
                                    ['emoji'=>'🚚','label'=>'Di Jalan','rank'=>3],
                                    ['emoji'=>'✅','label'=>'Sudah Tiba','rank'=>3],
                                ];
                            @endphp
                            @foreach($ks as $i => $k)
                                @if($i > 0)<div class="od-ks-line {{ $curTask >= $k['rank'] ? 'done' : '' }}"></div>@endif
                                @php $kCls = $curTask >= $k['rank'] ? 'done' : ($curTask === $k['rank']-1 ? 'active' : ''); @endphp
                                <div class="od-ks {{ $kCls }}">
                                    <div class="od-ks-dot">
                                        @if($curTask >= $k['rank'])
                                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="#fff" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                            {{ $k['emoji'] }}
                                        @endif
                                    </div>
                                    <div class="od-ks-label">{{ $k['label'] }}</div>
                                </div>
                            @endforeach
                        </div>

                        <div style="padding:14px 20px;">
                            @if($order->courier_task_status === 'assigned')
                                <div style="background:#FFF7ED;border-radius:8px;padding:10px 14px;font-size:0.8rem;color:#C2410C;font-weight:600;">
                                    📋 Kurir kami sedang menuju toko untuk mengambil paket Anda.
                                </div>
                            @elseif($order->courier_task_status === 'picked_up')
                                <div style="background:#EFF6FF;border-radius:8px;padding:10px 14px;font-size:0.8rem;color:#0369A1;font-weight:600;">
                                    🚚 Paket sedang dalam perjalanan menuju alamat Anda.
                                    @if($order->picked_up_at)
                                        <div style="font-size:0.72rem;margin-top:3px;opacity:0.85;">Diambil pukul {{ $order->picked_up_at->format('H:i') }} WIB pada {{ $order->picked_up_at->format('d M Y') }}</div>
                                    @endif
                                    @if($order->tracking_number)
                                        <div style="margin-top:8px;display:flex;align-items:center;justify-content:space-between;background:rgba(255,255,255,0.6);border-radius:6px;padding:6px 10px;">
                                            <span style="font-size:0.72rem;font-weight:700;color:#0369A1;">Resi: <span style="font-family:monospace;font-size:0.8rem;font-weight:900;color:#1D4ED8;">{{ $order->tracking_number }}</span></span>
                                            <button onclick="navigator.clipboard.writeText('{{ $order->tracking_number }}');this.textContent='✅';setTimeout(()=>this.textContent='📋',1500)"
                                                    style="background:#1D4ED8;color:#fff;border:none;padding:3px 8px;border-radius:5px;font-size:0.7rem;font-weight:700;cursor:pointer;">📋</button>
                                        </div>
                                    @endif
                                </div>
                            @elseif($order->courier_task_status === 'delivered')
                                <div style="background:#F0FDF4;border-radius:8px;padding:10px 14px;font-size:0.8rem;color:#15803D;font-weight:600;">
                                    ✅ Kurir sudah mengantarkan paket. Silakan cek dan konfirmasi penerimaan.
                                    @if($order->delivered_at)
                                        <div style="font-size:0.72rem;margin-top:3px;opacity:0.85;">Diantar pukul {{ $order->delivered_at->format('H:i') }} WIB pada {{ $order->delivered_at->format('d M Y') }}</div>
                                    @endif
                                </div>

                                {{-- ══ BUKTI FOTO PENGIRIMAN KURIR ══ --}}
                                @php
                                    $deliveryProofs = $order->deliveryProofs ?? collect();
                                    $proofDelivered = $deliveryProofs->where('type', 'delivered')->last();
                                    $proofPickUp    = $deliveryProofs->where('type', 'pick_up')->last();
                                @endphp

                                @if($proofDelivered)
                                <div style="margin-top:14px;background:#F8FFFE;border:1.5px solid #A7F3D0;border-radius:12px;overflow:hidden;">
                                    
                                    {{-- Header bukti --}}
                                    <div style="padding:12px 16px;border-bottom:1px solid #D1FAE5;display:flex;align-items:center;gap:10px;">
                                        <div style="width:32px;height:32px;border-radius:8px;background:#D1FAE5;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">📸</div>
                                        <div>
                                            <div style="font-size:0.78rem;font-weight:800;color:#065F46;text-transform:uppercase;letter-spacing:0.05em;">Bukti Pengiriman</div>
                                            <div style="font-size:0.68rem;color:#6EE7B7;">Foto resmi dari kurir kami</div>
                                        </div>
                                    </div>

                                    {{-- Info kurir --}}
                                    @if($order->courier)
                                    <div style="padding:10px 16px;display:flex;align-items:center;gap:10px;border-bottom:1px solid #D1FAE5;">
                                        <div style="width:32px;height:32px;border-radius:50%;background:#059669;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:0.85rem;flex-shrink:0;">
                                            {{ strtoupper(substr($order->courier->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-size:0.8rem;font-weight:700;color:#1A1A2E;">{{ $order->courier->name }}</div>
                                            <div style="font-size:0.68rem;color:#9CA3AF;">Kurir Pengantar</div>
                                        </div>
                                        <div style="margin-left:auto;text-align:right;">
                                            <div style="font-size:0.7rem;font-weight:700;color:#059669;">✅ Terkirim</div>
                                            @if($order->delivered_at)
                                            <div style="font-size:0.65rem;color:#9CA3AF;">{{ $order->delivered_at->format('d M Y, H:i') }} WIB</div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Foto bukti serah terima --}}
                                    <div style="padding:14px 16px;">
                                        <div style="font-size:0.7rem;font-weight:700;color:#065F46;text-transform:uppercase;margin-bottom:8px;">🤝 Foto Serah Terima Paket</div>
                                        <a href="{{ asset('storage/' . $proofDelivered->photo_path) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $proofDelivered->photo_path) }}"
                                                 style="width:100%;border-radius:10px;object-fit:cover;max-height:220px;border:2px solid #A7F3D0;cursor:zoom-in;transition:opacity 0.15s;"
                                                 onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'"
                                                 alt="Bukti pengiriman">
                                        </a>
                                        @if($proofDelivered->notes)
                                        <div style="margin-top:8px;background:#ECFDF5;border-radius:8px;padding:8px 12px;font-size:0.78rem;color:#065F46;">
                                            💬 <em>{{ $proofDelivered->notes }}</em>
                                        </div>
                                        @endif
                                        <div style="margin-top:6px;font-size:0.68rem;color:#9CA3AF;">
                                            Difoto: {{ $proofDelivered->created_at->format('d M Y, H:i') }} WIB · Klik foto untuk memperbesar
                                        </div>
                                    </div>

                                    {{-- Foto pickup (opsional, bisa di-collapse) --}}
                                    @if($proofPickUp)
                                    <div style="padding:0 16px 14px;">
                                        <div style="font-size:0.7rem;font-weight:700;color:#92400E;text-transform:uppercase;margin-bottom:8px;">📦 Foto Pengambilan dari Toko</div>
                                        <a href="{{ asset('storage/' . $proofPickUp->photo_path) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $proofPickUp->photo_path) }}"
                                                 style="width:100%;border-radius:10px;object-fit:cover;max-height:160px;border:2px solid #FDE68A;cursor:zoom-in;transition:opacity 0.15s;"
                                                 onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'"
                                                 alt="Foto pickup">
                                        </a>
                                        @if($proofPickUp->notes)
                                        <div style="margin-top:6px;font-size:0.75rem;color:#92400E;font-style:italic;">💬 {{ $proofPickUp->notes }}</div>
                                        @endif
                                    </div>
                                    @endif

                                </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- ITEM PESANAN --}}
                    <div class="od-card">
                        <div class="od-card-hd">
                            <div class="od-card-icon" style="background:#FFF7ED;">🛍️</div>
                            <span class="od-card-title">Item Pesanan</span>
                        </div>
                        <div style="padding:8px 20px;">
                            @foreach($order->items as $item)
                            @php
                                $imgUrl = null;
                                if ($item->variant && $item->variant->image) $imgUrl = $item->variant->image->image_path;
                                if (!$imgUrl && $item->product?->images?->count()) {
                                    $primary = $item->product->images->where('is_primary',true)->first();
                                    $imgUrl  = $primary ? $primary->image_path : $item->product->images->first()->image_path;
                                }
                            @endphp
                            <div class="od-item-row">
                                @if($imgUrl)
                                    <img src="{{ asset('storage/'.$imgUrl) }}" class="od-item-img">
                                @else
                                    <div class="od-item-ph">📦</div>
                                @endif
                                <div style="flex:1;">
                                    <div class="od-item-name">{{ $item->product_name ?? $item->product?->name }}</div>
                                    @if($item->variant_info)
                                        <div class="od-item-var">{{ $item->variant_info }}</div>
                                    @endif
                                    <div style="font-size:0.72rem;color:#9CA3AF;">× {{ $item->quantity }}</div>

                                    {{-- TOMBOL ULASAN — hanya muncul jika status completed --}}
                                    @if($order->status === 'completed' && $item->product)
                                        @php
                                            $hasReviewed = \App\Models\Review::where('user_id', Auth::id())
                                                ->where('order_id', $order->id)
                                                ->where('product_id', $item->product->id)
                                                ->exists();
                                        @endphp
                                        @if($hasReviewed)
                                            <span class="od-reviewed-badge">
                                                <svg width="12" height="12" fill="#15803D" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                Sudah Diulas
                                            </span>
                                        @else
                                            <a href="{{ route('reviews.create', ['order' => $order->id, 'product' => $item->product->id, 'variant' => $item->product_variant_id]) }}"
                                               class="od-review-btn">
                                                ⭐ Beri Ulasan
                                            </a>
                                        @endif
                                    @endif
                                </div>
                                <div class="od-item-price">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</div>
                            </div>
                            @endforeach
                        </div>
                        <div class="od-totals">
                            <div class="od-total-row">
                                <span class="od-total-label">Subtotal</span>
                                <span class="od-total-val">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="od-total-row">
                                <span class="od-total-label">Ongkir {{ $order->courier_name ? '('.$order->courier_name.')' : '' }}</span>
                                <span class="od-total-val">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            <div class="od-grand-total">
                                <span class="od-grand-label">Total Bayar</span>
                                <span class="od-grand-val">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- ALAMAT PENGIRIMAN --}}
                    <div class="od-card">
                        <div class="od-card-hd">
                            <div class="od-card-icon" style="background:#FFF1F2;">📍</div>
                            <span class="od-card-title">Alamat Pengiriman</span>
                        </div>
                        <div class="od-card-bd">
                            <div style="background:#F8F9FE;border-radius:10px;padding:14px 16px;font-size:0.875rem;color:#374151;line-height:1.7;">
                                <strong style="display:block;font-size:0.95rem;color:#1A1A2E;margin-bottom:2px;">{{ $order->recipient_name }}</strong>
                                {{ $order->recipient_phone }}<br>
                                {{ $order->shipping_address }},<br>
                                Kel. {{ $order->village ?? '-' }}, Kec. {{ $order->district ?? '-' }},
                                {{ $order->city ?? '-' }}, {{ $order->province ?? '-' }} {{ $order->postal_code ?? '' }}
                            </div>
                        </div>
                    </div>

                </div>{{-- end od-left --}}

                {{-- ══ KOLOM KANAN ══ --}}
                <div class="od-right">

                    {{-- INFO PESANAN --}}
                    <div class="od-card">
                        <div class="od-card-hd">
                            <div class="od-card-icon" style="background:#EEF2FF;">📋</div>
                            <span class="od-card-title">Info Pesanan</span>
                        </div>
                        <div style="padding:0 20px;">
                            <div class="od-info-row">
                                <span class="od-info-label">No. Pesanan</span>
                                <span class="od-info-val" style="font-size:0.78rem;">#{{ $order->order_number }}</span>
                            </div>
                            <div class="od-info-row">
                                <span class="od-info-label">Tanggal</span>
                                <span class="od-info-val">{{ $order->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="od-info-row">
                                <span class="od-info-label">Metode Bayar</span>
                                <span class="od-info-val">Midtrans</span>
                            </div>
                            <div class="od-info-row">
                                <span class="od-info-label">Status Bayar</span>
                                <span class="od-info-val" style="{{ $isPaid ? 'color:#15803D;' : 'color:#DC2626;' }}">
                                    {{ $isPaid ? '✅ Lunas' : '⏳ Belum Bayar' }}
                                </span>
                            </div>
                            <div class="od-info-row">
                                <span class="od-info-label">Pengiriman</span>
                                <span class="od-info-val"> Kurir {{ $order->courier_name ?? 'Kurir' }}</span>
                            </div>
                            @if($order->shipping_distance)
                            <div class="od-info-row">
                                <span class="od-info-label">Jarak</span>
                                <span class="od-info-val">{{ $order->shipping_distance }} KM</span>
                            </div>
                            @endif
                            @if($order->shipping_eta)
                            <div class="od-info-row">
                                <span class="od-info-label">Estimasi Tiba</span>
                                <span class="od-info-val">{{ $order->shipping_eta }}</span>
                            </div>
                            @endif
                            @if($order->tracking_number)
                            <div class="od-info-row">
                                <span class="od-info-label">No. Resi</span>
                                <span class="od-info-val" style="color:#7C3AED;font-family:monospace;font-size:0.78rem;background:#F5F3FF;padding:2px 8px;border-radius:5px;">{{ $order->tracking_number }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- TOMBOL AKSI --}}
                    <div class="od-card">
                        <div class="od-card-hd">
                            <div class="od-card-icon" style="background:#F0FDF4;">⚡</div>
                            <span class="od-card-title">Aksi</span>
                        </div>
                        <div style="padding:14px 20px;display:flex;flex-direction:column;gap:10px;">

                            {{-- BAYAR (jika masih pending bayar) --}}
                            @if($canPay)
                            <button id="btn-pay" class="od-btn-pay">
                                💳 Bayar Sekarang
                            </button>
                            <p style="font-size:0.7rem;color:#9CA3AF;text-align:center;margin-top:-4px;">Klik untuk membuka halaman pembayaran</p>
                            @endif

                            {{-- KONFIRMASI TERIMA (jika kurir sudah delivered) --}}
                            @if($canConfirm)
                            <form action="{{ route('customer.orders.confirm-received', $order->id) }}" method="POST"
                                  onsubmit="return confirm('Konfirmasi pesanan sudah diterima?')">
                                @csrf
                                <button type="submit" class="od-btn-confirm" style="width:100%;">
                                    ✅ Pesanan Sudah Diterima
                                </button>
                            </form>
                            <p style="font-size:0.7rem;color:#9CA3AF;text-align:center;">Tekan setelah paket benar-benar diterima. Setelah ini kamu bisa memberi ulasan.</p>
                            @endif

                            {{-- INFO jika shipped tapi kurir belum delivered --}}
                            @if($order->status === 'shipped' && $curTask < 3)
                            <div style="background:#F9FAFB;border-radius:8px;padding:10px 14px;font-size:0.78rem;color:#6B7280;text-align:center;">
                                Menunggu konfirmasi dari kurir bahwa paket telah diterima.
                            </div>
                            @endif

                            {{-- SUDAH LUNAS --}}
                            @if($order->payment_status === 'paid' && !$canPay && !$canConfirm && $order->status !== 'completed')
                            <div style="display:flex;align-items:center;gap:8px;background:#F0FDF4;border:1.5px solid #86EFAC;padding:12px 14px;border-radius:10px;font-size:0.825rem;font-weight:700;color:#15803D;">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Pembayaran telah diterima
                            </div>
                            @endif

                            {{-- SELESAI + reminder ulasan --}}
                            @if($order->status === 'completed')
                            <div style="background:#FFFBEB;border:1.5px solid #FDE68A;border-radius:10px;padding:12px 14px;font-size:0.8rem;color:#92400E;text-align:center;">
                                🎉 Pesanan selesai! Jangan lupa beri ulasan untuk produk yang kamu beli ya!
                            </div>
                            @endif

                            {{-- PERMINTAAN BATAL --}}
                            @if($order->cancel_request_status === 'pending')
                            <div style="background:#FFFBEB;border-radius:8px;padding:10px 14px;font-size:0.78rem;color:#B45309;font-weight:600;">
                                ⏳ Permintaan pembatalan menunggu persetujuan admin.<br>
                                <em style="font-weight:400;">Alasan: {{ $order->cancel_reason }}</em>
                            </div>
                            @elseif($order->cancel_request_status === 'rejected')
                            <div style="background:#FFF1F2;border-radius:8px;padding:10px 14px;font-size:0.78rem;color:#BE123C;font-weight:600;">
                                ❌ Permintaan pembatalan ditolak.<br>
                                <em style="font-weight:400;">{{ $order->cancel_reject_reason }}</em>
                            </div>
                            @endif

                            {{-- TOMBOL AJUKAN BATAL --}}
                            @if($cancellable)
                            <button type="button" class="od-btn-cancel"
                                    onclick="document.getElementById('modalCancel').classList.add('active')">
                                ✖ Ajukan Pembatalan
                            </button>
                            @endif

                        </div>
                    </div>



                </div>{{-- end od-right --}}
            </div>{{-- end od-layout --}}

        </div>
    </div>

    {{-- MODAL BATAL --}}
    <div class="od-modal" id="modalCancel">
        <div class="od-modal-box">
            <div class="od-modal-title">✖ Ajukan Pembatalan Pesanan</div>
            <div class="od-modal-sub">Pesanan #{{ $order->order_number }} — Pembatalan perlu persetujuan admin.</div>
            <form action="{{ route('customer.orders.request-cancel', $order->id) }}" method="POST">
                @csrf
                <label style="font-size:0.8rem;font-weight:700;color:#555;display:block;margin-bottom:6px;">Alasan Pembatalan *</label>
                <textarea name="cancel_reason" class="od-modal-textarea" required
                          placeholder="Contoh: Salah ukuran, ingin ganti produk..."></textarea>
                <div class="od-modal-actions">
                    <button type="button" class="od-modal-close"
                            onclick="document.getElementById('modalCancel').classList.remove('active')">Kembali</button>
                    <button type="submit" class="od-modal-submit">Kirim Permintaan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT MIDTRANS --}}
    @if($canPay && $order->snap_token)
    <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        document.getElementById('btn-pay')?.addEventListener('click', function() {
            window.snap.pay('{{ $order->snap_token }}', {
                onSuccess: () => {
                    fetch('{{ route("customer.orders.mark-paid", $order->id) }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                    }).then(() => window.location.reload());
                },
                onPending: () => window.location.reload(),
                onError:   () => window.location.reload(),
                onClose:   () => {}
            });
        });
    </script>
    @endif
</x-app-layout>