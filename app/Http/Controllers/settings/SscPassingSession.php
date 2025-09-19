<?php

namespace App\Http\Controllers\settings;

use App\Models\SscPassingSession as SscPassingSessionModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SscPassingSession extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('content.settings.ssc-session');
    }
    public function getSscSession(Request $request)
    {
        $sscSessions = SscPassingSessionModel::all();
        return response()->json([
            'data' => $sscSessions,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        SscPassingSessionModel::create([
            'session_name' => $request->session_name,
            'user_id' => $userId,
        ]);

        return response()->json(['success' => 'SscSession saved successfully.']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sscSession = SscPassingSessionModel::find($id);
        return response()->json($sscSession);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $sscSession = SscPassingSessionModel::find($id);
        $sscSession->session_name = $request->session_name;
        $sscSession->save();

        return response()->json(['success' => 'SscSession updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        SscPassingSessionModel::find($id)->delete();

        return response()->json(['success' => 'SscSession deleted successfully.']);
    }
}
