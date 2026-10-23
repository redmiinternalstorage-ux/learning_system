<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        // Seed defaults if table is empty
        if (SiteSetting::count() === 0) {
            foreach (SiteSetting::defaults() as $row) {
                SiteSetting::create($row);
            }
        }

        $settings = SiteSetting::orderBy('group')->orderBy('label')->get()->groupBy('group');
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $inputs = $request->except(['_token', '_method']);

        foreach ($inputs as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? implode(',', $value) : $value]
            );
        }

        // Handle unchecked booleans (checkboxes not submitted = false)
        $boolKeys = SiteSetting::where('type', 'boolean')->pluck('key');
        foreach ($boolKeys as $bk) {
            if (!array_key_exists($bk, $inputs)) {
                SiteSetting::set($bk, '0');
            }
        }

        ActivityLogger::log('settings.updated', null, null, 'Site Settings', 'Admin updated site settings');

        return back()->with('success', '✅ Settings saved successfully.');
    }

    public function toggleMaintenance(Request $request)
    {
        $current = (bool) SiteSetting::get('maintenance_mode', false);
        $newStatus = !$current;
        SiteSetting::set('maintenance_mode', $newStatus ? '1' : '0');

        $statusText = $newStatus ? 'ENABLED (Under Maintenance)' : 'DISABLED (Site Live)';
        ActivityLogger::log('settings.updated', null, null, 'Maintenance Mode', "Admin changed Maintenance Mode to {$statusText}");

        if ($request->wantsJson()) {
            return response()->json([
                'ok'             => true,
                'is_maintenance' => $newStatus,
                'message'        => "Maintenance mode is now {$statusText}."
            ]);
        }

        return back()->with('success', "🛠️ Maintenance mode is now {$statusText}.");
    }
}
