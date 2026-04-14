<?php

namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Http\Request;

class MidtransCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $orderId = $request->order_id;
        $transactionStatus = $request->transaction_status;

        $order = Order::where('order_number', $orderId)->first();

        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan']);
        }

        // Pembayaran berhasil
        if ($transactionStatus == 'settlement') {

            $order->update([
                'status' => 'paid',
                'payment_status' => 'paid'
            ]);
        }

        // Pending
        if ($transactionStatus == 'pending') {

            $order->update([
                'status' => 'pending'
            ]);
        }

        // Gagal / expired
        if (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {

            $order->update([
                'status' => 'cancelled'
            ]);
        }

        return response()->json(['success' => true]);
    }
}