<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'total_price',
        'discount',
        'grand_total',
        'customer_id',
        'user_id',
        
    ];

    /**
     * Each order belongs to one customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Each order belongs to one user (creator/admin)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * One order has many order details
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'orders_id');
    }

    /**
     * Many products through order_details pivot
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_details', 'orders_id', 'products_id')
                    ->withPivot('qty', 'price')
                    ->withTimestamps();
    }
}
