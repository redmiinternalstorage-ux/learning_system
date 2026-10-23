<?php

namespace App\Http\Controllers;

use App\Helpers\MailLogger;
use App\Helpers\ActivityLogger;
use App\Mail\SalaryPaidMail;
use App\Models\FinanceAccount;
use App\Models\FinanceTransaction;
use App\Models\SalaryPayment;
use App\Models\StaffWorkLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FinanceController extends Controller
{
    const HOURLY_RATE = 1000; // ₹1,000 per hour — fixed salary rate

    /**
     * Helper to get or initialize Main Bank Account with ₹100,000 starting balance
     */
    private function getMainBankAccount()
    {
        $main = FinanceAccount::where('type', 'bank')->first();
        if (!$main) {
            $main = FinanceAccount::create([
                'name'           => 'Main Bank Account',
                'account_number' => 'ACC-MAIN-1001',
                'type'           => 'bank',
                'balance'        => 100000.00,
                'description'    => 'Primary Organization Reserve Account',
            ]);

            FinanceTransaction::create([
                'transaction_code' => FinanceTransaction::generateCode(),
                'transaction_type' => 'deposit',
                'flow_type'        => 'income',
                'to_account_id'    => $main->id,
                'sender_name'      => 'System Opening Reserve',
                'receiver_name'    => 'Main Bank Account',
                'amount'           => 100000.00,
                'status'           => 'completed',
                'auto_approved'    => true,
                'notes'            => 'Initial opening balance for Finance Department (₹100,000)',
                'created_by'       => Auth::id(),
            ]);
        }
        return $main;
    }

    /**
     * Core Transaction Status Processor (Updates balances safely inside DB transactions)
     */
    private function processStatusUpdate(FinanceTransaction $transaction, string $newStatus)
    {
        $oldStatus = $transaction->status;

        if ($oldStatus === $newStatus) {
            return $transaction;
        }

        $mainAccount = $this->getMainBankAccount();

        DB::transaction(function () use ($transaction, $oldStatus, $newStatus, $mainAccount) {
            $isOldActive = in_array($oldStatus, ['approved', 'completed']);
            $isNewActive = in_array($newStatus, ['approved', 'completed']);

            $amount = (float) $transaction->amount;

            $fromAccount = $transaction->from_account_id ? FinanceAccount::find($transaction->from_account_id) : $mainAccount;
            $toAccount   = $transaction->to_account_id ? FinanceAccount::find($transaction->to_account_id) : $mainAccount;

            // If becoming active (was pending/failed -> now approved/completed)
            if (!$isOldActive && $isNewActive) {
                if ($transaction->flow_type === 'income') {
                    if ($toAccount) {
                        $toAccount->increment('balance', $amount);
                    }
                } elseif ($transaction->flow_type === 'expense') {
                    if ($fromAccount) {
                        if ($fromAccount->balance < $amount) {
                            throw new \Exception("Insufficient balance in {$fromAccount->name}! Available: ₹" . number_format($fromAccount->balance, 2));
                        }
                        $fromAccount->decrement('balance', $amount);
                    }
                } elseif ($transaction->flow_type === 'transfer') {
                    if ($fromAccount && $toAccount) {
                        if ($fromAccount->balance < $amount) {
                            throw new \Exception("Insufficient balance in {$fromAccount->name}! Available: ₹" . number_format($fromAccount->balance, 2));
                        }
                        $fromAccount->decrement('balance', $amount);
                        $toAccount->increment('balance', $amount);
                    }
                }
            }
            // If becoming inactive (was approved/completed -> now pending/failed)
            elseif ($isOldActive && !$isNewActive) {
                if ($transaction->flow_type === 'income') {
                    if ($toAccount) {
                        $toAccount->decrement('balance', $amount);
                    }
                } elseif ($transaction->flow_type === 'expense') {
                    if ($fromAccount) {
                        $fromAccount->increment('balance', $amount);
                    }
                } elseif ($transaction->flow_type === 'transfer') {
                    if ($fromAccount && $toAccount) {
                        $fromAccount->increment('balance', $amount);
                        $toAccount->decrement('balance', $amount);
                    }
                }
            }

            $transaction->update(['status' => $newStatus]);

            // Sync linked Salary Payment if present
            if ($transaction->salary_payment_id) {
                $salaryPayment = SalaryPayment::find($transaction->salary_payment_id);
                if ($salaryPayment) {
                    $salaryPayment->update([
                        'payment_status' => ($newStatus === 'completed' ? 'paid' : $newStatus),
                        'paid_at'        => ($isNewActive ? now() : null),
                    ]);
                }
            }
        });

        return $transaction;
    }

    /**
     * Finance Department Dashboard
     */
    public function dashboard(Request $request)
    {
        $mainAccount = $this->getMainBankAccount();
        $accounts    = FinanceAccount::orderBy('id')->get();

        // Check PIN verification status from session
        $pinUnlocked = session('finance_pin_verified', false);

        // Stats calculations
        $availableBalance = $mainAccount->balance;

        $totalIncome = (float) FinanceTransaction::whereIn('status', ['approved', 'completed'])
            ->where(function ($q) use ($mainAccount) {
                $q->where('flow_type', 'income')
                  ->orWhere('to_account_id', $mainAccount->id);
            })
            ->where('transaction_type', '!=', 'transfer')
            ->sum('amount');

        $totalExpenses = (float) FinanceTransaction::whereIn('status', ['approved', 'completed'])
            ->where(function ($q) use ($mainAccount) {
                $q->where('flow_type', 'expense')
                  ->orWhere('from_account_id', $mainAccount->id);
            })
            ->where('transaction_type', '!=', 'deposit')
            ->sum('amount');

        $pendingCount  = FinanceTransaction::where('status', 'pending')->count();
        $pendingAmount = (float) FinanceTransaction::where('status', 'pending')->sum('amount');

        // Staff overview data (Optimized batch queries)
        $staffUsers = User::where('role', 'staff')->get();
        $staffIds   = $staffUsers->pluck('id')->toArray();

        $attendanceDaysSum = !empty($staffIds)
            ? StaffWorkLog::whereIn('user_id', $staffIds)
                ->whereMonth('work_date', now()->month)
                ->whereYear('work_date', now()->year)
                ->where('status', 'present')
                ->groupBy('user_id')
                ->selectRaw('user_id, COUNT(*) as present_days')
                ->pluck('present_days', 'user_id')
            : collect();

        $latestPayments = !empty($staffIds)
            ? SalaryPayment::whereIn('staff_id', $staffIds)
                ->orderBy('created_at', 'desc')
                ->get()
                ->unique('staff_id')
                ->keyBy('staff_id')
            : collect();

        $paidMonthSum = !empty($staffIds)
            ? SalaryPayment::whereIn('staff_id', $staffIds)
                ->where('payment_status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->groupBy('staff_id')
                ->selectRaw('staff_id, SUM(amount) as total_paid')
                ->pluck('total_paid', 'staff_id')
            : collect();

        $staff = $staffUsers->map(function ($s) use ($attendanceDaysSum, $latestPayments, $paidMonthSum) {
            $presentDays       = $attendanceDaysSum[$s->id] ?? 0;
            $s->month_hours    = $presentDays; // Storing present days count
            $s->month_earned   = $presentDays * 1000; // ₹1,000 per day Present
            $s->last_payment   = $latestPayments[$s->id] ?? null;

            $paidMonth         = $paidMonthSum[$s->id] ?? 0;
            $s->pending_amount = max(0, $s->month_earned - $paidMonth);
            return $s;
        });

        // Transactions list with Search & Filter
        $query = FinanceTransaction::with(['fromAccount', 'toAccount', 'staff', 'creator'])->latest();

        if ($request->filled('q')) {
            $search = '%' . trim($request->q) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', $search)
                  ->orWhere('sender_name', 'like', $search)
                  ->orWhere('receiver_name', 'like', $search)
                  ->orWhere('client_name', 'like', $search)
                  ->orWhere('notes', 'like', $search)
                  ->orWhere('amount', 'like', $search);
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('transaction_type', $request->type);
        }

        $transactions = $query->paginate(15)->withQueryString();

        return view('finance.dashboard', compact(
            'mainAccount',
            'accounts',
            'availableBalance',
            'totalIncome',
            'totalExpenses',
            'pendingCount',
            'pendingAmount',
            'staff',
            'transactions',
            'pinUnlocked'
        ));
    }

    /**
     * Money Transfer between accounts/departments
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required|exists:finance_accounts,id',
            'to_account_id'   => 'required|exists:finance_accounts,id|different:from_account_id',
            'amount'          => 'required|numeric|gt:0',
            'status'          => 'required|in:pending,approved,failed,completed',
            'notes'           => 'nullable|string|max:500',
            'auto_approve'    => 'nullable|boolean',
        ]);

        $fromAccount = FinanceAccount::findOrFail($request->from_account_id);
        $toAccount   = FinanceAccount::findOrFail($request->to_account_id);
        $amount      = (float) $request->amount;

        $status = $request->status;
        if ($request->has('auto_approve') && in_array($status, ['completed', 'approved', 'pending'])) {
            $status = 'completed';
        }

        $flowType = ($fromAccount->type === 'bank') ? 'expense' : (($toAccount->type === 'bank') ? 'income' : 'transfer');

        $transaction = FinanceTransaction::create([
            'transaction_code' => FinanceTransaction::generateCode(),
            'transaction_type' => 'transfer',
            'flow_type'        => $flowType,
            'from_account_id'  => $fromAccount->id,
            'to_account_id'    => $toAccount->id,
            'sender_name'      => $fromAccount->name,
            'receiver_name'    => $toAccount->name,
            'amount'           => $amount,
            'status'           => 'pending', // Temporary status before processing update
            'auto_approved'    => ($status === 'completed'),
            'notes'            => $request->notes,
            'created_by'       => Auth::id(),
        ]);

        try {
            $this->processStatusUpdate($transaction, $status);
        } catch (\Exception $e) {
            $transaction->update(['status' => 'failed']);
            return back()->with('error', '❌ Transfer Failed: ' . $e->getMessage());
        }

        $indicator = ($flowType === 'income') ? '+' : (($flowType === 'expense') ? '−' : '⇄');

        return back()->with('success', "✅ Money Transfer of {$indicator} ₹" . number_format($amount, 2) . " from {$fromAccount->name} to {$toAccount->name} processed successfully! (Status: " . ucfirst($status) . ")");
    }

    /**
     * Staff Salary Payment from Finance Department
     */
    public function pay(Request $request, User $staff)
    {
        abort_if($staff->role !== 'staff', 404);

        $request->validate([
            'period_label' => 'required|string|max:60',
            'hours_worked' => 'required|numeric|min:0',
            'amount'       => 'nullable|numeric|gt:0',
            'status'       => 'required|in:pending,approved,failed,completed',
            'auto_approve' => 'nullable|boolean',
            'bank_account' => 'nullable|string|max:30',
            'bank_name'    => 'nullable|string|max:100',
            'notes'        => 'nullable|string|max:500',
        ]);

        $hours  = (float) $request->hours_worked;
        $amount = $request->filled('amount') ? (float) $request->amount : round($hours * self::HOURLY_RATE, 2);

        $status = $request->status;
        if ($request->has('auto_approve') && in_array($status, ['completed', 'approved', 'pending'])) {
            $status = 'completed';
        }

        $mainAccount  = $this->getMainBankAccount();
        $staffAccount = FinanceAccount::where('type', 'staff')->first();

        // Update staff bank details if provided
        if ($request->filled('bank_account') || $request->filled('bank_name')) {
            $staff->update([
                'bank_account' => $request->bank_account ?? $staff->bank_account,
                'bank_name'    => $request->bank_name ?? $staff->bank_name,
            ]);
        }

        $salaryPayment = SalaryPayment::create([
            'staff_id'       => $staff->id,
            'paid_by'        => Auth::id(),
            'amount'         => $amount,
            'currency'       => 'INR',
            'period_label'   => $request->period_label,
            'hours_worked'   => (int) ceil($hours),
            'notes'          => $request->notes,
            'mail_sent'      => false,
            'payment_status' => 'pending',
            'bank_account'   => $request->bank_account ?? $staff->bank_account,
            'bank_name'      => $request->bank_name ?? $staff->bank_name,
            'hourly_rate'    => self::HOURLY_RATE,
            'paid_at'        => null,
        ]);

        $transaction = FinanceTransaction::create([
            'transaction_code'  => FinanceTransaction::generateCode(),
            'transaction_type'  => 'salary',
            'flow_type'         => 'expense',
            'from_account_id'   => $mainAccount->id,
            'to_account_id'     => $staffAccount?->id,
            'sender_name'       => 'Finance Department (Main Bank)',
            'receiver_name'     => "Staff: {$staff->name}",
            'staff_id'          => $staff->id,
            'salary_payment_id' => $salaryPayment->id,
            'amount'            => $amount,
            'status'            => 'pending',
            'auto_approved'     => ($status === 'completed'),
            'notes'             => $request->notes ?? "Salary for {$request->period_label} ({$hours} hrs)",
            'created_by'        => Auth::id(),
        ]);

        try {
            $this->processStatusUpdate($transaction, $status);
        } catch (\Exception $e) {
            $transaction->update(['status' => 'failed']);
            $salaryPayment->update(['payment_status' => 'failed']);
            return back()->with('error', '❌ Salary Payment Failed: ' . $e->getMessage());
        }

        // Send Email if completed
        if (in_array($status, ['approved', 'completed']) && $staff->email) {
            try {
                MailLogger::log(
                    'salary_paid',
                    $staff->email,
                    $staff->name,
                    $staff->id,
                    fn() => Mail::to($staff->email)->send(new SalaryPaidMail(
                        staffName:   $staff->name,
                        period:      $request->period_label,
                        amount:      (string) $amount,
                        currency:    'INR',
                        hoursWorked: (int) ceil($hours),
                        paidBy:      Auth::user()->name,
                        notes:       $request->notes ?? '',
                    )),
                    'finance'
                );
                $salaryPayment->update(['mail_sent' => true]);
            } catch (\Exception $e) {
                // Log exception silently
            }
        }

        return back()->with('success', "✅ Staff Salary Payment of − ₹" . number_format($amount, 2) . " for {$staff->name} recorded! (Status: " . ucfirst($status) . ")");
    }

    /**
     * Add Client Income / Course Payment Details
     */
    public function clientPayment(Request $request)
    {
        $request->validate([
            'client_name'  => 'required|string|max:150',
            'client_email' => 'nullable|email|max:150',
            'amount'       => 'required|numeric|gt:0',
            'status'       => 'required|in:pending,approved,failed,completed',
            'auto_approve' => 'nullable|boolean',
            'notes'        => 'nullable|string|max:500',
        ]);

        $amount = (float) $request->amount;
        $status = $request->status;
        if ($request->has('auto_approve') && in_array($status, ['completed', 'approved', 'pending'])) {
            $status = 'completed';
        }

        $mainAccount   = $this->getMainBankAccount();
        $clientAccount = FinanceAccount::where('type', 'client')->first();

        $transaction = FinanceTransaction::create([
            'transaction_code' => FinanceTransaction::generateCode(),
            'transaction_type' => 'client_payment',
            'flow_type'        => 'income',
            'from_account_id'  => $clientAccount?->id,
            'to_account_id'    => $mainAccount->id,
            'sender_name'      => "Client: {$request->client_name}",
            'receiver_name'    => 'Main Bank Account',
            'client_name'      => $request->client_name,
            'client_email'     => $request->client_email,
            'amount'           => $amount,
            'status'           => 'pending',
            'auto_approved'    => ($status === 'completed'),
            'notes'            => $request->notes ?? 'Client Payment / Fee Income',
            'created_by'       => Auth::id(),
        ]);

        try {
            $this->processStatusUpdate($transaction, $status);
        } catch (\Exception $e) {
            $transaction->update(['status' => 'failed']);
            return back()->with('error', '❌ Client Payment Failed: ' . $e->getMessage());
        }

        return back()->with('success', "✅ Client Income Payment of + ₹" . number_format($amount, 2) . " received from {$request->client_name}! (Status: " . ucfirst($status) . ")");
    }

    /**
     * Update Status of existing transaction (Pending, Approved, Failed, Completed)
     */
    public function updateStatus(Request $request, FinanceTransaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,failed,completed',
        ]);

        $newStatus = $request->status;

        if ($transaction->status === $newStatus) {
            return back()->with('info', "Transaction #{$transaction->transaction_code} is already in " . ucfirst($newStatus) . " status.");
        }

        try {
            $this->processStatusUpdate($transaction, $newStatus);
        } catch (\Exception $e) {
            return back()->with('error', '❌ Status Update Failed: ' . $e->getMessage());
        }

        return back()->with('success', "✅ Transaction #{$transaction->transaction_code} status updated to " . ucfirst($newStatus) . ".");
    }

    /**
     * Mark pending salary payment as paid (legacy compatibility)
     */
    public function markPaid(Request $request, SalaryPayment $payment)
    {
        $transaction = FinanceTransaction::where('salary_payment_id', $payment->id)->first();
        if ($transaction) {
            try {
                $this->processStatusUpdate($transaction, 'completed');
                return back()->with('success', "🎉 Salary payment of − ₹" . number_format($payment->amount, 2) . " marked as Paid!");
            } catch (\Exception $e) {
                return back()->with('error', '❌ Could not mark paid: ' . $e->getMessage());
            }
        }

        $mainAccount = $this->getMainBankAccount();
        if ($mainAccount->balance < $payment->amount) {
            return back()->with('error', "❌ Insufficient Bank Balance to mark paid! Available balance is ₹" . number_format($mainAccount->balance, 2));
        }

        DB::transaction(function () use ($payment, $mainAccount, $request) {
            $mainAccount->decrement('balance', $payment->amount);
            $payment->update([
                'payment_status' => 'paid',
                'paid_at'        => now(),
                'bank_account'   => $request->input('bank_account', $payment->bank_account),
                'bank_name'      => $request->input('bank_name', $payment->bank_name),
            ]);
        });

        return back()->with('success', "🎉 Salary payment of − ₹" . number_format($payment->amount, 2) . " marked as Paid!");
    }

    /**
     * Automatic payment/approval functionality: Approve all pending transactions
     */
    public function autoApproveAll()
    {
        $pendingTransactions = FinanceTransaction::where('status', 'pending')->get();
        if ($pendingTransactions->isEmpty()) {
            return back()->with('info', "No pending transactions to auto-approve.");
        }

        $count  = 0;
        $failed = 0;

        foreach ($pendingTransactions as $txn) {
            try {
                $this->processStatusUpdate($txn, 'completed');
                $count++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        if ($failed > 0) {
            return back()->with('warning', "⚡ Approved {$count} pending transactions! ({$failed} failed due to insufficient funds).");
        }

        return back()->with('success', "⚡ Automatically approved and completed {$count} pending transactions! Balances updated.");
    }

    /**
     * Verify PIN for a specific action (AJAX — used before payment/transfer)
     */
    public function verifyActionPin(Request $request)
    {
        $pin = trim($request->input('pin', ''));
        $correctPin = (string) config('app.finance_pin', env('FINANCE_PIN', '123'));

        if ($pin === $correctPin) {
            return response()->json(['success' => true, 'message' => '✅ PIN verified. Proceeding with action.']);
        }

        return response()->json(['success' => false, 'message' => '❌ Incorrect PIN. Access denied.'], 422);
    }

    /**
     * Mark / stamp a transaction with a custom note or marking
     */
    public function markTransaction(Request $request, FinanceTransaction $transaction)
    {
        $request->validate([
            'mark_note' => 'required|string|max:200',
        ]);

        $existing = $transaction->notes ?? '';
        $stamp    = "\n[MARKED by " . Auth::user()->name . " on " . now()->format('d M Y, h:i A') . "]: " . $request->mark_note;
        $transaction->update(['notes' => $existing . $stamp]);

        return back()->with('success', '✅ Transaction marked/stamped successfully.');
    }

    /**
     * Export Finance Data Sheet to akpatel2029@gmail.com only
     */
    public function exportSheet(Request $request)
    {
        $user         = Auth::user();
        $targetEmail  = 'akpatel2029@gmail.com';
        $mainAccount  = $this->getMainBankAccount();
        $accounts     = FinanceAccount::orderBy('id')->get();

        // Compile finance data
        $totalIncome = (float) FinanceTransaction::whereIn('status', ['approved', 'completed'])
            ->where('flow_type', 'income')->where('transaction_type', '!=', 'transfer')->sum('amount');

        $totalExpenses = (float) FinanceTransaction::whereIn('status', ['approved', 'completed'])
            ->where('flow_type', 'expense')->where('transaction_type', '!=', 'deposit')->sum('amount');

        $transactions = FinanceTransaction::with(['fromAccount', 'toAccount', 'staff'])
            ->latest()->take(50)->get()->map(fn($t) => [
                'code'      => $t->transaction_code,
                'type'      => str_replace('_', ' ', ucfirst($t->transaction_type)),
                'flow'      => $t->flow_type,
                'sender'    => $t->sender_name,
                'receiver'  => $t->receiver_name,
                'amount'    => (float) $t->amount,
                'status'    => ucfirst($t->status),
                'date'      => $t->created_at->format('d M Y, h:i A'),
                'notes'     => $t->notes ?? '—',
            ])->toArray();

        $staff = \App\Models\User::whereIn('role', ['staff', 'finance', 'admin'])
            ->get()->map(function ($s) {
                $totalPaid = \App\Models\SalaryPayment::where('staff_id', $s->id)
                    ->where('payment_status', 'paid')->sum('amount');
                return [
                    'name'       => $s->name,
                    'email'      => $s->email,
                    'role'       => ucfirst($s->role),
                    'total_paid' => (float) $totalPaid,
                    'bank'       => $s->bank_name ?? 'N/A',
                    'account'    => $s->bank_account ?? 'N/A',
                ];
            })->toArray();

        $data = [
            'finance_summary' => [
                'available_balance'   => (float) $mainAccount->balance,
                'total_income'        => $totalIncome,
                'total_expenses'      => $totalExpenses,
                'total_transactions'  => FinanceTransaction::count(),
                'pending_count'       => FinanceTransaction::where('status', 'pending')->count(),
                'accounts_count'      => $accounts->count(),
            ],
            'accounts'     => $accounts->map(fn($a) => [
                'name'    => $a->name,
                'type'    => $a->type,
                'balance' => (float) $a->balance,
                'number'  => $a->account_number,
            ])->toArray(),
            'transactions' => $transactions,
            'staff'        => $staff,
            'generated_at' => now()->format('d F Y, h:i A'),
            'generated_by' => $user->name,
        ];

        // Generate PDF using master sheet template (reuse)
        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.finance-vault-pdf', compact('data'));
            $pdf->setPaper('A4', 'portrait');
            $pdfBytes = $pdf->output();
        } catch (\Throwable $e) {
            // Fallback: use existing master sheet
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.master-sheet-pdf', ['data' => $data]);
            $pdf->setPaper('A4', 'portrait');
            $pdfBytes = $pdf->output();
        }

        $mailLog = \App\Helpers\MailLogger::log(
            'finance_sheet_export',
            $targetEmail,
            'AK Patel (Admin)',
            $user->id,
            fn() => \Illuminate\Support\Facades\Mail::to($targetEmail)->send(
                new \App\Mail\FinanceSheetMail($user->name, $data, $pdfBytes, $user->name)
            ),
            'finance_export'
        );

        \App\Helpers\ActivityLogger::log('finance.sheet_exported', 'User', $user->id, $user->name,
            "Finance sheet exported by {$user->name} and sent to {$targetEmail}.");

        if ($request->wantsJson()) {
            return response()->json([
                'ok'      => $mailLog->success,
                'message' => $mailLog->success
                    ? "📧 Finance Data Vault exported & sent to {$targetEmail} successfully!"
                    : "❌ Export failed: " . ($mailLog->error_message ?? 'Mail error'),
            ], $mailLog->success ? 200 : 422);
        }

        return back()->with($mailLog->success ? 'success' : 'error',
            $mailLog->success
                ? "📧 Finance sheet dispatched to {$targetEmail}!"
                : "❌ Export failed: " . $mailLog->error_message
        );
    }

    /**
     * Verify PIN 123 for Available Balance reveal or Lock
     */
    public function verifyPin(Request $request)
    {
        $pin = trim($request->input('pin', ''));

        if ($pin === 'lock') {
            session(['finance_pin_verified' => false]);
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'locked' => true]);
            }
            return back()->with('info', '🔒 Available balance locked.');
        }

        if ($pin === (string) config('app.finance_pin', env('FINANCE_PIN', '123'))) {
            session(['finance_pin_verified' => true]);

            if ($request->wantsJson()) {
                $mainAccount = $this->getMainBankAccount();
                return response()->json([
                    'success' => true,
                    'balance' => number_format($mainAccount->balance, 2),
                    'message' => 'PIN verified successfully!',
                ]);
            }

            return back()->with('success', '🔓 PIN Verified! Available balance unlocked.');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect PIN. Please try again.',
            ], 422);
        }

        return back()->with('error', '❌ Incorrect PIN. Please try again.');
    }

    /**
     * Staff Detail View
     */
    public function staffDetail(User $staff)
    {
        abort_if($staff->role !== 'staff', 404);

        $logs = StaffWorkLog::where('user_id', $staff->id)
            ->whereNotNull('clock_out')
            ->orderByDesc('work_date')
            ->get();

        $byMonth = $logs->groupBy(fn($l) => $l->work_date->format('Y-m'));
        $monthStats = $byMonth->map(function ($items, $key) {
            $seconds = $items->sum('duration_seconds');
            $hours   = round($seconds / 3600, 2);
            return [
                'label'    => \Carbon\Carbon::createFromFormat('Y-m', $key)->format('F Y'),
                'hours'    => $hours,
                'days'     => $items->pluck('work_date')->unique()->count(),
                'earned'   => round($hours * self::HOURLY_RATE, 0),
                'sessions' => $items->count(),
            ];
        });

        $currentMonthHours = round(
            StaffWorkLog::where('user_id', $staff->id)
                ->whereMonth('work_date', now()->month)
                ->whereYear('work_date', now()->year)
                ->whereNotNull('clock_out')
                ->sum('duration_seconds') / 3600, 2
        );
        $currentMonthEarned = round($currentMonthHours * self::HOURLY_RATE, 0);

        $payments = $staff->salaryPayments()->with('paidBy')->latest()->get();
        $totalPaidEver = $payments->where('payment_status', 'paid')->sum('amount');
        $accounts = FinanceAccount::orderBy('id')->get();

        return view('finance.staff-detail', compact(
            'staff',
            'monthStats',
            'payments',
            'logs',
            'currentMonthHours',
            'currentMonthEarned',
            'totalPaidEver',
            'accounts'
        ));
    }

    /**
     * Full Transaction History
     */
    public function history(Request $request)
    {
        $query = FinanceTransaction::with(['fromAccount', 'toAccount', 'staff', 'creator'])->latest();

        if ($request->filled('q')) {
            $search = '%' . trim($request->q) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', $search)
                  ->orWhere('sender_name', 'like', $search)
                  ->orWhere('receiver_name', 'like', $search)
                  ->orWhere('client_name', 'like', $search)
                  ->orWhere('notes', 'like', $search)
                  ->orWhere('amount', 'like', $search);
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('transaction_type', $request->type);
        }

        $transactions = $query->paginate(25)->withQueryString();
        $accounts     = FinanceAccount::orderBy('id')->get();
        $mainAccount  = $this->getMainBankAccount();

        return view('finance.history', compact('transactions', 'accounts', 'mainAccount'));
    }
}
