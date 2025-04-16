<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function store(Request $request)
    {


        // Validate the incoming data
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id|integer|different:sender_id',  // Ensure receiver isn't the same as the sender
            'messages' => 'required|string',
        ]);

        // Create and save the message
        $message = Message::create([
            'sender_id' => auth()->id(),  // Logged-in user as the sender
            'receiver_id' => $validated['receiver_id'],  // Receiver ID
            'messages' => $validated['messages'],  // The actual message
        ]);

        return response()->json([
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'message' => $message->messages,
            'is_read' => $message->is_read,
            'created_at' => $message->created_at,
        ], 201);
    }

    public function fetchMessages($userId)
    {
        $messages = Message::where(function ($query) use ($userId) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $userId);
        })
        ->orWhere(function ($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', Auth::id());
        })
        ->orderBy('created_at', 'asc')
        ->get();

        return response()->json($messages);
    }

    public function markAsRead($messageId)
    {
        $message = Message::find($messageId);

        if (!$message) {
            return response()->json(['error' => 'Message not found.'], 404);
        }

        if ($message->receiver_id == Auth::id() && !$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return response()->json([
            'message' => 'Message marked as read!',
            'data' => $message
        ]);
    }
}
