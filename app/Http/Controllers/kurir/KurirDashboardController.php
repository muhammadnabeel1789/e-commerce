<?php

namespace App\Http\Controllers\Kurir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class KurirDashboardController extends Controller
{
    public function index()
    {
        $courierId = Auth::id();

        $stats = [
            'assigned'  => Order::where('courier_id', $courierId)
                                ->where('courier_task_status', 'assigned')
                                ->count(),
            'picked_up' => Order::where('courier_id', $courierId)
                                ->where('courier_task_status', 'picked_up')
                                ->count(),
            'delivered' => Order::where('courier_id', $courierId)
                                ->where('courier_task_status', 'delivered')
                                ->count(),
            'total'     => Order::where('courier_id', $courierId)
                                ->count(),
        ];

        $activeOrders = Order::with(['items', 'deliveryProofs'])
            ->where('courier_id', $courierId)
            ->whereIn('courier_task_status', ['assigned', 'picked_up'])
            ->latest()
            ->take(5)
            ->get();

        $recentDelivered = Order::with(['deliveryProofs'])
            ->where('courier_id', $courierId)
            ->where('courier_task_status', 'delivered')
            ->latest('delivered_at')
            ->take(5)
            ->get();

        return view('kurir.dashboard', compact('stats', 'activeOrders', 'recentDelivered'));
    }
}