<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Auth;

class OrderController extends Controller
{
    // List orders (paginated)
    public function index(Request $r)
    {
        $perPage = $r->input('per_page', 15);
        $orders = Order::with(['orderDetails.product','customer','user'])->orderBy('created_at', 'desc')->paginate($perPage);
        return response()->json($orders);
    }

    // Create order
    public function store(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            // price will be taken from product.price on server side
            'customer_id' => 'nullable|exists:customers,id',
            // discount is percentage (0-100)
            'discount' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'validator error', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        DB::beginTransaction();
        try {
            $subtotal = 0;
            // calculate subtotal using current product prices
            foreach ($data['items'] as $it) {
                $product = Product::find($it['product_id']);
                $price = $product->price ?? 0;
                $subtotal += $it['qty'] * $price;
                
            }
            $discountPercent = $data['discount'] ?? 0;
            $discountAmount = ($discountPercent / 100) * $subtotal;
            $grand = $subtotal - $discountAmount;

            $order = Order::create([
                'code' => 'ORD'.time(),
                'total_price' => $subtotal,
                'discount' => $discountPercent,
                'grand_total' => $grand,
                'customer_id' => $data['customer_id'] ?? null,
                'user_id' => Auth::id() ?? null,
            ]);

            foreach ($data['items'] as $it) {
                $product = Product::find($it['product_id']);
                $price = $product->price ?? 0;
                OrderDetail::create([
                    'orders_id' => $order->id,
                    'products_id' => $product->id,
                    'qty' => $it['qty'],
                    'price' => $price,
                ]);

                if (isset($product->stock)) {
                    $product->stock = max(0, $product->stock - $it['qty']);
                    $product->save();
                }
            }

            DB::commit();
            return response()->json(['status'=>'success','id'=>$order->id,'message'=>'Order created'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>'error','message'=>'Failed to create order','error'=>$e->getMessage()], 500);
        }
    }

    // Show order details
    public function details($id)
    {
       $order = Order::with(['orderDetails.product','customer','user'])->find($id);
        if (!$order) return response()->json(['status'=>'error','message'=>'Order not found'], 404);
        return response()->json(['status'=>'success','data'=>$order]);
    }

    // Update order (basic: update discount or customer)
    public function update(Request $r, $id)
    {
        $order = Order::find($id);
        if (!$order) return response()->json(['status'=>'error','message'=>'Order not found'], 404);

        $validator = Validator::make($r->all(), [
            'customer_id' => 'nullable|exists:customers,id',
            'discount' => 'nullable|numeric|min:0',
        ]);
        if ($validator->fails()) return response()->json(['status'=>'validator error','errors'=>$validator->errors()], 422);

        $order->update($validator->validated());
        return response()->json(['status'=>'success','order'=>$order]);
    }

    // Delete order
    public function destroy($id)
    {
        $order = Order::find($id);
        if (!$order) return response()->json(['status'=>'error','message'=>'Order not found'], 404);
        $order->delete();
        return response()->json(['status'=>'success','message'=>'Order deleted']);
    }
}
