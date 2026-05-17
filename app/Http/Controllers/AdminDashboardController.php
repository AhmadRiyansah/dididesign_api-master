<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'    => User::count(),
            'total_products' => Product::count(),
            'total_orders'   => Order::count(),
            'total_couriers' => Courier::count(),
            'revenue'        => Order::where('payment_status', 'paid')->sum('grand_total'),
            'pending_orders' => Order::where('order_status', 'pending')->count(),
        ];

        $recentOrders = Order::with(['customer.profile'])
            ->latest()
            ->take(8)
            ->get();

        $topProducts = Product::withCount('images')
            ->orderBy('is_popular', 'desc')
            ->take(5)
            ->get();

        $availableCouriers = Courier::with('user.profile')
            ->where('is_available', true)
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'recentOrders', 'topProducts', 'availableCouriers'
        ));
    }
}
