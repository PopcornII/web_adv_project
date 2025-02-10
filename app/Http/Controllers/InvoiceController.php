<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display the invoice list with search and filter options.
     */
    public function index(Request $request)
    {
        // Search and filter invoices
        $query = Order::query()->with('user')->latest();

        if ($request->has('search') && $request->search) {
            $query->where('id', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $invoices = $query->paginate(10);
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show a specific invoice's details.
     */
    public function show($id)
    {
        $invoice = Order::with( 'user', 'orderItems.menuItem')->findOrFail($id);
        if (request()->ajax()) {
            return view('invoices.show', compact('invoice'));
        }
        return view('invoices.show', compact('invoice')); // Default fallback
    }

    /**
     * Generate and download the PDF invoice.
     */
    public function downloadPdf($id)
    {
        $invoice = Order::with('orderItems.menuItem')->findOrFail($id);
        $pdf = PDF::loadView('invoices.pdf', compact('invoice'))
            ->setPaper('a5', 'portrait');

        return $pdf->download('invoice_' . $id . '.pdf');
    }

    /**
     * Update the order status (e.g., pending, completed, canceled).
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,canceled'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $validated['status']]);

        return redirect()->route('invoices.index')->with('success', 'Order status updated successfully.');
    }

}
