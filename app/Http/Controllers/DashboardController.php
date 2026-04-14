<?php

namespace App\Http\Controllers; // Namespace Customer (Root Controllers)

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order; 

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil User yang sedang login
        $userId = Auth::id();

        // Hitung Statistik Khusus User Ini
        // Sesuaikan 'status' dengan value di database Anda (misal: pending, processing, completed)
        $pendingOrders = Order::where('user_id', $userId)->where('status', 'pending')->count();
        $processingOrders = Order::where('user_id', $userId)->whereIn('status', ['processing', 'shipped', 'dikirim'])->count();
        $completedOrders = Order::where('user_id', $userId)->where('status', 'completed')->count();

        // Ambil 5 Order Terakhir User Ini
        $recentOrders = Order::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // Kirim data ke view dashboard
        return view('customer.dashboard', compact('pendingOrders', 'processingOrders', 'completedOrders', 'recentOrders'));
    }
}