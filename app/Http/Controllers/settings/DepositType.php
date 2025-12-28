<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DepositType as DepositTypeModel;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class DepositType extends Controller
{
    public function index()
    {
        return view('content.settings.deposit-type');
    }

    public function getDepositTypes(Request $request)
    {
        $depositTypes = DepositTypeModel::all();
        return response()->json([
            'data' => $depositTypes,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'deposit_type_name' => 'required|string|max:255|unique:deposit_types,deposit_type_name',
        ]);
        
        $user = Auth::user();
        $userId = $user->id;
        
        $depositType = DepositTypeModel::create([
            'deposit_type_name' => $request->deposit_type_name,
            'user_id' => $userId,
        ]);

        return response()->json($depositType, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'deposit_type_name' => 'required|string|max:255|unique:deposit_types,deposit_type_name,' . $id,
        ]);

        $depositType = DepositTypeModel::findOrFail($id);
        $depositType->update([
            'deposit_type_name' => $request->deposit_type_name,
        ]);

        return response()->json($depositType);
    }

    public function destroy($id)
    {
        $depositType = DepositTypeModel::findOrFail($id);
        $depositType->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}





