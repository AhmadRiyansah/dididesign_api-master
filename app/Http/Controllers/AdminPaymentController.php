<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = Order::with(['customer.profile'])
            ->latest();

        if ($status !== 'all') {
            $query->where('payment_status', $status);
        }

        $orders = $query->paginate(20);

        $stats = [
            'total_paid'    => Order::where('payment_status', 'paid')->sum('grand_total'),
            'total_pending' => Order::where('payment_status', 'pending')->count(),
            'total_orders'  => Order::count(),
        ];

        return view('admin.pembayaran.index', compact('orders', 'stats', 'status'));
    }
}
