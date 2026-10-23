<?php

namespace App\Http\Controllers;

use App\Helpers\MailLogger;
use App\Mail\TicketOpenedMail;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class HelpCenterController extends Controller
{
    // ── Help Center Home (student) ────────────────────────────────────────
    public function index()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())
            ->with(['replies']) // eager load to avoid N+1
            ->latest()
            ->paginate(10);
        return view('help.index', compact('tickets'));
    }

    // ── Submit new ticket ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'subject'  => 'required|string|max:200',
            'message'  => 'required|string|max:3000',
            'category' => 'required|in:' . implode(',', array_keys(SupportTicket::$categories)),
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $ticket = SupportTicket::create([
            'user_id'  => Auth::id(),
            'subject'  => $request->subject,
            'message'  => $request->message,
            'category' => $request->category,
            'priority' => $request->priority,
            'status'   => 'open',
        ]);

        // Confirmation email to user
        $user = Auth::user();
        MailLogger::log(
            'ticket_opened',
            $user->email, $user->name, $user->id,
            fn() => Mail::to($user->email)->send(new TicketOpenedMail(
                recipientName:  $user->name,
                ticketSubject:  $ticket->subject,
                ticketMessage:  $ticket->message,
                ticketId:       $ticket->id,
                category:       $ticket->category,
                priority:       $ticket->priority,
            )),
            'help_center'
        );

        return redirect()->route('help.ticket', $ticket)
            ->with('success', '✅ Ticket #' . $ticket->id . ' submitted! Confirmation sent to your email.');
    }

    // ── View single ticket + replies ──────────────────────────────────────
    public function show(SupportTicket $ticket)
    {
        abort_if($ticket->user_id !== Auth::id(), 403);
        $ticket->load('replies.user');
        // Mark as read by user
        $ticket->update(['user_read' => true]);
        return view('help.ticket', compact('ticket'));
    }

    // ── Student add reply ─────────────────────────────────────────────────
    public function reply(Request $request, SupportTicket $ticket)
    {
        abort_if($ticket->user_id !== Auth::id(), 403);
        $request->validate(['message' => 'required|string|max:3000']);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id'   => Auth::id(),
            'message'   => $request->message,
            'is_admin'  => false,
        ]);

        $ticket->update(['status' => 'in_progress', 'admin_read' => false]);

        return back()->with('success', 'Reply sent successfully.');
    }
}
