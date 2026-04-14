<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Pengguna</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- STATS --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="text-xs text-gray-400 font-semibold uppercase">Total Customer</div>
                    <div class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['total_customers'] }}</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="text-xs text-gray-400 font-semibold uppercase">Total Admin</div>
                    <div class="text-2xl font-extrabold text-blue-600 mt-1">{{ $stats['total_admins'] }}</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="text-xs text-gray-400 font-semibold uppercase">Total Kurir</div>
                    <div class="text-2xl font-extrabold text-indigo-600 mt-1">{{ $stats['total_couriers'] }}</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="text-xs text-gray-400 font-semibold uppercase">Customer Aktif</div>
                    <div class="text-2xl font-extrabold text-green-600 mt-1">{{ $stats['active_customers'] }}</div>
                </div>
            </div>

            {{-- FILTER --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 p-5">
                <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Pengguna</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, Email, atau HP"
                               class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Role</label>
                        <select name="role" class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            <option value="customer" {{ request('role','customer') == 'customer' ? 'selected' : '' }}>Customer</option>
                            <option value="kurir"    {{ request('role') == 'kurir'    ? 'selected' : '' }}>Kurir</option>
                            <option value="admin"    {{ request('role') == 'admin'    ? 'selected' : '' }}>Admin</option>
                            <option value="all"      {{ request('role') == 'all'      ? 'selected' : '' }}>Semua</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                        <select name="status" class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm transition">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>

            {{-- ALERTS --}}
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('error') }}</div>
            @endif

            {{-- TABLE --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Pengguna</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Email & HP</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Orders</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Bergabung</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        @if($user->avatar)
                                            <img class="w-10 h-10 rounded-full object-cover flex-shrink-0" src="{{ \Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                                <span class="text-indigo-700 font-extrabold text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                        <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-800">{{ $user->email }}</div>
                                    <div class="text-xs text-gray-400">{{ $user->phone ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->role === 'admin')
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full">Admin</span>
                                    @elseif($user->role === 'kurir')
                                        <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs font-bold rounded-full">🚚 Kurir</span>
                                    @else
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">Customer</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">Aktif</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($user->role === 'kurir')
                                        {{ $user->courier_orders_count ?? 0 }} diantar
                                    @else
                                        {{ $user->orders_count }} pesanan
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $user->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $user->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.users.show', $user) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-lg text-xs font-bold transition">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $user->is_active ? 'bg-red-50 text-red-600 hover:bg-red-600 hover:text-white' : 'bg-green-50 text-green-600 hover:bg-green-600 hover:text-white' }}"
                                                onclick="return confirm('{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} user {{ addslashes($user->name) }}?')">
                                                <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-400 text-sm">Tidak ada data pengguna</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>