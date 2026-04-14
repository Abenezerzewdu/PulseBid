<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Transaction;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MessageController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    //   Display the message dashboard with all conversations.
     
    public function index()
    {
        $user = auth()->user();

        // Fetch transactions involving the user
        $transactions = Transaction::where(function ($query) use ($user) {
            $query->where('seller_id', $user->id)
                  ->orWhere('buyer_id', $user->id);
        })
        ->with(['auction', 'seller', 'buyer', 'messages' => function ($query) {
            $query->latest()->limit(1);
        }])
        ->get()
        ->map(function ($transaction) use ($user) {
            $otherUser = $transaction->seller_id === $user->id 
                ? $transaction->buyer 
                : $transaction->seller;

            $latestMessage = $transaction->messages->first();
            $unreadCount = $transaction->messages()
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->count();

            return [
                'id' => $transaction->id,
                'other_user' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'avatar' => null, // Placeholder for now
                ],
                'auction' => [
                    'id' => $transaction->auction->id,
                    'title' => $transaction->auction->title,
                    'status' => $transaction->auction->status,
                ],
                'latest_message' => $latestMessage ? [
                    'content' => $latestMessage->content,
                    'type' => $latestMessage->type,
                    'created_at' => $latestMessage->created_at->toIso8601String(),
                ] : null,
                'unread_count' => $unreadCount,
            ];
        })
        ->sortByDesc(fn($t) => $t['latest_message']['created_at'] ?? '0')
        ->values();

        return Inertia::render('Messages/Index', [
            'conversations' => $transactions,
        ]);
    }

    /**
     * Fetch messages for a specific conversation.
     */
    public function show(Transaction $transaction)
    {
        $this->authorizeAccess($transaction);

        $messages = $transaction->messages()
            ->with('sender')
            ->oldest()
            ->get();

        // Mark messages as read
        $transaction->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'messages' => $messages->map(fn($m) => [
                'id' => $m->id,
                'sender_id' => $m->sender_id,
                'content' => $m->content,
                'type' => $m->type,
                'attachment_url' => $m->attachment_path ? Storage::url($m->attachment_path) : null,
                'created_at' => $m->created_at->toIso8601String(),
                'read_at' => $m->read_at ? $m->read_at->toIso8601String() : null,
            ]),
        ]);
    }

    /**
     * Store a new message.
     */
    public function store(Request $request, Transaction $transaction)
    {
    //Check whether the user have won bid and made transaction
        $this->authorizeAccess($transaction);

        $validated=$request->validate([
            'content' => 'required_without:attachment|string|nullable',
            'attachment' => 'nullable|file|max:10240', // 10MB max
            'type' => 'required|in:text,file',
        ]);

         $message = $this->messageService->sendMessage(
        $transaction,
        auth()->user(),
        $validated
    );

        return response()->json($message);
    }

    /**
     * Ensure the user can access this transaction's messages.
     */
    private function authorizeAccess(Transaction $transaction)
    {
        $userId = auth()->id();
        if ($transaction->seller_id !== $userId && $transaction->buyer_id !== $userId) {
            abort(403);
        }
    }
}
