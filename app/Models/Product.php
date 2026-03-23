<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
        'description',
        'price',
        'stock',
        'color',
        'image',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
    ];

    // Auto-append image_url to JSON
    protected $appends = ['image_url'];

public function getImageUrlAttribute(): ?string
{
    return $this->image
        ? asset('storage/' . $this->image)   // ✅ no extra "products/"
        : asset('images/no-image.png');
}




    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'products_id');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_details', 'products_id', 'orders_id')
            ->withPivot('qty', 'price');
    }
}
