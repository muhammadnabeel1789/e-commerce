<?php

namespace App\Http\Controllers\Kurir;

use App\Http\Controllers\Controller;
use App\Models\DeliveryProof;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KurirController extends Controller
{
    // ──────────────────────────────────────────────
    // DASHBOARD KURIR
    // Tampilkan statistik & pesanan yang ditugaskan
    // ──────────────────────────────────────────────
    public function dashboard()
    {
        $courierId = Auth::id();

        $stats = [
            'assigned'  => Order::where('courier_id', $courierId)->where('courier_task_status', 'assigned')->count(),
            'picked_up' => Order::where('courier_id', $courierId)->where('courier_task_status', 'picked_up')->count(),
            'delivered' => Order::where('courier_id', $courierId)->where('courier_task_status', 'delivered')->count(),
            'total'     => Order::where('courier_id', $courierId)->count(),
        ];

        // Pesanan aktif hari ini (assigned + picked_up)
        $activeOrders = Order::with(['items', 'deliveryProofs'])
            ->where('courier_id', $courierId)
            ->whereIn('courier_task_status', ['assigned', 'picked_up'])
            ->latest()
            ->take(5)
            ->get();

        // Pesanan selesai terbaru
        $recentDelivered = Order::with(['deliveryProofs'])
            ->where('courier_id', $courierId)
            ->where('courier_task_status', 'delivered')
            ->latest('delivered_at')
            ->take(5)
            ->get();

        return view('kurir.dashboard', compact('stats', 'activeOrders', 'recentDelivered'));
    }

    // ──────────────────────────────────────────────
    // LIST SEMUA PESANAN YANG DITUGASKAN KE KURIR INI
    // ──────────────────────────────────────────────
    public function index(Request $request)
    {
        $courierId = Auth::id();
        $status    = $request->query('status', 'aktif');

        $query = Order::with(['items', 'deliveryProofs'])
            ->where('courier_id', $courierId);

        if ($status === 'aktif') {
            $query->whereIn('courier_task_status', ['assigned', 'picked_up']);
        } elseif ($status === 'selesai') {
            $query->where('courier_task_status', 'delivered');
        }

        $orders    = $query->latest()->paginate(10);
        $activeTab = $status;

        // Hitung untuk badge tab
        $counts = [
            'aktif'  => Order::where('courier_id', $courierId)->whereIn('courier_task_status', ['assigned', 'picked_up'])->count(),
            'selesai'=> Order::where('courier_id', $courierId)->where('courier_task_status', 'delivered')->count(),
        ];

        return view('kurir.orders.index', compact('orders', 'activeTab', 'counts'));
    }

    // ──────────────────────────────────────────────
    // DETAIL PESANAN (dari sudut pandang kurir)
    // ──────────────────────────────────────────────
    public function show($id)
    {
        $order = Order::with(['items.product', 'items.variant', 'deliveryProofs', 'user'])
            ->where('courier_id', Auth::id())
            ->findOrFail($id);

        $pickUpProof   = $order->deliveryProofs->where('type', 'pick_up')->last();
        $deliveredProof = $order->deliveryProofs->where('type', 'delivered')->last();

        return view('kurir.orders.show', compact('order', 'pickUpProof', 'deliveredProof'));
    }

    // ──────────────────────────────────────────────
    // KONFIRMASI PICK UP (Ambil paket dari toko)
    // Kurir upload foto paket saat diambil
    // ──────────────────────────────────────────────
    public function confirmPickUp(Request $request, $id)
    {
        $order = Order::where('courier_id', Auth::id())
            ->where('courier_task_status', 'assigned')
            ->findOrFail($id);

        $request->validate([
            'photo'    => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // max 5MB
            'notes'    => 'nullable|string|max:500',
        ], [
            'photo.required' => 'Foto bukti pengambilan wajib diupload.',
            'photo.image'    => 'File harus berupa gambar.',
            'photo.max'      => 'Ukuran foto maksimal 5MB.',
        ]);

        // Simpan foto
        $photoPath = $request->file('photo')->store('delivery-proofs/pick-up', 'public');

        // Buat record bukti
        DeliveryProof::create([
            'order_id'   => $order->id,
            'courier_id' => Auth::id(),
            'type'       => 'pick_up',
            'photo_path' => $photoPath,
            'notes'      => $request->notes,
        ]);

        // Update status pesanan
        $order->courier_task_status = 'picked_up';
        $order->picked_up_at        = now();
        $order->save();

        return redirect()
            ->route('kurir.orders.show', $order->id)
            ->with('success', '✅ Konfirmasi pengambilan berhasil! Paket dalam perjalanan.');
    }

    // ──────────────────────────────────────────────
    // KONFIRMASI DELIVERED (Paket sudah diterima customer)
    // Kurir upload foto saat menyerahkan ke penerima
    // ──────────────────────────────────────────────
    public function confirmDelivered(Request $request, $id)
    {
        $order = Order::where('courier_id', Auth::id())
            ->where('courier_task_status', 'picked_up')
            ->findOrFail($id);

        $request->validate([
            'photo'    => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'notes'    => 'nullable|string|max:500',
        ], [
            'photo.required' => 'Foto bukti pengiriman wajib diupload.',
            'photo.image'    => 'File harus berupa gambar.',
            'photo.max'      => 'Ukuran foto maksimal 5MB.',
        ]);

        // Simpan foto
        $photoPath = $request->file('photo')->store('delivery-proofs/delivered', 'public');

        // Buat record bukti
        DeliveryProof::create([
            'order_id'   => $order->id,
            'courier_id' => Auth::id(),
            'type'       => 'delivered',
            'photo_path' => $photoPath,
            'notes'      => $request->notes,
        ]);

        // Update status pesanan + update status order utama → shipped
        // (Admin tetap yang mengubah ke completed setelah konfirmasi customer)
        $order->courier_task_status = 'delivered';
        $order->delivered_at        = now();

        // Jika order masih processing, otomatis update ke shipped
        if ($order->status === 'processing') {
            $order->status     = 'shipped';
            $order->shipped_at = now();
            // Generate resi otomatis jika belum ada
            if (empty($order->tracking_number)) {
                $courier = strtoupper(substr($order->courier_name ?? 'REG', 0, 3));
                $order->tracking_number = $courier . '-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
            }
        }

        $order->save();

        return redirect()
            ->route('kurir.orders.show', $order->id)
            ->with('success', '🎉 Pengiriman berhasil dikonfirmasi! Foto bukti telah tersimpan.');
    }
}