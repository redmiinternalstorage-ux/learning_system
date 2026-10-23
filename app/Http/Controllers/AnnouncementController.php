<?php
namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|max:255', 'body' => 'required']);
        Announcement::create([
            'title'     => $request->title,
            'body'      => $request->body,
            'is_active' => $request->boolean('is_active', true),
        ]);
        return redirect()->route('admin.announcements')->with('success', 'Announcement created!');
    }

    public function edit(Announcement $a)
    {
        return view('admin.announcements.edit', compact('a'));
    }

    public function update(Request $request, Announcement $a)
    {
        $request->validate(['title' => 'required|max:255', 'body' => 'required']);
        $a->update([
            'title'     => $request->title,
            'body'      => $request->body,
            'is_active' => $request->boolean('is_active'),
        ]);
        return redirect()->route('admin.announcements')->with('success', 'Updated!');
    }

    public function destroy(Announcement $a)
    {
        $a->delete();
        return back()->with('success', 'Deleted.');
    }

    public function toggle(Announcement $a)
    {
        $a->update(['is_active' => !$a->is_active]);
        return back()->with('success', 'Status updated.');
    }
}
