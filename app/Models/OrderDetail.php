<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'orders_id',
        'products_id',
        'qty',
        'price',
    ];





    public function order()
    {
        return $this->belongsTo(Order::class, 'orders_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'products_id');
    }

}
