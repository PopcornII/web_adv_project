<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    /**
     * Display a list of orders.
     */
    public function index(Request $request)
    {
        // Build query for filtering orders
        $ordersQuery = Order::with('orderItems.menuItem')->latest();
        
        // Search by order ID or menu item name
        if ($request->has('search') && $request->search) {
            $ordersQuery->where('id', 'like', '%' . $request->search . '%')
                        ->orWhereHas('orderItems.menuItem', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->search . '%');
                        });
        }

        // // Filter by category
        // if ($request->has('category') && $request->category) {
        //     $ordersQuery->whereHas('orderItems.menuItem', function ($query) use ($request) {
        //         $query->where('category', $request->category);
        //     });
        // }

        $orders = $ordersQuery->get();

        // Get filtered menu items based on search and category
        $menuItemsQuery = MenuItem::query();

        if ($request->has('search') && $request->search) {
            $menuItemsQuery->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category) {
            $menuItemsQuery->where('category', $request->category);
        }

        $menuItems = $menuItemsQuery->get();
        $categories = MenuItem::distinct()->pluck('category'); // Get distinct categories for filter

        return view('orders.index', compact('orders', 'menuItems', 'categories'));
    }


    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $menuItems = MenuItem::all();
        return view('orders.create', compact('menuItems'));
    }

    /**
     * Store a new order and generate an invoice.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $total_price = 0;

        $order = Order::create([
            'user_id' => Auth::id(),
            'total_price' => 0,
            'status' => 'pending'
        ]);

        foreach ($validated['items'] as $item) {
            $menuItem = MenuItem::findOrFail($item['id']);
            $subtotal = $menuItem->price * $item['quantity'];

            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $menuItem->id,
                'quantity' => $item['quantity'],
                'price' => $menuItem->price,
                'subtotal' => $subtotal
            ]);

            $total_price += $subtotal;
        }

        $order->update(['total_price' => $total_price]);

        return response()->json(['order_id' => $order->id, 'total_price' => $total_price]);
    }

    /**
     * Display a specific order.
     */
    public function show($id)
    {
        $order = Order::with('orderItems.menuItem')->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing an order.
     */
    public function edit($id)
    {
      
        $order = Order::with('orderItems.menuItem')->findOrFail($id);
        $menuItems = MenuItem::all();
        return view('orders.edit', compact('order', 'menuItems'));
    }
    

    /**
     * Update an order.
     */
    public function update(Request $request, $id)
    {
       // Validate the request
        $request->validate([
            'order_items' => 'required|array',
            'order_items.*.id' => 'required|exists:order_items,id',
            'order_items.*.quantity' => 'required|integer|min:1',
        ]);

        // Find the order
        $order = Order::findOrFail($id);

        // Update order items
        foreach ($request->order_items as $item) {
            $orderItem = $order->orderItems()->find($item['id']);
            $orderItem->quantity = $item['quantity'];
            $orderItem->save();
        }

        // Recalculate the total price
        $totalPrice = $order->orderItems->sum(function ($item) {
            return $item->quantity * $item->menuItem->price;
        });

        // Update order total price
        $order->total_price = $totalPrice;
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Order updated successfully!');
    }

    /**
     * Delete an order.
     */
    public function destroy($id){
        $order = Order::findOrFail($id);
        $order->orderItems()->delete();
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }
}
