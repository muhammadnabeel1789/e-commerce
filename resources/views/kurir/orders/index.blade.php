<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🚚 Daftar Pesanan 
        </h2>
    </x-slot>

    <style>
        .koi-wrap * { font-family: inherit, sans-serif; }
        .koi-wrap { background: #F0F2F5; min-height: 100vh; padding: 28px 0 60px; }
        .koi-container { max-width: 1000px; margin: 0 auto; padding: 0 24px; }

        .koi-tabs { display: flex; gap: 6px; margin-bottom: 20px; background: #fff; padding: 6px;
                    border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.07); }
        .koi-tab { padding: 9px 20px; border-radius: 8px; font-size: 0.82rem; font-weight: 700; text-decoration: none;
                   color: #888; transition: all 0.15s; display: flex; align-items: center; gap: 6px; }
        .koi-tab.active { background: #4F46E5; color: #fff; box-shadow: 0 2px 8px rgba(79,70,229,0.3); }
        .koi-tab:hover:not(.active) { background: #F5F5F5; color: #444; }
        .koi-tab-badge { background: rgba(255,255,255,0.3); padding: 1px 7px; border-radius: 100px; font-size: 0.7rem; }
        .koi-tab:not(.active) .koi-tab-badge { background: #F0F0F5; color: #777; }

        .koi-card { background: #fff; border-radius: 14px; box-shadow: 0 1px 5px rgba(0,0,0,0.06); overflow: hidden; }
        .koi-row { padding: 18px 22px; border-bottom: 1px solid #F5F5F8; transition: background 0.15s; }
        .koi-row:last-child { border-bottom: none; }
        .koi-row:hover { background: #FAFBFF; }
        .koi-row-inner { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }

        .koi-order-num { font-size: 0.9rem; font-weight: 800; color: #4F46E5; }
        .koi-date { font-size: 0.75rem; color: #AAA; margin-top: 2px; }
        .koi-addr { font-size: 0.8rem; color: #555; margin-top: 6px; max-width: 500px; line-height: 1.4; }
        .koi-addr strong { color: #1A1A2E; }

        .koi-task-pill { display: inline-flex; align-items: center; gap: 5px; padding: 5px 14px; border-radius: 100px;
                         font-size: 0.73rem; font-weight: 700; }
        .tp-assigned  { background: #FFF7ED; color: #C2410C; border: 1.5px solid #FED7AA; }
        .tp-picked_up { background: #EFF6FF; color: #1D4ED8; border: 1.5px solid #BFDBFE; }
        .tp-delivered { background: #F0FDF4; color: #15803D; border: 1.5px solid #86EFAC; }

        .koi-detail-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; border-radius: 9px;
                          background: #EEF2FF; color: #4F46E5; font-size: 0.8rem; font-weight: 700; text-decoration: none; transition: all 0.15s; }
        .koi-detail-btn:hover { background: #4F46E5; color: #fff; }

        .koi-empty { text-align: center; padding: 60px 20px; color: #AAA; }
        .koi-empty-icon { width: 64px; height: 64px; background: #EEF2FF; border-radius: 50%; display: flex; align-items: center;
                          justify-content: center; margin: 0 auto 16px; }
    </style>

    <div class="koi-wrap">
        <div class="koi-container">

            {{-- Alert --}}
            @if(session('success'))
            <div style="margin-bottom:16px;background:#F0FDF4;border-left:4px solid #22C55E;padding:12px 16px;border-radius:10px;font-size:0.825rem;font-weight:600;color:#15803D;">
                {{ session('success') }}
            </div>
            @endif

            {{-- Tabs --}}
            <div class="koi-tabs">
                <a href="{{ route('kurir.orders.index', ['status'=>'aktif']) }}"
                   class="koi-tab {{ $activeTab === 'aktif' ? 'active' : '' }}">
                    📦 Aktif
                    @if($counts['aktif'] > 0)
                        <span class="koi-tab-badge">{{ $counts['aktif'] }}</span>
                    @endif
                </a>
                <a href="{{ route('kurir.orders.index', ['status'=>'selesai']) }}"
                   class="koi-tab {{ $activeTab === 'selesai' ? 'active' : '' }}">
                    ✅ Selesai
                    @if($counts['selesai'] > 0)
                        <span class="koi-tab-badge">{{ $counts['selesai'] }}</span>
                    @endif
                </a>
            </div>

            {{-- Tabel --}}
            <div class="koi-card">
                @forelse($orders as $order)
                <div class="koi-row">
                    <div class="koi-row-inner">
                        <div style="flex:1;">
                            <div class="koi-order-num">#{{ $order->order_number }}</div>
                            <div class="koi-date">{{ $order->created_at->format('d M Y, H:i') }} WIB</div>
                            <div class="koi-addr">
                               <div class="ks-addr-box">
                                <strong>{{ $order->recipient_name }}</strong> · {{ $order->recipient_phone }}<br>
                        {{ $order->address_detail ?? $order->shipping_address }},
                        Kel. {{ $order->village ?? '' }}, Kec. {{ $order->district ?? '-' }}<br>
                        {{ $order->city ?? '-' }}, {{ $order->province ?? '-' }} {{ $order->postal_code ?? '' }}
                    </div> 
                            </div>

                            <div style="margin-top:8px;">
                                <span class="koi-task-pill tp-{{ $order->courier_task_status }}">
                                    @if($order->courier_task_status === 'assigned') 📋 Perlu Diambil
                                    @elseif($order->courier_task_status === 'picked_up') 🚚 Dalam Perjalanan
                                    @elseif($order->courier_task_status === 'delivered') ✅ Terkirim
                                    @endif
                                </span>
                                @if($order->tracking_number)
                                    <span style="display:inline-flex;align-items:center;gap:4px;margin-left:8px;font-size:0.72rem;font-weight:800;color:#7C3AED;background:#F5F3FF;border:1px solid #DDD6FE;padding:3px 10px;border-radius:100px;font-family:monospace;">
                                        📦 {{ $order->tracking_number }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('kurir.orders.show', $order->id) }}" class="koi-detail-btn">
                                Detail
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="koi-empty">
                    <div class="koi-empty-icon">
                        <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#4F46E5">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p style="font-weight:700;color:#555;margin-bottom:4px;">
                        @if($activeTab === 'aktif') Tidak ada pesanan aktif saat ini.
                        @else Belum ada pengiriman yang selesai.
                        @endif
                    </p>
                    <p style="font-size:0.8rem;">Hubungi admin jika ada pesanan yang belum ditugaskan.</p>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($orders->hasPages())
            <div style="margin-top:20px;">{{ $orders->links() }}</div>
            @endif

        </div>
    </div>
</x-app-layout>