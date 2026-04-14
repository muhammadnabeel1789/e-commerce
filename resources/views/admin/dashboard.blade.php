<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Admin
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ── STATS CARDS ── --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md transition">
                    <div class="p-3 rounded-xl bg-blue-50 text-blue-600 flex-shrink-0">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Total Pesanan</div>
                        <div class="text-2xl font-extrabold text-gray-900">{{ number_format($stats['total_orders'] ?? 0) }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md transition">
                    <div class="p-3 rounded-xl bg-green-50 text-green-600 flex-shrink-0">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Pendapatan</div>
                        <div class="text-xl font-extrabold text-green-600">Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md transition">
                    <div class="p-3 rounded-xl bg-purple-50 text-purple-600 flex-shrink-0">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Produk Aktif</div>
                        <div class="text-2xl font-extrabold text-gray-900">{{ number_format($stats['total_products'] ?? 0) }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md transition">
                    <div class="p-3 rounded-xl bg-yellow-50 text-yellow-600 flex-shrink-0">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Pelanggan</div>
                        <div class="text-2xl font-extrabold text-gray-900">{{ number_format($stats['total_customers'] ?? 0) }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md transition {{ ($stats['low_stock_count'] ?? 0) > 0 ? 'ring-2 ring-red-300' : '' }}">
                    <div class="p-3 rounded-xl bg-red-50 text-red-600 flex-shrink-0">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Stok Menipis</div>
                        <div class="text-2xl font-extrabold {{ ($stats['low_stock_count'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($stats['low_stock_count'] ?? 0) }}</div>
                    </div>
                </div>

            </div>

            {{-- ── GRAFIK PENDAPATAN ── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

                {{-- Grafik 1: Pendapatan Bulanan --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 min-w-0">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">📈 Pendapatan Bulanan</h3>
                            <p class="text-xs text-gray-400 mt-0.5">12 bulan terakhir</p>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-400">Total Periode</div>
                            <div class="text-sm font-extrabold text-green-600">
                                Rp {{ number_format(array_sum($monthlyData ?? []), 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    <div style="position:relative; height:250px; width:100%;">
                        <canvas id="chartMonthly"></canvas>
                    </div>
                </div>

                {{-- Grafik 2: Pendapatan Harian --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 min-w-0">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">📊 Pendapatan Harian</h3>
                            <p class="text-xs text-gray-400 mt-0.5">30 hari terakhir</p>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-400">Total Periode</div>
                            <div class="text-sm font-extrabold text-indigo-600">
                                Rp {{ number_format(array_sum($dailyData ?? []), 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    <div style="position:relative; height:250px; width:100%;">
                        <canvas id="chartDaily"></canvas>
                    </div>
                </div>

            </div>

            {{-- ── SECTION KURIR ── --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-700 flex items-center gap-2">
                        🚚 Status Kurir Hari Ini
                    </h3>
                    <a href="{{ route('admin.users.index', ['role'=>'kurir']) }}" class="text-xs text-indigo-600 font-semibold hover:underline">
                        Kelola Kurir →
                    </a>
                </div>

                {{-- 4 stat kurir --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-3">
                        <div class="p-3 rounded-xl bg-indigo-50 flex-shrink-0">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 font-semibold uppercase">Total Kurir</div>
                            <div class="text-2xl font-extrabold text-gray-900">{{ $stats['total_couriers'] ?? 0 }}</div>
                            <div class="text-xs text-green-600 font-semibold">{{ $stats['active_couriers'] ?? 0 }} aktif</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-3">
                        <div class="p-3 rounded-xl bg-orange-50 flex-shrink-0">
                            <svg class="w-6 h-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 font-semibold uppercase">Menunggu Pickup</div>
                            <div class="text-2xl font-extrabold text-orange-600">
                                {{ \App\Models\Order::whereNotNull('courier_id')->where('courier_task_status','assigned')->count() }}
                            </div>
                            <div class="text-xs text-gray-400">paket belum diambil</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-3">
                        <div class="p-3 rounded-xl bg-blue-50 flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 font-semibold uppercase">Dalam Pengiriman</div>
                            <div class="text-2xl font-extrabold text-blue-600">
                                {{ \App\Models\Order::whereNotNull('courier_id')->where('courier_task_status','picked_up')->count() }}
                            </div>
                            <div class="text-xs text-gray-400">sedang diantar</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-3">
                        <div class="p-3 rounded-xl bg-green-50 flex-shrink-0">
                            <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 font-semibold uppercase">Terkirim Hari Ini</div>
                            <div class="text-2xl font-extrabold text-green-600">{{ $stats['orders_delivered_today'] ?? 0 }}</div>
                            <div class="text-xs text-gray-400">paket selesai hari ini</div>
                        </div>
                    </div>
                </div>

                {{-- Tabel kurir --}}
                @if(isset($couriers) && $couriers->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <span class="text-sm font-bold text-gray-700">Daftar Kurir & Status Pengiriman</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kurir</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-orange-500 uppercase tracking-wider">Menunggu Pickup</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-blue-500 uppercase tracking-wider">Dalam Perjalanan</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-green-500 uppercase tracking-wider">Terkirim Hari Ini</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Total Selesai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($couriers as $courier)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-extrabold text-base flex-shrink-0">
                                                {{ strtoupper(substr($courier->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">{{ $courier->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $courier->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($courier->is_active ?? true)
                                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Aktif</span>
                                        @else
                                            <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($courier->assigned_count > 0)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-700 font-extrabold text-sm">{{ $courier->assigned_count }}</span>
                                        @else
                                            <span class="text-gray-300 text-sm">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($courier->picked_up_count > 0)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-extrabold text-sm">{{ $courier->picked_up_count }}</span>
                                        @else
                                            <span class="text-gray-300 text-sm">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($courier->delivered_today > 0)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-700 font-extrabold text-sm">{{ $courier->delivered_today }}</span>
                                        @else
                                            <span class="text-gray-300 text-sm">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-sm font-bold text-gray-700">{{ $courier->total_delivered }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="bg-white rounded-xl border border-dashed border-gray-200 p-8 text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    <p class="text-sm font-semibold">Belum ada kurir terdaftar.</p>
                    <a href="{{ route('admin.users.index', ['role'=>'kurir']) }}" class="mt-2 inline-block text-xs text-indigo-600 font-bold hover:underline">Tambah Kurir →</a>
                </div>
                @endif
            </div>

            {{-- ── STOK MENIPIS ── --}}
            @if(($lowStockProducts ?? collect())->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-red-200 mb-8 overflow-hidden">
                <div class="px-6 py-4 border-b border-red-100 bg-red-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        <h3 class="text-sm font-bold text-red-700 uppercase tracking-wide">⚠ Peringatan Stok Menipis</h3>
                    </div>
                    <a href="{{ route('admin.products.index') }}" class="text-xs text-red-600 font-semibold hover:underline">Lihat Semua →</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($lowStockProducts as $product)
                    <div class="px-6 py-4 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3">
                                @php $img = $product->images->firstWhere('is_primary', true) ?? $product->images->first(); @endphp
                                @if($img)
                                    <img src="{{ \Storage::url($img->image_path) }}" class="w-10 h-10 rounded-lg object-cover border border-gray-200 flex-shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 flex-shrink-0">📦</div>
                                @endif
                                <div>
                                    <div class="text-sm font-semibold text-gray-800">{{ $product->name }}</div>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        @foreach($product->low_variants as $variant)
                                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full {{ $variant->stock == 0 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                            {{ strtoupper($variant->size ?? '-') }} / {{ ucfirst($variant->color ?? '-') }} —
                                            @if($variant->stock == 0)<strong>Habis</strong>@else Sisa <strong>{{ $variant->stock }}</strong> pcs @endif
                                        </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.stock-logs.index', $product->id) }}" class="text-xs text-indigo-600 font-semibold hover:underline whitespace-nowrap flex-shrink-0">Tambah Stok →</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ── ORDER TERBARU ── --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Pesanan Terbaru</h3>
                    <a href="{{ route('admin.orders.index') }}" class="text-xs text-indigo-600 font-semibold hover:underline">Lihat Semua →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">No Order</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tanggal & Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($recentOrders ?? [] as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-indigo-600">#{{ $order->order_number }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-700 uppercase flex-shrink-0">
                                            {{ substr($order->user->name ?? 'G', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $order->user->name ?? 'Guest' }}</div>
                                            <div class="text-xs text-gray-400">{{ $order->user->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $sc = match($order->status) {
                                            'paid','completed'       => 'bg-green-100 text-green-800',
                                            'pending'               => 'bg-yellow-100 text-yellow-800',
                                            'cancelled','failed'    => 'bg-red-100 text-red-800',
                                            'shipped','shipping'    => 'bg-blue-100 text-blue-800',
                                            'processing'            => 'bg-indigo-100 text-indigo-800',
                                            default                 => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $sc }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-indigo-600 hover:underline font-semibold">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <p class="font-medium">Belum ada data pesanan.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- ── CHART.JS + SCRIPT GRAFIK ── --}}
    {{-- Load Chart.js lalu init kedua grafik setelah DOM siap --}}
    <script>
    (function() {
        var script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
        script.onload = function () { initCharts(); };
        document.head.appendChild(script);

        function initCharts() {
            function formatRp(value) {
                if (value >= 1000000000) return 'Rp ' + (value / 1000000000).toFixed(1) + ' M';
                if (value >= 1000000)    return 'Rp ' + (value / 1000000).toFixed(1) + ' jt';
                if (value >= 1000)       return 'Rp ' + (value / 1000).toFixed(0) + ' rb';
                return 'Rp ' + value;
            }

            var gridColor = 'rgba(0,0,0,0.05)';
            var tickColor = '#9ca3af';

            // ── GRAFIK 1: Bulanan ──
            var monthlyLabels  = @json($monthlyLabels ?? []);
            var monthlyRevenue = @json($monthlyData   ?? []);
            var monthlyOrders  = @json($monthlyOrders ?? []);

            new Chart(document.getElementById('chartMonthly'), {
                type: 'bar',
                data: {
                    labels: monthlyLabels,
                    datasets: [
                        {
                            label: 'Pendapatan',
                            data: monthlyRevenue,
                            backgroundColor: 'rgba(34,197,94,0.75)',
                            borderColor: 'rgba(22,163,74,1)',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            yAxisID: 'yRev',
                            order: 2,
                        },
                        {
                            label: 'Jumlah Order',
                            data: monthlyOrders,
                            type: 'line',
                            borderColor: 'rgba(99,102,241,0.9)',
                            backgroundColor: 'rgba(99,102,241,0.12)',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointBackgroundColor: '#6366f1',
                            tension: 0.4,
                            fill: false,
                            yAxisID: 'yOrd',
                            order: 1,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 11 }, color: '#6b7280', boxWidth: 12, padding: 12 } },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return ctx.dataset.yAxisID === 'yRev'
                                        ? ' ' + formatRp(ctx.parsed.y)
                                        : ' ' + ctx.parsed.y + ' pesanan';
                                }
                            }
                        }
                    },
                    scales: {
                        x: { grid: { color: gridColor }, ticks: { color: tickColor, font: { size: 10 } } },
                        yRev: {
                            position: 'left',
                            grid: { color: gridColor },
                            ticks: { color: tickColor, font: { size: 10 }, callback: function(v) { return formatRp(v); } }
                        },
                        yOrd: {
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: { color: '#a5b4fc', font: { size: 10 }, stepSize: 1 }
                        }
                    }
                }
            });

            // ── GRAFIK 2: Harian ──
            var dailyLabels  = @json($dailyLabels ?? []);
            var dailyRevenue = @json($dailyData   ?? []);
            var dailyOrders  = @json($dailyOrders ?? []);

            new Chart(document.getElementById('chartDaily'), {
                type: 'line',
                data: {
                    labels: dailyLabels,
                    datasets: [
                        {
                            label: 'Pendapatan',
                            data: dailyRevenue,
                            borderColor: 'rgba(99,102,241,1)',
                            backgroundColor: 'rgba(99,102,241,0.12)',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#6366f1',
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'yRev',
                            order: 2,
                        },
                        {
                            label: 'Jumlah Order',
                            data: dailyOrders,
                            borderColor: 'rgba(251,146,60,0.9)',
                            backgroundColor: 'transparent',
                            borderWidth: 1.5,
                            borderDash: [5, 4],
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            pointBackgroundColor: '#fb923c',
                            tension: 0.4,
                            fill: false,
                            yAxisID: 'yOrd',
                            order: 1,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 11 }, color: '#6b7280', boxWidth: 12, padding: 12 } },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return ctx.dataset.yAxisID === 'yRev'
                                        ? ' ' + formatRp(ctx.parsed.y)
                                        : ' ' + ctx.parsed.y + ' pesanan';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: gridColor },
                            ticks: { color: tickColor, font: { size: 10 }, maxTicksLimit: 10 }
                        },
                        yRev: {
                            position: 'left',
                            grid: { color: gridColor },
                            ticks: { color: tickColor, font: { size: 10 }, callback: function(v) { return formatRp(v); } }
                        },
                        yOrd: {
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: { color: '#fdba74', font: { size: 10 }, stepSize: 1 }
                        }
                    }
                }
            });
        }
    })();
    </script>

</x-app-layout>