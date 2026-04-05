<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $fillable = [
        'auction_id',
        'user_id',
        'amount',
    ];

    // Which auction this bid belongs to
    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    // Who placed the bid
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
