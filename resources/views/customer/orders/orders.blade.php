<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pesanan Saya') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .op-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }

        .op-wrap {
            background: #F0F2F5;
            min-height: 100vh;
            padding: 32px 0 60px;
        }

        .op-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ── PAGE TITLE ── */
        .op-page-title {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
        }
        .op-page-title h1 { font-size: 1.35rem; font-weight: 800; color: #1A1A2E; }
        .op-page-title span { font-size: 0.8rem; color: #888; font-weight: 500; }

        /* ── STATS ── */
        .op-stats {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 14px; margin-bottom: 20px;
        }
        .op-stat-card {
            background: #fff; border-radius: 12px; padding: 16px 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            display: flex; align-items: center; gap: 14px;
        }
        .op-stat-icon {
            width: 42px; height: 42px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .op-stat-label { font-size: 0.72rem; color: #AAA; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
        .op-stat-value { font-size: 1.1rem; font-weight: 800; color: #1A1A2E; }

        /* ── FILTER TABS ── */
        .op-tabs {
            display: flex; gap: 4px; flex-wrap: wrap;
            background: #fff; padding: 6px; border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
            margin-bottom: 20px;
        }
        .op-tab {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 8px;
            font-size: 0.8rem; font-weight: 700;
            text-decoration: none; transition: all 0.18s;
            color: #888; background: transparent;
            text-transform: uppercase; letter-spacing: 0.03em;
            white-space: nowrap;
        }
        .op-tab.active {
            background: #4F46E5; color: #fff;
            box-shadow: 0 2px 8px rgba(79,70,229,0.3);
        }
        .op-tab:hover:not(.active) { background: #F5F5F5; color: #444; }

        .op-tab-count {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 20px; height: 18px; padding: 0 5px;
            border-radius: 100px; font-size: 0.65rem; font-weight: 800;
        }
        .op-tab.active .op-tab-count { background: rgba(255,255,255,0.25); color: #fff; }
        .op-tab:not(.active) .op-tab-count { background: #F0F0F5; color: #777; }

        /* ── TABLE CARD ── */
        .op-table-card {
            background: #fff; border-radius: 16px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.07); overflow: hidden;
        }
        .op-table-head {
            display: grid; grid-template-columns: 2fr 1.2fr 1.2fr 1fr 1fr;
            padding: 13px 24px; background: #F8F9FE;
            border-bottom: 1px solid #EDEEF5;
        }
        .op-th { font-size: 0.68rem; font-weight: 700; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.07em; }

        .op-row {
            display: grid; grid-template-columns: 2fr 1.2fr 1.2fr 1fr 1fr;
            padding: 16px 24px; border-bottom: 1px solid #F5F5F8;
            align-items: center; transition: background 0.15s;
            opacity: 0; transform: translateY(8px);
            animation: rowUp 0.3s forwards;
        }
        .op-row:last-child { border-bottom: none; }
        .op-row:hover { background: #FAFBFF; }

        @keyframes rowUp { to { opacity:1; transform:translateY(0); } }
        .op-row:nth-child(1){animation-delay:0.03s} .op-row:nth-child(2){animation-delay:0.06s}
        .op-row:nth-child(3){animation-delay:0.09s} .op-row:nth-child(4){animation-delay:0.12s}
        .op-row:nth-child(5){animation-delay:0.15s} .op-row:nth-child(6){animation-delay:0.18s}
        .op-row:nth-child(7){animation-delay:0.21s} .op-row:nth-child(8){animation-delay:0.24s}
        .op-row:nth-child(9){animation-delay:0.27s} .op-row:nth-child(10){animation-delay:0.30s}

        .op-order-num { font-size: 0.82rem; font-weight: 700; color: #4F46E5; }
        .op-order-date { font-size: 0.75rem; color: #AAA; margin-top: 3px; display: flex; align-items: center; gap: 4px; }
        .op-time { font-size: 0.78rem; color: #888; }
        .op-total { font-size: 0.9rem; font-weight: 700; color: #1A1A2E; }

        .op-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 12px; border-radius: 100px;
            font-size: 0.72rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.05em;
        }
        .op-badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; opacity:0.7; }
        .b-pending    { background:#FFF7ED; color:#C2410C; }
        .b-paid       { background:#EFF6FF; color:#1D4ED8; }
        .b-processing { background:#F5F3FF; color:#6D28D9; }
        .b-shipped    { background:#ECFDF5; color:#065F46; }
        .b-completed  { background:#F0FDF4; color:#14532D; }
        .b-cancelled  { background:#FFF1F2; color:#BE123C; }

        .op-detail-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 16px; border-radius: 8px;
            background: #EEF2FF; color: #4F46E5;
            font-size: 0.78rem; font-weight: 700;
            text-decoration: none; transition: all 0.15s;
        }
        .op-detail-btn:hover { background: #4F46E5; color: #fff; }

        /* ── EMPTY STATE ── */
        .op-empty { text-align: center; padding: 80px 20px; }
        .op-empty-icon {
            width: 72px; height: 72px; background: #EEF2FF; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;
        }
        .op-empty h3 { font-size: 1.1rem; font-weight: 800; color: #1A1A2E; margin-bottom: 6px; }
        .op-empty p  { font-size: 0.85rem; color: #AAA; margin-bottom: 24px; }
        .op-shop-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 28px; background: #4F46E5; color: #fff;
            border-radius: 10px; font-size: 0.85rem; font-weight: 700;
            text-decoration: none; transition: all 0.15s;
        }
        .op-shop-btn:hover { background: #4338CA; }

        @media(max-width: 768px) {
            .op-table-head { display: none; }
            .op-row { grid-template-columns: 1fr; gap: 8px; padding: 14px 16px; }
            .op-stats { grid-template-columns: 1fr; }
            .op-tab { padding: 7px 10px; font-size: 0.72rem; }
        }
    </style>

    <div class="op-wrap">
        <div class="op-container">

            <div class="op-page-title">
                <h1>Pesanan Saya</h1>
                <span>
                    Total {{ $orders->total() }} pesanan
                    @if($activeTab !== 'semua') — Filter: {{ ucfirst($activeTab) }} @endif
                </span>
            </div>

            {{-- Stats --}}
            <div class="op-stats">
                <div class="op-stat-card">
                    <div class="op-stat-icon" style="background:#EEF2FF;">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#4F46E5">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <div class="op-stat-label">Total Pesanan</div>
                        <div class="op-stat-value">{{ $counts->sum() }}</div>
                    </div>
                </div>
                <div class="op-stat-card">
                    <div class="op-stat-icon" style="background:#F0FDF4;">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#16A34A">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <div class="op-stat-label">Selesai</div>
                        <div class="op-stat-value">{{ $counts->get('completed', 0) }}</div>
                    </div>
                </div>
                <div class="op-stat-card">
                    <div class="op-stat-icon" style="background:#FFF7ED;">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#EA580C">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="op-stat-label">Menunggu</div>
                        <div class="op-stat-value">{{ $counts->get('pending',0) + $counts->get('processing',0) + $counts->get('shipped',0) }}</div>
                    </div>
                </div>
            </div>

            {{-- ── FILTER TABS ── --}}
            @php
                $tabs = [
                    ['key'=>'semua',      'label'=>'Semua',      'status'=>null],
                    ['key'=>'pending',    'label'=>'Pending',    'status'=>'pending'],
                    ['key'=>'processing', 'label'=>'Diproses',   'status'=>'processing'],
                    ['key'=>'shipped',    'label'=>'Dikirim',    'status'=>'shipped'],
                    ['key'=>'completed',  'label'=>'Selesai',    'status'=>'completed'],
                    ['key'=>'cancelled',  'label'=>'Dibatalkan', 'status'=>'cancelled'],
                ];
            @endphp

            <div class="op-tabs">
                @foreach($tabs as $tab)
                    @php
                        $url      = $tab['status']
                                    ? route('customer.orders.index', ['status' => $tab['status']])
                                    : route('customer.orders.index');
                        $isActive = $activeTab === $tab['key'];
                        $count    = $tab['status'] ? $counts->get($tab['status'], 0) : $counts->sum();
                    @endphp
                    <a href="{{ $url }}" class="op-tab {{ $isActive ? 'active' : '' }}">
                        {{ $tab['label'] }}
                        @if($count > 0)
                            <span class="op-tab-count">{{ $count }}</span>
                        @endif
                    </a>
                @endforeach
            </div>

            {{-- TABLE --}}
            <div class="op-table-card">
                @if($orders->count() > 0)

                    <div class="op-table-head">
                        <div class="op-th">No. Pesanan</div>
                        <div class="op-th">Tanggal dan Waktu</div>
                        <div class="op-th">Total</div>
                        <div class="op-th">Status Pesanan</div>
                        <div class="op-th">Aksi</div>
                    </div>

                    @foreach($orders as $order)
                        @php
                            $bMap   = ['pending'=>'b-pending','paid'=>'b-paid','processing'=>'b-processing','shipped'=>'b-shipped','completed'=>'b-completed','cancelled'=>'b-cancelled'];
                            $bLabel = ['pending'=>'Menunggu Bayar','paid'=>'Dibayar','processing'=>'Diproses','shipped'=>'Dikirim','completed'=>'Selesai','cancelled'=>'Dibatalkan'];
                            $bc = $bMap[$order->status]   ?? 'b-pending';
                            $bl = $bLabel[$order->status] ?? ucfirst($order->status);
                        @endphp
                        <div class="op-row">
                            <div>
                                <div class="op-order-num">{{ $order->order_number }}</div>
                            </div>
                            <div class="op-order-date">
    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    {{ $order->created_at->format('d M Y') }} 
    <span class="op-time">{{ $order->created_at->format('H:i') }} WIB</span>
</div>
                            <div class="op-total">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                            <div><span class="op-badge {{ $bc }}">{{ $bl }}</span></div>
                            <div>
                                <a href="{{ route('customer.orders.show', $order->id) }}" class="op-detail-btn">
                                    Detail
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach

                @else
                    <div class="op-empty">
                        <div class="op-empty-icon">
                            <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#4F46E5">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <h3>
                            @if($activeTab !== 'semua')
                                Tidak ada pesanan "{{ ucfirst($activeTab) }}"
                            @else
                                Belum Ada Pesanan
                            @endif
                        </h3>
                        <p>
                            @if($activeTab !== 'semua')
                                Coba pilih tab lain atau mulai belanja sekarang.
                            @else
                                Kamu belum pernah melakukan pembelian. Yuk mulai belanja!
                            @endif
                        </p>
                        <a href="{{ route('products.index') }}" class="op-shop-btn">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            Mulai Belanja
                        </a>
                    </div>
                @endif
            </div>

            {{-- Pagination — tetap bawa ?status=xxx --}}
            @if($orders->hasPages())
            <div style="margin-top:20px;">
                {{ $orders->links() }}
            </div>
            @endif

        </div>
    </div>
</x-app-layout>