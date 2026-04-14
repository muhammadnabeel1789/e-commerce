<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil input filter tanggal (default: bulan ini jika ingin summary, atau kosong jika ingin semua)
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Order::query()->with('user');

        // 2. Terapkan Filter Tanggal jika ada
        if ($startDate && $endDate) {
            $start = \Carbon\Carbon::parse($startDate)->startOfDay();
            $end = \Carbon\Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        // 3. Hitung Agregasi (Statistik) untuk Pesanan Berbayar
        $paidStatuses = ['paid', 'processing', 'shipped', 'completed'];
        
        // Buat query clone untuk perhitungan statistik (tanpa pagination)
        $statsQuery = clone $query;
        $statsQuery->whereIn('status', $paidStatuses);

        $totalRevenue = $statsQuery->sum('total');
        $totalOrders = $statsQuery->count();
        
        $totalItemsSold = \Illuminate\Support\Facades\DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', $paidStatuses)
            ->where(function($q) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $q->whereBetween('orders.created_at', [
                        \Carbon\Carbon::parse($startDate)->startOfDay(),
                        \Carbon\Carbon::parse($endDate)->endOfDay()
                    ]);
                }
            })
            ->sum('quantity');

        // 4. Ambil Data dengan Pagination
        $orders = $query->latest()->paginate(10);

        return view('admin.orders.index', compact(
            'orders', 'totalRevenue', 'totalOrders', 'totalItemsSold', 
            'startDate', 'endDate'
        ));
    }

    public function show($id)
    {
        $order = Order::with([
            'items.product',
            'items.product.images',
            'items.variant',
            'items.variant.image',
            'user',
            'courier',
            'deliveryProofs',
        ])->findOrFail($id);
 
        // Ambil semua user dengan role kurir untuk dropdown assign kurir
        $couriers = \App\Models\User::where('role', 'kurir')->orderBy('name')->get();
 
        return view('admin.orders.show', compact('order', 'couriers'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status'          => 'required|in:pending,paid,processing,shipped,completed,cancelled',
            'tracking_number' => 'nullable|string|max:255',
        ]);

        $newStatus = $request->status;

        // Cek apakah metode pembayaran adalah COD
        $isCOD = strtolower($order->payment_method ?? '') === 'cod';

        // ── Validasi alur status ──
        $allowedTransitions = [
            'pending'    => $isCOD ? ['processing', 'cancelled'] : ['paid', 'cancelled'],
            'paid'       => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped'    => ['completed'],
            'completed'  => [],
            'cancelled'  => [],
        ];

        $currentStatus = $order->status;

        if ($currentStatus === $newStatus) {
            return redirect()->back()->with('info', 'Status tidak berubah.');
        }

        if (!in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])) {
            $msg = match(true) {
                in_array($currentStatus, ['completed', 'cancelled']) =>
                    "Pesanan dengan status '{$currentStatus}' tidak bisa diubah lagi.",
                default =>
                    "Tidak bisa mengubah status dari '{$currentStatus}' ke '{$newStatus}'.",
            };
            return redirect()->back()->with('error', $msg);
        }

        // ── Update Status ──
        $order->status = $newStatus;

        // ── Logika otomatis saat Processing (Dikemas) ──
        if ($newStatus === 'processing') {
            if (empty($request->tracking_number)) {
                if(empty($order->tracking_number)) {
                    $courier = strtoupper(substr($order->courier_name ?? 'REG', 0, 3));
                    $order->tracking_number = $courier . '-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
                }
            } else {
                $order->tracking_number = $request->tracking_number;
            }
        }

        // ── Logika otomatis saat Shipped ──
        if ($newStatus === 'shipped') {
            if (is_null($order->shipped_at)) {
                $order->shipped_at = now();
            }
        }

        // ── Logika otomatis saat Paid ──
        if ($newStatus === 'paid') {
            $order->payment_status = 'paid';
        }

        // ── Jika admin cancel langsung → kembalikan stok ──
        if ($newStatus === 'cancelled') {
            $this->restoreStock($order);

            if ($order->cancel_request_status === 'pending') {
                $order->cancel_request_status = 'approved';
            }
        }

        $order->save();

        $successMessages = [
            'paid'       => '✅ Pembayaran dikonfirmasi. Pesanan siap diproses.',
            'processing' => '📦 Pesanan sedang dikemas.',
            'shipped'    => '🚚 Pesanan dikirim! Resi: ' . $order->tracking_number,
            'completed'  => '🎉 Pesanan selesai!',
            'cancelled'  => '❌ Pesanan dibatalkan. Stok produk telah dikembalikan.',
        ];

        $msg = $successMessages[$newStatus] ?? 'Status pesanan diperbarui.';

        return redirect()->route('admin.orders.show', $order->id)->with('success', $msg);
    }

    // ──────────────────────────────────────────────
    // APPROVE permintaan batal dari customer
    // ── Stok dikembalikan saat disetujui ──
    // ──────────────────────────────────────────────
    public function approveCancelRequest($id)
    {
        $order = Order::with('items')->findOrFail($id);

        if ($order->cancel_request_status !== 'pending') {
            return redirect()->back()->with('error', 'Tidak ada permintaan pembatalan yang perlu disetujui.');
        }

        // Pastikan status masih bisa dibatalkan
        $nonCancellableStatuses = ['shipped', 'completed', 'cancelled'];
        if (in_array($order->status, $nonCancellableStatuses)) {
            $order->cancel_request_status = 'rejected';
            $order->cancel_reject_reason  = 'Pesanan sudah dalam status ' . $order->status . ', tidak dapat dibatalkan.';
            $order->save();
            return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah ' . $order->status . '.');
        }

        // ✅ Kembalikan stok semua item di pesanan ini
        $this->restoreStock($order);

        $order->status                = 'cancelled';
        $order->cancel_request_status = 'approved';
        $order->save();

        return redirect()->back()->with('success', '✅ Permintaan pembatalan disetujui. Pesanan dibatalkan & stok dikembalikan.');
    }

    // ──────────────────────────────────────────────
    // REJECT permintaan batal dari customer
    // ──────────────────────────────────────────────
    public function rejectCancelRequest(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->cancel_request_status !== 'pending') {
            return redirect()->back()->with('error', 'Tidak ada permintaan pembatalan yang perlu ditolak.');
        }

        $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        $order->cancel_request_status = 'rejected';
        $order->cancel_reject_reason  = $request->reject_reason;
        $order->save();

        return redirect()->back()->with('success', '❌ Permintaan pembatalan telah ditolak.');
    }

    // ──────────────────────────────────────────────
    // HELPER: Kembalikan stok produk & varian
    // ──────────────────────────────────────────────
    private function restoreStock(Order $order): void
    {
        // Load items jika belum
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            // Kembalikan stok produk utama
            Product::where('id', $item->product_id)
                ->increment('stock', $item->quantity);

            // Kembalikan stok varian jika ada
            if ($item->product_variant_id) {
                ProductVariant::where('id', $item->product_variant_id)
                    ->increment('stock', $item->quantity);
            }
        }
    }

     // ──────────────────────────────────────────────
    // ASSIGN KURIR ke pesanan (oleh admin)
    // ──────────────────────────────────────────────
    public function assignCourier(\Illuminate\Http\Request $request, $id)
    {
        $order = \App\Models\Order::findOrFail($id);
 
        $request->validate([
            'courier_id' => 'required|exists:users,id',
        ]);
 
        // Pastikan user yang dipilih memiliki role kurir
        $courier = \App\Models\User::findOrFail($request->courier_id);
        if ($courier->role !== 'kurir') {
            return redirect()->back()->with('error', 'User yang dipilih bukan kurir.');
        }
 
        // Pesanan harus dalam status processing atau shipped untuk ditugaskan ke kurir
        if (!in_array($order->status, ['processing', 'shipped'])) {
            return redirect()->back()->with('error', 'Kurir hanya bisa ditugaskan untuk pesanan berstatus Processing atau Shipped.');
        }
 
        $order->courier_id          = $courier->id;
        $order->courier_task_status = 'assigned';
        $order->save();
 
        return redirect()->back()->with('success', "✅ Kurir {$courier->name} berhasil ditugaskan untuk pesanan #{$order->order_number}.");
    }
 
    // ──────────────────────────────────────────────
    // UNASSIGN KURIR dari pesanan
    // ──────────────────────────────────────────────
    public function unassignCourier($id)
    {
        $order = \App\Models\Order::findOrFail($id);
 
        // Tidak bisa unassign jika kurir sudah pick up atau delivered
        if (in_array($order->courier_task_status, ['picked_up', 'delivered'])) {
            return redirect()->back()->with('error', 'Tidak bisa melepas kurir karena sudah dalam proses pengiriman.');
        }
 
        $courierName = $order->courier->name ?? 'Kurir';
        $order->courier_id          = null;
        $order->courier_task_status = null;
        $order->save();
 
        return redirect()->back()->with('success', "Kurir {$courierName} berhasil dilepas dari pesanan #{$order->order_number}.");
    }

     public function printResi($id)
    {
        $order = Order::with([
            'items',
            'user',
            'courier',
        ])->findOrFail($id);
 
        // Resi HANYA bisa dicetak saat pesanan diproses
        if ($order->status !== 'processing') {
            return redirect()->route('admin.orders.show', $id)
                ->with('error', '🖨️ Resi hanya bisa dicetak saat pesanan berstatus Diproses (Sedang Dikemas).');
        }
 
        return view('admin.orders.print-resi', compact('order'));
    }

    /**
     * Cetak Laporan Penjualan (Hanya Transaksi Sukses)
     */
    public function report(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $paidStatuses = ['paid', 'processing', 'shipped', 'completed'];

        $query = Order::with(['user', 'items'])
            ->whereIn('status', $paidStatuses)
            ->latest();

        if ($startDate && $endDate) {
            $start = \Carbon\Carbon::parse($startDate)->startOfDay();
            $end = \Carbon\Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        // Ambil SEMUA data transaksi sukses dalam periode tersebut (tanpa pagination)
        $orders = $query->get();

        $totalRevenue = $orders->sum('total');
        $totalOrders = $orders->count();
        $totalItemsSold = $orders->sum(function($order) {
            return $order->items->sum('quantity');
        });

        // Ambil Informasi Toko untuk Laporan
        $shopSettings = [
            'shop_name'    => \App\Models\Setting::where('key', 'shop_name')->value('value') ?? 'Fashion Store',
            'shop_phone'   => \App\Models\Setting::where('key', 'shop_phone')->value('value') ?? '-',
            'shop_email'   => \App\Models\Setting::where('key', 'shop_email')->value('value') ?? '-',
            'shop_address' => \App\Models\Setting::where('key', 'shop_address')->value('value') ?? '-',
        ];

        return view('admin.orders.report', array_merge(compact(
            'orders', 'totalRevenue', 'totalOrders', 'totalItemsSold', 
            'startDate', 'endDate'
        ), $shopSettings));
    }
}