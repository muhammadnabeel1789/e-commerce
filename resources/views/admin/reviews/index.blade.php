<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Ulasan Produk
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .rv-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }

        .rv-wrap { background: #F0F2F5; min-height: 100vh; padding: 32px 0 60px; }
        .rv-container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

        /* ── Page Title ── */
        .rv-title { font-size: 1.35rem; font-weight: 800; color: #1A1A2E; margin-bottom: 24px; }

        /* ── Stats ── */
        .rv-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 24px; }
        .rv-stat {
            background: #fff; border-radius: 12px; padding: 18px 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            display: flex; align-items: center; gap: 14px;
        }
        .rv-stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .rv-stat-label { font-size: 0.72rem; color: #AAA; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
        .rv-stat-value { font-size: 1.2rem; font-weight: 800; color: #1A1A2E; }

        /* ── Tabs ── */
        .rv-tabs { display: flex; gap: 4px; background: #fff; padding: 6px; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.07); margin-bottom: 20px; }
        .rv-tab {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 18px; border-radius: 8px;
            font-size: 0.8rem; font-weight: 700; text-decoration: none;
            color: #888; transition: all 0.18s;
            text-transform: uppercase; letter-spacing: 0.03em;
        }
        .rv-tab.active { background: #4F46E5; color: #fff; box-shadow: 0 2px 8px rgba(79,70,229,0.3); }
        .rv-tab:hover:not(.active) { background: #F5F5F5; color: #444; }
        .rv-tab-count {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 20px; height: 18px; padding: 0 5px;
            border-radius: 100px; font-size: 0.65rem; font-weight: 800;
        }
        .rv-tab.active .rv-tab-count { background: rgba(255,255,255,0.25); color: #fff; }
        .rv-tab:not(.active) .rv-tab-count { background: #F0F0F5; color: #777; }

        /* ── Review Cards ── */
        .rv-card {
            background: #fff; border-radius: 14px;
            box-shadow: 0 1px 5px rgba(0,0,0,0.07);
            margin-bottom: 14px; overflow: hidden;
            border-left: 4px solid #E5E7EB;
            transition: border-color 0.2s;
            opacity: 0; transform: translateY(8px);
            animation: rvFade 0.3s forwards;
        }
        .rv-card.pending { border-left-color: #F59E0B; }
        .rv-card.approved { border-left-color: #22C55E; }

        @keyframes rvFade { to { opacity:1; transform:translateY(0); } }
        .rv-card:nth-child(1){animation-delay:0.04s} .rv-card:nth-child(2){animation-delay:0.08s}
        .rv-card:nth-child(3){animation-delay:0.12s} .rv-card:nth-child(4){animation-delay:0.16s}
        .rv-card:nth-child(5){animation-delay:0.20s}

        .rv-card-inner { padding: 18px 20px; display: flex; gap: 16px; align-items: flex-start; }

        /* Product image */
        .rv-prod-img {
            width: 64px; height: 64px; border-radius: 10px; object-fit: cover;
            border: 1px solid #F0F0F0; flex-shrink: 0; background: #F5F5F5;
        }
        .rv-prod-img-ph {
            width: 64px; height: 64px; border-radius: 10px; background: #F5F5F5;
            border: 1px solid #EBEBEB; display: flex; align-items: center;
            justify-content: center; flex-shrink: 0;
        }

        /* Content */
        .rv-content { flex: 1; min-width: 0; }
        .rv-top-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; flex-wrap: wrap; margin-bottom: 6px; }

        .rv-product-name { font-size: 0.9rem; font-weight: 800; color: #1A1A2E; }
        .rv-reviewer { font-size: 0.78rem; color: #6B7280; margin-top: 2px; }
        .rv-reviewer span { font-weight: 700; color: #4F46E5; }

        /* Star rating */
        .rv-stars { display: flex; gap: 2px; margin-bottom: 6px; }
        .rv-star { font-size: 14px; }
        .rv-star.on { color: #F59E0B; }
        .rv-star.off { color: #D1D5DB; }

        .rv-comment {
            font-size: 0.825rem; color: #374151; background: #F9FAFB;
            padding: 10px 14px; border-radius: 8px; border-left: 3px solid #E5E7EB;
            margin-bottom: 10px; line-height: 1.6;
        }

        /* Review images */
        .rv-imgs { display: flex; gap: 8px; margin-bottom: 10px; flex-wrap: wrap; }
        .rv-img-thumb {
            width: 52px; height: 52px; border-radius: 6px; object-fit: cover;
            border: 1px solid #E5E7EB; cursor: pointer; transition: opacity 0.15s;
        }
        .rv-img-thumb:hover { opacity: 0.8; }

        /* Badge status */
        .rv-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 12px; border-radius: 100px;
            font-size: 0.7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.05em; flex-shrink: 0;
        }
        .rv-badge.pending  { background: #FFF7ED; color: #C2410C; border: 1.5px solid #FED7AA; }
        .rv-badge.approved { background: #F0FDF4; color: #15803D; border: 1.5px solid #86EFAC; }

        .rv-date { font-size: 0.7rem; color: #AAA; }

        /* Action buttons */
        .rv-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .rv-btn {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 7px 16px; border-radius: 8px;
            font-size: 0.78rem; font-weight: 700; border: none;
            cursor: pointer; transition: all 0.15s; text-decoration: none;
        }
        .rv-btn-approve { background: #F0FDF4; color: #15803D; }
        .rv-btn-approve:hover { background: #22C55E; color: #fff; }
        .rv-btn-delete  { background: #FFF1F2; color: #BE123C; }
        .rv-btn-delete:hover { background: #E11D48; color: #fff; }
        .rv-btn-approved-label {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 0.78rem; font-weight: 700; color: #15803D;
            background: #F0FDF4; padding: 7px 14px; border-radius: 8px;
        }

        /* Empty */
        .rv-empty { text-align: center; padding: 80px 20px; }
        .rv-empty-icon {
            width: 72px; height: 72px; background: #EEF2FF; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;
        }
        .rv-empty h3 { font-size: 1.05rem; font-weight: 800; color: #1A1A2E; margin-bottom: 6px; }
        .rv-empty p  { font-size: 0.85rem; color: #AAA; }

        /* Alert */
        .rv-alert {
            padding: 12px 16px; border-radius: 10px; margin-bottom: 20px;
            font-size: 0.825rem; font-weight: 600; display: flex; align-items: center; gap: 8px;
        }
        .rv-alert.success { background: #F0FDF4; border-left: 4px solid #22C55E; color: #15803D; }
        .rv-alert.info    { background: #EFF6FF; border-left: 4px solid #3B82F6; color: #1D4ED8; }

        @media(max-width: 768px) {
            .rv-stats { grid-template-columns: 1fr; }
            .rv-card-inner { flex-direction: column; }
        }
    </style>

    <div class="rv-wrap">
        <div class="rv-container">
            
            {{-- Alert --}}
            @if(session('success'))
                <div class="rv-alert success">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Stats --}}
            <div class="rv-stats">
                <div class="rv-stat">
                    <div class="rv-stat-icon" style="background:#FFF7ED;">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#EA580C">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="rv-stat-label">Menunggu Review</div>
                        <div class="rv-stat-value">{{ $pendingCount }}</div>
                    </div>
                </div>
                <div class="rv-stat">
                    <div class="rv-stat-icon" style="background:#F0FDF4;">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#16A34A">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <div class="rv-stat-label">Disetujui</div>
                        <div class="rv-stat-value">{{ $approvedCount }}</div>
                    </div>
                </div>
                <div class="rv-stat">
                    <div class="rv-stat-icon" style="background:#EEF2FF;">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#4F46E5">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="rv-stat-label">Total Ulasan</div>
                        <div class="rv-stat-value">{{ $pendingCount + $approvedCount }}</div>
                    </div>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="rv-tabs">
                <a href="{{ route('admin.reviews.index', ['filter'=>'pending']) }}"
                   class="rv-tab {{ $filter === 'pending' ? 'active' : '' }}">
                    Menunggu
                    @if($pendingCount > 0)
                        <span class="rv-tab-count">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.reviews.index', ['filter'=>'approved']) }}"
                   class="rv-tab {{ $filter === 'approved' ? 'active' : '' }}">
                    Disetujui
                    @if($approvedCount > 0)
                        <span class="rv-tab-count">{{ $approvedCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.reviews.index', ['filter'=>'all']) }}"
                   class="rv-tab {{ $filter === 'all' ? 'active' : '' }}">
                    Semua
                    <span class="rv-tab-count">{{ $pendingCount + $approvedCount }}</span>
                </a>
            </div>

            {{-- Review Cards --}}
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
                    <div class="rv-card {{ $statusClass }}">
                        <div class="rv-card-inner">

                            {{-- Foto Produk --}}
                            @if($imgUrl)
                                <img src="{{ asset('storage/' . $imgUrl) }}" class="rv-prod-img" alt="{{ $review->product->name }}">
                            @else
                                <div class="rv-prod-img-ph">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#CCC">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Konten --}}
                            <div class="rv-content">
                                <div class="rv-top-row">
                                    <div>
                                        <div class="rv-product-name">{{ $review->product->name }}</div>
                                        @if($review->variant)
                                            <div style="font-size:0.65rem; font-weight:800; color:#4F46E5; background:#EEF2FF; padding:2px 8px; border-radius:5px; display:inline-block; margin-top:2px;">
                                                VARIAN: {{ strtoupper($review->variant->name) }}
                                            </div>
                                        @endif
                                        <div class="rv-reviewer">
                                            Oleh: <span>{{ $review->user->name }}</span>
                                            &nbsp;·&nbsp;
                                            <span class="rv-date">{{ $review->created_at->format('d M Y, H:i') }}</span>
                                        </div>
                                    </div>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <span class="rv-badge {{ $statusClass }}">
                                            {{ $review->is_approved ? 'Disetujui' : 'Menunggu' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Bintang --}}
                                <div class="rv-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="rv-star {{ $i <= $review->rating ? 'on' : 'off' }}">★</span>
                                    @endfor
                                    <span style="font-size:0.72rem;color:#9CA3AF;margin-left:4px;font-weight:700;">{{ $review->rating }}/5</span>
                                </div>

                                {{-- Komentar --}}
                                <div class="rv-comment">{{ $review->comment }}</div>

                                {{-- Gambar Review --}}
                                @if($review->images->count() > 0)
                                    <div class="rv-imgs">
                                        @foreach($review->images as $img)
                                            <img src="{{ asset('storage/' . $img->image_path) }}"
                                                 class="rv-img-thumb"
                                                 onclick="window.open(this.src)"
                                                 title="Klik untuk perbesar">
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Tombol Aksi --}}
                                <div class="rv-actions">
                                    @if(!$review->is_approved)
                                        {{-- Tombol APPROVE --}}
                                        <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rv-btn rv-btn-approve">
                                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Setujui & Tampilkan
                                            </button>
                                        </form>
                                    @else
                                        <span class="rv-btn-approved-label">
                                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Sudah Tampil di Produk
                                        </span>
                                    @endif

                                    {{-- Tombol HAPUS --}}
                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" style="display:inline;"
                                          onsubmit="return confirm('Hapus ulasan dari {{ addslashes($review->user->name) }}? Tindakan ini tidak bisa dibatalkan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rv-btn rv-btn-delete">
                                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Pagination --}}
                @if($reviews->hasPages())
                    <div style="margin-top: 20px;">{{ $reviews->links() }}</div>
                @endif

            @else
                <div class="rv-empty">
                    <div class="rv-empty-icon">
                        <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#4F46E5">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <h3>
                        @if($filter === 'pending') Tidak ada ulasan yang menunggu persetujuan
                        @elseif($filter === 'approved') Belum ada ulasan yang disetujui
                        @else Belum ada ulasan sama sekali
                        @endif
                    </h3>
                    <p>Ulasan dari pelanggan akan muncul di sini setelah mereka menyelesaikan pesanan.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>