<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Conversations list — shows all users the current user can chat with.
     */
    public function index()
    {
        $user = Auth::user();

        $contactsQuery = User::where('id', '!=', $user->id);

        if ($user->isClient()) {
            $contactsQuery->where('role', 'admin');
        } elseif ($user->isStaff()) {
            $contactsQuery->whereIn('role', ['admin', 'staff']);
        }

        $contacts = $contactsQuery->orderBy('name')->get();

        $conversations = $contacts->map(function ($contact) use ($user) {
            $lastMessage = Message::where(function ($q) use ($user, $contact) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $contact->id);
                })
                ->orWhere(function ($q) use ($user, $contact) {
                    $q->where('sender_id', $contact->id)->where('receiver_id', $user->id);
                })
                ->orderByDesc('created_at')
                ->first();

            $unread = Message::where('sender_id', $contact->id)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->count();

            return (object) [
                'contact'      => $contact,
                'last_message' => $lastMessage,
                'unread'       => $unread,
            ];
        })->sortByDesc(function ($c) {
            return optional($c->last_message)->created_at ?? now()->subYears(10);
        })->values();

        $totalUnread = $conversations->sum('unread');

        return view('messages.index', compact('conversations', 'totalUnread'));
    }

    /**
     * Show the chat thread with a specific user.
     */
    public function chat(User $contact)
    {
        $user = Auth::user();

        if ($user->isClient() && !$contact->isAdmin()) {
            abort(403, 'You can only message administrators.');
        }
        if ($user->isStaff() && $contact->isClient()) {
            abort(403, 'Staff cannot directly message clients.');
        }

        $messages = Message::where(function ($q) use ($user, $contact) {
                $q->where('sender_id', $user->id)->where('receiver_id', $contact->id);
            })
            ->orWhere(function ($q) use ($user, $contact) {
                $q->where('sender_id', $contact->id)->where('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        Message::where('sender_id', $contact->id)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.chat', compact('contact', 'messages'));
    }

    /**
     * Send a message — returns JSON for AJAX, redirect for normal POST.
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body'        => 'required|string|max:5000',
        ]);

        $user     = Auth::user();
        $receiver = User::findOrFail($request->receiver_id);

        if ($user->isClient() && !$receiver->isAdmin()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'You can only message administrators.'], 403);
            }
            abort(403, 'You can only message administrators.');
        }
        if ($user->isStaff() && $receiver->isClient()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Staff cannot directly message clients.'], 403);
            }
            abort(403, 'Staff cannot directly message clients.');
        }

        $message = Message::create([
            'sender_id'   => $user->id,
            'receiver_id' => $receiver->id,
            'body'        => $request->body,
        ]);

        // If AJAX request, return JSON instead of redirect
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id'         => $message->id,
                    'sender_id'  => $message->sender_id,
                    'body'       => $message->body,
                    'created_at' => $message->created_at->format('g:i A'),
                    'is_sent'    => true,
                ],
            ]);
        }

        return redirect()->route('messages.chat', $receiver->id);
    }

    /**
     * Fetch new messages after a given message ID (for polling).
     */
    public function fetch(Request $request, User $contact)
    {
        $user = Auth::user();

        if ($user->isClient() && !$contact->isAdmin()) {
            return response()->json([], 403);
        }
        if ($user->isStaff() && $contact->isClient()) {
            return response()->json([], 403);
        }

        $afterId = $request->query('after', 0);

        $newMessages = Message::where('id', '>', $afterId)
            ->where(function ($q) use ($user, $contact) {
                $q->where(function ($q2) use ($user, $contact) {
                    $q2->where('sender_id', $user->id)->where('receiver_id', $contact->id);
                })->orWhere(function ($q2) use ($user, $contact) {
                    $q2->where('sender_id', $contact->id)->where('receiver_id', $user->id);
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark received messages as read
        Message::where('sender_id', $contact->id)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $result = $newMessages->map(function ($msg) use ($user) {
            return [
                'id'         => $msg->id,
                'sender_id'  => $msg->sender_id,
                'body'       => $msg->body,
                'created_at' => $msg->created_at->format('g:i A'),
                'is_sent'    => $msg->sender_id === $user->id,
                'read_at'    => $msg->read_at,
            ];
        });

        return response()->json($result);
    }

    /**
     * Return unread count as JSON (for navbar badge polling).
     */
    public function unreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['unread' => $count]);
    }
}
