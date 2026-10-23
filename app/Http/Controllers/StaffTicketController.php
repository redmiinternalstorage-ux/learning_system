<?php

namespace App\Http\Controllers;

use App\Helpers\MailLogger;
use App\Mail\TicketReplyMail;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class StaffTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user','lastReply'])->latest();

        if ($request->filled('status'))
            $query->where('status', $request->status);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('subject','like',"%$s%")
                  ->orWhereHas('user', fn($u) => $u->where('name','like',"%$s%"));
            });
        }

        $tickets = $query->paginate(15)->withQueryString();

        $stats = [
            'open'        => SupportTicket::where('status','open')->count(),
            'in_progress' => SupportTicket::where('status','in_progress')->count(),
            'resolved'    => SupportTicket::where('status','resolved')->count(),
            'unread'      => SupportTicket::where('admin_read',false)->count(),
        ];

        return view('staff.tickets.index', compact('tickets','stats'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load('replies.user','user');
        $ticket->update(['admin_read' => true]);
        return view('staff.tickets.show', compact('ticket'));
    }

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

        $mailSent = false;
        if ($request->boolean('send_email', true)) {
            MailLogger::log(
                'ticket_reply',
                $ticket->user->email,
                $ticket->user->name,
                $ticket->user_id,
                fn() => Mail::to($ticket->user->email)->send(new TicketReplyMail(
                    recipientName: $ticket->user->name,
                    ticketSubject: $ticket->subject,
                    replyMessage:  $request->message,
                    ticketId:      $ticket->id,
                    ticketStatus:  $request->new_status,
                )),
                'staff_support'
            );
            $reply->update(['mail_sent' => true]);
            $mailSent = true;
        }

        return back()->with('success', '✅ Reply sent' . ($mailSent ? ' + email notified' : '') . '.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate(['status' => 'required|in:open,in_progress,resolved,closed']);
        $data = ['status' => $request->status];
        if ($request->status === 'resolved') $data['resolved_at'] = now();
        $ticket->update($data);
        return back()->with('success', 'Status updated.');
    }

    public function transfer(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'target_user_id' => 'required|exists:users,id',
            'transfer_note'  => 'nullable|string|max:500',
        ]);

        $targetUser  = \App\Models\User::findOrFail($request->target_user_id);
        $currentUser = Auth::user();
        $note        = trim($request->transfer_note ?? '');

        $ticket->update([
            'assigned_to'    => $targetUser->id,
            'transferred_by' => $currentUser->id,
            'transfer_note'  => $note,
            'status'         => 'in_progress',
        ]);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $currentUser->id,
            'message'   => "➡️ Ticket transferred to {$targetUser->name} by {$currentUser->name}" . ($note ? " | Note: {$note}" : ""),
            'is_admin'  => true,
            'mail_sent' => false,
        ]);

        return back()->with('success', "✅ Ticket successfully transferred to {$targetUser->name}.");
    }
}
