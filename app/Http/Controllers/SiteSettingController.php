<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    /**
     * Show the form for editing the site settings.
     */
    public function edit()
    {
        $settings = SiteSetting::getSettings();

        return view('mpm.page.site-settings.edit', compact('settings'));
    }

    /**
     * Update the site settings.
     */
    public function update(Request $request)
    {
        $settings = SiteSetting::getSettings();

        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'pt_time' => 'required|date_format:H:i',
            'games_time' => 'required|date_format:H:i',
            'parade_time' => 'required|date_format:H:i',
            'roll_call_time' => 'required|date_format:H:i',
        ]);

        $settings->update($validated);

        return redirect()->back()->with('success', 'Site settings updated successfully!');
    }
}
