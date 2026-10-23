<?php

namespace App\Http\Controllers;

use App\Models\SalaryPayment;
use App\Models\StaffWorkLog;
use App\Models\SupportTicket;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StaffWorkLogController extends Controller
{
    const DAILY_RATE = 1000; // ₹1,000 per day (Day-Wise Payout)

    /** Staff Portal Dashboard */
    public function dashboard()
    {
        $user = Auth::user();

        // Check if attendance marked for today
        $todayLog = StaffWorkLog::where('user_id', $user->id)
            ->whereDate('work_date', today())
            ->where('status', 'present')
            ->first();

        // Present days this month
        $presentDaysMonth = StaffWorkLog::where('user_id', $user->id)
            ->whereMonth('work_date', now()->month)
            ->whereYear('work_date', now()->year)
            ->where('status', 'present')
            ->count();

        $earnedMonth = $presentDaysMonth * self::DAILY_RATE;

        // All-time total present days
        $totalPresentDays = StaffWorkLog::where('user_id', $user->id)
            ->where('status', 'present')
            ->count();

        $totalEarned = $totalPresentDays * self::DAILY_RATE;

        // Recent 15 attendance logs
        $logs = StaffWorkLog::where('user_id', $user->id)
            ->orderByDesc('work_date')
            ->limit(15)
            ->get();

        // Salary payments history
        $payments = $user->salaryPayments()->with('paidBy')->latest()->get();

        // Assigned tickets to this staff member
        $assignedTickets = SupportTicket::whereIn('status', ['open', 'in_progress'])
            ->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)->orWhereNull('assigned_to');
            })
            ->latest()->limit(5)->get();

        // Assigned Tasks strictly visible ONLY to this assigned staff member
        $assignedTasks = Task::where('assigned_to', $user->id)
            ->with('course')
            ->latest()
            ->get();

        // Active chats waiting
        $waitingChats = ChatMessage::where('chat_status', 'waiting')
            ->distinct('session_id')
            ->count('session_id');

        $totalPaid = $payments->where('payment_status', 'paid')->sum('amount');
        $pendingPay = max(0, $earnedMonth - $totalPaid);

        return view('staff.dashboard', compact(
            'user', 'todayLog', 'presentDaysMonth', 'earnedMonth',
            'totalPresentDays', 'totalEarned', 'logs', 'payments',
            'assignedTickets', 'assignedTasks', 'waitingChats', 'totalPaid', 'pendingPay'
        ));
    }

    /** Mark Attendance as Present for Today */
    public function markPresent(Request $request)
    {
        $user = Auth::user();

        $already = StaffWorkLog::where('user_id', $user->id)
            ->whereDate('work_date', today())
            ->first();

        if ($already && $already->status === 'present') {
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'message' => 'Attendance already marked Present for today.']);
            }
            return back()->with('info', 'Attendance already marked Present for today.');
        }

        if ($already) {
            $already->update([
                'status'    => 'present',
                'marked_at' => now(),
            ]);
        } else {
            StaffWorkLog::create([
                'user_id'   => $user->id,
                'work_date' => today(),
                'status'    => 'present',
                'marked_at' => now(),
            ]);
        }

        \App\Helpers\ActivityLogger::log('attendance.marked', 'StaffWorkLog', null, $user->name, 'Marked Present for today');

        if ($request->wantsJson()) {
            return response()->json([
                'ok'         => true,
                'message'    => '✅ Attendance marked Present for today!',
                'date'       => today()->format('d M Y'),
                'daily_rate' => self::DAILY_RATE,
            ]);
        }

        return back()->with('success', '🎉 Attendance marked Present for today! Day-wise count updated (+₹1,000).');
    }

    /** Main Admin Attendance Sheet View */
    public function adminAttendance(Request $request)
    {
        $monthStr = $request->input('month', now()->format('Y-m'));
        $dateObj  = Carbon::createFromFormat('Y-m', $monthStr);

        $daysInMonth = $dateObj->daysInMonth;
        $staffMembers = User::where('role', 'staff')->get();

        // Get all attendance logs for the month
        $attendanceLogs = StaffWorkLog::whereMonth('work_date', $dateObj->month)
            ->whereYear('work_date', $dateObj->year)
            ->get()
            ->groupBy('user_id');

        $matrix = [];
        foreach ($staffMembers as $staff) {
            $userLogs = $attendanceLogs[$staff->id] ?? collect();
            $logByDay = $userLogs->keyBy(fn($l) => $l->work_date->format('j'));

            $days = [];
            $presentCount = 0;

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $log = $logByDay[$d] ?? null;
                $isPresent = ($log && $log->status === 'present');
                if ($isPresent) $presentCount++;

                $days[$d] = [
                    'present'   => $isPresent,
                    'marked_at' => $log?->marked_at?->format('h:i A'),
                ];
            }

            $matrix[] = [
                'staff'         => $staff,
                'days'          => $days,
                'present_count' => $presentCount,
                'earned'        => $presentCount * self::DAILY_RATE,
            ];
        }

        return view('admin.attendance.index', compact('monthStr', 'dateObj', 'daysInMonth', 'matrix', 'staffMembers'));
    }

    /** Main Admin Toggle Staff Attendance for Specific Date */
    public function toggleAttendance(Request $request)
    {
        $request->validate([
            'staff_id'  => 'required|exists:users,id',
            'work_date' => 'required|date',
            'status'    => 'required|in:present,absent',
        ]);

        $staff = User::findOrFail($request->staff_id);
        $workDate = Carbon::parse($request->work_date);

        $log = StaffWorkLog::where('user_id', $staff->id)
            ->whereDate('work_date', $workDate)
            ->first();

        if ($request->status === 'present') {
            if ($log) {
                $log->update(['status' => 'present', 'marked_at' => now()]);
            } else {
                StaffWorkLog::create([
                    'user_id'   => $staff->id,
                    'work_date' => $workDate,
                    'status'    => 'present',
                    'marked_at' => now(),
                ]);
            }
            $msg = "Marked Present for {$staff->name} on " . $workDate->format('d M Y');
        } else {
            if ($log) {
                $log->update(['status' => 'absent']);
            }
            $msg = "Marked Absent for {$staff->name} on " . $workDate->format('d M Y');
        }

        return back()->with('success', "✅ {$msg}");
    }

    /** Update staff bank details */
    public function updateBankDetails(Request $request)
    {
        $request->validate([
            'bank_name'      => 'required|string|max:100',
            'account_holder' => 'required|string|max:100',
            'bank_account'   => 'required|string|max:30',
            'bank_ifsc'      => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $user->update([
            'bank_name'      => $request->bank_name,
            'account_holder' => $request->account_holder,
            'bank_account'   => $request->bank_account,
            'bank_ifsc'      => strtoupper($request->bank_ifsc),
        ]);

        return back()->with('success', '✅ Bank details updated successfully.');
    }

    /** Full work history page */
    public function history()
    {
        $user = Auth::user();
        $logs = StaffWorkLog::where('user_id', $user->id)
            ->orderByDesc('work_date')
            ->paginate(20);

        return view('staff.history', compact('user', 'logs'));
    }
}
