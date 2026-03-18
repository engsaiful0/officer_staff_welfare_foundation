<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\Controller;
use App\Models\Permission as PermissionModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Permission extends Controller
{
    public function index()
    {
        return view('content.settings.permission');
    }

    public function getPermissions(Request $request)
    {
        $permissions = PermissionModel::select('id', 'name')->orderBy('id', 'desc')->get();
        return response()->json([
            'data' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        PermissionModel::create([
            'name' => $validated['name'],
        ]);

        return response()->json(['message' => 'Permission added successfully.'], Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
        ]);

        $permission = PermissionModel::findOrFail($id);
        $permission->update([
            'name' => $validated['name'],
        ]);

        return response()->json(['message' => 'Permission updated successfully.']);
    }

    public function destroy($id)
    {
        $permission = PermissionModel::findOrFail($id);
        $permission->delete();
        return response()->json(['message' => 'Permission deleted successfully.']);
    }
}

