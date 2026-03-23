<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $r = request();
        $validator = Validator::make($r->all(), [
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date',
            'group_by'  => 'nullable|in:day,month,product',
            'per_page'  => 'nullable|integer',
            'limit'     => 'nullable|integer|min:1|max:2000',
            'export'    => 'nullable|in:csv,json',
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => "validator error",
                "errors" => $validator->errors()
            ], 422);
        }

        $dateFrom = $r->input('date_from') ? Carbon::parse($r->input('date_from'))->startOfDay() : null;
        $dateTo   = $r->input('date_to')   ? Carbon::parse($r->input('date_to'))->endOfDay()   : null;

        $ordersQuery = Order::query();
        if ($dateFrom && $dateTo) {
            $ordersQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
        } elseif ($dateFrom) {
            $ordersQuery->where('created_at', '>=', $dateFrom);
        } elseif ($dateTo) {
            $ordersQuery->where('created_at', '<=', $dateTo);
        }

        // Summary
        $totalOrders  = (clone $ordersQuery)->count();
        $totalRevenue = (clone $ordersQuery)->sum('grand_total');

        $orderIdsSub = (clone $ordersQuery)->select('id');
        $totalItems  = DB::table('order_details')
            ->whereIn('orders_id', $orderIdsSub)
            ->sum('qty');

        $perPage = $r->input('per_page', 15);
        $orders  = (clone $ordersQuery)
            ->with(['orderDetails.product','customer','user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $result = [
            'summary' => [
                'total_orders'  => $totalOrders,
                'total_revenue' => (float) $totalRevenue,
                'total_items'   => (int) $totalItems,
            ],
            'orders' => $orders,
        ];

        // Optional grouping by product
        if ($r->filled('group_by') && $r->input('group_by') === 'product') {
            $limit   = (int) $r->input('limit', 100);
            $perPage = (int) $r->input('per_page', $limit);

            $productSalesQuery = DB::table('order_details')
                ->join('orders', 'orders.id', '=', 'order_details.orders_id')
                ->join('products', 'products.id', '=', 'order_details.products_id')
                ->when($dateFrom && $dateTo, function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('orders.created_at', [$dateFrom, $dateTo]);
                })
                ->select(
                    'products.id as product_id',
                    'products.name as product_name',
                    DB::raw('SUM(order_details.qty) as qty_sold'),
                    DB::raw('SUM(order_details.qty * order_details.price) as revenue')
                )
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('qty_sold');

            if ($r->input('export') === 'csv') {
                $filename = 'product_sales_' . now()->format('Ymd_His') . '.csv';
                $headers = [
                    'Content-Type'        => 'text/csv',
                    'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                ];

                $callback = function () use ($productSalesQuery) {
                    $out = fopen('php://output', 'w');
                    fputcsv($out, ['product_id','product_name','qty_sold','revenue']);
                    $productSalesQuery->chunk(500, function($rows) use ($out) {
                        foreach ($rows as $r) {
                            fputcsv($out, [
                                (int) $r->product_id,
                                $r->product_name,
                                (int) $r->qty_sold,
                                (float) $r->revenue
                            ]);
                        }
                    });
                    fclose($out);
                };

                return response()->stream($callback, 200, $headers);
            }

            $productSales = $productSalesQuery->paginate($perPage);
            $result['product_sales'] = $productSales;
        }

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "validator error",
                "errors" => $validator->errors()
            ], 422);
        }

        $report = \App\Models\Report::create($request->only('name','description'));

        return response()->json([
            "status"  => "success",
            "message" => "Report created successfully",
            "report"  => $report
        ], 201);
    }

    public function show($id)
    {
        $order = Order::with(['orderDetails.product','customer','user'])->find($id);
        if (!$order) {
            return response()->json([
                "status"  => "error",
                "message" => "Order not found"
            ], 404);
        }

        $items = $order->orderDetails->map(function ($it) {
            return [
                'product_id' => $it->products_id,
                'name'       => optional($it->product)->name,
                'qty'        => (int) $it->qty,
                'price'      => (float) $it->price,
                'line_total' => (float) ($it->qty * $it->price),
            ];
        });

        $summary = [
            'order_id'    => $order->id,
            'code'        => $order->code,
            'total_price' => (float) $order->total_price,
            'discount'    => (float) $order->discount,
            'grand_total' => (float) $order->grand_total,
            'items_count' => $items->sum('qty'),
        ];

        return response()->json([
            'status'  => 'success',
            'summary' => $summary,
            'items'   => $items,
            'order'   => $order
        ]);
    }

    public function update(Request $request, $id)
    {
        $report = \App\Models\Report::find($id);
        if (!$report) {
            return response()->json([
                "status"  => "error",
                "message" => "Report not found"
            ], 404);
        }

        $report->update($request->only('name','description'));

        return response()->json([
            "status"  => "success",
            "message" => "Report updated successfully",
            "report"  => $report
        ]);
    }

    public function destroy($id)
    {
        $report = \App\Models\Report::find($id);
        if (!$report) {
            return response()->json([
                "status"  => "error",
                "message" => "Report not found"
            ], 404);
        }

        $report->delete();

        return response()->json([
            "status"  => "success",
            "message" => "Report deleted successfully"
        ]);
    }
}
