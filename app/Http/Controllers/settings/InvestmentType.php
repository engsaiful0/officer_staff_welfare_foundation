<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvestmentType as InvestmentTypeModel;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class InvestmentType extends Controller
{
    public function index()
    {
        return view('content.settings.investment-type');
    }

    public function getInvestmentTypes(Request $request)
    {
        $investmentTypes = InvestmentTypeModel::all();
        return response()->json([
            'data' => $investmentTypes,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'investment_type_name' => 'required|string|max:255|unique:investment_types,investment_type_name',
        ]);
        
        $user = Auth::user();
        $userId = $user->id;
        
        $investmentType = InvestmentTypeModel::create([
            'investment_type_name' => $request->investment_type_name,
            'user_id' => $userId,
        ]);

        return response()->json($investmentType, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'investment_type_name' => 'required|string|max:255|unique:investment_types,investment_type_name,' . $id,
        ]);

        $investmentType = InvestmentTypeModel::findOrFail($id);
        $investmentType->update([
            'investment_type_name' => $request->investment_type_name,
        ]);

        return response()->json($investmentType);
    }

    public function destroy($id)
    {
        $investmentType = InvestmentTypeModel::findOrFail($id);
        $investmentType->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

