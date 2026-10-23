<?php

namespace App\Http\Controllers;

use App\Helpers\MailLogger;
use App\Mail\PasswordChangedMail;
use App\Models\UserDatePassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $user->load(['datePasswords' => fn($q) => $q->orderBy('day_of_month')]);
        return view('profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update(['name' => $request->name, 'email' => $request->email]);

        // ── Immediate password change ──────────────────────────────────────
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);

            // Log via MailLogger so it appears in mail audit logs
            MailLogger::log(
                'password_changed',
                $user->email,
                $user->name,
                $user->id,
                fn() => Mail::to($user->email)->send(new PasswordChangedMail(
                    $user->name,
                    now()->format('d M Y, h:i A')
                )),
                'profile_update'
            );
        }

        return back()->with('success', 'Profile and account settings updated successfully!');
    }

    // ── Manage up to 3 Date-Based Password Entries ───────────────────────
    public function addDatePassword(Request $request)
    {
        $user = Auth::user();

        // Limit to max 3 entries
        if ($user->datePasswords()->count() >= 3) {
            return back()->with('error', 'You can configure up to 3 date-based password entries maximum.');
        }

        $request->validate([
            'day_of_month' => 'required|integer|min:1|max:31',
            'date_password'=> 'required|string|min:8|confirmed',
        ], [
            'day_of_month.required'  => 'Please select a day of the month (1–31).',
            'day_of_month.min'       => 'Day of month must be between 1 and 31.',
            'day_of_month.max'       => 'Day of month must be between 1 and 31.',
            'date_password.required' => 'Please enter the password for this date.',
            'date_password.min'      => 'Date password must be at least 8 characters.',
            'date_password.confirmed'=> 'Date password confirmation does not match.',
        ]);

        // Save or update for this day
        UserDatePassword::updateOrCreate(
            [
                'user_id'      => $user->id,
                'day_of_month' => $request->day_of_month,
            ],
            [
                'password'  => Hash::make($request->date_password),
                'is_active' => true,
            ]
        );

        return back()->with('success', 'Date-based password for Day ' . $request->day_of_month . ' saved successfully! 🗓️');
    }

    public function deleteDatePassword(UserDatePassword $datePassword)
    {
        if ($datePassword->user_id !== Auth::id()) {
            abort(403);
        }

        $day = $datePassword->day_of_month;
        $datePassword->delete();

        return back()->with('success', 'Date-based password entry for Day ' . $day . ' deleted.');
    }

    public function toggleDatePassword(UserDatePassword $datePassword)
    {
        if ($datePassword->user_id !== Auth::id()) {
            abort(403);
        }

        $datePassword->update(['is_active' => !$datePassword->is_active]);

        $status = $datePassword->is_active ? 'enabled' : 'disabled';
        return back()->with('success', 'Date password for Day ' . $datePassword->day_of_month . ' is now ' . $status . '.');
    }
}
