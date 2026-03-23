<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RolePermission;

class RolePermissionController extends Controller
{
    public function index()
    {
        return RolePermission::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'role_id' => 'required|integer',
            'permission_id' => 'required|integer',
        ]);

        return RolePermission::create($data);
    }

    public function show($id)
    {
        return RolePermission::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'role_id' => 'required|integer',
            'permission_id' => 'required|integer',
        ]);

        $rolePermission = RolePermission::findOrFail($id);
        $rolePermission->update($data);
        return $rolePermission;
    }

    public function destroy($id)
    {
        $rolePermission = RolePermission::findOrFail($id);
        $rolePermission->delete();
        return response()->json(['message' => 'Role-Permission association deleted']);
    }

    public function permissionsByRole($roleId)
    {
        return RolePermission::where('role_id', $roleId)->get();
    }

    public function assignPermissions(Request $request, $roleId)
    {
        $permissionIds = $request->input('permission_ids', []); // Expecting an array of permission IDs

        // Remove existing permissions for the role
        RolePermission::where('role_id', $roleId)->delete();

        // Assign new permissions
        foreach ($permissionIds as $permissionId) {
            RolePermission::create([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ]);
        }

        return response()->json(['message' => 'Permissions assigned successfully']);
    }
}
