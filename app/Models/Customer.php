<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Customer extends Model
{
    use SoftDeletes;



    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];


    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

   public function orders()
{
    return $this->hasMany(Order::class, 'customers_id');
}
}
 