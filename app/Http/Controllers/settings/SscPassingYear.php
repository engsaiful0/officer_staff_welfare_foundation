<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SscPassingYear as SscPassingYearModel;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class SscPassingYear extends Controller
{
  public function index()
  {
    return view('content.settings.ssc-passing-year');
  }

  public function getSscPassingYear(Request $request)
  {
    $passingYears = SscPassingYearModel::all();
    return response()->json([
      'data' => $passingYears,
    ]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'passing_year_name' => 'required|numeric|unique:ssc_passing_years,passing_year_name',
    ]);
    $user = Auth::user();
    $userId = $user->id;
    $passingYear = SscPassingYearModel::create([
      'passing_year_name' => $request->passing_year_name,
      'user_id' => $userId,
    ]);

    return response()->json($passingYear, Response::HTTP_CREATED);
  }

  public function update(Request $request, $id)
  {
    $request->validate([
      'passing_year_name' => 'required|numeric|unique:ssc_passing_years,passing_year_name,' . $id,
    ]);

    $passingYear = SscPassingYearModel::findOrFail($id);
    $passingYear->update([
      'passing_year_name' => $request->passing_year_name,
    ]);

    return response()->json($passingYear);
  }

  public function destroy($id)
  {
    $passingYear = SscPassingYearModel::findOrFail($id);
    $passingYear->delete();

    return response()->json(null, Response::HTTP_NO_CONTENT);
  }
}
