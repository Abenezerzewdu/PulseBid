<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'transaction_id',
        'sender_id',
        'content',
        'read_at',
        'type',
        'attachment_path',
    ];

    /**
     * Get the transaction this message belongs to.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
