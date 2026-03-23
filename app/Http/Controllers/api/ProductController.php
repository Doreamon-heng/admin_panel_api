<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // List products with pagination
    public function index(Request $request)
    {
        $products = Product::with('category')->paginate($request->per_page ?? 5);

        return response()->json([
            "status" => "success",
            "data"   => $products
        ]);
    }

    // Create product
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'stock'       => 'required|integer',
            'color'       => 'nullable|string|max:50',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "validator error",
                "errors" => $validator->errors()
            ], 422);
        }

        $imagePath = null;

        // Handle file upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }
        // Optional: handle base64 string uploads
        elseif ($request->image && ! $request->hasFile('image')) {
            $imageData = base64_decode($request->image);
            $imageName = uniqid().'.png';
            Storage::disk('public')->put('products/'.$imageName, $imageData);
            $imagePath = 'products/'.$imageName;
        }

        $product = Product::create([
            'name'        => $request->name,
            'price'       => $request->price,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'stock'       => $request->stock,
            'color'       => $request->color,
            'image'       => $imagePath,
        ]);

        return response()->json([
            "status"    => "success",
            "product"   => $product->load('category'),
            "message"   => "Product Created Successfully"
        ], 201);
    }

    // Get product details
    public function show($id)
    {
        $product = Product::with(['category','orders'])->find($id);

        if (!$product) {
            return response()->json([
                "status"  => "error",
                "message" => "Product not found"
            ], 404);
        }

        return response()->json([
            "status" => "success",
            "data"   => $product
        ]);
    }

    // Update product
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'description' => 'nullable|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'stock'       => 'required|integer',
            'color'       => 'nullable|string|max:50',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "validator error",
                "errors" => $validator->errors()
            ], 422);
        }

        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                "status"  => "error",
                "message" => "Product Not Found"
            ], 404);
        }

        $imagePath = $product->image;

        // Handle file upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }
        // Optional: handle base64 string uploads
        elseif ($request->image && ! $request->hasFile('image')) {
            $imageData = base64_decode($request->image);
            $imageName = uniqid().'.png';
            Storage::disk('public')->put('products/'.$imageName, $imageData);
            $imagePath = 'products/'.$imageName;
        }

        $product->update([
            'name'        => $request->name,
            'price'       => $request->price,
            'description' => $request->description,
            'category_id' => $request->category_id ?? $product->category_id,
            'stock'       => $request->stock,
            'color'       => $request->color,
            'image'       => $imagePath,
        ]);

        return response()->json([
            "status"    => "success",
            "product"   => $product->load('category'),
            "message"   => "Product Updated Successfully"
        ], 200);
    }

    // Delete product
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                "status"  => "error",
                "message" => "Product Not Found"
            ], 404);
        }

        $product->delete();

        return response()->json([
            "status"  => "success",
            "message" => "Product Deleted Successfully"
        ], 200);
    }

    // Get product with orders
    public function withOrders($id)
    {
        $product = Product::with(['category','orders'])->find($id);

        if (!$product) {
            return response()->json([
                "status"  => "error",
                "message" => "Product Not Found"
            ], 404);
        }

        return response()->json([
            "status" => "success",
            "data"   => $product
        ]);
    }
}
