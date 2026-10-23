<?php

namespace App\Http\Controllers;

use App\Models\StaffChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffChatController extends Controller
{
    /**
     * Show WhatsApp-style Team Chat Interface (Main Admin & Staff Admins ONLY)
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        // Strictly fetch ONLY admin, staff, and finance team members (EXCLUDING students)
        $teamMembers = User::whereIn('role', ['admin', 'staff', 'finance'])
            ->where('id', '!=', $currentUser->id)
            ->where('is_blocked', false)
            ->get();

        // Calculate latest message & unread count for each staff/admin member
        $teamMembers->transform(function ($member) use ($currentUser) {
            $lastMsg = StaffChatMessage::where(function ($q) use ($currentUser, $member) {
                $q->where('sender_id', $currentUser->id)->where('receiver_id', $member->id);
            })->orWhere(function ($q) use ($currentUser, $member) {
                $q->where('sender_id', $member->id)->where('receiver_id', $currentUser->id);
            })->latest()->first();

            $unread = StaffChatMessage::where('sender_id', $member->id)
                ->where('receiver_id', $currentUser->id)
                ->where('is_read', false)
                ->count();

            $member->last_message = $lastMsg;
            $member->unread_count = $unread;
            $member->last_activity_ts = $lastMsg ? $lastMsg->created_at->timestamp : 0;
            return $member;
        });

        // Sort contacts by latest message timestamp
        $teamMembers = $teamMembers->sortByDesc('last_activity_ts')->values();

        // Selected recipient user (strictly restricted to admin/staff/finance roles only)
        $activeUser = null;
        $messages = collect();

        if ($request->filled('user_id')) {
            $activeUser = User::whereIn('role', ['admin', 'staff', 'finance'])
                ->where('is_blocked', false)
                ->where('id', '!=', $currentUser->id)
                ->find($request->user_id);
        }

        if (!$activeUser && $teamMembers->isNotEmpty()) {
            $activeUser = $teamMembers->first();
        }

        if ($activeUser) {
            // Mark messages from activeUser to currentUser as read
            StaffChatMessage::where('sender_id', $activeUser->id)
                ->where('receiver_id', $currentUser->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            // Fetch internal staff conversation
            $messages = StaffChatMessage::where(function ($q) use ($currentUser, $activeUser) {
                $q->where('sender_id', $currentUser->id)->where('receiver_id', $activeUser->id);
            })->orWhere(function ($q) use ($currentUser, $activeUser) {
                $q->where('sender_id', $activeUser->id)->where('receiver_id', $currentUser->id);
            })->orderBy('created_at', 'asc')->get();
        }

        return view('admin.staff-chat.index', compact('teamMembers', 'activeUser', 'messages'));
    }

    /**
     * Send message to a staff/admin team member
     */
    public function send(Request $request, User $user)
    {
        $request->validate([
            'message' => 'required|string|max:3000',
        ]);

        $currentUser = Auth::user();

        // Strictly ensure recipient is an admin, staff, or finance team member
        if (!in_array($user->role, ['admin', 'staff', 'finance'])) {
            return response()->json(['error' => 'Invalid recipient — Team Chat is restricted to staff and admins only.'], 403);
        }

        $msg = StaffChatMessage::create([
            'sender_id'   => $currentUser->id,
            'receiver_id' => $user->id,
            'message'     => $request->message,
            'is_read'     => false,
        ]);

        return response()->json([
            'ok'      => true,
            'message' => [
                'id'          => $msg->id,
                'sender_id'   => $msg->sender_id,
                'receiver_id' => $msg->receiver_id,
                'message'     => e($msg->message),
                'is_me'       => true,
                'time'        => $msg->created_at->format('h:i A'),
                'ts'          => $msg->created_at->valueOf(),
            ]
        ]);
    }

    /**
     * Poll new messages for active staff chat
     */
    public function poll(Request $request, User $user)
    {
        $currentUser = Auth::user();

        if (!in_array($user->role, ['admin', 'staff', 'finance'])) {
            return response()->json(['messages' => []]);
        }

        $since = ($request->filled('since') && $request->since > 0)
            ? \Carbon\Carbon::createFromTimestampMs((int) $request->since)
            : now()->subSeconds(3);

        $newMessages = StaffChatMessage::where('sender_id', $user->id)
            ->where('receiver_id', $currentUser->id)
            ->where('created_at', '>', $since)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark as read
        if ($newMessages->isNotEmpty()) {
            StaffChatMessage::whereIn('id', $newMessages->pluck('id'))->update(['is_read' => true]);
        }

        $formatted = $newMessages->map(fn($m) => [
            'id'          => $m->id,
            'sender_id'   => $m->sender_id,
            'receiver_id' => $m->receiver_id,
            'message'     => e($m->message),
            'is_me'       => false,
            'sender_name' => $user->name,
            'time'        => $m->created_at->format('h:i A'),
            'ts'          => $m->created_at->valueOf(),
        ]);

        return response()->json([
            'messages' => $formatted
        ]);
    }
}
