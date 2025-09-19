<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppSettings extends Controller
{
    public function index()
    {
        $settings = AppSetting::firstOrCreate([]);
        return view('content.settings.app-settings', compact('settings'));
    }

    public function update(Request $request, $id)
    {
        $settings = AppSetting::find($id);
        $data = $request->except(['_token', '_method', 'logo', 'fevicon']);
        $user = Auth::user();
        $userId = $user->id;
        $data['user_id'] = $userId;
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = 'logo.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('assets/img/branding'), $logoName);
            $data['logo'] = $logoName;
        }

        if ($request->hasFile('fevicon')) {
            $fevicon = $request->file('fevicon');
            $feviconName = 'fevicon.' . $fevicon->getClientOriginalExtension();
            $fevicon->move(public_path('assets/img/branding'), $feviconName);
            $data['fevicon'] = $feviconName;
        }

        $settings->update($data);
        return redirect()->back()->with('success', 'Settings updated successfully');
    }
}
