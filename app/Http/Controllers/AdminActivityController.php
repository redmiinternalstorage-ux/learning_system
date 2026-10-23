<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AdminActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('target_label', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs    = $query->paginate(25)->withQueryString();
        $actions = array_keys(ActivityLog::$actionMeta);

        // Summary counts per action today
        $todayCounts = ActivityLog::whereDate('created_at', today())
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->pluck('count', 'action');

        return view('admin.activity-logs', compact('logs', 'actions', 'todayCounts'));
    }

    public function clear()
    {
        ActivityLog::where('created_at', '<', now()->subDays(30))->delete();
        return back()->with('success', 'Activity logs older than 30 days cleared.');
    }
}
