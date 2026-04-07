<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceBidRequest;
use App\Models\Auction;
 use App\Services\AuctionService;
class BidController extends Controller
{
//bid placing
     public function store(PlaceBidRequest $request, Auction $auction, AuctionService $service)
    {
        $result = $service->placeBid(
            user: $request->user(),
            auction: $auction,
            amount: $request->amount
        );

        return response()->json($result);
    }
}
