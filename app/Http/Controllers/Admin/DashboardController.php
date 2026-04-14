<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Stok menipis
        $lowStockProductIds = ProductVariant::where('stock', '<=', 5)
            ->distinct()
            ->pluck('product_id')
            ->toArray();

        // Stats utama
        $stats = [
            'total_products'  => Product::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_orders'    => Order::count(),
            'total_revenue'   => Order::where('status', '!=', 'cancelled')
                ->where(function ($q) {
                    $q->whereIn('payment_status', ['paid', 'settlement', 'capture'])
                      ->orWhereIn('status', ['paid', 'processing', 'shipped', 'completed']);
                })
                ->sum('total'),
            'low_stock_count'        => count($lowStockProductIds),
            'total_couriers'         => User::where('role', 'kurir')->count(),
            'active_couriers'        => User::where('role', 'kurir')->where('is_active', true)->count(),
            'orders_on_delivery'     => Order::whereNotNull('courier_id')
                ->whereIn('courier_task_status', ['assigned', 'picked_up'])->count(),
            'orders_delivered_today' => Order::whereNotNull('courier_id')
                ->where('courier_task_status', 'delivered')
                ->whereDate('delivered_at', today())->count(),
        ];

        // 5 Order terbaru
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // Produk stok menipis
        $lowStockProducts = Product::with(['images', 'variants'])
            ->where('is_active', true)
            ->whereIn('id', $lowStockProductIds)
            ->get()
            ->map(function ($product) {
                $product->low_variants = $product->variants
                    ->where('stock', '<=', 5)
                    ->sortBy('stock')
                    ->values();
                return $product;
            })
            ->sortBy(fn($p) => $p->low_variants->min('stock'))
            ->take(5)
            ->values();

        // Daftar kurir + statistik (query by courier_id, bukan user_id)
        $couriers = User::where('role', 'kurir')
            ->orderBy('name')
            ->get();

        // Hitung stats per kurir menggunakan courier_id
        $courierIds = $couriers->pluck('id');

        $assignedMap = Order::whereIn('courier_id', $courierIds)
            ->where('courier_task_status', 'assigned')
            ->selectRaw('courier_id, count(*) as total')
            ->groupBy('courier_id')
            ->pluck('total', 'courier_id')
            ->map(fn($v) => (int) $v);

        $pickedUpMap = Order::whereIn('courier_id', $courierIds)
            ->where('courier_task_status', 'picked_up')
            ->selectRaw('courier_id, count(*) as total')
            ->groupBy('courier_id')
            ->pluck('total', 'courier_id')
            ->map(fn($v) => (int) $v);

        $deliveredTodayMap = Order::whereIn('courier_id', $courierIds)
            ->where('courier_task_status', 'delivered')
            ->whereDate('delivered_at', today())
            ->selectRaw('courier_id, count(*) as total')
            ->groupBy('courier_id')
            ->pluck('total', 'courier_id')
            ->map(fn($v) => (int) $v);

        $totalDeliveredMap = Order::whereIn('courier_id', $courierIds)
            ->where('courier_task_status', 'delivered')
            ->selectRaw('courier_id, count(*) as total')
            ->groupBy('courier_id')
            ->pluck('total', 'courier_id')
            ->map(fn($v) => (int) $v);

        // Set attribute ke masing-masing kurir
        $couriers->each(function ($courier) use ($assignedMap, $pickedUpMap, $deliveredTodayMap, $totalDeliveredMap) {
            $courier->assigned_count  = $assignedMap[$courier->id]      ?? 0;
            $courier->picked_up_count = $pickedUpMap[$courier->id]      ?? 0;
            $courier->delivered_today = $deliveredTodayMap[$courier->id] ?? 0;
            $courier->total_delivered = $totalDeliveredMap[$courier->id] ?? 0;
        });

        // ── GRAFIK 1: Pendapatan per Bulan (12 bulan terakhir) ──
        $monthlyRevenue = Order::where('status', '!=', 'cancelled')
            ->where(function ($q) {
                $q->whereIn('payment_status', ['paid', 'settlement', 'capture'])
                  ->orWhereIn('status', ['paid', 'processing', 'shipped', 'completed']);
            })
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total) as revenue, COUNT(*) as orders")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Lengkapi 12 bulan terakhir (termasuk bulan yang 0 pendapatan)
        $monthlyLabels = [];
        $monthlyData   = [];
        $monthlyOrders = [];
        for ($i = 11; $i >= 0; $i--) {
            $date  = Carbon::now()->subMonths($i);
            $key   = $date->format('Y-m');
            $label = $date->locale('id')->isoFormat('MMM YYYY');
            $row   = $monthlyRevenue->firstWhere('month', $key);
            $monthlyLabels[]  = $label;
            $monthlyData[]    = $row ? (float) $row->revenue : 0;
            $monthlyOrders[]  = $row ? (int) $row->orders   : 0;
        }

        // ── GRAFIK 2: Pendapatan per Hari (30 hari terakhir) ──
        $dailyRevenue = Order::where('status', '!=', 'cancelled')
            ->where(function ($q) {
                $q->whereIn('payment_status', ['paid', 'settlement', 'capture'])
                  ->orWhereIn('status', ['paid', 'processing', 'shipped', 'completed']);
            })
            ->where('created_at', '>=', Carbon::now()->subDays(29)->startOfDay())
            ->selectRaw("DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Lengkapi 30 hari terakhir
        $dailyLabels = [];
        $dailyData   = [];
        $dailyOrders = [];
        for ($i = 29; $i >= 0; $i--) {
            $date  = Carbon::now()->subDays($i)->toDateString();
            $label = Carbon::parse($date)->locale('id')->isoFormat('D MMM');
            $row   = $dailyRevenue->firstWhere('date', $date);
            $dailyLabels[]  = $label;
            $dailyData[]    = $row ? (float) $row->revenue : 0;
            $dailyOrders[]  = $row ? (int) $row->orders   : 0;
        }

        return view('admin.dashboard', compact(
            'stats', 'recentOrders', 'lowStockProducts', 'couriers',
            'monthlyLabels', 'monthlyData', 'monthlyOrders',
            'dailyLabels', 'dailyData', 'dailyOrders'
        ));
    }
}