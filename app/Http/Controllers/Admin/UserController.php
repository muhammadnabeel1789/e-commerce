<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        $role = $request->get('role', 'customer');
        if ($role !== 'all') {
            $query->where('role', $role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $sortBy    = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->withCount(['orders', 'reviews'])
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total_customers'  => User::where('role', 'customer')->count(),
            'active_customers' => User::where('role', 'customer')->where('is_active', true)->count(),
            'total_admins'     => User::where('role', 'admin')->count(),
            'total_couriers'   => User::where('role', 'kurir')->count(),
            'new_this_month'   => User::where('role', 'customer')
                ->whereMonth('created_at', now()->month)->count(),
        ];

        // Hitung jumlah pengiriman kurir (courier_id) secara manual jika relasi belum didefinisikan di model
        $userIds = $users->pluck('id');
        $courierCountMap = \App\Models\Order::whereIn('courier_id', $userIds)
            ->selectRaw('courier_id, count(*) as total')
            ->groupBy('courier_id')
            ->pluck('total', 'courier_id')
            ->map(fn($v) => (int) $v);

        $users->each(function ($user) use ($courierCountMap) {
            if ($user->role === 'kurir') {
                $user->courier_orders_count = $courierCountMap[$user->id] ?? 0;
            }
        });

        return view('admin.users.index', compact('users', 'stats', 'role'));
    }

    public function show(User $user)
    {
        if ($user->role === 'kurir') {
            // Kurir: load pesanan yang dia antar (courier_id), bukan sebagai customer
            $user->load([
                'reviews'   => fn($q) => $q->latest()->take(5),
                'addresses',
            ]);

            $courierOrders = Order::with(['items'])
                ->where('courier_id', $user->id)
                ->latest()
                ->take(10)
                ->get();

            $userStats = [
                'total_orders'   => 0,
                'total_spent'    => 0,
                'total_reviews'  => $user->reviews()->count(),
                'average_rating' => $user->reviews()->avg('rating'),
                'member_since'   => $user->created_at->diffForHumans(),
            ];

            $courierStats = [
                'assigned'  => Order::where('courier_id', $user->id)->where('courier_task_status', 'assigned')->count(),
                'picked_up' => Order::where('courier_id', $user->id)->where('courier_task_status', 'picked_up')->count(),
                'delivered' => Order::where('courier_id', $user->id)->where('courier_task_status', 'delivered')->count(),
                'total'     => Order::where('courier_id', $user->id)->count(),
                'today'     => Order::where('courier_id', $user->id)
                                    ->where('courier_task_status', 'delivered')
                                    ->whereDate('delivered_at', today())
                                    ->count(),
            ];

            // Kirim $courierOrders ke view sebagai pengganti $user->orders
            return view('admin.users.show', compact('user', 'userStats', 'courierStats', 'courierOrders'));
        }

        // Customer / Admin
        $user->load([
            'orders'    => fn($q) => $q->latest()->take(10),
            'reviews'   => fn($q) => $q->latest()->take(5),
            'addresses',
        ]);

        $userStats = [
            'total_orders'   => $user->orders()->count(),
            'total_spent'    => $user->orders()->where('status', 'completed')->sum('total'),
            'total_reviews'  => $user->reviews()->count(),
            'average_rating' => $user->reviews()->avg('rating'),
            'member_since'   => $user->created_at->diffForHumans(),
        ];

        $courierStats  = null;
        $courierOrders = collect();

        return view('admin.users.show', compact('user', 'userStats', 'courierStats', 'courierOrders'));
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "User berhasil {$status}!");
    }
}