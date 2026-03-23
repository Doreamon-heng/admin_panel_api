<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Validator;

class RoleController extends Controller
{
    //get all roles with pagination
    public function index(Request $r)
    {
        $roles = Role::paginate($r->per_page ?? 10);
        return response()->json($roles);
    }

    //create new role
    public function create(Request $r)
    {
        $validator = Validator::make($r->all(), [
            "name" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        ;


        $role = new Role();
        $role->name = $r->name;
        $role->save();
        return response()->json(['message' => 'Role created successfully', 'role' => $role], 201);
    }

    //get role details by id
    public function detail($id)
    {
        $role = Role::find($id);

        // if(!$role){
        //     return response()->json(['message' => 'Role not found', "status" => "error"], 404);
        // }
        try {
            $role = Role::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Role not found', "status" => "error"], 404);
        }
        return response()->json($role, 200);
    }


    //update role by id
    public function update(Request $r, $id)
    {
        try {
            $role = Role::find($id);
            if (!$role) {
                return response()->json(['message' => 'Role not found', "status" => "error"], 404);
            }
            $validator = Validator::make($r->all(), [
                "name" => "required",

            ]);
            if ($validator->fails()) {
                return response()->json(($validator->errors()), 422);
            }
            $role->name = $r->name ?? $role->name;
            $role->save();
            return response()->json(['message' => 'Role updated successfully', 'role' => $role], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Role not found', "status" => "error"], 404);
        }
    }

    //delete role 
    public function delete($id)
    {
        // try {
        //     $role = Role::find($id);
        //     if (!$role) {
        //         return response()->json(['message' => 'Role not found', "status" => "error"], 404);
        //     }
        //     $validator = Validator::make(['id' => $id], [
        //         "id" => "required|exists:roles,id",
        //     ]);
        //     if ($validator->fails()) {
        //         return response()->json(($validator->errors()), 422);
        //     }
        //     $role->delete();

        //     return response()->json(['message' => 'Role deleted successfully'], 200);
        // } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        //     return response()->json(['message' => 'Role not found', "status" => "error"], 404);
        // }

        $role = Role::find($id);
        if(!$role){
            return response()->json(['message' => 'Role not found', "status" => "error"], 404);
        } else {
            $role->delete();
            return response()->json(['message' => 'Role deleted successfully'], 200);
        }
    }

}
