<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'monthly');

        $revenueByMonth = Order::where('payment_status', 'paid')
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(grand_total) as total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at), MONTH(created_at)')
            ->get();

        $topProducts = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(10)
            ->get();

        $stats = [
            'total_revenue'   => Order::where('payment_status', 'paid')->sum('grand_total'),
            'total_orders'    => Order::count(),
            'completed_orders'=> Order::where('order_status', 'delivered')->count(),
            'total_products'  => Product::count(),
        ];

        return view('admin.laporan.index', compact('revenueByMonth', 'topProducts', 'stats'));
    }
}
