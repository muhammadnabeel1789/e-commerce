<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <div class="flex">
                <div class="shrink-0 flex items-center">
                    @php
                        $logoUrl = route('home');
                        if (Auth::check()) {
                            if (Auth::user()->role === 'kurir') $logoUrl = route('kurir.dashboard');
                        }
                    @endphp
                    <a href="{{ $logoUrl }}"
                        class="hover:opacity-80 transition flex items-center">
                        @include('partials.logo')
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">

                    {{-- Beranda: semua role kecuali kurir --}}
                    @if(!Auth::check() || Auth::user()->role !== 'kurir')
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                            {{ __('Beranda') }}
                        </x-nav-link>
                    @endif

                    {{-- Katalog: hanya customer & guest --}}
                    @if(!Auth::check() || Auth::user()->role === 'customer')
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                            {{ __('Katalog') }}
                        </x-nav-link>
                    @endif

                    {{-- ── MENU ADMIN ── --}}
                    @auth
                        @if(Auth::user()->role === 'admin')
                            <div class="hidden sm:flex sm:items-center sm:ml-6">
                                <x-dropdown align="left" width="48">
                                    <x-slot name="trigger">
                                        <button
                                            class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-red-600 hover:text-red-800 focus:outline-none transition">
                                            <div>Menu Admin</div>
                                            <div class="ml-1"><i class="fas fa-chevron-down text-xs"></i></div>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <div class="px-4 py-2 text-xs text-gray-400 uppercase font-bold border-b">Master Data
                                        </div>
                                        <x-dropdown-link :href="route('admin.categories.index')">Kategori</x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.brands.index')">Brand</x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.products.index')">Produk</x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.users.index')">Pengguna</x-dropdown-link>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        @endif

                        {{-- ── MENU KURIR ── --}}
                        @if(Auth::user()->role === 'kurir')
                            <x-nav-link :href="route('kurir.dashboard')" :active="request()->routeIs('kurir.dashboard')">
                                🏠 Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('kurir.orders.index')" :active="request()->routeIs('kurir.orders.*')">
                                📦 Pesanan
                                @if(isset($courierOrders) && $courierOrders > 0)
                                    <span
                                        class="ml-2 bg-indigo-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $courierOrders }}</span>
                                @endif
                            </x-nav-link>
                        @endif
                    @endauth

                </div>
            </div>

            {{-- Search bar: hanya customer & guest --}}
            @if(!Auth::check() || Auth::user()->role === 'customer')
                <div class="hidden sm:flex flex-1 items-center justify-center px-8">
                    <form action="{{ route('products.index') }}" method="GET" class="w-full max-w-md relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                            class="w-full bg-gray-100 border-transparent rounded-full py-2 pl-4 pr-10 focus:bg-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition text-sm">
                        <button type="submit" class="absolute right-0 top-0 mt-2 mr-3 text-gray-400 hover:text-indigo-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            @endif

            <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">

                {{-- Keranjang: hanya customer --}}
                @if(Auth::check() && Auth::user()->role === 'customer')
                    <a href="{{ route('cart.index') }}" class="text-gray-500 hover:text-indigo-600 relative group p-2">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        @php
                            $cartCount = \App\Models\Cart::where('user_id', Auth::id())->withCount('items')->first()->items_count ?? 0;
                        @endphp
                        @if($cartCount > 0)
                            <span
                                class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm animate-pulse">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                @endif

                {{-- Badge role kurir --}}
                @if(Auth::check() && Auth::user()->role === 'kurir')
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">
                        🚚 KURIR
                    </span>
                @endif

                <div class="relative ml-3">
                    @auth
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-gray-200 text-sm leading-4 font-medium rounded-full text-gray-600 bg-white hover:text-gray-900 focus:outline-none transition shadow-sm hover:shadow-md">
                                    <div class="mr-2 relative">
                                        @if(Auth::user()->role === 'admin')
                                            <i class="fas fa-user-shield text-lg text-red-500"></i>
                                            @php
                                                $newOrders = \App\Models\Order::whereIn('status', ['pending', 'paid'])->count();
                                            @endphp
                                            @if($newOrders > 0)
                                                <span
                                                    class="absolute -top-1 -right-2 bg-red-500 text-white text-[9px] font-bold px-1 rounded-full animate-pulse">
                                                    {{ $newOrders }}
                                                </span>
                                            @endif
                                        @elseif(Auth::user()->role === 'kurir')
                                            <i class="fas fa-truck text-lg text-indigo-500"></i>
                                            @php
                                                $courierOrders = \App\Models\Order::where('courier_id', Auth::id())
                                                    ->where('courier_task_status', 'assigned')
                                                    ->count();
                                            @endphp
                                            @if($courierOrders > 0)
                                                <span
                                                    class="absolute -top-1 -right-2 bg-indigo-500 text-white text-[9px] font-bold px-1 rounded-full animate-pulse">
                                                    {{ $courierOrders }}
                                                </span>
                                            @endif
                                        @else
                                            <i class="fas fa-user-circle text-lg"></i>
                                        @endif
                                    </div>
                                    <div class="max-w-[100px] truncate">{{ Auth::user()->name }}</div>
                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <div class="text-xs text-gray-500">Halo, {{ Auth::user()->name }}</div>
                                    <div
                                        class="text-xs font-bold mt-0.5
                                            {{ Auth::user()->role === 'admin' ? 'text-red-500' : (Auth::user()->role === 'kurir' ? 'text-indigo-500' : 'text-gray-400') }}">
                                        {{ strtoupper(Auth::user()->role) }}
                                    </div>
                                </div>

                                {{-- ── DROPDOWN CUSTOMER ── --}}
                                @if(Auth::user()->role === 'customer')
                                    <x-dropdown-link :href="route('dashboard')">
                                        <i class="fas fa-columns w-5 text-gray-400"></i> Dashboard
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('customer.orders.index')">
                                        <i class="fas fa-box-open w-5 text-gray-400"></i> Pesanan Saya
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('addresses.index')">
                                        <i class="fas fa-map-marker-alt w-5 text-gray-400"></i> Alamat
                                    </x-dropdown-link>

                                    {{-- ── DROPDOWN ADMIN ── --}}
                                @elseif(Auth::user()->role === 'admin')
                                    <x-dropdown-link :href="route('admin.dashboard')" class="text-red-600 font-bold">
                                        <i class="fas fa-tachometer-alt w-5"></i> Dashboard Admin
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.orders.index')">
                                        <i class="fas fa-clipboard-list w-5 text-gray-400"></i> Kelola Pesanan
                                        @if(isset($newOrders) && $newOrders > 0)
                                            <span
                                                class="ml-1 inline-flex items-center justify-center bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $newOrders }}</span>
                                        @endif
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.reviews.index')">
                                        <i class="fas fa-star w-5 text-yellow-400"></i> Ulasan Pelanggan
                                        @php
                                            $newReviews = \App\Models\Review::where('is_approved', false)->count();
                                        @endphp
                                        @if($newReviews > 0)
                                            <span
                                                class="ml-1 inline-flex items-center justify-center bg-yellow-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $newReviews }}</span>
                                        @endif
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.settings.index')">
                                        <i class="fas fa-cog w-5 text-gray-400"></i> Pengaturan Toko & Ongkir
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.stock-logs.index')">
                                        <i class="fas fa-history w-5 text-gray-400"></i> Stock Log Produk
                                    </x-dropdown-link>


                                    {{-- ── DROPDOWN KURIR ── --}}
                                @elseif(Auth::user()->role === 'kurir')
                                    <x-dropdown-link :href="route('kurir.dashboard')" class="text-indigo-600 font-bold">
                                        <i class="fas fa-tachometer-alt w-5"></i> Dashboard Kurir
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('kurir.orders.index')">
                                        <i class="fas fa-box w-5 text-gray-400"></i> Pesanan
                                        @if(isset($courierOrders) && $courierOrders > 0)
                                            <span
                                                class="ml-1 inline-flex items-center justify-center bg-indigo-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $courierOrders }}</span>
                                        @endif
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('kurir.orders.index', ['status' => 'selesai'])">
                                        <i class="fas fa-check-circle w-5 text-gray-400"></i> Terkirim
                                    </x-dropdown-link>
                                @endif

                                <div class="border-t border-gray-100 my-1"></div>

                                <x-dropdown-link :href="route('profile.edit')">
                                    <i class="fas fa-user-circle w-5 text-gray-400"></i> Profile
                                </x-dropdown-link>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt w-5"></i> {{ __('Keluar') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @else
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('login') }}"
                                class="text-sm font-bold text-gray-600 hover:text-indigo-600 px-3 py-2">Masuk</a>
                            <span class="text-gray-300">|</span>
                            <a href="{{ route('register') }}"
                                class="px-4 py-2 rounded-full bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition shadow-sm">
                                Daftar
                            </a>
                        </div>
                    @endauth
                </div>
            </div>

            {{-- ── MOBILE HAMBURGER ── --}}
            <div class="-mr-2 flex items-center sm:hidden">
                @if(Auth::check() && Auth::user()->role === 'customer')
                    <a href="{{ route('cart.index') }}" class="mr-4 relative text-gray-500">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        @if(isset($cartCount) && $cartCount > 0)
                            <span
                                class="absolute -top-2 -right-2 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                @endif

                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ── MOBILE MENU ── --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            @if(!Auth::check() || Auth::user()->role !== 'kurir')
                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                    {{ __('Beranda') }}
                </x-responsive-nav-link>
            @endif

            @if(!Auth::check() || Auth::user()->role === 'customer')
                <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                    {{ __('Katalog Produk') }}
                </x-responsive-nav-link>
            @endif
        </div>

        @auth
            {{-- ── MOBILE: CUSTOMER ── --}}
            @if(Auth::user()->role === 'customer')
                <div class="pt-2 pb-2 border-t border-gray-200 bg-indigo-50">
                    <div class="px-4 py-2 text-xs font-bold text-indigo-400 uppercase tracking-wider">
                        Menu Pelanggan
                    </div>
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('customer.orders.index')"
                        :active="request()->routeIs('customer.orders.index')">
                        Pesanan Saya
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('addresses.index')" :active="request()->routeIs('addresses.*')">
                        Alamat Pengiriman
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.index')">
                        Keranjang
                    </x-responsive-nav-link>
                </div>
            @endif

            {{-- ── MOBILE: ADMIN ── --}}
            @if(Auth::user()->role === 'admin')
                <div class="pt-2 pb-2 border-t border-gray-200 bg-red-50">
                    <div class="px-4 py-2 text-xs font-bold text-red-400 uppercase tracking-wider">Admin Area</div>
                    <x-responsive-nav-link :href="route('admin.dashboard')" class="text-red-700">Dashboard
                        Admin</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.orders.index')" class="text-red-700">
                        Kelola Pesanan
                        @php
                            $newOrdersMobile = \App\Models\Order::whereIn('status', ['pending', 'paid'])->count();
                        @endphp
                        @if($newOrdersMobile > 0)
                            <span
                                class="ml-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $newOrdersMobile }}</span>
                        @endif
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.products.index')" class="text-red-700">Kelola
                        Produk</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.users.index')" class="text-red-700">Kelola
                        Pengguna</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.reviews.index')" class="text-red-700 font-bold">
                        Ulasan Pelanggan
                        @php
                            $newReviewsMobile = \App\Models\Review::where('is_approved', false)->count();
                        @endphp
                        @if($newReviewsMobile > 0)
                            <span
                                class="ml-2 bg-yellow-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $newReviewsMobile }}</span>
                        @endif
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.settings.index')" class="text-red-700 font-bold">Pengaturan Toko
                        & Ongkir</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.stock-logs.index')" class="text-red-700 font-bold">Stock Log
                        Produk</x-responsive-nav-link>
                </div>
            @endif

            {{-- ── MOBILE: KURIR ── --}}
            @if(Auth::user()->role === 'kurir')
                <div class="pt-2 pb-2 border-t border-gray-200 bg-indigo-50">
                    <div class="px-4 py-2 text-xs font-bold text-indigo-400 uppercase tracking-wider">
                        🚚 Menu Kurir
                    </div>
                    <x-responsive-nav-link :href="route('kurir.dashboard')" :active="request()->routeIs('kurir.dashboard')">
                        Dashboard Kurir
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('kurir.orders.index', ['status' => 'aktif'])"
                        :active="request()->routeIs('kurir.orders.index')">
                        Pengiriman Aktif
                        @php
                            $courierOrdersMobile = \App\Models\Order::where('courier_id', Auth::id())->where('courier_task_status', 'assigned')->count();
                        @endphp
                        @if($courierOrdersMobile > 0)
                            <span
                                class="ml-2 bg-indigo-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $courierOrdersMobile }}</span>
                        @endif
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('kurir.orders.index', ['status' => 'selesai'])">
                        Riwayat Terkirim
                    </x-responsive-nav-link>
                </div>
            @endif

            {{-- ── MOBILE: USER INFO & LOGOUT ── --}}
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4 flex items-center">
                    <div class="flex-shrink-0">
                        @if(Auth::user()->role === 'admin')
                            <i class="fas fa-user-shield text-3xl text-red-400"></i>
                        @elseif(Auth::user()->role === 'kurir')
                            <i class="fas fa-truck text-3xl text-indigo-400"></i>
                        @else
                            <i class="fas fa-user-circle text-3xl text-gray-400"></i>
                        @endif
                    </div>
                    <div class="ml-3">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div
                            class="font-medium text-sm
                                {{ Auth::user()->role === 'admin' ? 'text-red-500' : (Auth::user()->role === 'kurir' ? 'text-indigo-500' : 'text-gray-500') }}">
                            {{ Auth::user()->email }} · {{ strtoupper(Auth::user()->role) }}
                        </div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">Edit Profil</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600">
                            Keluar
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-4 border-t border-gray-200">
                <div class="space-y-1">
                    <x-responsive-nav-link :href="route('login')">Masuk</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">Daftar Akun Baru</x-responsive-nav-link>
                </div>
            </div>
        @endauth
    </div>
</nav>