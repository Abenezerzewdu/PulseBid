<?php
namespace App\Services;;

class MessageService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }


     public function sendMessage($transaction, $user, $data)
    {
        $attachmentPath = null;

        if (isset($data['attachment'])) {
            $attachmentPath = $data['attachment']->store('attachments', 'public');
        }

        $message = $transaction->messages()->create([
            'sender_id' => $user->id,
            'content' => $data['content'] ?? null,
            'type' => $data['type'],
            'attachment_path' => $attachmentPath,
        ]);

        //  Fire WebSocket event
            event(new MessageSent($message));
        return $message;
    }
}
