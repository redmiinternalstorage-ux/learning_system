<?php

namespace App\Http\Controllers;

use App\Models\CollegeAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminCollegeController extends Controller
{
    public function index()
    {
        $colleges = CollegeAdmin::withCount('students')->latest()->paginate(20);
        return view('admin.colleges.index', compact('colleges'));
    }

    public function create()
    {
        return view('admin.colleges.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'college_name'   => 'required|string|max:255',
            'email'          => 'required|email|unique:college_admins,email',
            'password'       => 'required|min:6',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:500',
            'website'        => 'nullable|url|max:255',
            'domain'         => 'nullable|string|max:100',
            'status'         => 'required|in:pending,active,inactive',
        ]);

        CollegeAdmin::create([
            'college_name'   => $request->college_name,
            'email'          => strtolower($request->email),
            'password'       => Hash::make($request->password),
            'contact_person' => $request->contact_person,
            'phone'          => $request->phone,
            'address'        => $request->address,
            'website'        => $request->website,
            'domain'         => $request->domain ? strtolower($request->domain) : null,
            'status'         => $request->status,
            'notes'          => $request->notes,
        ]);

        return redirect()->route('admin.colleges.index')
            ->with('success', 'College account created successfully.');
    }

    public function edit(CollegeAdmin $college)
    {
        return view('admin.colleges.edit', compact('college'));
    }

    public function update(Request $request, CollegeAdmin $college)
    {
        $request->validate([
            'college_name'   => 'required|string|max:255',
            'email'          => 'required|email|unique:college_admins,email,' . $college->id,
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'status'         => 'required|in:pending,active,inactive',
        ]);

        $college->update($request->only('college_name', 'email', 'contact_person', 'phone', 'address', 'website', 'domain', 'status', 'notes'));

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $college->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.colleges.index')
            ->with('success', 'College updated.');
    }

    public function destroy(CollegeAdmin $college)
    {
        $college->delete();
        return back()->with('success', 'College account deleted.');
    }

    public function toggleStatus(CollegeAdmin $college)
    {
        $college->update(['status' => $college->isActive() ? 'inactive' : 'active']);
        return back()->with('success', 'College status updated.');
    }
}
