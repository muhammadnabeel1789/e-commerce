<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Pengguna</h2>
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-xl text-sm transition">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- PROFILE CARD --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex items-start gap-6">
                    {{-- Avatar --}}
                    @if($user->avatar)
                        <img class="w-24 h-24 rounded-2xl object-cover border border-gray-200 flex-shrink-0" src="{{ \Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                    @else
                        <div class="w-24 h-24 rounded-2xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-indigo-700 font-extrabold text-4xl">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                    @endif

                    {{-- Info --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-2xl font-extrabold text-gray-900">{{ $user->name }}</h3>
                            @if($user->role === 'admin')
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-bold rounded-full">Admin</span>
                            @elseif($user->role === 'kurir')
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-bold rounded-full">🚚 Kurir</span>
                            @else
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-bold rounded-full">Customer</span>
                            @endif
                            @if($user->is_active)
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-bold rounded-full">Aktif</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-bold rounded-full">Nonaktif</span>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mt-4">
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Email</p>
                                <p class="font-semibold text-gray-800">{{ $user->email }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Telepon</p>
                                <p class="font-semibold text-gray-800">{{ $user->phone ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Terdaftar</p>
                                <p class="font-semibold text-gray-800">{{ $user->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-gray-400">{{ $user->created_at->format('H:i') }} WIB</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Member Sejak</p>
                                <p class="font-semibold text-gray-800">{{ $userStats['member_since'] }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Toggle status --}}
                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            onclick="return confirm('{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} user ini?')"
                            class="px-4 py-2 rounded-xl text-sm font-bold transition {{ $user->is_active ? 'bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-200' : 'bg-green-50 text-green-600 hover:bg-green-600 hover:text-white border border-green-200' }}">
                            <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} mr-1"></i>
                            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- ══ KURIR SECTION ══ --}}
            @if($user->role === 'kurir' && $courierStats)
            <div class="mb-6">
                <h4 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">🚚 Statistik Pengiriman</h4>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-center">
                        <div class="text-xs text-gray-400 uppercase font-semibold mb-1">Total Tugas</div>
                        <div class="text-3xl font-extrabold text-gray-900">{{ $courierStats['total'] }}</div>
                    </div>
                    <div class="bg-white rounded-xl border border-orange-100 shadow-sm p-5 text-center">
                        <div class="text-xs text-orange-400 uppercase font-semibold mb-1">Menunggu Pickup</div>
                        <div class="text-3xl font-extrabold text-orange-500">{{ $courierStats['assigned'] }}</div>
                    </div>
                    <div class="bg-white rounded-xl border border-blue-100 shadow-sm p-5 text-center">
                        <div class="text-xs text-blue-400 uppercase font-semibold mb-1">Dalam Perjalanan</div>
                        <div class="text-3xl font-extrabold text-blue-500">{{ $courierStats['picked_up'] }}</div>
                    </div>
                    <div class="bg-white rounded-xl border border-green-100 shadow-sm p-5 text-center">
                        <div class="text-xs text-green-400 uppercase font-semibold mb-1">Total Terkirim</div>
                        <div class="text-3xl font-extrabold text-green-600">{{ $courierStats['delivered'] }}</div>
                    </div>
                    <div class="bg-white rounded-xl border border-indigo-100 shadow-sm p-5 text-center">
                        <div class="text-xs text-indigo-400 uppercase font-semibold mb-1">Terkirim Hari Ini</div>
                        <div class="text-3xl font-extrabold text-indigo-600">{{ $courierStats['today'] }}</div>
                    </div>
                </div>

                {{-- Riwayat pengiriman kurir --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-sm font-bold text-gray-700">Riwayat Pengiriman</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">No. Order</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Alamat</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status Kurir</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($courierOrders as $order)
                                @php
                                    $tl = ['assigned'=>['📋 Ditugaskan','bg-yellow-100 text-yellow-800'],
                                           'picked_up'=>['🚚 Dalam Perjalanan','bg-blue-100 text-blue-800'],
                                           'delivered'=>['✅ Terkirim','bg-green-100 text-green-800']][$order->courier_task_status ?? ''] ?? ['-','bg-gray-100 text-gray-600'];
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-semibold text-indigo-600 hover:underline">#{{ $order->order_number }}</a>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-700">{{ $order->recipient_name }}</td>
                                    <td class="px-6 py-3 text-xs text-gray-500 max-w-xs truncate">
                                        {{ $order->district ?? '-' }}, {{ $order->city ?? '-' }}
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $tl[1] }}">{{ $tl[0] }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-xs text-gray-400">
                                        @if($order->delivered_at) {{ $order->delivered_at->format('d M Y, H:i') }} WIB
                                        @elseif($order->picked_up_at) Pickup: {{ $order->picked_up_at->format('d M Y, H:i') }} WIB
                                        @else {{ $order->created_at->format('d M Y, H:i') }} WIB @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="px-6 py-6 text-center text-gray-400 text-sm">Belum ada riwayat pengiriman</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- ══ CUSTOMER SECTION ══ --}}
            @if($user->role === 'customer')
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
                    <div class="text-xs text-gray-400 uppercase font-semibold mb-1">Total Pesanan</div>
                    <div class="text-2xl font-extrabold text-gray-900">{{ $userStats['total_orders'] }}</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
                    <div class="text-xs text-gray-400 uppercase font-semibold mb-1">Total Belanja</div>
                    <div class="text-xl font-extrabold text-green-600">Rp {{ number_format($userStats['total_spent'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
                    <div class="text-xs text-gray-400 uppercase font-semibold mb-1">Total Review</div>
                    <div class="text-2xl font-extrabold text-blue-600">{{ $userStats['total_reviews'] }}</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
                    <div class="text-xs text-gray-400 uppercase font-semibold mb-1">Rating Rata-rata</div>
                    <div class="text-2xl font-extrabold text-yellow-500">{{ number_format($userStats['average_rating'] ?? 0, 1) }} ⭐</div>
                </div>
            </div>

            {{-- Pesanan terbaru --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b bg-gray-50">
                    <h3 class="text-sm font-bold text-gray-700">Pesanan Terbaru</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">No. Order</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($user->orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-semibold text-gray-900">{{ $order->order_number }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500">
                                    {{ $order->created_at->format('d M Y') }}<br>
                                    <span class="text-xs text-gray-400">{{ $order->created_at->format('H:i') }} WIB</span>
                                </td>
                                <td class="px-6 py-3 text-sm font-semibold text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td class="px-6 py-3">
                                    @php $c = $order->status === 'completed' ? 'bg-green-100 text-green-800' : ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $c }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td class="px-6 py-3">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:underline text-sm font-semibold">Lihat</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-6 py-6 text-center text-gray-400 text-sm">Belum ada pesanan</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Alamat --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="text-sm font-bold text-gray-700 mb-4">Alamat Pengiriman</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($user->addresses as $address)
                    <div class="border border-gray-200 rounded-xl p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-bold text-gray-800">{{ $address->label ?? 'Alamat' }}</h4>
                            @if($address->is_default || $address->is_primary)
                                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">Default</span>
                            @endif
                        </div>
                        <p class="text-sm font-semibold text-gray-800">{{ $address->recipient_name }}</p>
                        <p class="text-sm text-gray-500">{{ $address->phone }}</p>
                        <p class="text-sm text-gray-600 mt-2">{{ $address->address ?? $address->address_detail }}</p>
                        <p class="text-sm text-gray-500">Kel. {{ $address->village ?? '-' }}, Kec. {{ $address->district ?? '-' }}</p>
                        <p class="text-sm text-gray-500">{{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400">Belum ada alamat tersimpan</p>
                    @endforelse
                </div>
            </div>

            {{-- Reviews --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-700 mb-4">Review Terbaru</h3>
                <div class="space-y-4">
                    @forelse($user->reviews as $review)
                    <div class="border-b border-gray-100 pb-4">
                        <div class="flex justify-between items-start mb-1">
                            <div>
                                <h4 class="font-semibold text-gray-800 text-sm">{{ $review->product->name ?? '-' }}</h4>
                                <div class="flex items-center gap-0.5 mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-xs {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                    @endfor
                                    <span class="ml-2 text-xs text-gray-500">{{ $review->rating }}/5</span>
                                </div>
                            </div>
                            <div class="text-xs text-gray-400 text-right">
                                {{ $review->created_at->format('d M Y') }}<br>
                                {{ $review->created_at->format('H:i') }} WIB
                            </div>
                        </div>
                        <p class="text-sm text-gray-600">{{ $review->comment }}</p>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400">Belum ada review</p>
                    @endforelse
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>