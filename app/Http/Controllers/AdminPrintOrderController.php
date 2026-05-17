<?php

namespace App\Http\Controllers;

use App\Models\PrintOrder;
use Illuminate\Http\Request;

class AdminPrintOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PrintOrder::with(['user.profile'])->latest();

        if ($status = $request->query('status')) {
            $query->where('order_status', $status);
        }

        $printOrders = $query->paginate(15);

        return view('admin.print-orders.index', compact('printOrders'));
    }

    public function updateStatus(Request $request, PrintOrder $printOrder)
    {
        $validated = $request->validate([
            'order_status' => 'required|in:process,printing,done,cancel',
        ]);

        $printOrder->update($validated);

        return redirect()->back()->with('success', "Status cetak #{$printOrder->order_code} berhasil diperbarui!");
    }
}
