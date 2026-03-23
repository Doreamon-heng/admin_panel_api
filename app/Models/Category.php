<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'status', 'description', 'image'
    ];

    protected $appends = ['image_url'];

    // Accessor for full image URL
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            // Adjust folder name depending on where you store images
            return asset('uploads/categories/' . basename($this->image));
        }
        return asset('images/no-image.png'); // fallback
    }

    // Relationship: Category has many Products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
