<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    // List categories with pagination
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "validator error",
                "errors" => $validator->errors()
            ], 422);
        }

        $categories = Category::paginate($request->per_page ?? 10);

        return response()->json([
            "status" => "success",
            "data"   => $categories
        ]);
    }

    // Create category
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'status'      => 'required|boolean',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "validator error",
                "errors" => $validator->errors()
            ], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image      = $request->file('image');
            $imageName  = Str::slug($request->name) . '-' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/categories');
            $image->move($destinationPath, $imageName);
            $imagePath  = 'uploads/categories/' . $imageName;
        }

        $category = Category::create([
            'name'        => $request->name,
            'status'      => $request->status,
            'description' => $request->description,
            'image'       => $imagePath,
        ]);

        return response()->json([
            "status"   => "success",
            "category" => $category,
            "message"  => "Category Created Successfully"
        ], 201);
    }

    // Get category with products
    public function get($id)
    {
        $category = Category::with('products')->find($id);

        if (!$category) {
            return response()->json([
                "status"  => "error",
                "message" => "Category not found"
            ], 404);
        }

        return response()->json([
            "status" => "success",
            "data"   => $category
        ]);
    }

    // Update category
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'status'      => 'required|boolean',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "validator error",
                "errors" => $validator->errors()
            ], 422);
        }

        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                "status"  => "error",
                "message" => "Category Not Found"
            ], 404);
        }

        $imagePath = $category->image;
        if ($request->hasFile('image')) {
            $image      = $request->file('image');
            $imageName  = Str::slug($request->name) . '-' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/categories');
            $image->move($destinationPath, $imageName);
            $imagePath  = 'uploads/categories/' . $imageName;
        }

        $category->update([
            'name'        => $request->name,
            'status'      => $request->status,
            'description' => $request->description,
            'image'       => $imagePath,
        ]);

        return response()->json([
            "status"   => "success",
            "category" => $category,
            "message"  => "Category Updated Successfully"
        ], 200);
    }

    // Delete category
    public function delete($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                "status"  => "error",
                "message" => "Category Not Found"
            ], 404);
        }

        $category->delete();

        return response()->json([
            "status"  => "success",
            "message" => "Category Deleted Successfully"
        ], 200);
    }
}
