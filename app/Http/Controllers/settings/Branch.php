<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch as BranchModel;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class Branch extends Controller
{
    public function index()
    {
        return view('content.settings.branch');
    }

    public function getbranch(Request $request)
    {
        $branches = BranchModel::all();
        return response()->json([
            'data' => $branches,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_name' => 'required|string|max:255|unique:branches,branch_name',
            'branch_address' => 'required|string|max:1000',
        ]);
        
        $user = Auth::user();
        $userId = $user->id;
        
        $branch = BranchModel::create([
            'branch_name' => $request->branch_name,
            'branch_address' => $request->branch_address,
            'user_id' => $userId,
        ]);

        return response()->json($branch, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'branch_name' => 'required|string|max:255|unique:branches,branch_name,' . $id,
            'branch_address' => 'required|string|max:1000',
        ]);

        $branch = BranchModel::findOrFail($id);
        $branch->update([
            'branch_name' => $request->branch_name,
            'branch_address' => $request->branch_address,
        ]);

        return response()->json($branch);
    }

    public function destroy($id)
    {
        $branch = BranchModel::findOrFail($id);
        $branch->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
