<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Storage;

class MessageSent implements ShouldBroadcast
{
    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('sender');
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->message->transaction_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'type' => $this->message->type,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'attachment_url' => $this->message->attachment_path
                ? Storage::url($this->message->attachment_path)
                : null,
            'created_at' => $this->message->created_at->toIso8601String(),
        ];
    }
}
