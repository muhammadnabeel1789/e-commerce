<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ulasan Saya') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .ur-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }

        .ur-wrap { background: #F0F2F5; min-height: 100vh; padding: 32px 0 60px; }
        .ur-container { max-width: 860px; margin: 0 auto; padding: 0 24px; }

        .ur-title { font-size: 1.35rem; font-weight: 800; color: #1A1A2E; margin-bottom: 24px; }

        /* Alert */
        .ur-alert {
            padding: 12px 16px; border-radius: 10px; margin-bottom: 20px;
            font-size: 0.825rem; font-weight: 600; display: flex; align-items: center; gap: 8px;
        }
        .ur-alert.success { background: #F0FDF4; border-left: 4px solid #22C55E; color: #15803D; }
        .ur-alert.info    { background: #EFF6FF; border-left: 4px solid #3B82F6; color: #1D4ED8; }

        /* Card */
        .ur-card {
            background: #fff; border-radius: 14px;
            box-shadow: 0 1px 5px rgba(0,0,0,0.07);
            margin-bottom: 14px; overflow: hidden;
            border-left: 4px solid #E5E7EB;
            opacity: 0; transform: translateY(8px);
            animation: urFade 0.3s forwards;
        }
        .ur-card.approved { border-left-color: #22C55E; }
        .ur-card.pending  { border-left-color: #F59E0B; }
        @keyframes urFade { to { opacity:1; transform:translateY(0); } }
        .ur-card:nth-child(1){animation-delay:0.04s} .ur-card:nth-child(2){animation-delay:0.08s}
        .ur-card:nth-child(3){animation-delay:0.12s} .ur-card:nth-child(4){animation-delay:0.16s}

        .ur-card-inner { padding: 18px 20px; display: flex; gap: 16px; align-items: flex-start; }

        .ur-prod-img {
            width: 60px; height: 60px; border-radius: 10px; object-fit: cover;
            border: 1px solid #F0F0F0; flex-shrink: 0; background: #F5F5F5;
        }
        .ur-prod-img-ph {
            width: 60px; height: 60px; border-radius: 10px; background: #F5F5F5;
            border: 1px solid #EBEBEB; display: flex; align-items: center;
            justify-content: center; flex-shrink: 0;
        }

        .ur-content { flex: 1; min-width: 0; }
        .ur-top-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; flex-wrap: wrap; margin-bottom: 6px; }
        .ur-product-name { font-size: 0.9rem; font-weight: 800; color: #1A1A2E; }
        .ur-date { font-size: 0.72rem; color: #AAA; margin-top: 2px; }

        .ur-stars { display: flex; gap: 2px; margin-bottom: 6px; }
        .ur-star { font-size: 14px; }
        .ur-star.on  { color: #F59E0B; }
        .ur-star.off { color: #D1D5DB; }

        .ur-comment {
            font-size: 0.825rem; color: #374151; background: #F9FAFB;
            padding: 10px 14px; border-radius: 8px; border-left: 3px solid #E5E7EB;
            margin-bottom: 10px; line-height: 1.6;
        }

        .ur-imgs { display: flex; gap: 8px; margin-bottom: 10px; flex-wrap: wrap; }
        .ur-img-thumb {
            width: 52px; height: 52px; border-radius: 6px; object-fit: cover;
            border: 1px solid #E5E7EB; cursor: pointer; transition: opacity 0.15s;
        }
        .ur-img-thumb:hover { opacity: 0.8; }

        /* Status badge */
        .ur-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 12px; border-radius: 100px;
            font-size: 0.7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.05em; flex-shrink: 0;
        }
        .ur-badge.approved { background: #F0FDF4; color: #15803D; border: 1.5px solid #86EFAC; }
        .ur-badge.pending  { background: #FFF7ED; color: #C2410C; border: 1.5px solid #FED7AA; }

        /* Empty state */
        .ur-empty { text-align: center; padding: 80px 20px; background: #fff; border-radius: 16px; box-shadow: 0 1px 5px rgba(0,0,0,0.07); }
        .ur-empty-icon {
            width: 72px; height: 72px; background: #EEF2FF; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;
        }
        .ur-empty h3 { font-size: 1.05rem; font-weight: 800; color: #1A1A2E; margin-bottom: 6px; }
        .ur-empty p  { font-size: 0.85rem; color: #AAA; margin-bottom: 24px; }
        .ur-shop-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 24px; background: #4F46E5; color: #fff;
            border-radius: 10px; font-size: 0.85rem; font-weight: 700;
            text-decoration: none; transition: all 0.15s;
        }
        .ur-shop-btn:hover { background: #4338CA; }

        @media(max-width: 640px) {
            .ur-card-inner { flex-direction: column; }
        }
    </style>

    <div class="ur-wrap">
        <div class="ur-container">
            <div class="ur-title">Ulasan Saya</div>

            {{-- Alert --}}
            @if(session('success'))
                <div class="ur-alert success">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('info'))
                <div class="ur-alert info">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('info') }}
                </div>
            @endif

            @if($reviews->count() > 0)
                @foreach($reviews as $review)
                    @php
                        $statusClass = $review->is_approved ? 'approved' : 'pending';
                        
                        // Prioritas: Gambar varian, kemudian gambar utama produk
                        $imgUrl = null;
                        if ($review->variant && $review->variant->image) {
                            $imgUrl = $review->variant->image->image_path;
                        } else {
                            $imgUrl = $review->product->images->first()?->image_path;
                        }
                    @endphp
                    <div class="ur-card {{ $statusClass }}">
                        <div class="ur-card-inner">

                            {{-- Foto Produk --}}
                            @if($imgUrl)
                                <img src="{{ asset('storage/' . $imgUrl) }}" class="ur-prod-img" alt="{{ $review->product->name }}">
                            @else
                                <div class="ur-prod-img-ph">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#CCC">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Konten --}}
                            <div class="ur-content">
                                <div class="ur-top-row">
                                    <div>
                                        <div class="ur-product-name">{{ $review->product->name }}</div>
                                        @if($review->variant)
                                            <div class="text-[10px] font-bold text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded inline-block mt-1">
                                                VARIANT: {{ strtoupper($review->variant->name) }}
                                            </div>
                                        @endif
                                        <div class="ur-date">Diulas pada: {{ $review->created_at->format('d M Y, H:i') }}</div>
                                    </div>
                                    {{-- Status Badge --}}
                                    @if($review->is_approved)
                                        <span class="ur-badge approved">
                                            <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Ditampilkan
                                        </span>
                                    @else
                                        <span class="ur-badge pending">
                                            <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Menunggu Persetujuan
                                        </span>
                                    @endif
                                </div>

                                {{-- Bintang --}}
                                <div class="ur-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="ur-star {{ $i <= $review->rating ? 'on' : 'off' }}">★</span>
                                    @endfor
                                    <span style="font-size:0.72rem;color:#9CA3AF;margin-left:4px;font-weight:700;">{{ $review->rating }}/5</span>
                                </div>

                                {{-- Komentar --}}
                                <div class="ur-comment">{{ $review->comment }}</div>

                                {{-- Gambar --}}
                                @if($review->images->count() > 0)
                                    <div class="ur-imgs">
                                        @foreach($review->images as $img)
                                            <img src="{{ asset('storage/' . $img->image_path) }}"
                                                 class="ur-img-thumb"
                                                 onclick="window.open(this.src)"
                                                 title="Klik untuk perbesar">
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Info jika masih pending --}}
                                @if(!$review->is_approved)
                                    <p style="font-size:0.75rem; color:#92400E; background:#FFF7ED; padding:8px 12px; border-radius:6px; margin:0; display:inline-flex; align-items:center; gap:6px;">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Ulasan Anda sedang direview oleh admin dan akan segera ditampilkan.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Pagination --}}
                @if($reviews->hasPages())
                    <div style="margin-top: 20px;">{{ $reviews->links() }}</div>
                @endif

            @else
                <div class="ur-empty">
                    <div class="ur-empty-icon">
                        <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#4F46E5">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <h3>Belum Ada Ulasan</h3>
                    <p>Kamu bisa memberi ulasan setelah pesanan berstatus <strong>Selesai</strong>.</p>
                    <a href="{{ route('customer.orders.index') }}" class="ur-shop-btn">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Lihat Pesanan Saya
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>