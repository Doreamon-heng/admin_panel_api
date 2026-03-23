<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Validator;

class CustomerController extends Controller
{
    // Get all customers
    public function index(Request $r)
    {
        $customer = Customer::paginate($r->per_page ?? 5);
        return response()->json($customer);
    }

    // Create customer
    public function store(Request $r)
    {
        $validator = Validator::make($r->all(), [
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:customers,email",
            "phone" => "required|numeric",
            "address" => "nullable|string|max:300"
            
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "validator error",
                "errors" => $validator->errors(),
            ], 422);
        }

        $customer = Customer::create($validator->validated());

        return response()->json([
            "status" => "success",
            "message" => "Customer was created successfully!",
            "data" => $customer
        ], 201);
    }

    // Customer details
    public function details($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                "status" => "error",
                "message" => "Customer not found"
            ], 404);
        }
        return response()->json($customer);
    }

    // Update customer
    public function update(Request $r, $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                "status" => "error",
                "message" => "Customer Not Found"
            ], 404);
        }

        $validator = Validator::make($r->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:customers,email,' . $id,
            'phone' => 'sometimes|required|numeric',
            'address' => 'nullable|string|max:300'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "validator error",
                "errors" => $validator->errors()
            ], 422);
        }

        $customer->update($validator->validated());

        return response()->json([
            "status" => "success",
            "data" => $customer,
            "message" => "Customer Updated Successfully"
        ], 200);
    }

    // Delete customer
    public function delete($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                "status" => "error",
                "message" => "Customer Not Found"
            ], 404);
        }

        $customer->delete();

        return response()->json([
            "status" => "success",
            "message" => "Customer Deleted Successfully"
        ], 200);
    }
}
