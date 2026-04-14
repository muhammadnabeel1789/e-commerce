<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - {{ now()->format('d/m/Y') }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            color: #1a1a1a;
            line-height: 1.5;
            margin: 0;
            padding: 40px;
            background: #fff;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }

        .period {
            margin-bottom: 20px;
            font-size: 13px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            border: 1px solid #eee;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #999;
            display: block;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 800;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th {
            background: #f8f9fa;
            text-align: left;
            padding: 12px 10px;
            border-bottom: 2px solid #eee;
            text-transform: uppercase;
            font-weight: 700;
            color: #666;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }

        .status-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 100px;
            background: #eee;
        }

        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        @media print {
            body { padding: 20px; }
            .no-print { display: none; }
        }

        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #4F46E5;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(79,70,229,0.4);
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="header">
        <h1>Laporan Penjualan</h1>
        <p>{{ $shop_name }}</p>
        <div style="font-size:11px; margin-top:5px; color:#666;">
            {{ $shop_address }} <br>
            Telp: {{ $shop_phone }} | Email: {{ $shop_email }}
        </div>
    </div>

    <div class="period">
        <strong>Periode Laporan:</strong> 
        {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Awal' }} 
        s/d 
        {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : 'Sekarang' }}
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Total Pendapatan</span>
            <div class="stat-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <span class="stat-label">Total Pesanan Sukses</span>
            <div class="stat-value">{{ $totalOrders }} Transaksi</div>
        </div>
        <div class="stat-card">
            <span class="stat-label">Total Produk Terjual</span>
            <div class="stat-value">{{ $totalItemsSold }} Item</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="50">No</th>
                <th>No. Order</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th class="text-right">Total Transaksi</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $index => $order)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="font-bold">#{{ $order->order_number ?? $order->id }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <div class="font-bold">{{ $order->user->name }}</div>
                    <div style="font-size:10px; color:#999;">{{ $order->user->email }}</div>
                </td>
                <td class="text-right font-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                <td class="text-center">
                    <span class="status-badge">
                        {{ strtoupper($order->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#fdfdfd;">
                <td colspan="4" class="text-right font-bold" style="padding: 15px;">TOTAL KESELURUHAN:</td>
                <td class="text-right font-bold" style="font-size:14px; border-top: 2px solid #333;">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div>Dicetak pada: {{ now()->format('d M Y H:i:s') }}</div>
        <div>Dicetak oleh: Admin {{ $shop_name }}</div>
    </div>

    <button class="print-btn no-print" onclick="window.print()">
        🖨️ Cetak Ulang
    </button>

</body>
</html>
