<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminAdController extends Controller
{
    /**
     * Display a listing of ads with full metrics.
     */
    public function index()
    {
        $ads = Ad::latest()->paginate(10);

        // Aggregate statistics for ad management dashboard
        $totalAdsRun = Ad::count();
        $activeAdsCount = Ad::where('is_active', true)->count();
        $totalCollection = Ad::sum('total_amount');
        $totalAdvanceReceived = Ad::sum('advance_paid');
        $totalPendingBalance = max(0, $totalCollection - $totalAdvanceReceived);

        return view('admin.ads.index', compact(
            'ads',
            'totalAdsRun',
            'activeAdsCount',
            'totalCollection',
            'totalAdvanceReceived',
            'totalPendingBalance'
        ));
    }

    /**
     * Show the form for creating a new ad.
     */
    public function create()
    {
        return view('admin.ads.create');
    }

    /**
     * Store a newly created ad in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'type'          => 'required|in:image,video',
            'media_file'    => 'nullable|file|max:25600', // max 25MB
            'media_url'     => 'nullable|string|max:2048',
            'link_url'      => 'nullable|url|max:2048',
            'powered_by'    => 'required|string|max:255',
            'start_date'    => 'nullable|date',
            'deadline'      => 'nullable|date',
            'duration_days' => 'nullable|integer|min:1',
            'total_amount'  => 'nullable|numeric|min:0',
            'advance_paid'  => 'nullable|numeric|min:0',
            'is_active'     => 'sometimes|boolean',
        ]);

        if (!$request->hasFile('media_file') && !$request->filled('media_url')) {
            return back()->withErrors(['media_file' => 'You must upload a file or provide a media URL.'])->withInput();
        }

        $mediaPath = '';
        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $destinationPath = public_path('uploads/ads');
            
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $filename);
            $mediaPath = '/uploads/ads/' . $filename;
        } else {
            $mediaPath = $request->media_url;
        }

        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : now();
        $duration = (int)($request->duration_days ?? 7);
        $deadline = $request->deadline ? \Carbon\Carbon::parse($request->deadline) : (clone $startDate)->addDays($duration);

        Ad::create([
            'title'         => $request->title,
            'description'   => $request->description,
            'type'          => $request->type,
            'media_path'    => $mediaPath,
            'link_url'      => $request->link_url,
            'powered_by'    => $request->powered_by,
            'start_date'    => $startDate,
            'deadline'      => $deadline,
            'duration_days' => $duration,
            'total_amount'  => $request->total_amount ?? 0,
            'advance_paid'  => $request->advance_paid ?? 0,
            'is_active'     => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return redirect()->route('admin.ads.index')->with('success', 'Ad campaign created & published successfully!');
    }

    /**
     * Show the form for editing the specified ad.
     */
    public function edit(Ad $ad)
    {
        return view('admin.ads.edit', compact('ad'));
    }

    /**
     * Update the specified ad in storage.
     */
    public function update(Request $request, Ad $ad)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'type'          => 'required|in:image,video',
            'media_file'    => 'nullable|file|max:25600', // max 25MB
            'media_url'     => 'nullable|string|max:2048',
            'link_url'      => 'nullable|url|max:2048',
            'powered_by'    => 'required|string|max:255',
            'start_date'    => 'nullable|date',
            'deadline'      => 'nullable|date',
            'duration_days' => 'nullable|integer|min:1',
            'total_amount'  => 'nullable|numeric|min:0',
            'advance_paid'  => 'nullable|numeric|min:0',
            'is_active'     => 'sometimes|boolean',
        ]);

        $mediaPath = $ad->media_path;

        if ($request->hasFile('media_file')) {
            if ($ad->media_path && File::exists(public_path($ad->media_path))) {
                File::delete(public_path($ad->media_path));
            }

            $file = $request->file('media_file');
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $destinationPath = public_path('uploads/ads');
            
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $filename);
            $mediaPath = '/uploads/ads/' . $filename;
        } elseif ($request->filled('media_url')) {
            if (strpos($ad->media_path, '/uploads/ads/') === 0 && File::exists(public_path($ad->media_path))) {
                File::delete(public_path($ad->media_path));
            }
            $mediaPath = $request->media_url;
        }

        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : ($ad->start_date ?? now());
        $duration = (int)($request->duration_days ?? $ad->duration_days ?? 7);
        $deadline = $request->deadline ? \Carbon\Carbon::parse($request->deadline) : (clone $startDate)->addDays($duration);

        $ad->update([
            'title'         => $request->title,
            'description'   => $request->description,
            'type'          => $request->type,
            'media_path'    => $mediaPath,
            'link_url'      => $request->link_url,
            'powered_by'    => $request->powered_by,
            'start_date'    => $startDate,
            'deadline'      => $deadline,
            'duration_days' => $duration,
            'total_amount'  => $request->total_amount ?? 0,
            'advance_paid'  => $request->advance_paid ?? 0,
            'is_active'     => $request->has('is_active') ? (bool)$request->is_active : false,
        ]);

        return redirect()->route('admin.ads.index')->with('success', 'Ad campaign updated successfully!');
    }

    /**
     * Remove the specified ad from storage.
     */
    public function destroy(Ad $ad)
    {
        if ($ad->media_path && File::exists(public_path($ad->media_path))) {
            File::delete(public_path($ad->media_path));
        }

        $ad->delete();

        return redirect()->route('admin.ads.index')->with('success', 'Ad campaign deleted successfully!');
    }

    /**
     * Toggle the status of the specified ad.
     */
    public function toggle(Ad $ad)
    {
        $ad->is_active = !$ad->is_active;
        $ad->save();

        return back()->with('success', 'Ad campaign status updated!');
    }
}
