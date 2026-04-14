<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🚚 Dashboard Kurir
        </h2>
    </x-slot>

    <style>
        .kr-wrap * { font-family: inherit, sans-serif; }
        .kr-wrap { background: #F0F2F5; min-height: 100vh; padding: 28px 0 60px; }
        .kr-container { max-width: 1100px; margin: 0 auto; padding: 0 24px; }

        .kr-greeting { margin-bottom: 24px; }
        .kr-greeting h1 { font-size: 1.4rem; font-weight: 800; color: #1A1A2E; }
        .kr-greeting p { font-size: 0.85rem; color: #888; margin-top: 4px; }

        /* Stats */
        .kr-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
        .kr-stat { background: #fff; border-radius: 14px; padding: 18px 20px; box-shadow: 0 1px 5px rgba(0,0,0,0.06);
                   display: flex; align-items: center; gap: 14px; }
        .kr-stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .kr-stat-label { font-size: 0.72rem; color: #AAA; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
        .kr-stat-val { font-size: 1.4rem; font-weight: 800; color: #1A1A2E; line-height: 1.1; }

        /* Cards */
        .kr-section-title { font-size: 0.85rem; font-weight: 800; color: #1A1A2E; text-transform: uppercase;
                            letter-spacing: 0.06em; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
        .kr-card { background: #fff; border-radius: 14px; box-shadow: 0 1px 5px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 24px; }

        /* Order rows */
        .kr-order-row { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px;
                        border-bottom: 1px solid #F5F5F8; transition: background 0.15s; flex-wrap: wrap; gap: 10px; }
        .kr-order-row:last-child { border-bottom: none; }
        .kr-order-row:hover { background: #FAFBFF; }
        .kr-order-num { font-size: 0.85rem; font-weight: 800; color: #4F46E5; }
        .kr-order-addr { font-size: 0.78rem; color: #888; margin-top: 3px; max-width: 400px; }
        .kr-task-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 100px;
                         font-size: 0.72rem; font-weight: 700; text-transform: uppercase; }
        .task-assigned  { background: #FFF7ED; color: #C2410C; }
        .task-picked_up { background: #EFF6FF; color: #1D4ED8; }
        .task-delivered { background: #F0FDF4; color: #15803D; }

        .kr-detail-btn { display: inline-flex; align-items: center; gap: 5px; padding: 7px 16px; border-radius: 8px;
                         background: #EEF2FF; color: #4F46E5; font-size: 0.78rem; font-weight: 700; text-decoration: none;
                         transition: all 0.15s; }
        .kr-detail-btn:hover { background: #4F46E5; color: #fff; }

        .kr-empty { text-align: center; padding: 40px 20px; color: #AAA; font-size: 0.85rem; }

        /* Quick action banner */
        .kr-quick-action { background: linear-gradient(135deg, #4F46E5, #7C3AED); border-radius: 14px;
                           padding: 20px 24px; margin-bottom: 24px; color: #fff;
                           display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .kr-quick-action h3 { font-size: 1rem; font-weight: 800; margin-bottom: 4px; }
        .kr-quick-action p { font-size: 0.8rem; opacity: 0.85; }
        .kr-quick-btn { padding: 10px 22px; background: #fff; color: #4F46E5; border-radius: 8px;
                        font-size: 0.85rem; font-weight: 800; text-decoration: none; transition: opacity 0.15s; }
        .kr-quick-btn:hover { opacity: 0.9; }
        }
    </style>

    <div class="kr-wrap">
        <div class="kr-container">

            {{-- Greeting --}}
            <div class="kr-greeting">
                <h1>Halo, {{ Auth::user()->name }}! 👋</h1>
                <p>{{ now()->format('l, d F Y') }} — Semangat bertugas hari ini!</p>
            </div>

            {{-- Quick Action (jika ada pesanan aktif) --}}
            @if($stats['assigned'] + $stats['picked_up'] > 0)
            <div class="kr-quick-action">
                <div>
                    <h3>📦 {{ $stats['assigned'] + $stats['picked_up'] }} Pesanan Aktif</h3>
                    <p>
                        @if($stats['assigned'] > 0) {{ $stats['assigned'] }} menunggu diambil &nbsp;·&nbsp; @endif
                        @if($stats['picked_up'] > 0) {{ $stats['picked_up'] }} dalam pengiriman @endif
                    </p>
                </div>
                <a href="{{ route('kurir.orders.index') }}" class="kr-quick-btn">
                    Lihat Pesanan →
                </a>
            </div>
            @endif

            {{-- Statistik --}}
            <div class="kr-stats">
                <div class="kr-stat">
                    <div class="kr-stat-icon" style="background:#FFF7ED;">
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#EA580C">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <div class="kr-stat-label">Total Tugas</div>
                        <div class="kr-stat-val">{{ $stats['total'] }}</div>
                    </div>
                </div>
                <div class="kr-stat">
                    <div class="kr-stat-icon" style="background:#FFF7ED;">
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#C2410C">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="kr-stat-label">Menunggu Pickup</div>
                        <div class="kr-stat-val">{{ $stats['assigned'] }}</div>
                    </div>
                </div>
                <div class="kr-stat">
                    <div class="kr-stat-icon" style="background:#EFF6FF;">
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#1D4ED8">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                        </svg>
                    </div>
                    <div>
                        <div class="kr-stat-label">Dalam Pengiriman</div>
                        <div class="kr-stat-val">{{ $stats['picked_up'] }}</div>
                    </div>
                </div>
                <div class="kr-stat">
                    <div class="kr-stat-icon" style="background:#F0FDF4;">
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#15803D">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <div class="kr-stat-label">Terkirim</div>
                        <div class="kr-stat-val">{{ $stats['delivered'] }}</div>
                    </div>
                </div>
            </div>

            {{-- Pesanan Aktif --}}
            <div class="kr-section-title">
                <span>📦</span> Pesanan Aktif Saat Ini
            </div>
            <div class="kr-card">
                @forelse($activeOrders as $order)
                <div class="kr-order-row">
                    <div>
                        <div class="kr-order-num">#{{ $order->order_number }}</div>
                        <div class="kr-order-addr">
                            📍 {{ $order->recipient_name }} — {{ $order->shipping_address }},  Kel. {{ $order->village ?? '' }}, Kec. {{ $order->district ?? '-' }}<br>
                        {{ $order->city ?? '-' }}, {{ $order->province ?? '-' }} {{ $order->postal_code ?? '' }}
                        </div>
                        <div style="margin-top:6px;">
                            <span class="kr-task-badge task-{{ $order->courier_task_status }}">
                                @if($order->courier_task_status === 'assigned') 📋 Perlu Diambil
                                @elseif($order->courier_task_status === 'picked_up') 🚚 Dalam Pengiriman
                                @endif
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('kurir.orders.show', $order->id) }}" class="kr-detail-btn">
                        Lihat Detail →
                    </a>
                </div>
                @empty
                <div class="kr-empty">
                    ✅ Tidak ada pesanan aktif saat ini.
                </div>
                @endforelse
                @if($activeOrders->count() > 0)
                <div style="padding:12px 20px; border-top:1px solid #F5F5F8;">
                    <a href="{{ route('kurir.orders.index') }}" style="font-size:0.8rem;font-weight:700;color:#4F46E5;text-decoration:none;">
                        Lihat semua pesanan aktif →
                    </a>
                </div>
                @endif
            </div>

            {{-- Pengiriman Terbaru --}}
            @if($recentDelivered->count() > 0)
            <div class="kr-section-title">
                <span>✅</span> Pengiriman Selesai Terbaru
            </div>
            <div class="kr-card">
                @foreach($recentDelivered as $order)
                <div class="kr-order-row">
                    <div>
                        <div class="kr-order-num">#{{ $order->order_number }}</div>
                        <div class="kr-order-addr">
                            📍 {{ $order->recipient_name }} — {{ $order->shipping_address }},  Kel. {{ $order->village ?? '' }}, Kec. {{ $order->district ?? '-' }}<br>
                        {{ $order->city ?? '-' }}, {{ $order->province ?? '-' }} {{ $order->postal_code ?? '' }}
                        </div>
                        @if($order->delivered_at)
                        <div style="font-size:0.72rem;color:#15803D;margin-top:4px;font-weight:600;">
                            ✅ Terkirim: {{ $order->delivered_at->format('d M Y, H:i') }} WIB
                        </div>
                        @endif
                    </div>
                    <a href="{{ route('kurir.orders.show', $order->id) }}" class="kr-detail-btn">
                        Detail
                    </a>
                </div>
                @endforeach
                <div style="padding:12px 20px; border-top:1px solid #F5F5F8;">
                    <a href="{{ route('kurir.orders.index', ['status'=>'selesai']) }}" style="font-size:0.8rem;font-weight:700;color:#4F46E5;text-decoration:none;">
                        Lihat semua pengiriman selesai →
                    </a>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>