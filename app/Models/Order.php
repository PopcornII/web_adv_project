<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total_price', 'status'];

    /**
     * An order has many order items.
     */
    public function orderItems() 
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * An order belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function scopeIncomeByMonth($query, $month = null)
    {
        if ($month) {
            return $query->whereMonth('created_at', $month)
                         ->selectRaw('MONTH(created_at) as month, SUM(total_price) as income')
                         ->groupBy('month');
        }
    
        return $query->selectRaw('MONTH(created_at) as month, SUM(total_price) as income')
                     ->groupBy('month');
    }
    
    public function scopeOrderStatusSummary($query, $month = null)
    {
        if ($month) {
            return $query->whereMonth('created_at', $month)
                         ->selectRaw('status, COUNT(id) as count')
                         ->groupBy('status');
        }
    
        return $query->selectRaw('status, COUNT(id) as count')
                     ->groupBy('status');
    }


}