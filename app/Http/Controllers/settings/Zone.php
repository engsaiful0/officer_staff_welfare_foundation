<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Zone as ZoneModel;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class Zone extends Controller
{
    public function index()
    {
        return view('content.settings.zone');
    }

    public function getZone(Request $request)
    {
        $zones = ZoneModel::all();
        return response()->json([
            'data' => $zones,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'zone_name' => 'required|string|max:255|unique:zones,zone_name',
        ]);

        $user = Auth::user();
        $userId = $user ? $user->id : null;

        $zone = ZoneModel::create([
            'zone_name' => $request->zone_name,
            'user_id' => $userId,
        ]);

        return response()->json($zone, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'zone_name' => 'required|string|max:255|unique:zones,zone_name,' . $id,
        ]);

        $zone = ZoneModel::findOrFail($id);
        $zone->update([
            'zone_name' => $request->zone_name,
        ]);

        return response()->json($zone);
    }

    public function destroy($id)
    {
        $zone = ZoneModel::findOrFail($id);
        $zone->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
