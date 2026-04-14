<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AuctionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $now = Carbon::now();
        $status = $this->deriveStatus();

        $timeLeft = null;
        $startsIn = null;

        if ($status === 'live' && $this->end_time) {
            $timeLeft = $this->formatCountdown($now->diffInSeconds($this->end_time, false));
        } elseif ($status === 'upcoming' && $this->start_time) {
            $startsIn = $this->formatCountdown($now->diffInSeconds($this->start_time, false));
        }

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'status' => $status,
            'current_price' => $this->current_price,
            'starting_price' => $this->starting_price,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'timeLeft' => $timeLeft,
            'startsIn' => $startsIn,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', fn () => [
                'name' => $this->user->name,
            ]),
            'bids' => $this->whenLoaded('bids', fn () => 
                $this->bids->map(fn ($bid) => [
                    'id' => $bid->id,
                    'amount' => $bid->amount,
                    'user_id' => $bid->user_id,
                    'created_at' => $bid->created_at,
                    'user' => $bid->relationLoaded('user') ? ['name' => $bid->user->name] : null,
                ])->sortByDesc('amount')->values()
            ),
        ];
    }

    private function deriveStatus(): string
    {
        $now = Carbon::now();

        if ($this->end_time && $now->greaterThan($this->end_time)) {
            return 'ended';
        }

        if ($this->start_time && $now->lessThan($this->start_time)) {
            return 'upcoming';
        }

        return 'live';
    }

    private function formatCountdown(int $seconds): string
    {
        if ($seconds <= 0) return '—';

        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;

        if ($h > 0) return "{$h}h {$m}m";
        if ($m > 0) return "{$m}m {$s}s";
        return "{$s}s";
    }
}
