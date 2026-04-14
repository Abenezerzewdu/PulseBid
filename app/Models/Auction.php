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
        'slug',
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

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopeFilter($query, array $filters)
    {
        $now = \Carbon\Carbon::now();

        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        });

        $query->when($filters['status'] ?? null, function ($query, $status) use ($now) {
            if ($status !== 'all') {
                match ($status) {
                    'live' => $query->where('start_time', '<=', $now)->where('end_time', '>=', $now),
                    'upcoming' => $query->where('start_time', '>', $now),
                    'ended' => $query->where('end_time', '<', $now),
                    default => $query,
                };
            }
        });
    }

    public function scopeSort($query, ?string $sort)
    {
        match ($sort) {
            'ending' => $query->orderBy('end_time', 'asc'),
            'price_asc' => $query->orderBy('current_price', 'asc'),
            'price_desc' => $query->orderBy('current_price', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };
    }

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
}
