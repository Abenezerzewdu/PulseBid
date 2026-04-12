<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceBidRequest;
use App\Models\Auction;
use App\Services\AuctionService;

class BidController extends Controller
{
    /**
     * Place a bid on an auction.
     *
     * On success  → redirect back to the auction show page with a flash notice.
     * On failure  → Laravel's ValidationException automatically flashes errors
     *               back to the Inertia page (no extra handling needed).
     */
    public function store(PlaceBidRequest $request, Auction $auction, AuctionService $service)
    {
        $service->placeBid(
            user:    $request->user(),
            auction: $auction,
            amount:  (float) $request->amount,
        );

        return redirect()
            ->route('auctions.show', $auction->slug)
            ->with('success', 'Your bid was placed successfully!');
    }
}
