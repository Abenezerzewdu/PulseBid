<?php

use Illuminate\Support\Facades\Broadcast;

use App\Models\Transaction;

Broadcast::channel('chat.{transactionId}', function ($user, $transactionId) {
    $transaction = Transaction::find($transactionId);

    return $transaction &&
        ($transaction->buyer_id === $user->id ||
         $transaction->seller_id === $user->id);
});
