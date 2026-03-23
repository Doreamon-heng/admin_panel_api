<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;


class UserController extends Controller
{
    //
    function index(Request $req)
    {
        //req by paginated
        // $users = User::paginate(10);

        $users = User::paginate($req->per_page ?? 5); // req per_page or default 5
        return response()->json($users);
    }

    //create user
    function store(Request $r)
    {
        
        $validator = Validator::make($r->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
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
            'password' => bcrypt($r->password),
        ]);
        return response()->json([
            "user" => $user,
            "message" => " User Created Successfully"

        ], 201);

    }

    //get user details
    function get(Request $r, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                "status" => "error",
                "message" => "User Not Found"

            ], 404);

        } else {
            return response()->json($user);
        }
    }

    //update user
    function update(Request $r, $id){
        $user = User::find($id);
        if(!$user){
            return response()->json([
                "status"=> "error",
                "message"=> "User Not Found"
            ], 404);
        }
        $validator = Validator::make($r->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,'.$id,
            'password' => 'sometimes|required|string|min:8',
        ]);
        if($validator->fails()){
            return response()->json([
                "status"=> "validator error",
                "errors"=> $validator->errors()
            ], 422);
            
        }
        $user->update($validator->validated());
        // dd( $validator->validated());
        return response()->json([
            "status"=> "success",
            "user"=> $user,
            "message"=> " User Updated Successfully",
            
        ], 200);
    }



    //delete user
    function delete(Request $r, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                "status" => "error",
                "message" => "User Not Found"

            ], 404);
        }
        $user->delete();
        return response()->json([
            "status" => "success",
            "message" => "User Deleted Successfully"

        ], 200);
    }

}
