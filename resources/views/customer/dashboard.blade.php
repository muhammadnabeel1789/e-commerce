<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="col-span-1 md:col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white relative">
                    <div class="relative z-10">
                        <h3 class="text-2xl font-bold">Halo, {{ Auth::user()->name }}! 👋</h3>
                        <p class="mt-2 text-indigo-100">Selamat datang kembali. Cek status pesananmu terkini di bawah ini.</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-400">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-gray-500 text-sm">Menunggu Pembayaran</div>
                            <div class="text-2xl font-bold text-gray-800">{{ $pendingOrders ?? 0 }}</div>
                        </div>
                        <div class="p-3 bg-yellow-50 rounded-full text-yellow-500">
                            <i class="fas fa-wallet text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-400">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-gray-500 text-sm">Sedang Diproses</div>
                            <div class="text-2xl font-bold text-gray-800">{{ $processingOrders ?? 0 }}</div>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-full text-blue-500">
                            <i class="fas fa-box-open text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-400">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-gray-500 text-sm">Pesanan Selesai</div>
                            <div class="text-2xl font-bold text-gray-800">{{ $completedOrders ?? 0 }}</div>
                        </div>
                        <div class="p-3 bg-green-50 rounded-full text-green-500">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('customer.orders.index') }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition text-center group border border-gray-100">
                    <div class="w-10 h-10 mx-auto bg-indigo-50 rounded-full flex items-center justify-center text-indigo-600 mb-2 group-hover:bg-indigo-600 group-hover:text-white transition">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Semua Pesanan</span>
                </a>

                <a href="{{ route('cart.index') }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition text-center group border border-gray-100">
                    <div class="w-10 h-10 mx-auto bg-pink-50 rounded-full flex items-center justify-center text-pink-600 mb-2 group-hover:bg-pink-600 group-hover:text-white transition">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Keranjang</span>
                </a>

                <a href="{{ route('addresses.index') }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition text-center group border border-gray-100">
                    <div class="w-10 h-10 mx-auto bg-orange-50 rounded-full flex items-center justify-center text-orange-600 mb-2 group-hover:bg-orange-600 group-hover:text-white transition">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Alamat Saya</span>
                </a>

                <a href="{{ route('profile.edit') }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition text-center group border border-gray-100">
                    <div class="w-10 h-10 mx-auto bg-gray-50 rounded-full flex items-center justify-center text-gray-600 mb-2 group-hover:bg-gray-600 group-hover:text-white transition">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Edit Profil</span>
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Pesanan Terbaru</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal & Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pesanan</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentOrders as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                    #{{ $order->order_number ?? $order->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $order->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $order->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClass = match($order->status) {
                                            'paid', 'completed', 'success' => 'bg-green-100 text-green-800',
                                            'pending', 'unpaid' => 'bg-yellow-100 text-yellow-800',
                                            'cancelled', 'failed' => 'bg-red-100 text-red-800',
                                            'processing', 'shipped' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('customer.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-shopping-basket text-4xl text-gray-300 mb-3"></i>
                                        <p>Belum ada pesanan terbaru.</p>
                                        <a href="{{ route('products.index') }}" class="mt-2 text-indigo-600 font-medium hover:underline">Mulai Belanja</a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>