<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuctionRequest;
use App\Models\Auction;
use App\Services\AuctionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuctionController extends Controller
{
    public function store(
    StoreAuctionRequest $request,
    AuctionService $service
) {
    $auction = $service->createAuction(
        $request->user(),
        $request->validated()
    );

    return redirect()->route('auctions.show', $auction->id);
}

    public function create() {
         return Inertia::render('Auctions/Create');
    }

    public function show(Auction $auction){
        return Inertia::render('Auctions/Show', [
    'auction' => [
        'id' => $auction->id,
        'title' => $auction->title,
        'current_price' => $auction->current_price,
        'starting_price' => $auction->starting_price,
        'end_time' => $auction->end_time,
        'bids' => $auction->bids->map(fn ($bid) => [
            'id' => $bid->id,
            'amount' => $bid->amount,
            'user' => [
                'name' => $bid->user->name,
            ]
        ])
    ],
    'authUserId' => auth()->id(),
]);
    }
}
