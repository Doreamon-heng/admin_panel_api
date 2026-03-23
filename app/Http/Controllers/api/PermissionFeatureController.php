<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermissionFeature;

class PermissionFeatureController extends Controller
{
    public function index()
    {
        return PermissionFeature::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
            'feature_name' => 'required|string|max:255',
        ]);

        return PermissionFeature::create($request->all());
    }

    public function show($id)
    {
        return PermissionFeature::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $permissionFeature = PermissionFeature::findOrFail($id);

        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
            'feature_name' => 'required|string|max:255',
        ]);

        $permissionFeature->update($request->all());

        return $permissionFeature;
    }

    public function destroy($id)
    {
        $permissionFeature = PermissionFeature::findOrFail($id);
        $permissionFeature->delete();

        return response()->json(null, 204);
    }   

    public function featuresByPermission($permissionId)
    {
        return PermissionFeature::where('permission_id', $permissionId)->get();
    }

    public function assignFeatures(Request $request, $permissionId)
    {
        $request->validate([
            'features' => 'required|array',
            'features.*' => 'required|string|max:255',
        ]);

        // Delete existing features for the permission
        PermissionFeature::where('permission_id', $permissionId)->delete();

        // Insert new features
        $features = $request->input('features');
        foreach ($features as $feature) {
            PermissionFeature::create([
                'permission_id' => $permissionId,
                'feature_name' => $feature,
            ]);
        }

        return response()->json(['message' => 'Features assigned successfully']);
    }

    public function removeFeature($permissionId, $featureId)
    {
        $permissionFeature = PermissionFeature::where('permission_id', $permissionId)
            ->where('id', $featureId)
            ->firstOrFail();

        $permissionFeature->delete();

        return response()->json(['message' => 'Feature removed successfully']);
    }

    public function removeAllFeatures($permissionId)
    {
        PermissionFeature::where('permission_id', $permissionId)->delete();

        return response()->json(['message' => 'All features removed successfully']);
    }   

    public function getAllFeatures()
    {
        return PermissionFeature::all();
    }

    public function getFeaturesByPermission($permissionId)
    {
        return PermissionFeature::where('permission_id', $permissionId)->get();
    }

}
