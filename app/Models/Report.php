<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description'];

    /**
     * A report can be linked to many orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Generate a sales report between two dates.
     */
    public static function salesReport($startDate, $endDate)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(grand_total) as total')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    /**
     * Generate an inventory report (products + stock).
     */
    public static function inventoryReport()
    {
        return Product::select('id', 'name', 'stock', 'category_id')
            ->with('category:id,name')
            ->orderBy('name', 'asc')
            ->get();
    }
}
