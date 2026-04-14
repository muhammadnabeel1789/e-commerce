<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

class OrderController extends Controller
{
    // ============================================================
    // HELPER: Setup Midtrans Config
    // ============================================================
    private function setupMidtrans()
    {
        MidtransConfig::$serverKey    = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized  = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds        = config('midtrans.is_3ds');
    }

    // ============================================================
    // HELPER: Hitung harga item
    // ============================================================
    private function resolveItemPrice(Product $product, ?ProductVariant $variant = null): float
    {
        $additional = $variant ? ($variant->additional_price ?? 0) : 0;
        return (float) ($product->price + $additional);
    }

    // ============================================================
    // 1. HALAMAN CHECKOUT
    // ============================================================
    public function checkout(Request $request)
    {
        if ($request->has('selected_items')) {
            Session::forget('direct_buy');
        }

        $addresses = Auth::user()->addresses;

        if (Session::has('direct_buy')) {
            $sessionData = Session::get('direct_buy');
            $product     = Product::with('images')->findOrFail($sessionData['product_id']);
            $variant     = $sessionData['variant_id'] ? ProductVariant::find($sessionData['variant_id']) : null;
            $price       = $this->resolveItemPrice($product, $variant);

            $directItem = (object)[
                'product'  => $product,
                'variant'  => $variant,
                'quantity' => $sessionData['quantity'],
                'price'    => $price,
            ];

            $cart             = null;
            $cartItems        = collect([$directItem]);
            $checkoutSubtotal = $price * $sessionData['quantity'];

            return view('customer.checkout.index', compact(
                'addresses', 'cart', 'directItem', 'cartItems', 'checkoutSubtotal'
            ));
        }

        $cart = Cart::where('user_id', Auth::id())
            ->with('items.product.images', 'items.variant')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        if ($request->has('selected_items')) {
            $raw         = (array) $request->input('selected_items', []);
            $selectedIds = array_values(array_filter(array_map('intval', $raw)));
            if (empty($selectedIds)) {
                return redirect()->route('cart.index')->with('error', 'Pilih minimal 1 produk untuk di-checkout!');
            }
            Session::put('selected_cart_items', $selectedIds);
        } elseif (Session::has('selected_cart_items')) {
            $selectedIds = Session::get('selected_cart_items');
        } else {
            return redirect()->route('cart.index')->with('error', 'Pilih minimal 1 produk untuk di-checkout!');
        }

        $cartItems = $cart->items->filter(fn($item) => in_array((int) $item->id, $selectedIds))->values();

        if ($cartItems->isEmpty()) {
            Session::forget('selected_cart_items');
            return redirect()->route('cart.index')->with('error', 'Item yang dipilih tidak ditemukan. Silakan pilih ulang.');
        }

        $checkoutSubtotal = 0;

        foreach ($cartItems as $item) {
            $item->final_price = $this->resolveItemPrice($item->product, $item->variant);
            $checkoutSubtotal += $item->final_price * $item->quantity;
        }

        $directItem = null;

        return view('customer.checkout.index', compact(
            'cart', 'addresses', 'directItem', 'cartItems', 'checkoutSubtotal'
        ));
    }

    // ============================================================
    // 2. PROSES SIMPAN PESANAN + GENERATE SNAP TOKEN
    // ============================================================
    public function store(Request $request)
    {
        $request->validate([
            'address_id'    => 'required|exists:user_addresses,id',
            'courier_name'  => 'required|string',
            'shipping_cost' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $address     = UserAddress::findOrFail($request->address_id);
            $isDirectBuy = Session::has('direct_buy');
            $subtotal    = 0;
            $itemDetails = [];
            $cartItems   = collect();

            if ($isDirectBuy) {
                $sessionData = Session::get('direct_buy');
                $product     = Product::findOrFail($sessionData['product_id']);
                $variant     = $sessionData['variant_id'] ? ProductVariant::find($sessionData['variant_id']) : null;
                $price       = $this->resolveItemPrice($product, $variant);
                $qty         = $sessionData['quantity'];
                $subtotal    = $price * $qty;

                $itemDetails[] = [
                    'id'       => 'PROD-' . $product->id,
                    'price'    => (int) $price,
                    'quantity' => (int) $qty,
                    'name'     => substr($product->name, 0, 50),
                ];
            } else {
                $cart        = Cart::with(['items.product', 'items.variant'])->where('user_id', Auth::id())->first();
                $selectedIds = Session::get('selected_cart_items', []);
                $cartItems   = $cart
                    ? $cart->items()->whereIn('id', $selectedIds)->with('product', 'variant')->get()
                    : collect();

                if ($cartItems->isEmpty()) {
                    return back()->with('error', 'Keranjang belanja Anda kosong.');
                }

                foreach ($cartItems as $item) {
                    $price     = $this->resolveItemPrice($item->product, $item->variant);
                    $subtotal += $price * $item->quantity;
                    $itemDetails[] = [
                        'id'       => 'PROD-' . $item->product_id,
                        'price'    => (int) $price,
                        'quantity' => (int) $item->quantity,
                        'name'     => substr($item->product->name, 0, 50),
                    ];
                }
            }

            $shippingCost = (int) $request->shipping_cost;
            $grandTotal   = $subtotal + $shippingCost;

            // Shipping cost sebagai item Midtrans
            if ($shippingCost > 0) {
                $itemDetails[] = [
                    'id'       => 'SHIPPING',
                    'price'    => $shippingCost,
                    'quantity' => 1,
                    'name'     => 'Ongkos Kirim (' . strtoupper($request->courier_name) . ')',
                ];
            }

            // Generate order number unik
            do {
                $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            } while (Order::where('order_number', $orderNumber)->exists());

            // Hitung estimasi & jarak untuk disimpan di database
            $coords = \App\Services\ShippingService::getCityCoordinates((string) $address->city, (string) $address->province);
            // Hitung berat total (re-calculate untuk keamanan data)
            $totalWeightForStorage = 0;
            if ($isDirectBuy) {
                 $sessionData = Session::get('direct_buy');
                 $product = Product::find($sessionData['product_id']);
                 $totalWeightForStorage = ($product->weight ?? 100) * $sessionData['quantity'];
            } else {
                 foreach ($cartItems as $item) {
                     $totalWeightForStorage += ($item->product->weight ?? 100) * $item->quantity;
                 }
            }
            $shippingInfo = \App\Services\ShippingService::calculate((float) $coords['lat'], (float) $coords['lng'], (int) $totalWeightForStorage);

            $order = Order::create([
                'user_id'          => Auth::id(),
                'order_number'     => $orderNumber,
                'status'           => 'pending',
                'payment_status'   => 'unpaid',
                'payment_method'   => 'midtrans',
                'courier_name'     => $request->courier_name,
                'shipping_cost'    => $shippingCost,
                'subtotal'         => $subtotal,
                'total'            => $grandTotal,
                'recipient_name'   => $address->recipient_name,
                'recipient_phone'  => $address->phone,
                'shipping_address' => $address->address,
                'district'         => $address->district ?? '',
                'village'          => $address->village ?? '',
                'city'             => $address->city,
                'province'         => $address->province ?? '',
                'postal_code'      => $address->postal_code,
                'shipping_eta'      => $shippingInfo['eta'],
                'shipping_distance' => $shippingInfo['distance_km'],
                'snap_token'       => null,
            ]);

            // Simpan item pesanan & kurangi stok
            if ($isDirectBuy) {
                $sessionData = Session::get('direct_buy');
                $product     = Product::findOrFail($sessionData['product_id']);
                $variant     = $sessionData['variant_id'] ? ProductVariant::find($sessionData['variant_id']) : null;
                $price       = $this->resolveItemPrice($product, $variant);
                $qty         = $sessionData['quantity'];

                $order->items()->create([
                    'product_id'         => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name'       => $product->name,
                    'variant_info'       => $variant?->name,
                    'quantity'           => $qty,
                    'price'              => $price,
                    'subtotal'           => $price * $qty,
                ]);

                if ($product->stock < $qty) throw new \Exception("Stok produk tidak mencukupi.");
                $product->decrement('stock', $qty);
                if ($variant) {
                    if ($variant->stock < $qty) throw new \Exception("Stok varian tidak mencukupi.");
                    $variant->decrement('stock', $qty);
                }
            } else {
                foreach ($cartItems as $item) {
                    $price = $this->resolveItemPrice($item->product, $item->variant);
                    $order->items()->create([
                        'product_id'         => $item->product_id,
                        'product_variant_id' => $item->variant?->id,
                        'product_name'       => $item->product->name,
                        'variant_info'       => $item->variant?->name,
                        'quantity'           => $item->quantity,
                        'price'              => $price,
                        'subtotal'           => $price * $item->quantity,
                    ]);

                    $prod = Product::find($item->product_id);
                    if ($prod) {
                        if ($prod->stock < $item->quantity) throw new \Exception("Stok tidak mencukupi untuk produk: {$prod->name}");
                        $prod->decrement('stock', $item->quantity);
                    }
                    if ($item->variant) {
                        if ($item->variant->stock < $item->quantity) throw new \Exception("Stok varian tidak mencukupi.");
                        $item->variant->decrement('stock', $item->quantity);
                    }
                }
            }

            // Generate Midtrans Snap Token
            $this->setupMidtrans();
            $user = Auth::user();

            $midtransParams = [
                'transaction_details' => [
                    'order_id'     => $order->order_number,
                    'gross_amount' => (int) $grandTotal,
                ],
                'item_details'     => $itemDetails,
                'customer_details' => [
                    'first_name' => $user->name,
                    'email'      => $user->email,
                    'phone'      => $address->phone,
                    'billing_address' => [
                        'first_name'   => $address->recipient_name,
                        'phone'        => $address->phone,
                        'address'      => $address->address,
                        'city'         => $address->city,
                        'postal_code'  => $address->postal_code,
                        'country_code' => 'IDN',
                    ],
                    'shipping_address' => [
                        'first_name'   => $address->recipient_name,
                        'phone'        => $address->phone,
                        'address'      => $address->address,
                        'city'         => $address->city,
                        'postal_code'  => $address->postal_code,
                        'country_code' => 'IDN',
                    ],
                ],
                'callbacks' => [
                    'finish' => route('customer.orders.show', $order->id),
                ],
            ];

            $snapToken = Snap::getSnapToken($midtransParams);
            $order->update(['snap_token' => $snapToken]);

            // Cart & session TIDAK dihapus di sini.
            // Akan dihapus di midtransCallback() setelah pembayaran dikonfirmasi sukses.
            // Ini agar jika user tutup popup Midtrans dan kembali ke checkout,
            // cart masih ada dan halaman checkout tidak error "keranjang kosong".

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status'       => 'success',
                    'snap_token'   => $snapToken,
                    'redirect_url' => route('customer.orders.show', $order->id),
                ]);
            }

            return redirect()->route('customer.orders.show', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Gagal memproses pesanan: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    // ============================================================
    // 3. DAFTAR PESANAN CUSTOMER
    // ============================================================
    public function index(Request $request)
    {
        $status        = $request->query('status');
        $validStatuses = ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'];
        $query         = Order::where('user_id', Auth::id())->orderBy('created_at', 'desc');

        if ($status && in_array($status, $validStatuses)) {
            $query->where('status', $status);
        }

        $orders    = $query->paginate(10)->withQueryString();
        $activeTab = $status ?? 'semua';
        $counts    = Order::where('user_id', Auth::id())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('customer.orders.orders', compact('orders', 'activeTab', 'counts'));
    }

    // ============================================================
    // 4. DETAIL PESANAN CUSTOMER
    // ============================================================
    public function show($id)
    {
        $order = Order::with([
                'items.product.images',
                'items.variant',
                'deliveryProofs',  // untuk bukti foto kurir
                'courier',         // untuk info nama kurir
            ])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        return view('customer.orders.order-detail', compact('order'));
    }

    // ============================================================
    // 5. CUSTOMER KONFIRMASI PESANAN DITERIMA
    //    Customer tekan "Pesanan Diterima" → status jadi completed
    // ============================================================
    public function confirmReceived(Request $request, $id)
    {
        $order = Order::where('user_id', Auth::id())
            ->where('id', $id)
            ->where('status', 'shipped') // hanya bisa jika status shipped
            ->firstOrFail();

        $order->status = 'completed';
        $order->save();

        return redirect()->route('customer.orders.show', $order->id)
            ->with('success', '🎉 Terima kasih! Pesanan telah dikonfirmasi sebagai selesai. Jangan lupa beri ulasan ya!');
    }

    // ============================================================
    // 6. WEBHOOK / CALLBACK MIDTRANS
    //    Dipanggil otomatis oleh server Midtrans setelah customer bayar
    // ============================================================
    public function midtransCallback(Request $request)
    {
        $this->setupMidtrans();

        $notif             = new \Midtrans\Notification();
        $transactionStatus = $notif->transaction_status;
        $paymentType       = $notif->payment_type;
        $orderId           = $notif->order_id;
        $fraudStatus       = $notif->fraud_status ?? null;

        $order = Order::where('order_number', $orderId)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Jangan proses lagi jika sudah selesai/dibatalkan
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return response()->json(['message' => 'Order already finalized'], 200);
        }

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $order->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'midtrans',
                    'status'         => 'paid',
                ]);
                // Hapus cart setelah pembayaran dikonfirmasi sukses
                $this->clearCartAfterPayment($order);
            } elseif ($fraudStatus === 'challenge') {
                $order->update(['payment_status' => 'challenge']);
            }
        } elseif ($transactionStatus === 'settlement') {
            // Pembayaran berhasil — status otomatis jadi 'paid'
            $order->update([
                'payment_status' => 'paid',
                'payment_method' => 'midtrans',
                'status'         => 'paid',
            ]);
            // Hapus cart setelah pembayaran dikonfirmasi sukses
            $this->clearCartAfterPayment($order);
        } elseif ($transactionStatus === 'pending') {
            // Masih menunggu pembayaran (transfer bank, dll)
            $order->update(['payment_status' => 'pending']);
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            // Pembayaran gagal/expired — kembalikan stok
            $order->update([
                'payment_status' => $transactionStatus,
                'status'         => 'cancelled',
            ]);

            foreach ($order->items as $item) {
                Product::find($item->product_id)?->increment('stock', $item->quantity);
                if ($item->product_variant_id) {
                    ProductVariant::find($item->product_variant_id)?->increment('stock', $item->quantity);
                }
            }
        }

        return response()->json(['message' => 'OK']);
    }

    // ============================================================
    // 7. INVOICE PESANAN CUSTOMER
    // ============================================================
    public function invoice($id)
    {
        $order = Order::with(['items.product', 'items.variant'])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->firstOrFail();

        return view('customer.orders.invoice', compact('order'));
    }

    // ============================================================
    // 8. HALAMAN PEMBAYARAN (jika customer ingin bayar ulang)
    // ============================================================
    public function paymentPage($orderId)
    {
        $order = Order::where('user_id', Auth::id())
            ->where('id', $orderId)
            ->firstOrFail();

        return view('customer.orders.payment', compact('order'));
    }

    // ============================================================
    // 9. BAYAR ULANG — Generate snap token baru jika expired
    // ============================================================
    public function pay(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('customer.orders.show', $order->id)
                ->with('info', 'Pesanan ini sudah dibayar.');
        }

        if ($order->status === 'cancelled') {
            return redirect()->route('customer.orders.show', $order->id)
                ->with('error', 'Pesanan ini sudah dibatalkan.');
        }

        // Jika snap token masih ada, langsung redirect ke halaman order (ada tombol bayar)
        if ($order->snap_token) {
            return redirect()->route('customer.orders.show', $order->id);
        }

        // Generate snap token baru
        try {
            $this->setupMidtrans();

            $itemDetails = [];
            foreach ($order->items as $item) {
                $itemDetails[] = [
                    'id'       => 'PROD-' . $item->product_id,
                    'price'    => (int) $item->price,
                    'quantity' => (int) $item->quantity,
                    'name'     => substr($item->product_name, 0, 50),
                ];
            }
            if ($order->shipping_cost > 0) {
                $itemDetails[] = [
                    'id'       => 'SHIPPING',
                    'price'    => (int) $order->shipping_cost,
                    'quantity' => 1,
                    'name'     => 'Ongkos Kirim',
                ];
            }

            $user           = Auth::user();
            $midtransParams = [
                'transaction_details' => [
                    'order_id'     => $order->order_number,
                    'gross_amount' => (int) $order->total,
                ],
                'item_details'     => $itemDetails,
                'customer_details' => [
                    'first_name' => $user->name,
                    'email'      => $user->email,
                    'phone'      => $order->recipient_phone,
                ],
                'callbacks' => [
                    'finish' => route('customer.orders.show', $order->id),
                ],
            ];

            $snapToken = Snap::getSnapToken($midtransParams);
            $order->update(['snap_token' => $snapToken]);

        } catch (\Exception $e) {
            return redirect()->route('customer.orders.show', $order->id)
                ->with('error', 'Gagal membuat token pembayaran: ' . $e->getMessage());
        }

        return redirect()->route('customer.orders.show', $order->id);
    }

    // ============================================================
    // HELPER: Hapus cart setelah pembayaran sukses dikonfirmasi
    // ============================================================
    private function clearCartAfterPayment(Order $order): void
    {
        // Hapus item cart berdasarkan product_id yang ada di order
        $cart = Cart::where('user_id', $order->user_id)->first();
        if ($cart) {
            $productIds = $order->items->pluck('product_id')->toArray();
            $cart->items()->whereIn('product_id', $productIds)->delete();
        }
    }

    // ============================================================
    // 10. MARK PAID — Dipanggil dari frontend setelah Snap berhasil
    //     Solusi untuk localhost yang tidak bisa menerima webhook Midtrans
    //     Di production, status sudah diupdate oleh webhook otomatis
    // ============================================================
    public function markPaid(Request $request, $id)
    {
        $order = Order::where('user_id', Auth::id())
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        // Verifikasi ke Midtrans API langsung menggunakan transaction_id dari snap result
        $this->setupMidtrans();

        try {
            // Cek status transaksi langsung ke Midtrans menggunakan order_number
            $status = \Midtrans\Transaction::status($order->order_number);

            $transactionStatus = is_array($status) ? ($status['transaction_status'] ?? null) : ($status->transaction_status ?? null);
            $fraudStatus       = is_array($status) ? ($status['fraud_status'] ?? null) : ($status->fraud_status ?? null);
            $paymentType       = is_array($status) ? ($status['payment_type'] ?? null) : ($status->payment_type ?? null);

            $isPaid = ($transactionStatus === 'settlement') ||
                      ($transactionStatus === 'capture' && $fraudStatus === 'accept');

            if ($isPaid) {
                $order->update([
                    'status'         => 'paid',
                    'payment_status' => 'paid',
                    'payment_method' => $paymentType ?? $order->payment_method,
                ]);

                return response()->json(['status' => 'paid', 'message' => 'Pembayaran dikonfirmasi']);
            }

            if ($transactionStatus === 'pending') {
                $order->update(['payment_status' => 'pending']);
                return response()->json(['status' => 'pending', 'message' => 'Pembayaran masih diproses']);
            }

            return response()->json(['status' => $transactionStatus, 'message' => 'Status: ' . $transactionStatus]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function requestCancel(Request $request, $id)
{
    $order = Order::where('id', $id)
                  ->where('user_id', auth()->id())
                  ->firstOrFail();

    // Hanya boleh request batal jika status masih pending, paid, atau processing
    $cancellableStatuses = ['pending', 'paid', 'processing'];
    if (!in_array($order->status, $cancellableStatuses)) {
        return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah dalam status ' . $order->status . '.');
    }

    // Cek apakah sudah ada permintaan sebelumnya
    if ($order->cancel_request_status === 'pending') {
        return redirect()->back()->with('info', 'Permintaan pembatalan Anda sudah diajukan dan sedang menunggu konfirmasi admin.');
    }

    $request->validate([
        'cancel_reason' => 'required|string|max:500',
    ]);

    $order->cancel_request_status = 'pending';
    $order->cancel_reason         = $request->cancel_reason;
    $order->cancel_requested_at   = now();
    // Reset reject reason jika pernah ditolak sebelumnya (pengajuan ulang)
    $order->cancel_reject_reason  = null;
    $order->save();

    return redirect()->back()->with('success', '📩 Permintaan pembatalan berhasil diajukan. Mohon tunggu konfirmasi dari admin.');
}

    // ============================================================
    // 10b. CANCEL SNAP (Dipanggil ketika user close Midtrans popup)
    // ============================================================
    public function cancelSnap(Request $request)
    {
        $snapToken = $request->snap_token;
        if (!$snapToken) return response()->json(['status' => 'error'], 400);

        // Hanya batalkan jika status masih pending/unpaid
        $order = Order::where('user_id', Auth::id())
            ->where('snap_token', $snapToken)
            ->whereIn('status', ['pending'])
            ->first();

        if ($order) {
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'cancel'
            ]);

            // Kembalikan stock
            foreach ($order->items as $item) {
                Product::find($item->product_id)?->increment('stock', $item->quantity);
                if ($item->product_variant_id) {
                    ProductVariant::find($item->product_variant_id)?->increment('stock', $item->quantity);
                }
            }

            return response()->json(['status' => 'success', 'message' => 'Pesanan dibatalkan.']);
        }
        
        return response()->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan atau sudah diproses.'], 404);
    }

    // ============================================================
    // 10c. UPDATE SNAP RESULT (Dipanggil JS sebelum redirect)
    // ============================================================
    public function updateSnapResult(Request $request)
    {
        $orderId = $request->order_id;
        if (!$orderId) return response()->json(['status' => 'error']);

        $order = Order::where('order_number', $orderId)->where('user_id', Auth::id())->first();

        if ($order) {
            $paymentType = 'midtrans';
            $status = $request->transaction_status;
            
            $updates = [
                'payment_method' => $paymentType,
            ];

            if ($status == 'settlement' || $status == 'capture') {
                $updates['status'] = 'paid';
                $updates['payment_status'] = 'paid';
                
                // Hapus cart jika belum pernah
                if ($order->payment_status != 'paid') {
                    $this->clearCartAfterPayment($order);
                }
            } elseif ($status == 'pending') {
                $updates['status'] = 'pending';
                $updates['payment_status'] = 'pending';
            }

            $order->update($updates);
        }

        return response()->json(['status' => 'success']);
    }

    // ============================================================
    // 11. CALCULATE SHIPPING (AJAX) 
    // ============================================================
    public function calculateShipping(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:user_addresses,id',
        ]);

        $address = UserAddress::where('user_id', Auth::id())->where('id', $request->address_id)->firstOrFail();
        
        // Dapatkan berat total produk di keranjang atau dari sesi pembelian langsung (direct_buy)
        $totalWeightGram = 0;
        
        if (Session::has('direct_buy')) {
            $sessionData = Session::get('direct_buy');
            $product     = Product::find($sessionData['product_id']);
            if ($product) {
                // Konversi string float ke integer atau gunakan default 100 gram
                $weight = $product->weight ? (int)$product->weight : 100;
                $totalWeightGram = $weight * $sessionData['quantity'];
            }
        } else {
            $cart = Cart::with('items.product')->where('user_id', Auth::id())->first();
            $selectedIds = Session::get('selected_cart_items', []);
            
            if ($cart && !empty($selectedIds)) {
                $cartItems = $cart->items()->whereIn('id', $selectedIds)->with('product')->get();
                foreach ($cartItems as $item) {
                    $weight = ($item->product && $item->product->weight) ? (int)$item->product->weight : 100;
                    $totalWeightGram += $weight * $item->quantity;
                }
            }
        }

        // Dapatkan koordinat kota dari alamat
        $coords = \App\Services\ShippingService::getCityCoordinates((string) $address->city, (string) $address->province);
        
        // Kalkulasi harga pengiriman
        $shippingCost = \App\Services\ShippingService::calculate((float) $coords['lat'], (float) $coords['lng'], $totalWeightGram);

        return response()->json([
            'status' => 'success',
            'cost' => $shippingCost['cost'],
            'distance_km' => $shippingCost['distance_km'],
            'weight_gram' => $totalWeightGram,
            'eta' => $shippingCost['eta'],
            'ongkir_per_km' => $shippingCost['ongkir_per_km'],
            'ongkir_per_gram' => $shippingCost['ongkir_per_gram'],
            'min_cost' => $shippingCost['min_cost'],
            'formatted_cost' => number_format($shippingCost['cost'], 0, ',', '.')
        ]);
    }
}