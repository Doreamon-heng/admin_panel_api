<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;


class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    public function store(Request $request)
    {
        $permission = Permission::create($request->only('name', 'description'));
        return response()->json($permission, 201);
    }

    public function show($id)
    {
        $permission = Permission::findOrFail($id);
        return response()->json($permission);
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->update($request->only('name', 'description'));
        return response()->json($permission);
    }   

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return response()->json(null, 204);
    }

    public function features($id)
    {
        $permission = Permission::findOrFail($id);
        $features = $permission->features; // Assuming a relationship is defined in the Permission model
        return response()->json($features);
    }

    public function assignFeatures(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $features = $request->input('features', []); // Expecting an array of feature names

        // Sync features (this will replace existing features with the new ones)
        $permission->features()->sync($features); // Assuming a relationship is defined in the Permission model

        return response()->json(['message' => 'Features assigned successfully']);
    }

    public function removeFeature($permissionId, $featureId)
    {
        $permission = Permission::findOrFail($permissionId);
        $permission->features()->detach($featureId); // Assuming a relationship is defined in the Permission model

        return response()->json(['message' => 'Feature removed successfully']);
    }
}

