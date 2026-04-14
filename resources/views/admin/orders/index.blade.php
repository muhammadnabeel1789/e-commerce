<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center sm:px-0">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <i class="fas fa-clipboard-list text-indigo-600"></i>
                Kelola Pesanan & Laporan
            </h2>
            <a href="{{ route('admin.orders.report', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
               target="_blank"
               class="hidden md:flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm no-print">
                <i class="fas fa-print"></i> Cetak Laporan
            </a>
        </div>
    </x-slot>

    <style>
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; }
            .bg-white { border: none !important; box-shadow: none !important; }
            .shadow-sm { box-shadow: none !important; }
            .py-12 { padding-top: 0 !important; padding-bottom: 0 !important; }
            .max-w-7xl { max-width: 100% !important; }
            .sm\:px-6 { padding-left: 0 !important; padding-right: 0 !important; }
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Bagian Statistik (Hanya muncul jika ada filter atau data) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 border-l-4 border-indigo-500">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Pendapatan</p>
                            <p class="text-xl font-black text-gray-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 border-l-4 border-green-500">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-600">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Pesanan</p>
                            <p class="text-xl font-black text-gray-800">{{ $totalOrders }} <span class="text-gray-400 text-xs font-medium">Transaksi</span></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 border-l-4 border-orange-500">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-orange-600">
                            <i class="fas fa-box"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Produk Terjual</p>
                            <p class="text-xl font-black text-gray-800">{{ $totalItemsSold }} <span class="text-gray-400 text-xs font-medium">Item</span></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter Tanggal (no-print) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8 no-print">
                <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" 
                               class="w-full border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" 
                               class="w-full border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>
                    <div class="flex-none">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-bold transition shadow-sm flex items-center gap-2">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                    @if($startDate || $endDate)
                    <div class="flex-none">
                        <a href="{{ route('admin.orders.index') }}" class="text-gray-500 hover:text-indigo-600 text-sm font-medium px-2 py-2">
                            Reset
                        </a>
                    </div>
                    @endif
                </form>
            </div>

            {{-- Judul Laporan saat Print --}}
            <div class="hidden print-only mb-6 text-center border-b pb-4">
                <h1 class="text-2xl font-bold uppercase tracking-widest text-gray-800">Laporan Penjualan</h1>
                <p class="text-sm text-gray-500 italic mt-1">Periode: {{ $startDate ?? 'Semua' }} s/d {{ $endDate ?? 'Semua' }}</p>
            </div>

            {{-- Daftar Pesanan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    @if($orders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Order</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider no-print">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($orders as $order)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-indigo-600">
                                            #{{ $order->order_number ?? $order->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-bold text-gray-800">{{ $order->user->name }}</div>
                                            <div class="text-[10px] text-gray-400 font-medium">{{ $order->user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                           {{ $order->created_at->format('d/m/Y') }} 
                                           <span class="text-[10px] ml-1">{{ $order->created_at->format('H:i') }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right font-black text-gray-900">
                                            Rp {{ number_format($order->total, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @php
                                                $statusLabels = [
                                                    'pending'    => 'Menunggu',
                                                    'paid'       => 'Dibayar',
                                                    'processing' => 'Proses',
                                                    'shipped'    => 'Dikirim',
                                                    'completed'  => 'Selesai',
                                                    'cancelled'  => 'Batal',
                                                ];
                                                $colors = [
                                                    'pending'    => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                                                    'paid'       => 'bg-blue-50 text-blue-700 border-blue-100',
                                                    'processing' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                                    'shipped'    => 'bg-purple-50 text-purple-700 border-purple-100',
                                                    'completed'  => 'bg-green-50 text-green-700 border-green-100',
                                                    'cancelled'  => 'bg-red-50 text-red-700 border-red-100',
                                                ];
                                                $colorClass  = $colors[$order->status] ?? 'bg-gray-50 text-gray-800 border-gray-100';
                                                $statusLabel = $statusLabels[$order->status] ?? ucfirst($order->status);
                                            @endphp
                                            <span class="px-3 py-1 inline-flex text-[10px] leading-4 font-bold rounded-full border {{ $colorClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                            @if($order->cancel_request_status === 'pending')
                                                <div class="text-[9px] text-red-600 font-bold mt-1 animate-pulse">Permintaan Batal</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center no-print">
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold bg-indigo-50 px-3 py-1.5 rounded-lg transition">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                @if($startDate || $endDate)
                                <tfoot class="bg-gray-50/50 print-only">
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right font-bold uppercase text-xs tracking-widest">Total Periode:</td>
                                        <td class="px-6 py-4 text-right font-black text-indigo-600">Rp {{ number_format($orders->sum('total'), 0, ',', '.') }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                        
                        <div class="mt-4 no-print">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-16 no-print">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                                <i class="fas fa-shopping-bag text-3xl"></i>
                            </div>
                            <h3 class="text-gray-900 font-bold text-lg">Belum ada pesanan</h3>
                            <p class="text-gray-400 text-sm">Data pesanan pelanggan akan muncul di sini.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Footer Print --}}
            <div class="hidden print-only mt-10 pt-6 border-t flex justify-between text-[10px] text-gray-400 italic">
                <div>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</div>
                <div>Admin Fashion Store</div>
            </div>

        </div>
    </div>
</x-app-layout>