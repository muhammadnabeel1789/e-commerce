<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resi Pengiriman #{{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            background: #f0f0f0;
            color: #111;
        }

        /* ── Wrapper halaman A6 (10cm x 15cm) ── */
        .page {
            width: 10cm;
            min-height: 15cm;
            background: #fff;
            margin: 1cm auto;
            padding: 0.5cm;
            border: 1px solid #ccc;
            box-shadow: 0 2px 8px rgba(0,0,0,.15);
            position: relative;
        }

        /* ── Header toko ── */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #111;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .store-name {
            font-size: 18px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .store-tagline {
            font-size: 9px;
            color: #555;
            margin-top: 2px;
        }

        .resi-badge {
            text-align: right;
        }

        .resi-badge .label {
            font-size: 8px;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .resi-badge .number {
            font-size: 14px;
            font-weight: 900;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }

        /* ── Barcode placeholder ── */
        .barcode-area {
            text-align: center;
            margin: 8px 0;
            padding: 6px;
            border: 1px dashed #ccc;
            border-radius: 4px;
            background: #fafafa;
        }

        .barcode-text {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            letter-spacing: 3px;
            font-weight: bold;
        }

        .barcode-sub {
            font-size: 8px;
            color: #888;
            margin-top: 2px;
        }

        /* ── Section pembatas ── */
        .section-title {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #555;
            background: #f5f5f5;
            padding: 3px 6px;
            margin: 8px -8px;
            border-top: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
        }

        /* ── Grid alamat ── */
        .address-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 6px;
        }

        .address-box {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 6px 8px;
        }

        .address-box .box-title {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            color: #888;
            margin-bottom: 4px;
            padding-bottom: 3px;
            border-bottom: 1px solid #eee;
        }

        .address-box .name {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .address-box .phone {
            font-size: 10px;
            font-weight: bold;
            color: #333;
            margin-bottom: 3px;
        }

        .address-box .addr {
            font-size: 10px;
            line-height: 1.4;
            color: #444;
        }

        /* ── Info pengiriman ── */
        .shipping-info {
            margin-top: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 4px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 10px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-row .key {
            color: #666;
            flex-shrink: 0;
            width: 40%;
        }

        .info-row .val {
            font-weight: bold;
            text-align: right;
            color: #111;
        }

        /* ── Item pesanan ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 10px;
        }

        .items-table th {
            background: #f0f0f0;
            text-align: left;
            padding: 4px 6px;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #555;
        }

        .items-table th:last-child,
        .items-table td:last-child {
            text-align: right;
        }

        .items-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: top;
        }

        .items-table .item-name {
            font-weight: bold;
        }

        .items-table .item-variant {
            font-size: 9px;
            color: #888;
        }

        /* ── Total ── */
        .totals {
            margin-top: 6px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 8px;
            font-size: 10px;
            border-bottom: 1px solid #f0f0f0;
        }

        .total-row:last-child {
            border-bottom: none;
            background: #111;
            color: #fff;
            font-weight: bold;
            font-size: 11px;
        }

        .total-row .key {
            color: inherit;
        }

        /* ── Footer / catatan ── */
        .footer-note {
            margin-top: 10px;
            padding: 6px 8px;
            background: #fffbf0;
            border: 1px solid #f0e0a0;
            border-radius: 4px;
            font-size: 9px;
            color: #666;
        }

        .footer-note .note-title {
            font-weight: bold;
            color: #b08000;
            margin-bottom: 2px;
        }

        /* ── Garis potong bawah ── */
        .cut-line {
            margin: 12px 0 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #bbb;
            font-size: 9px;
        }

        .cut-line::before,
        .cut-line::after {
            content: '';
            flex: 1;
            border-top: 1px dashed #ccc;
        }

        /* ── Tanda tangan / stempel area ── */
        .stamp-area {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
        }

        .stamp-box {
            border: 1px dashed #ccc;
            border-radius: 4px;
            width: 48%;
            padding: 6px;
            text-align: center;
            min-height: 40px;
        }

        .stamp-box .stamp-label {
            font-size: 8px;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── Print styles ── */
        @media print {
            body {
                background: #fff;
            }

            .page {
                margin: 0;
                border: none;
                box-shadow: none;
                page-break-after: avoid;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A6 portrait;
                margin: 0.3cm;
            }
        }

        /* ── Tombol print (hanya tampil di layar) ── */
        .print-toolbar {
            width: 10cm;
            margin: 0.5cm auto 0;
            display: flex;
            gap: 8px;
        }

        .btn-print {
            flex: 1;
            padding: 10px;
            background: #4f46e5;
            color: #fff;
            font-weight: bold;
            font-size: 13px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            letter-spacing: 0.5px;
        }

        .btn-print:hover {
            background: #4338ca;
        }

        .btn-back {
            padding: 10px 16px;
            background: #fff;
            color: #555;
            font-size: 13px;
            border: 1px solid #ccc;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .btn-back:hover {
            background: #f5f5f5;
        }

        .payment-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: bold;
            background: {{ strtolower($order->payment_method ?? '') === 'cod' ? '#fef3c7' : '#d1fae5' }};
            color: {{ strtolower($order->payment_method ?? '') === 'cod' ? '#92400e' : '#065f46' }};
        }
    </style>
</head>
<body>

{{-- ── Toolbar (hanya tampil di layar, tidak ikut print) ── --}}
<div class="print-toolbar no-print">
    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-back">← Kembali</a>
    <button class="btn-print" onclick="window.print()">🖨️ Cetak Resi</button>
</div>

{{-- ══════════════════════════════════════════════
     RESI UTAMA
══════════════════════════════════════════════ --}}
<div class="page">

    {{-- Header toko --}}
    <div class="header">
        <div>
            <div class="store-name">{{ $shop_name ?? 'Fashion Store' }}</div>
            <div class="store-tagline">Fashion & Clothing Store</div>
        </div>
        <div class="resi-badge">
            <div class="label">No. Pesanan</div>
            <div class="number">#{{ $order->order_number }}</div>
        </div>
    </div>

    {{-- Barcode / tracking number --}}
    @if($order->tracking_number)
    <div class="barcode-area">
        <div class="barcode-text">{{ $order->tracking_number }}</div>
        <div class="barcode-sub">Nomor Resi / Tracking</div>
    </div>
    @else
    <div class="barcode-area">
        <div class="barcode-text">{{ $order->order_number }}</div>
        <div class="barcode-sub">No. Referensi Pesanan</div>
    </div>
    @endif

    {{-- Alamat pengirim & penerima --}}
    <div class="section-title">📦 Informasi Pengiriman</div>

    <div class="address-grid">
        {{-- Pengirim --}}
        <div class="address-box">
            <div class="box-title">✉️ Pengirim</div>
            <div class="name">{{ $shop_name ?? 'Fashion Store' }}</div>
            <div class="addr">
                {{ $shop_address ?? 'Jalan Raya PLN No. 123 Jakarta, Indonesia' }}
<br>
                Telp: {{ $shop_phone ?? '+62 812 3456 7890' }}
            </div>
        </div>

        {{-- Penerima --}}
        <div class="address-box">
            <div class="box-title">📍 Penerima</div>
            <div class="name">{{ $order->recipient_name }}</div>
            <div class="phone">{{ $order->recipient_phone }}</div>
            <div class="addr">
                {{ $order->shipping_address }},
                @if($order->district) {{ $order->district }}, @endif
                {{ $order->city }},
                @if($order->province) {{ $order->province }} @endif
                @if($order->postal_code) {{ $order->postal_code }} @endif
            </div>
        </div>
    </div>

    {{-- Info pengiriman --}}
    <div class="shipping-info">
        <div class="info-row">
            <span class="key">Tanggal Order</span>
            <span class="val">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
        </div>
        @if($order->shipped_at)
        <div class="info-row">
            <span class="key">Tgl Pengiriman</span>
            <span class="val">{{ \Carbon\Carbon::parse($order->shipped_at)->format('d M Y, H:i') }} WIB</span>
        </div>
        @endif
        <div class="info-row">
            <span class="key">Layanan Kurir</span>
            <span class="val">{{ strtoupper($order->courier_name ?? 'Kurir Internal') }}</span>
        </div>
        @if($order->courier)
        <div class="info-row">
            <span class="key">Petugas Kirim</span>
            <span class="val">{{ $order->courier->name }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="key">Metode Bayar</span>
            <span class="val">
                <span class="payment-badge">
                    {{ strtoupper($order->payment_method ?? 'TRANSFER') }}
                </span>
            </span>
        </div>
    </div>

    {{-- Daftar item --}}
    <div class="section-title">🛍️ Item Pesanan</div>
    <table class="items-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th style="text-align:center">Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    <div class="item-name">{{ $item->product_name }}</div>
                    @if($item->variant_info)
                        <div class="item-variant">{{ $item->variant_info }}</div>
                    @endif
                </td>
                <td style="text-align:center">{{ $item->quantity }}</td>
                <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Total --}}
    <div class="totals">
        <div class="total-row">
            <span class="key">Subtotal Produk</span>
            <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
        </div>
        @if(($order->discount_amount ?? 0) > 0)
        <div class="total-row">
            <span class="key">Diskon</span>
            <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="total-row">
            <span class="key">Ongkos Kirim</span>
            <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span class="key">TOTAL</span>
            <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Catatan dari customer --}}
    @if($order->notes)
    <div class="footer-note">
        <div class="note-title">📝 Catatan dari Customer:</div>
        {{ $order->notes }}
    </div>
    @endif

   

</div>

<script>
    // Auto print jika ada parameter ?autoprint=1 di URL
    window.addEventListener('load', function () {
        const params = new URLSearchParams(window.location.search);
        if (params.get('autoprint') === '1') {
            setTimeout(() => window.print(), 500);
        }
    });
</script>

</body>
</html>