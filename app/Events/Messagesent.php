<?php

namespace App\Events;

use App\Models\Message; // Import the Message model
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Messagesent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     * 
     * @param Message $message
     * @return void
     * 
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
       
        return [new Channel('chat.' . $this->message->receiver_id)]; 
    }

    public function broadcastAs()
    {
        return 'Messagesent';
    }

    /**
     * Get the broadcast data.
     * 
     * @return array
     * 
     */
    public function broadcastWith()
    {
        return [
            'message' => $this->message->messages, // Fixed field to match the model's field (messages, not message)
            'sender_id' => $this->message->sender_id,
            'created_at' => $this->message->created_at->diffForHumans(),
        ];
    }
}
