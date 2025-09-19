<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Relation as RelationModel;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class Relation extends Controller
{
    public function index()
    {
        return view('content.settings.relation');
    }

    public function getrelation(Request $request)
    {
        $relations = RelationModel::all();
        return response()->json([
            'data' => $relations,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'relation_name' => 'required|string|max:255|unique:relations,relation_name',
        ]);
        
        $user = Auth::user();
        $userId = $user->id;
        
        $relation = RelationModel::create([
            'relation_name' => $request->relation_name,
            'user_id' => $userId,
        ]);

        return response()->json($relation, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'relation_name' => 'required|string|max:255|unique:relations,relation_name,' . $id,
        ]);

        $relation = RelationModel::findOrFail($id);
        $relation->update([
            'relation_name' => $request->relation_name,
        ]);

        return response()->json($relation);
    }

    public function destroy($id)
    {
        $relation = RelationModel::findOrFail($id);
        $relation->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
