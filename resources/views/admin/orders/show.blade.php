<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.orders.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Pesanan: <span class="text-indigo-600">#{{ $order->order_number }}</span>
                </h2>
            </div>
            @php
                $statusConfig = [
                    'pending'    => ['label' => 'Menunggu Bayar', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'ring' => 'ring-yellow-300'],
                    'paid'       => ['label' => 'Lunas',          'bg' => 'bg-blue-100',   'text' => 'text-blue-800',   'ring' => 'ring-blue-300'],
                    'processing' => ['label' => 'Diproses',       'bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'ring' => 'ring-indigo-300'],
                    'shipped'    => ['label' => 'Dikirim',        'bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'ring' => 'ring-purple-300'],
                    'completed'  => ['label' => 'Selesai',        'bg' => 'bg-green-100',  'text' => 'text-green-800',  'ring' => 'ring-green-300'],
                    'cancelled'  => ['label' => 'Dibatalkan',     'bg' => 'bg-red-100',    'text' => 'text-red-800',    'ring' => 'ring-red-300'],
                ];
                $sc = $statusConfig[$order->status] ?? ['label' => ucfirst($order->status), 'bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'ring' => 'ring-gray-300'];
            @endphp
            <div class="flex items-center gap-3">
                {{-- Badge Status --}}
                <span class="px-4 py-1.5 rounded-full text-sm font-bold ring-1 {{ $sc['bg'] }} {{ $sc['text'] }} {{ $sc['ring'] }}">
                    {{ strtoupper($sc['label']) }}
                </span>

                {{-- ✅ Tombol Cetak Resi — HANYA muncul saat diproses --}}
                @if($order->status === 'processing')
                    <a href="{{ route('admin.orders.print-resi', $order->id) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-full transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Cetak Resi
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Alert --}}
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-300 text-green-800 px-5 py-3 rounded-lg shadow-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-300 text-red-800 px-5 py-3 rounded-lg shadow-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- ══════════════════════════════════════════════
                 NOTIFIKASI PERMINTAAN BATAL DARI CUSTOMER
            ══════════════════════════════════════════════ --}}
            @if($order->cancel_request_status === 'pending')
            <div class="mb-6 bg-amber-50 border-2 border-amber-400 rounded-xl p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-amber-800 mb-1">⚠️ Customer Mengajukan Pembatalan Pesanan</p>
                        <p class="text-sm text-amber-700 mb-1">
                            <span class="font-semibold">Alasan:</span> {{ $order->cancel_reason }}
                        </p>
                        <p class="text-xs text-amber-600">
                           Diajukan: {{ $order->cancel_requested_at ? \Carbon\Carbon::parse($order->cancel_requested_at)->format('d M Y, H:i') : '-' }} WIB
                        </p>

                        <div class="flex gap-3 mt-4">
                            {{-- Tombol SETUJUI --}}
                            <form action="{{ route('admin.orders.approve-cancel', $order->id) }}" method="POST"
                                  onsubmit="return confirm('Setujui pembatalan pesanan ini? Status akan berubah menjadi Cancelled.')">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Setujui Pembatalan
                                </button>
                            </form>

                            {{-- Tombol TOLAK (buka modal) --}}
                            <button type="button"
                                    onclick="document.getElementById('modalRejectCancel').classList.remove('hidden')"
                                    class="inline-flex items-center gap-2 px-5 py-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-bold rounded-lg border-2 border-gray-300 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Tolak Pembatalan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Tolak Pembatalan --}}
            <div id="modalRejectCancel" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-2xl mx-4">
                    <h3 class="text-base font-bold text-gray-900 mb-1">Tolak Permintaan Pembatalan</h3>
                    <p class="text-sm text-gray-500 mb-4">Berikan alasan penolakan yang jelas untuk customer.</p>

                    <form action="{{ route('admin.orders.reject-cancel', $order->id) }}" method="POST">
                        @csrf
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reject_reason"
                                  rows="3"
                                  required
                                  placeholder="Contoh: Pesanan sedang dalam proses pengemasan, tidak dapat dibatalkan."
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"></textarea>

                        <div class="flex gap-3 mt-4">
                            <button type="button"
                                    onclick="document.getElementById('modalRejectCancel').classList.add('hidden')"
                                    class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-lg transition">
                                Kembali
                            </button>
                            <button type="submit"
                                    class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg transition">
                                Kirim Penolakan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- Info: cancel request sudah ditolak --}}
            @if($order->cancel_request_status === 'rejected')
            <div class="mb-6 flex items-start gap-3 bg-gray-50 border border-gray-200 text-gray-600 px-5 py-3 rounded-lg text-sm">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Permintaan pembatalan dari customer sebelumnya telah <strong>ditolak</strong>. Alasan: {{ $order->cancel_reject_reason }}</span>
            </div>
            @endif

            {{-- STATUS TIMELINE --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">Alur Status Pesanan</h3>
                @php
                    $isCOD = strtolower($order->payment_method ?? '') === 'cod';
                    // Hilangkan 'paid' dari timeline jika metode pembayaran COD
                    $steps = $isCOD 
                        ? ['pending', 'processing', 'shipped', 'completed'] 
                        : ['pending', 'paid', 'processing', 'shipped', 'completed'];
                    
                    $currentIndex = array_search($order->status, $steps);
                    $isCancelled = $order->status === 'cancelled';
                @endphp

                @if($isCancelled)
                    <div class="flex items-center gap-3 text-red-600 bg-red-50 rounded-lg px-4 py-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-semibold">Pesanan ini telah dibatalkan.</span>
                    </div>
                @else
                    <div class="flex items-center">
                        @foreach($steps as $i => $step)
                            @php
                                $isDone    = $currentIndex !== false && $i < $currentIndex;
                                $isActive  = $currentIndex !== false && $i === $currentIndex;
                                $stepLabels = [
                                    'pending'    => ['icon' => '🕐', 'label' => 'Pending',    'sub' => 'Menunggu Bayar'],
                                    'paid'       => ['icon' => '💳', 'label' => 'Lunas',       'sub' => 'Sudah Dibayar'],
                                    'processing' => ['icon' => '📦', 'label' => 'Diproses',    'sub' => 'Sedang Dikemas'],
                                    'shipped'    => ['icon' => '🚚', 'label' => 'Dikirim',     'sub' => 'Dalam Perjalanan'],
                                    'completed'  => ['icon' => '✅', 'label' => 'Selesai',     'sub' => 'Pesanan Diterima'],
                                ];
                                $sl = $stepLabels[$step];
                            @endphp

                            {{-- Step circle --}}
                            <div class="flex flex-col items-center {{ $isActive ? 'scale-110' : '' }} transition-transform">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg
                                    {{ $isDone ? 'bg-indigo-600 text-white' : ($isActive ? 'bg-indigo-600 ring-4 ring-indigo-100 text-white' : 'bg-gray-100 text-gray-400') }}">
                                    @if($isDone)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @else
                                        {{ $sl['icon'] }}
                                    @endif
                                </div>
                                <div class="mt-2 text-center">
                                    <p class="text-xs font-bold {{ $isActive ? 'text-indigo-700' : ($isDone ? 'text-gray-700' : 'text-gray-400') }}">
                                        {{ $sl['label'] }}
                                    </p>
                                    <p class="text-xs {{ $isActive ? 'text-indigo-500' : 'text-gray-400' }}">
                                        {{ $sl['sub'] }}
                                    </p>
                                </div>
                            </div>

                            {{-- Connector line --}}
                            @if($i < count($steps) - 1)
                                <div class="flex-1 h-1 mx-2 rounded {{ ($currentIndex !== false && $i < $currentIndex) ? 'bg-indigo-500' : 'bg-gray-200' }}"></div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- KOLOM KIRI: Detail Pesanan --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Item Pesanan --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-base font-bold text-gray-900 mb-4 pb-3 border-b flex items-center gap-2">
                            <span class="text-lg">🛍️</span> Item Pesanan
                        </h3>
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-400 text-xs uppercase tracking-wide">
                                    <th class="pb-3">Produk</th>
                                    <th class="pb-3 text-center">Qty</th>
                                    <th class="pb-3 text-right">Harga Satuan</th>
                                    <th class="pb-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                               @foreach($order->items as $item)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="py-4">
                                            <div class="flex items-center gap-3">
                                                
                                                {{-- 🌟 LOGIKA GAMBAR VARIAN YANG BENAR 🌟 --}}
                                                @php
                                                    $imgUrl = null;
                                                    
                                                    // 1. Ambil gambar dari relasi varian -> image
                                                    if ($item->variant && $item->variant->image) {
                                                        $imgUrl = $item->variant->image->image_path;
                                                    }
                                                    
                                                    // 2. Jika Varian Tidak Punya Gambar, Fallback ke Gambar Utama Produk
                                                    if (!$imgUrl && $item->product && $item->product->images && $item->product->images->count() > 0) {
                                                        $primary = $item->product->images->where('is_primary', true)->first();
                                                        $imgUrl = $primary ? $primary->image_path : $item->product->images->first()->image_path;
                                                    }
                                                @endphp

                                                @if($imgUrl)
                                                    <img src="{{ asset('storage/' . $imgUrl) }}"
                                                         class="w-12 h-12 rounded-lg object-cover border border-gray-100 flex-shrink-0">
                                                @else
                                                    <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                                        <span class="text-xl">📦</span>
                                                    </div>
                                                @endif
                                                {{-- 🌟 END LOGIKA GAMBAR 🌟 --}}

                                                <div>
                                                    <p class="font-semibold text-gray-900">{{ $item->product_name ?? $item->product->name ?? 'Produk' }}</p>
                                                    @if(isset($item->variant_info) && $item->variant_info)
                                                        <p class="text-xs text-gray-400 mt-0.5">Varian: {{ $item->variant_info }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 text-center">
                                            <span class="bg-gray-100 text-gray-700 text-xs font-bold px-2 py-1 rounded-full">
                                                × {{ $item->quantity }}
                                            </span>
                                        </td>
                                        <td class="py-4 text-right text-gray-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="py-4 text-right font-bold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="pt-4 text-right text-sm text-gray-500">Subtotal Produk</td>
                                    <td class="pt-4 text-right text-sm">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="pt-1 text-right text-sm text-gray-500">
                                        Ongkir
                                        @if($order->courier_name)
                                            <span class="ml-1 text-xs bg-gray-100 px-1.5 py-0.5 rounded">{{ strtoupper($order->courier_name) }}</span>
                                        @endif
                                    </td>
                                    <td class="pt-1 text-right text-sm">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="text-base font-bold">
                                    <td colspan="3" class="pt-3 text-right border-t">Total Bayar</td>
                                    <td class="pt-3 text-right border-t text-indigo-600 text-lg">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Informasi Pengiriman & Pembeli --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-base font-bold text-gray-900 mb-4 pb-3 border-b flex items-center gap-2">
                            <span class="text-lg">📍</span> Informasi Pengiriman
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Penerima</p>
                                    <p class="font-bold text-gray-900">{{ $order->recipient_name }}</p>
                                    <p class="text-gray-600">{{ $order->recipient_phone }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Pembeli</p>
                                    <p class="font-medium text-gray-800">{{ $order->user->name ?? '-' }}</p>
                                    <p class="text-gray-500">{{ $order->user->email ?? '-' }}</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Alamat Lengkap</p>
                                <p class="text-gray-700 leading-relaxed">
                                    {{ $order->shipping_address }}<br>
                                    Kel. {{ $order->village }}, Kec. {{ $order->district }}, {{ $order->city }}<br>
                                    {{ $order->province }} {{ $order->postal_code }}
                                </p>
                                @if($order->shipping_distance || $order->shipping_eta)
                                <div class="mt-4 pt-3 border-t border-gray-50 flex gap-4">
                                    @if($order->shipping_distance)
                                    <div>
                                        <p class="text-[10px] text-gray-400 uppercase font-bold">Jarak</p>
                                        <p class="text-sm font-bold text-gray-700">{{ $order->shipping_distance }} KM</p>
                                    </div>
                                    @endif
                                    @if($order->shipping_eta)
                                    <div>
                                        <p class="text-[10px] text-gray-400 uppercase font-bold">Estimasi Tiba</p>
                                        <p class="text-sm font-bold text-gray-700">{{ $order->shipping_eta }}</p>
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Info Resi (jika sudah shipped) --}}
                    @if($order->tracking_number)
                        <div class="bg-purple-50 border border-purple-200 rounded-xl p-5 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">🚚</span>
                                <div>
                                    <p class="text-xs text-purple-500 font-semibold uppercase tracking-wide">Nomor Resi</p>
                                    <p class="text-lg font-bold text-purple-800 tracking-widest">{{ $order->tracking_number }}</p>
                                    @if($order->shipped_at)
                                        <p class="text-xs text-purple-400">Dikirim: {{ $order->shipped_at->format('d M Y, H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                            <button onclick="navigator.clipboard.writeText('{{ $order->tracking_number }}'); this.textContent='✅ Disalin!'; setTimeout(() => this.textContent='📋 Salin', 2000)"
                                    class="text-sm bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition font-medium">
                                📋 Salin
                            </button>
                        </div>
                    @endif
                </div>

                {{-- KOLOM KANAN: Panel Aksi --}}
                <div class="lg:col-span-1 space-y-5">

                    {{-- Info Singkat Pesanan --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Info Pesanan</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tanggal</span>
                                <span class="font-medium">{{ $order->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Jam</span>
                                <span class="font-medium">{{ $order->created_at->format('H:i') }} WIB</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Pembayaran</span>
                                <span class="font-medium text-xs">{{ $order->payment_method ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Status Bayar</span>
                               @php
    // Cek apakah payment_status sudah 'paid', ATAU status pesanan sudah masuk tahap diproses/dikirim/selesai
    $isPaid = ($order->payment_status === 'paid') || in_array($order->status, ['paid', 'processing', 'shipped', 'completed']);
@endphp

<span class="od-info-val {{ $isPaid ? 'paid' : 'unpaid' }}">
    {{ $isPaid ? '✅ LUNAS' : '⏳ BELUM BAYAR' }}
</span>
                            </div>
                        </div>
                    </div>

                 {{-- Form Update Status --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">⚙️ Update Status</h3>

    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" id="update-form">
        @csrf
        @method('PUT')

        {{-- Pilih Status --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Status Pesanan</label>
            <div class="space-y-2">
                @php
                    $isCOD = strtolower($order->payment_method ?? '') === 'cod';
                    
                    $statusOptions = [
                        'pending'    => ['emoji' => '🕐', 'label' => 'Pending — Menunggu Bayar',        'color' => 'yellow'],
                        'paid'       => ['emoji' => '💳', 'label' => 'Lunas — Sudah Dibayar',           'color' => 'blue'],
                        'processing' => ['emoji' => '📦', 'label' => 'Diproses — Sedang Dikemas',       'color' => 'indigo'],
                        'shipped'    => ['emoji' => '🚚', 'label' => 'Dikirim — Sedang Dalam Perjalanan','color' => 'purple'],
                        'completed'  => ['emoji' => '✅', 'label' => 'Selesai — Pesanan Diterima',      'color' => 'green'],
                        'cancelled'  => ['emoji' => '❌', 'label' => 'Dibatalkan',                      'color' => 'red'],
                    ];

                    // Jika COD, hilangkan opsi 'Paid' dari form update status
                    if ($isCOD) {
                        unset($statusOptions['paid']);
                    }
                    
                    // Tentukan status yang diperbolehkan untuk diubah
                    $allowedTransitions = [
                        'pending'    => $isCOD ? ['processing', 'cancelled'] : ['paid', 'cancelled'],
                        'paid'       => ['processing', 'cancelled'],
                        'processing' => ['shipped', 'cancelled'],
                        'shipped'    => ['completed'],
                        'completed'  => [],
                        'cancelled'  => [],
                    ];
                    
                    $currentStatus = $order->status;
                    $allowedNext = $allowedTransitions[$currentStatus] ?? [];
                @endphp

                @foreach($statusOptions as $val => $opt)
                    @php
                        // Cek apakah opsi ini boleh dipilih
                        $isAllowed = in_array($val, $allowedNext) || $val == $currentStatus;
                        $isCurrent = $order->status === $val;
                        
                        // Tentukan class untuk opsi
                        if ($isCurrent) {
                            $containerClass = 'border-indigo-400 bg-indigo-50';
                            $textClass = 'font-bold text-indigo-700';
                        } elseif ($isAllowed) {
                            $containerClass = 'border-gray-200 hover:border-gray-300 hover:bg-gray-50 cursor-pointer';
                            $textClass = 'text-gray-700';
                        } else {
                            $containerClass = 'border-gray-100 bg-gray-50 opacity-50 cursor-not-allowed';
                            $textClass = 'text-gray-400';
                        }
                    @endphp
                    
                    <label class="flex items-center gap-3 p-3 rounded-lg border transition {{ $containerClass }}
                        {{ !$isAllowed && !$isCurrent ? 'pointer-events-none' : '' }}">
                        
                        <input type="radio" 
                               name="status" 
                               value="{{ $val }}"
                               {{ $isCurrent ? 'checked' : '' }}
                               {{ !$isAllowed && !$isCurrent ? 'disabled' : '' }}
                               onchange="toggleResi(this.value)"
                               class="text-indigo-600 focus:ring-indigo-500 {{ !$isAllowed && !$isCurrent ? 'opacity-50' : '' }}">
                               
                        <span class="text-base">{{ $opt['emoji'] }}</span>
                        <span class="text-sm {{ $textClass }}">
                            {{ $opt['label'] }}
                            @if(!$isAllowed && !$isCurrent)
                                <span class="text-xs text-gray-400 ml-1"></span>
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
            
           
        </div>

        {{-- Input Resi (HANYA muncul saat status processing) --}}
        <div id="resi-section" class="{{ $order->status === 'processing' ? '' : 'hidden' }} mb-4 p-3 bg-purple-50 rounded-lg border border-purple-200">
            <label class="block text-sm font-semibold text-purple-700 mb-1">
                🚚 Nomor Resi
            </label>
            <input type="text"
                   name="tracking_number"
                   value="{{ $order->tracking_number }}"
                   placeholder="Kosongkan = generate otomatis"
                   class="block w-full text-sm border-purple-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
            <p class="text-xs text-purple-500 mt-1">
                💡 Biarkan kosong agar resi dibuat otomatis.
            </p>
        </div>

        <button type="submit"
                class="w-full bg-indigo-600 text-white py-2.5 rounded-lg hover:bg-indigo-700 transition font-bold text-sm flex items-center justify-center gap-2
                {{ in_array($order->status, ['completed', 'cancelled']) ? 'opacity-50 cursor-not-allowed' : '' }}"
                {{ in_array($order->status, ['completed', 'cancelled']) ? 'disabled' : '' }}>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Simpan Perubahan
        </button>
    </form>
</div>
                    {{-- Bukti Pembayaran --}}
                    @php $payment = $order->payments->last() ?? null; @endphp
                    @if($payment && $payment->payment_proof)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">💰 Bukti Pembayaran</h3>
                            <a href="{{ asset('storage/' . $payment->payment_proof) }}" target="_blank">
                                <img src="{{ asset('storage/' . $payment->payment_proof) }}"
                                     class="w-full rounded-lg border border-gray-200 hover:opacity-80 transition cursor-zoom-in">
                            </a>
                            <p class="text-xs text-gray-400 mt-2 text-center">Klik untuk memperbesar</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
       
    {{-- ═══════════════════════════════════════════
         ASSIGN KURIR (Panel Kanan Admin)
    ═══════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Header panel --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-700">Penugasan Kurir</h3>
            </div>

            {{-- Badge status tugas kurir --}}
            @if($order->courier)
                @php
                    $taskBadge = match($order->courier_task_status) {
                        'assigned'  => ['label' => 'Ditugaskan',   'class' => 'bg-yellow-100 text-yellow-700 ring-yellow-200'],
                        'picked_up' => ['label' => 'Paket Diambil','class' => 'bg-blue-100   text-blue-700   ring-blue-200'],
                        'delivered' => ['label' => 'Terkirim',     'class' => 'bg-green-100  text-green-700  ring-green-200'],
                        default     => ['label' => 'Belum Mulai',  'class' => 'bg-gray-100   text-gray-500   ring-gray-200'],
                    };
                @endphp
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold ring-1 {{ $taskBadge['class'] }}">
                    {{ $taskBadge['label'] }}
                </span>
            @else
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold ring-1 bg-gray-100 text-gray-400 ring-gray-200">
                    Belum Ditugaskan
                </span>
            @endif
        </div>

        <div class="p-5">

            {{-- ── Ada kurir yang ditugaskan ── --}}
            @if($order->courier)

                {{-- Kartu profil kurir --}}
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white font-bold text-base flex-shrink-0 shadow-sm">
                        {{ strtoupper(substr($order->courier->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ $order->courier->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $order->courier->email }}</p>
                    </div>
                </div>

                {{-- Progress bar status tugas --}}
                @php
                    $taskSteps = [
                        'assigned'  => ['idx' => 1, 'icon' => '📋', 'label' => 'Ditugaskan'],
                        'picked_up' => ['idx' => 2, 'icon' => '📦', 'label' => 'Paket Diambil'],
                        'delivered' => ['idx' => 3, 'icon' => '✅', 'label' => 'Terkirim'],
                    ];
                    $currentTaskIdx = $taskSteps[$order->courier_task_status]['idx'] ?? 0;
                @endphp
                <div class="flex items-center mb-5">
                    @foreach($taskSteps as $tKey => $tStep)
                        @php
                            $tDone   = $currentTaskIdx > $tStep['idx'];
                            $tActive = $currentTaskIdx === $tStep['idx'];
                        @endphp
                        <div class="flex flex-col items-center flex-shrink-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm
                                {{ $tDone   ? 'bg-indigo-600 text-white' :
                                   ($tActive ? 'bg-indigo-600 ring-4 ring-indigo-100 text-white' :
                                               'bg-gray-100 text-gray-400') }}">
                                @if($tDone)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @else
                                    {{ $tStep['icon'] }}
                                @endif
                            </div>
                            <p class="text-xs mt-1 font-semibold
                                {{ $tDone || $tActive ? 'text-indigo-600' : 'text-gray-400' }}">
                                {{ $tStep['label'] }}
                            </p>
                        </div>
                        @if(!$loop->last)
                            <div class="flex-1 h-0.5 mx-1 mb-4 rounded
                                {{ $currentTaskIdx > $tStep['idx'] ? 'bg-indigo-500' : 'bg-gray-200' }}"></div>
                        @endif
                    @endforeach
                </div>

                {{-- Foto bukti pengiriman --}}
                @if($order->deliveryProofs->count() > 0)
                <div class="mb-4">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Foto Bukti Pengiriman</p>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($order->deliveryProofs as $proof)
                        <div class="group relative">
                            <a href="{{ asset('storage/' . $proof->photo_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $proof->photo_path) }}"
                                     class="w-full h-24 object-cover rounded-lg border border-gray-200 group-hover:opacity-80 transition cursor-zoom-in">
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                    <span class="bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded-full">🔍 Lihat</span>
                                </div>
                            </a>
                            <div class="mt-1.5 text-center">
                                <span class="inline-block text-xs font-bold px-2 py-0.5 rounded-full
                                    {{ $proof->type === 'pick_up' ? 'bg-orange-100 text-orange-600' : 'bg-green-100 text-green-600' }}">
                                    {{ $proof->type === 'pick_up' ? '📦 Pickup' : '✅ Terkirim' }}
                                </span>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $proof->created_at->format('d M, H:i') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Tombol lepas kurir — hanya jika belum picked_up / delivered --}}
                @if(!in_array($order->courier_task_status, ['picked_up', 'delivered']))
                <form action="{{ route('admin.orders.unassign-courier', $order->id) }}" method="POST"
                      onsubmit="return confirm('Lepas kurir {{ $order->courier->name }} dari pesanan ini?')">
                    @csrf
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 text-sm text-red-600 border border-red-200 rounded-lg py-2.5 hover:bg-red-50 transition font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Lepas Kurir
                    </button>
                </form>
                @else
                <div class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-lg text-xs text-green-700">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold">Pengiriman sedang berjalan — kurir tidak bisa dilepas.</span>
                </div>
                @endif

            {{-- ── Belum ada kurir ── --}}
            @else
                @if(in_array($order->status, ['processing', 'shipped']))

                    {{-- Info jumlah kurir tersedia --}}
                    <div class="flex items-center gap-2 mb-4 p-3 bg-blue-50 border border-blue-100 rounded-lg">
                        <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-blue-700">
                            <span class="font-bold">{{ $couriers->count() }} kurir</span> tersedia untuk ditugaskan.
                        </p>
                    </div>

                    <form action="{{ route('admin.orders.assign-courier', $order->id) }}" method="POST">
                        @csrf

                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Pilih Kurir</label>

                        {{-- Daftar kurir sebagai radio card --}}
                        <div class="space-y-2 mb-4 max-h-52 overflow-y-auto pr-1">
                            @forelse($couriers as $c)
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 cursor-pointer transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                                    <input type="radio" name="courier_id" value="{{ $c->id }}" required
                                           class="text-indigo-600 focus:ring-indigo-500">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($c->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $c->name }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ $c->email }}</p>
                                    </div>
                                </label>
                            @empty
                                <div class="text-center py-4 text-xs text-gray-400">
                                    Tidak ada kurir yang terdaftar.
                                </div>
                            @endforelse
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-indigo-600 text-white text-sm font-bold py-2.5 rounded-lg hover:bg-indigo-700 active:bg-indigo-800 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Lanjutkan kurir
                        </button>
                    </form>

                @else
                    {{-- Status belum processing / shipped --}}
                    <div class="flex flex-col items-center py-6 text-center">
                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-gray-500 mb-1">Belum bisa ditugaskan</p>
                        <p class="text-xs text-gray-400">
                            Ubah status pesanan ke <span class="font-bold text-indigo-500">Processing</span> atau <span class="font-bold text-purple-500">Shipped</span> terlebih dahulu.
                        </p>
                    </div>
                @endif
            @endif

        </div>
    </div>
   
        <script>
    function toggleResi(status) {
        const resiSection = document.getElementById('resi-section');
        if (status === 'processing') {
            resiSection.classList.remove('hidden');
        } else {
            resiSection.classList.add('hidden');
        }
    }
    
    // Nonaktifkan form jika status sudah completed atau cancelled
    document.addEventListener('DOMContentLoaded', function() {
        const currentStatus = '{{ $order->status }}';
        const form = document.getElementById('update-form');

        if (['completed', 'cancelled'].includes(currentStatus)) {
            const inputs = form.querySelectorAll('input, button, select, textarea');
            inputs.forEach(input => {
                if (input.type !== 'hidden') {
                    input.disabled = true;
                }
            });

            // Tampilkan pesan
            const messageDiv = document.createElement('div');
            messageDiv.className = 'mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-700 text-sm';
            const labelMap = { completed: 'Selesai', cancelled: 'Dibatalkan' };
            messageDiv.innerHTML = `
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Status pesanan sudah <strong>${labelMap[currentStatus] ?? currentStatus}</strong> dan tidak dapat diubah lagi.</span>
                </div>
            `;
            form.appendChild(messageDiv);
        }
    });
</script>
</x-app-layout>