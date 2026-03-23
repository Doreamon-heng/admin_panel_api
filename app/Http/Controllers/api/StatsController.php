<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Customer;
use App\Models\User;

class StatsController extends Controller
{
    public function index()
    {
        return response()->json([
            'summary' => [
                'total_orders' => Order::count(),
                'total_revenue' => (float) Order::sum('grand_total'), // or total_price depending on your logic
                'total_items' => Product::count(), // or sum of product quantities if you track stock
            ],
            'products' => Product::count(),
            'categories' => Category::count(),
            'customers' => Customer::count(),
            'users' => User::count(),
        ]);
    }




}
