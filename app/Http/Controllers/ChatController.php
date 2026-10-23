<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    // ── Student: open chat widget ─────────────────────────────────────────
    public function student()
    {
        $user = Auth::user();

        // Get or create a session_id for this user
        $sessionId = session('chat_session_id');
        if (!$sessionId) {
            // Check if there is an existing open/active chat
            $existing = ChatMessage::where('user_id', $user->id)
                ->whereIn('chat_status', ['waiting', 'active'])
                ->orderByDesc('created_at')
                ->value('session_id');
            $sessionId = $existing ?? Str::uuid()->toString();
            session(['chat_session_id' => $sessionId]);
        }

        $messages = ChatMessage::where('session_id', $sessionId)
            ->orderBy('created_at')
            ->get();

        $chatStatus = ChatMessage::where('session_id', $sessionId)
            ->orderByDesc('created_at')
            ->value('chat_status') ?? 'waiting';

        return view('help.chat', compact('messages', 'sessionId', 'chatStatus'));
    }

    // ── Student: send message ─────────────────────────────────────────────
    public function studentSend(Request $request)
    {
        $request->validate(['message' => 'required|string|max:2000']);
        $user = Auth::user();
        $sessionId = session('chat_session_id') ?? Str::uuid()->toString();
        session(['chat_session_id' => $sessionId]);

        ChatMessage::create([
            'session_id'  => $sessionId,
            'user_id'     => $user->id,
            'message'     => $request->message,
            'is_admin'    => false,
            'chat_status' => 'waiting',
        ]);

        return response()->json(['ok' => true]);
    }

    // ── Student: poll for new messages (long-polling / AJAX) ─────────────
    public function studentPoll(Request $request)
    {
        $sessionId = session('chat_session_id');
        if (!$sessionId) return response()->json(['messages' => [], 'status' => 'waiting']);

        $since = ($request->filled('since') && $request->since > 0)
            ? \Carbon\Carbon::createFromTimestampMs((int) $request->since)
            : now()->subSeconds(2);

        $messages = ChatMessage::where('session_id', $sessionId)
            ->where('created_at', '>', $since)
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => [
                'id'       => $m->id,
                'message'  => $m->message,
                'is_admin' => $m->is_admin,
                'time'     => $m->created_at->format('h:i A'),
                'ts'       => $m->created_at->valueOf(),
            ]);

        // Mark admin messages as read
        ChatMessage::where('session_id', $sessionId)
            ->where('is_admin', true)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $status = ChatMessage::where('session_id', $sessionId)
            ->orderByDesc('created_at')
            ->value('chat_status') ?? 'waiting';

        return response()->json(['messages' => $messages, 'status' => $status]);
    }

    // ── Student: close chat ───────────────────────────────────────────────
    public function studentClose()
    {
        $sessionId = session('chat_session_id');
        if ($sessionId) {
            ChatMessage::where('session_id', $sessionId)->update(['chat_status' => 'closed']);
        }
        session()->forget('chat_session_id');
        return response()->json(['ok' => true]);
    }

    // ── Admin: all active chats ───────────────────────────────────────────
    public function adminIndex()
    {
        $sessions = ChatMessage::with('user')
            ->select('session_id', 'user_id')
            ->selectRaw('MAX(created_at) as last_at')
            ->selectRaw('SUM(CASE WHEN is_admin = 0 AND is_read = 0 THEN 1 ELSE 0 END) as unread')
            ->groupBy('session_id', 'user_id')
            ->orderByDesc('last_at')
            ->paginate(20);

        $sessionIds = $sessions->pluck('session_id')->toArray();
        if (!empty($sessionIds)) {
            $latestStatuses = ChatMessage::whereIn('session_id', $sessionIds)
                ->orderBy('id', 'desc')
                ->get()
                ->unique('session_id')
                ->pluck('chat_status', 'session_id');

            $sessions->getCollection()->transform(function($s) use ($latestStatuses) {
                $s->chat_status = $latestStatuses[$s->session_id] ?? 'waiting';
                return $s;
            });
        }

        return view('admin.chat.index', compact('sessions'));
    }

    // ── Admin: open single chat ───────────────────────────────────────────
    public function adminShow(string $sessionId)
    {
        $messages = ChatMessage::where('session_id', $sessionId)
            ->with(['user', 'admin', 'assignedTo', 'transferredBy'])
            ->orderBy('created_at')
            ->get();

        $firstMsg = $messages->first();
        abort_if(!$firstMsg, 404);

        $student = $firstMsg->user;
        $status  = $messages->last()->chat_status ?? 'waiting';

        // Mark all unread user messages as read
        ChatMessage::where('session_id', $sessionId)
            ->where('is_admin', false)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // List of staff/admins for transfer option
        $staffList = \App\Models\User::whereIn('role', ['admin', 'staff'])
            ->where('is_blocked', false)
            ->get();

        return view('admin.chat.show', compact('messages', 'sessionId', 'student', 'status', 'staffList'));
    }

    // ── Admin/Staff: transfer/move chat to another staff member ──────────
    public function transferChat(Request $request, string $sessionId)
    {
        $request->validate([
            'target_user_id' => 'required|exists:users,id',
            'transfer_note'  => 'nullable|string|max:500',
        ]);

        $first = ChatMessage::where('session_id', $sessionId)->first();
        abort_if(!$first, 404);

        $targetUser = \App\Models\User::findOrFail($request->target_user_id);
        $currentUser = Auth::user();
        $note = trim($request->transfer_note ?? '');

        // Update assignment on session
        ChatMessage::where('session_id', $sessionId)->update([
            'assigned_to'    => $targetUser->id,
            'transferred_by' => $currentUser->id,
            'transfer_note'  => $note,
            'chat_status'    => 'active',
        ]);

        // System notification message inside chat stream
        $transferNotice = "➡️ Chat transferred to {$targetUser->name} by {$currentUser->name}" . ($note ? " | Note: {$note}" : "");
        ChatMessage::create([
            'session_id'     => $sessionId,
            'user_id'        => $first->user_id,
            'admin_id'       => $currentUser->id,
            'message'        => $transferNotice,
            'is_admin'       => true,
            'chat_status'    => 'active',
            'assigned_to'    => $targetUser->id,
            'transferred_by' => $currentUser->id,
            'transfer_note'  => $note,
        ]);

        \App\Helpers\ActivityLogger::log('chat.transferred', 'ChatMessage', null, $sessionId,
            "Transferred to {$targetUser->name} by {$currentUser->name}");

        return redirect()->back()->with('success', "✅ Support Chat successfully transferred to {$targetUser->name}.");
    }

    // ── Admin: poll for new messages ──────────────────────────────────────
    public function adminPoll(Request $request, string $sessionId)
    {
        $since = ($request->filled('since') && $request->since > 0)
            ? \Carbon\Carbon::createFromTimestampMs((int) $request->since)
            : now()->subSeconds(2);

        $messages = ChatMessage::where('session_id', $sessionId)
            ->where('created_at', '>', $since)
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => [
                'id'       => $m->id,
                'message'  => $m->message,
                'is_admin' => $m->is_admin,
                'time'     => $m->created_at->format('h:i A'),
                'ts'       => $m->created_at->valueOf(),
                'sender'   => $m->is_admin ? 'Admin' : ($m->user->name ?? 'User'),
            ]);

        $status = ChatMessage::where('session_id', $sessionId)
            ->orderByDesc('created_at')
            ->value('chat_status') ?? 'waiting';

        return response()->json(['messages' => $messages, 'status' => $status]);
    }

    // ── Admin: close chat ─────────────────────────────────────────────────
    public function adminClose(string $sessionId)
    {
        ChatMessage::where('session_id', $sessionId)->update(['chat_status' => 'closed']);
        return response()->json(['ok' => true]);
    }
}
