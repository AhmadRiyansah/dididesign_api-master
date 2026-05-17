<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = Order::with(['customer.profile', 'courier.profile', 'items.product'])
            ->latest();

        if ($status === 'unassigned') {
            $query->whereNull('courier_id')->where('order_status', 'process');
        } elseif ($status !== 'all') {
            $query->where('order_status', $status);
        }

        $orders = $query->paginate(15);

        $totalOrders     = Order::count();
        $unassignedCount = Order::whereNull('courier_id')->where('order_status', 'process')->count();
        $shippingCount   = Order::where('order_status', 'shipping')->count();
        $doneCount       = Order::where('order_status', 'done')->count();

        $availableCouriers = Courier::with('user.profile')
            ->where('is_available', true)
            ->get();

        return view('admin.orders.index', compact(
            'orders', 'status', 'totalOrders',
            'unassignedCount', 'shippingCount', 'doneCount',
            'availableCouriers'
        ));
    }

    public function assignCourier(Request $request, Order $order)
    {
        $validated = $request->validate([
            'courier_id' => 'required|exists:users,id',
        ]);

        $order->update(['courier_id' => $validated['courier_id']]);

        Notification::create([
            'user_id' => $validated['courier_id'],
            'title'   => 'Pesanan Baru (Admin)',
            'body'    => "Admin mengassign pesanan {$order->order_code} kepada Anda.",
            'type'    => 'order',
            'is_read' => false,
            'data'    => json_encode(['order_id' => $order->id, 'order_code' => $order->order_code]),
        ]);

        return redirect()->back()->with('success', "Kurir berhasil di-assign ke pesanan #{$order->order_code}!");
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_status' => 'required|in:process,shipping,done,cancel',
        ]);

        $order->update($validated);

        return redirect()->back()->with('success', "Status pesanan #{$order->order_code} berhasil diperbarui!");
    }
}
