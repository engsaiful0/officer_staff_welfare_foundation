<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch as BranchModel;
use App\Models\Zone as ZoneModel;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class Branch extends Controller
{
    public function index()
    {
        $zones = ZoneModel::orderBy('zone_name')->get();
        return view('content.settings.branch', compact('zones'));
    }

    public function getbranch(Request $request)
    {
        $branches = BranchModel::with('zone')->get();
        return response()->json([
            'data' => $branches,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'zone_id' => 'nullable|exists:zones,id',
            'branch_name' => 'required|string|max:255|unique:branches,branch_name',
            'branch_address' => 'required|string|max:1000',
            'branch_code' => 'nullable|string|max:255',
            'branch_phone' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();
        $userId = $user->id;

        $branch = BranchModel::create([
            'zone_id' => $request->zone_id ?: null,
            'branch_name' => $request->branch_name,
            'branch_address' => $request->branch_address,
            'branch_code' => $request->branch_code,
            'branch_phone' => $request->branch_phone,
            'user_id' => $userId,
        ]);

        return response()->json($branch->load('zone'), Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'zone_id' => 'nullable|exists:zones,id',
            'branch_name' => 'required|string|max:255|unique:branches,branch_name,' . $id,
            'branch_address' => 'required|string|max:1000',
            'branch_code' => 'nullable|string|max:255',
            'branch_phone' => 'nullable|string|max:50',
        ]);

        $branch = BranchModel::findOrFail($id);
        $branch->update([
            'zone_id' => $request->zone_id ?: null,
            'branch_name' => $request->branch_name,
            'branch_address' => $request->branch_address,
            'branch_code' => $request->branch_code,
            'branch_phone' => $request->branch_phone,
        ]);

        return response()->json($branch->load('zone'));
    }

    public function destroy($id)
    {
        $branch = BranchModel::findOrFail($id);
        $branch->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
