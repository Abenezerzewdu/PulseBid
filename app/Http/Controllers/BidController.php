<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BidController extends Controller
{
    public function store(Request $request, Auction $auction)
{
    $user = auth()->user();

    // 1. Validate input
    $request->validate([
        'amount' => 'required|numeric|min:0',
    ]);

    // 2. Wrap in DB transaction (CRITICAL)
    return DB::transaction(function () use ($request, $auction, $user) {

        // 🔒 Reload auction with lock (prevents race conditions)
        $auction = Auction::lockForUpdate()->find($auction->id);

        $now = now();

        // 3. Check auction time
        if ($now->lt($auction->start_time)) {
            return response()->json(['error' => 'Auction has not started'], 400);
        }

        if ($now->gte($auction->end_time)) {
            return response()->json(['error' => 'Auction has ended'], 400);
        }

        // 4. Prevent seller from bidding
        if ($auction->user_id === $user->id) {
            return response()->json(['error' => 'You cannot bid on your own auction'], 403);
        }

        // 5. Get current price safely
        $currentPrice = $auction->current_price ?? $auction->starting_price;

        // 6. Validate bid amount
        if ($request->amount <= $currentPrice) {
            return response()->json(['error' => 'Bid must be higher than current price'], 400);
        }

        // 7. (Optional) Bid cooldown
        $lastBid = Bid::where('user_id', $user->id)
            ->where('auction_id', $auction->id)
            ->latest()
            ->first();

        if ($lastBid && now()->diffInSeconds($lastBid->created_at) < 5) {
            return response()->json(['error' => 'Please wait before placing another bid'], 429);
        }

        // 8. Save bid
        $bid = Bid::create([
            'auction_id' => $auction->id,
            'user_id' => $user->id,
            'amount' => $request->amount,
        ]);

        // 9. Update auction current price
        $auction->update([
            'current_price' => $request->amount,
        ]);

        // 10. Anti-sniping logic
        $secondsLeft = $now->diffInSeconds($auction->end_time, false);

        if ($secondsLeft <= 10) {
            $auction->end_time = $auction->end_time->addSeconds(10);
            $auction->save();
        }

        return response()->json([
            'message' => 'Bid placed successfully',
            'current_price' => $auction->current_price,
        ]);
    });
}
}
