<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Auth;
use Validator;

class AuthController extends Controller
{
    public function register(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => "validator error",
                "errors" => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $r->name,
            'email' => $r->email,
            'password' => Hash::make($r->password),
        ]);

        return response()->json([
            "status" => "success",
            "user" => $user,
            "message" => "Registered Successfully"
        ], 201);
    }

    public function login(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "validator error",
                "errors" => $validator->errors()
            ], 422);
        }

        if (Auth::attempt($validator->validated())) {
            $user = Auth::user();

            return response()->json([
                "status" => "success",
                "access_token" => $user->createToken("access_token")->plainTextToken,
                "user" => [
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                ]
            ], 200);
        } else {
            return response()->json([
                "status" => "error",
                "message" => "Invalid Credentials"
            ], 401);
        }
    }

    public function logout(Request $r)
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            "status" => "success",
            "message" => "Logged out successfully"
        ], 200);
    }
    
}
