<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Helpers\MailLogger;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class StaffController extends Controller
{
    // ── List all staff ────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = User::where('role', 'staff');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('position', 'like', "%{$s}%");
            });
        }

        $staff = $query->latest()->paginate(15)->withQueryString();

        // Single aggregate query instead of 4 separate COUNTs
        $allStaff = User::where('role', 'staff')
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN is_blocked=0 THEN 1 ELSE 0 END) as active, SUM(CASE WHEN is_blocked=1 THEN 1 ELSE 0 END) as blocked, COALESCE(SUM(CASE WHEN salary IS NOT NULL THEN salary ELSE 0 END), 0) as total_salary')
            ->first();

        $stats = [
            'total'        => (int) ($allStaff->total        ?? 0),
            'active'       => (int) ($allStaff->active       ?? 0),
            'blocked'      => (int) ($allStaff->blocked      ?? 0),
            'total_salary' => (float) ($allStaff->total_salary ?? 0),
        ];

        return view('admin.staff.index', compact('staff', 'stats'));
    }

    // ── Create staff form ─────────────────────────────────────────────────
    public function create()
    {
        return view('admin.staff.create');
    }

    // ── Store new staff member ────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:8|confirmed',
            'position'     => 'nullable|string|max:100',
            'salary'       => 'nullable|numeric|min:0',
            'salary_currency' => 'nullable|string|max:10',
            'salary_cycle' => 'nullable|in:daily,weekly,monthly,yearly',
            'joining_date' => 'nullable|date',
            'staff_notes'  => 'nullable|string|max:1000',
        ]);

        $staff = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'role'            => 'staff',
            'created_by'      => Auth::id(),
            'position'        => $request->position,
            'salary'          => $request->salary,
            'salary_currency' => $request->salary_currency ?? 'INR',
            'salary_cycle'    => $request->salary_cycle ?? 'monthly',
            'joining_date'    => $request->joining_date,
            'staff_notes'     => $request->staff_notes,
        ]);

        ActivityLogger::log('staff.created', 'User', $staff->id, $staff->name,
            "Position: {$staff->position}, Created by: " . Auth::user()->name);

        // Send welcome email
        MailLogger::log(
            'welcome',
            $staff->email, $staff->name, $staff->id,
            fn() => Mail::to($staff->email)->send(new WelcomeMail($staff)),
            'staff_management'
        );

        return redirect()->route('admin.staff.index')
            ->with('success', "✅ Staff account for \"{$staff->name}\" created successfully.");
    }

    // ── View staff details (salary card) ─────────────────────────────────
    public function show(User $staff)
    {
        abort_if($staff->role !== 'staff', 404);
        return view('admin.staff.show', compact('staff'));
    }

    // ── Edit staff form ───────────────────────────────────────────────────
    public function edit(User $staff)
    {
        abort_if($staff->role !== 'staff', 404);
        return view('admin.staff.edit', compact('staff'));
    }

    // ── Update staff (including salary) ──────────────────────────────────
    public function update(Request $request, User $staff)
    {
        abort_if($staff->role !== 'staff', 404);

        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email,' . $staff->id,
            'position'        => 'nullable|string|max:100',
            'salary'          => 'nullable|numeric|min:0',
            'salary_currency' => 'nullable|string|max:10',
            'salary_cycle'    => 'nullable|in:daily,weekly,monthly,yearly',
            'joining_date'    => 'nullable|date',
            'staff_notes'     => 'nullable|string|max:1000',
        ]);

        $staff->update([
            'name'            => $request->name,
            'email'           => $request->email,
            'position'        => $request->position,
            'salary'          => $request->salary,
            'salary_currency' => $request->salary_currency ?? 'INR',
            'salary_cycle'    => $request->salary_cycle ?? 'monthly',
            'joining_date'    => $request->joining_date,
            'staff_notes'     => $request->staff_notes,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $staff->update(['password' => Hash::make($request->password)]);
        }

        ActivityLogger::log('staff.updated', 'User', $staff->id, $staff->name,
            "Salary: {$staff->salary_currency} {$staff->salary}/{$staff->salary_cycle}");

        // Send update notice email
        MailLogger::log(
            'staff_updated',
            $staff->email, $staff->name, $staff->id,
            fn() => Mail::to($staff->email)->send(new \App\Mail\StaffAccountNoticeMail(
                $staff->name,
                'updated',
                'Account Details Updated',
                'Your staff profile and account details have been updated by the Main Admin.',
                ['Position' => $staff->position ?? 'Staff', 'Salary' => "{$staff->salary_currency} {$staff->salary}"]
            )),
            'staff_management'
        );

        return redirect()->route('admin.staff.index')
            ->with('success', "Staff account \"{$staff->name}\" updated.");
    }

    // ── Toggle block ──────────────────────────────────────────────────────
    public function toggleBlock(User $staff)
    {
        abort_if($staff->role !== 'staff', 404);

        $staff->update(['is_blocked' => !$staff->is_blocked]);
        $action = $staff->is_blocked ? 'blocked' : 'unblocked';
        ActivityLogger::log("staff.{$action}", 'User', $staff->id, $staff->name);

        // Send block/unblock notice email
        MailLogger::log(
            "staff_{$action}",
            $staff->email, $staff->name, $staff->id,
            fn() => Mail::to($staff->email)->send(new \App\Mail\StaffAccountNoticeMail(
                $staff->name,
                $action,
                $staff->is_blocked ? 'Account Access Restricted' : 'Account Restored',
                $staff->is_blocked
                    ? 'Your staff account access has been temporarily restricted by the Main Admin.'
                    : 'Your staff account access has been reactivated. You may now log in to the Staff Panel.',
                ['Status' => ucfirst($action)]
            )),
            'staff_management'
        );

        return back()->with('success', "Staff member \"{$staff->name}\" {$action}.");
    }

    // ── Delete staff ──────────────────────────────────────────────────────
    public function destroy(User $staff)
    {
        abort_if($staff->role !== 'staff', 404);

        $name  = $staff->name;
        $email = $staff->email;

        // Send deletion notice email before removing record
        MailLogger::log(
            'staff_deleted',
            $email, $name, $staff->id,
            fn() => Mail::to($email)->send(new \App\Mail\StaffAccountNoticeMail(
                $name,
                'deleted',
                'Staff Account Removed',
                'Your staff account at Learning Curve has been removed by the Main Admin. Thank you for your service.',
                ['Account Email' => $email]
            )),
            'staff_management'
        );

        $staff->delete();

        ActivityLogger::log('staff.deleted', 'User', null, $name, "Email: {$email}");

        return redirect()->route('admin.staff.index')
            ->with('success', "Staff account \"{$name}\" deleted.");
    }

    // ── Panel selection screen ────────────────────────────────────────────
    public function panelSelect()
    {
        // If not logged in or no admin access, redirect to admin login
        if (!Auth::check() || !Auth::user()->hasAdminAccess()) {
            return redirect()->route('admin.login');
        }
        return view('admin.panel-select');
    }
}
