<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'image', // Optional if you store images
    ];

    /**
     * Relationship: A menu item can appear in multiple order items.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Get the most sold items by category
    public static function mostSoldItems($date = null)
{
    $query = self::select('menu_items.name', 'menu_items.category', \DB::raw('SUM(order_items.quantity) as total_sold'))
        ->join('order_items', 'order_items.menu_item_id', '=', 'menu_items.id')
        ->join('orders', 'orders.id', '=', 'order_items.order_id')
        ->groupBy('menu_items.id', 'menu_items.category')
        ->orderBy('total_sold', 'desc')
        ->orderBy('menu_items.category', 'asc')
        ->take(3); // Limit to the top 5 most sold items
      

    // If a specific date is passed, filter by date
    if ($date) {
        $query->whereDate('orders.created_at', $date);
    }

    return $query;
}
    
}


