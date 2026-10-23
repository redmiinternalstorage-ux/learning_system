<?php

namespace App\Http\Controllers;

use App\Helpers\MailLogger;
use App\Mail\TicketReplyMail;
use App\Mail\TicketStatusUpdatedMail;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminSupportController extends Controller
{
    // ── All tickets (admin) ───────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'lastReply'])->latest();

        if ($request->filled('status'))   $query->where('status',   $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('subject', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%")
                                                    ->orWhere('email', 'like', "%{$s}%"));
            });
        }

        $tickets = $query->paginate(15)->withQueryString();

        // Single aggregate instead of 4 separate COUNT queries
        $agg = SupportTicket::selectRaw(
            "SUM(CASE WHEN status='open' THEN 1 ELSE 0 END) as open_count,
             SUM(CASE WHEN status='in_progress' THEN 1 ELSE 0 END) as in_progress_count,
             SUM(CASE WHEN status='resolved' THEN 1 ELSE 0 END) as resolved_count,
             SUM(CASE WHEN admin_read=0 THEN 1 ELSE 0 END) as unread_count"
        )->first();

        $stats = [
            'open'        => (int) ($agg->open_count        ?? 0),
            'in_progress' => (int) ($agg->in_progress_count ?? 0),
            'resolved'    => (int) ($agg->resolved_count    ?? 0),
            'unread'      => (int) ($agg->unread_count      ?? 0),
        ];

        return view('admin.support.index', compact('tickets', 'stats'));
    }

    // ── View ticket + reply form (admin) ──────────────────────────────────
    public function show(SupportTicket $ticket)
    {
        $ticket->load('replies.user', 'user');
        $ticket->update(['admin_read' => true]);
        return view('admin.support.show', compact('ticket'));
    }

    // ── Admin reply (web + optional email) ───────────────────────────────
    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message'    => 'required|string|max:5000',
            'new_status' => 'required|in:open,in_progress,resolved,closed',
            'send_email' => 'nullable|boolean',
        ]);

        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id'   => Auth::id(),
            'message'   => $request->message,
            'is_admin'  => true,
            'mail_sent' => false,
        ]);

        // Update ticket
        $updateData = [
            'status'        => $request->new_status,
            'admin_replied' => true,
            'user_read'     => false,
            'admin_read'    => true,
        ];
        if ($request->new_status === 'resolved') {
            $updateData['resolved_at'] = now();
        }
        $ticket->update($updateData);

        // Send email if checkbox ticked
        $mailSent = false;
        if ($request->boolean('send_email', true)) {
            MailLogger::log(
                'ticket_reply',
                $ticket->user->email,
                $ticket->user->name,
                $ticket->user_id,
                fn() => Mail::to($ticket->user->email)->send(new TicketReplyMail(
                    recipientName:  $ticket->user->name,
                    ticketSubject:  $ticket->subject,
                    replyMessage:   $request->message,
                    ticketId:       $ticket->id,
                    ticketStatus:   $request->new_status,
                )),
                'admin_support'
            );
            $reply->update(['mail_sent' => true]);
            $mailSent = true;
        }

        $msg = '✅ Reply posted' . ($mailSent ? ' and email sent to ' . $ticket->user->email : '') . '.';
        return back()->with('success', $msg);
    }

    // ── Quick status update ───────────────────────────────────────────────
    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate(['status' => 'required|in:open,in_progress,resolved,closed']);
        $oldStatus = $ticket->status;
        $newStatus = $request->status;

        $data = ['status' => $newStatus];
        if ($newStatus === 'resolved') $data['resolved_at'] = now();
        $ticket->update($data);

        // Send auto email if status changed
        if ($oldStatus !== $newStatus && $ticket->user) {
            MailLogger::log(
                'ticket_status_update',
                $ticket->user->email,
                $ticket->user->name,
                $ticket->user_id,
                fn() => Mail::to($ticket->user->email)->send(new TicketStatusUpdatedMail(
                    recipientName: $ticket->user->name,
                    ticketSubject: $ticket->subject,
                    ticketId:      $ticket->id,
                    oldStatus:     $oldStatus,
                    newStatus:     $newStatus,
                )),
                'admin_support'
            );
        }

        return back()->with('success', 'Status updated to ' . ucfirst($newStatus) . ' and notification sent to student.');
    }

    // ── Delete ticket ─────────────────────────────────────────────────────
    public function destroy(SupportTicket $ticket)
    {
        $ticket->delete();
        return redirect()->route('admin.support.index')->with('success', 'Ticket deleted.');
    }
}
