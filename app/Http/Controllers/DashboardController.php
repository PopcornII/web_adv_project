<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Show the dashboard view
    public function index(Request $request)
{
    $date = $request->input('date');

    // Convert date to the correct format (optional depending on your database format)
    $formattedDate = $date ? \Carbon\Carbon::parse($date)->format('Y-m-d') : null;

    $incomeData = Order::when($formattedDate, function ($query) use ($formattedDate) {
        return $query->whereDate('created_at', $formattedDate);
    })->incomeByMonth()->get();

    $orderStatusSummary = Order::orderStatusSummary()->get();
    $mostSoldItems = MenuItem::mostSoldItems($formattedDate)->get();

    return view('dashboard.index', compact('incomeData', 'orderStatusSummary', 'mostSoldItems'));
}
}
