<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; margin: 0; padding: 20px; font-size: 14px; }
        .invoice-container { max-width: 800px; margin: 0 auto; border: 1px solid #eee; padding: 30px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #000000ff; }
        .details-section { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .billing-info, .order-info { width: 48%; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background-color: #f8f9fa; font-weight: bold; }
        .totals { width: 50%; float: right; }
        .totals table th, .totals table td { border-bottom: none; padding: 6px 12px; }
        .totals table tr.grand-total { border-top: 2px solid #333; font-weight: bold; font-size: 16px; }
        .footer { clear: both; margin-top: 50px; text-align: center; color: #777; font-size: 12px; }
        .variant-text { font-size: 13px; color: #444; line-height: 1.6; }
        .variant-text span { display: block; }
        @media print {
            body { padding: 0; }
            .invoice-container { border: none; box-shadow: none; max-width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #4F46E5; color: white; border: none; cursor: pointer; border-radius: 5px;">Cetak Halaman Ini</button>
    </div>

    <div class="invoice-container">
        <div class="header">
            <div>
                <h1>{{ $shop_name ?? 'Fashion Store' }}</h1>
                <p>{{ $shop_address ?? 'Jalan Raya PLN No. 123, Jakarta, Indonesia' }}<br>Email: {{ $shop_email ?? 'FashionStore@gmail.com' }} <br>Telp: {{ $shop_phone ?? '+62 812 3456 7890' }}</p>
            </div>
            <div style="text-align: right;">
                <h2 style="margin: 0; color: #555;">INVOICE</h2>
                <p>
                    <strong>Order #:</strong> {{ $order->order_number }}<br>
                    <strong>Tanggal:</strong> {{ $order->created_at->format('d M Y') }}<br>
                    <strong>Jam:</strong> {{ $order->created_at->format('H:i') }} WIB
                </p>
            </div>
        </div>

        <div class="details-section">
            <div class="billing-info">
                <h3>Tagihan & Pengiriman Kepada:</h3>
                <p>
                    <strong>{{ $order->recipient_name }}</strong><br>
                    {{ $order->recipient_phone }}<br>
                    {{ $order->shipping_address }},<br>
                                Kel. {{ $order->village ?? '-' }}, Kec. {{ $order->district ?? '-' }},
                                {{ $order->city ?? '-' }}, {{ $order->province ?? '-' }} {{ $order->postal_code ?? '' }}
                </p>
            </div>
            <div class="order-info">
                <h3>Metode Pembayaran:</h3>
                <p>
                    {{ strtoupper($order->payment_method) }}<br>
                    <strong>Kurir:</strong> {{ strtoupper($order->courier_name) }}<br>
                    @if($order->tracking_number)
                        <strong>Resi:</strong> {{ $order->tracking_number }}
                    @endif
                </p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Varian</th>
                    <th style="text-align: right;">Harga</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>
                        @php
                            $variant = $item->variant ?? null;

                            if ($variant) {
                                $size  = $variant->size  ?? null;
                                $color = $variant->color ?? null;
                            } else {
                                $raw = $item->variant_info ?? '';
                                $raw = str_replace(['_', '|'], ' / ', $raw);

                                preg_match('/size[:\s]*([^\/_,]+)/i', $raw, $sMatch);
                                preg_match('/colou?r[:\s]*([^\/_,]+)/i', $raw, $cMatch);

                                $size  = isset($sMatch[1]) ? trim($sMatch[1]) : null;
                                $color = isset($cMatch[1]) ? trim($cMatch[1]) : null;

                                if (!$size && !$color && $raw) {
                                    $parts = preg_split('/\s*[\/,]\s*/', $raw);
                                    $size  = isset($parts[0]) ? trim($parts[0]) : null;
                                    $color = isset($parts[1]) ? trim($parts[1]) : null;
                                }
                            }
                        @endphp

                        @if($size || $color)
                            <div class="variant-text">
                                @if($size)
                                    <span>Ukuran: <strong>{{ strtoupper($size) }}</strong></span>
                                @endif
                                @if($color)
                                    <span>Warna: <strong>{{ ucfirst($color) }}</strong></span>
                                @endif
                            </div>
                        @else
                            <span style="color:#aaa;">-</span>
                        @endif
                    </td>
                    <td style="text-align: right;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <th>Subtotal Produk:</th>
                    <td style="text-align: right;">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Ongkos Kirim:</th>
                    <td style="text-align: right;">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                </tr>
                <tr class="grand-total">
                    <th>TOTAL KESELURUHAN:</th>
                    <td style="text-align: right;">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Terima kasih telah berbelanja di tempat kami!</p>
        </div>
    </div>
</body>
</html>