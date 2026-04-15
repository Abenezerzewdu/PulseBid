<?php
namespace App\Services;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

/**
 * Service: AuctionService
 * 
 * Purpose: 
 * Handles the strict business logic for mutating Auctions, entirely decoupling 
 * heavy algorithmic processing (bidding authorization, sniping deterrence) away from 
 * the Controllers into a clean, reusable architecture layer.
 */
class AuctionService
{
    public function placeBid(User $user, Auction $auction, float $amount): array
    {
        return DB::transaction(function () use ($user, $auction, $amount) {

            // Lock auction row
            $auction = Auction::lockForUpdate()->findOrFail($auction->id);

            $this->ensureAuctionIsActive($auction);
            $this->ensureNotSeller($user, $auction);
            $this->ensureValidBidAmount($auction, $amount);
            $this->ensureCooldown($user, $auction);

            // Create bid
            $bid = Bid::create([
                'auction_id' => $auction->id,
                'user_id' => $user->id,
                'amount' => $amount,
            ]);

            // Update auction price
            $auction->update([
                'current_price' => $amount,
            ]);

            // Handle anti-sniping
            $this->handleAntiSniping($auction);

            // REAL-TIME FIX: Dispatch the secure WebSockets `BidPlaced` event to the Echo channel. 
            // This guarantees all users statically viewing the Auction page receive dynamic top bid 
            // increments instantly via the network directly skipping reload.
            \App\Events\BidPlaced::dispatch($auction->id, [
                'id' => $bid->id,
                'amount' => $bid->amount,
                'user_id' => $bid->user_id,
                'created_at' => $bid->created_at,
                'user' => ['name' => $user->name],
            ]);

            return [
                'message' => 'Bid placed successfully',
                'current_price' => $auction->current_price,
            ];
        });
    }

    //ensuring aution is active by checking start and end times
    private function ensureAuctionIsActive(Auction $auction): void
    {
        $now = now();

        if ($now->lt($auction->start_time)) {
            throw ValidationException::withMessages([
                'auction' => 'Auction has not started',
            ]);
        }

        if ($now->gte($auction->end_time)) {
            throw ValidationException::withMessages([
                'auction' => 'Auction has ended',
            ]);
        }
    }

    //ensuring sellers doesn't bid in their auction
    private function ensureNotSeller(User $user, Auction $auction): void
    {
        if ($auction->user_id === $user->id) {
            throw ValidationException::withMessages([
                'user' => 'You cannot bid on your own auction',
            ]);
        }
    }

    //valid bid amount check
    private function ensureValidBidAmount(Auction $auction, float $amount): void
    {
        $currentPrice = $auction->current_price ?? $auction->starting_price;

        if ($amount <= $currentPrice) {
            throw ValidationException::withMessages([
                'amount' => 'Bid must be higher than current price',
            ]);
        }
    }

   //cooldown bids in <5 secs
    private function ensureCooldown(User $user, Auction $auction): void
    {
        $lastBid = Bid::where('user_id', $user->id)
            ->where('auction_id', $auction->id)
            ->latest()
            ->first();

        if ($lastBid && now()->diffInSeconds($lastBid->created_at) < 5) {
            throw ValidationException::withMessages([
                'cooldown' => 'Please wait before placing another bid',
            ]);
        }
    }

    //to add 10 sec on the last 
    private function handleAntiSniping(Auction $auction): void
    {
        $secondsLeft = now()->diffInSeconds($auction->end_time, false);

        if ($secondsLeft <= 10) {
            $auction->update([
                'end_time' => $auction->end_time->addSeconds(10),
            ]);
        }
    }

    //create an auction 
    public function createAuction(User $user, array $data): Auction
{
    // Handle image (optional)
    if (isset($data['image'])) {
        $data['image'] = $data['image']->store('auctions', 'public');
    }

    $slug = Str::slug($data['title']);

$originalSlug = $slug;
$count = 1;

while (Auction::where('slug', $slug)->exists()) {
    $slug = $originalSlug . '-' . $count++;
}

    return Auction::create([
        'user_id' => $user->id,
        'title' => $data['title'],
        'description' => $data['description'] ?? null,
        'image' => $data['image'] ?? null,
        'starting_price' => $data['starting_price'],
        'current_price' => null,
        'start_time' => $data['start_time'],
        'end_time' => $data['end_time'],
        'slug' => $slug,
    ]);
}
}