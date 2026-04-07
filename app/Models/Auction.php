<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image',
        'starting_price',
        'current_price',
        'start_time',
        'end_time',
        'winner_id',
    ];

    // Seller (owner of auction)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // All bids on this auction
    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    // Winner of auction
    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    // Transaction after auction ends
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    protected $casts = [
    'start_time' => 'datetime',
    'end_time' => 'datetime',
];
}
