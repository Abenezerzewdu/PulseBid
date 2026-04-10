<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuctionRequest;
use App\Models\Auction;
use App\Services\AuctionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuctionController extends Controller
{
   public function index(){
      return Inertia::render('Auctions/Index');
   }
    public function store(
    StoreAuctionRequest $request,
    AuctionService $service
) {
    $auction = $service->createAuction(
        $request->user(),
        $request->validated()
    );

    return redirect()->route('auctions.show', $auction->slug);
}

    public function create() {
         return Inertia::render('Auctions/Create');
    }

    public function show(Auction $auction){
        $auction->load(['user', 'bids.user']);

        return Inertia::render('Auctions/Show', [
            'auction' => [
                'id' => $auction->id,
                'title' => $auction->title,
                'description' => $auction->description,
                'image' => $auction->image,
                'current_price' => $auction->current_price,
                'starting_price' => $auction->starting_price,
                'start_time' => $auction->start_time,
                'end_time' => $auction->end_time,
                'user_id' => $auction->user_id,
                'user' => $auction->user ? ['name' => $auction->user->name] : null,
                'bids' => $auction->bids->map(fn ($bid) => [
                    'id' => $bid->id,
                    'amount' => $bid->amount,
                    'user_id' => $bid->user_id,
                    'created_at' => $bid->created_at,
                    'user' => [
                        'name' => $bid->user->name,
                    ],
                ])->sortByDesc('amount')->values(),
            ],
            'authUserId' => auth()->id(),
        ]);
    }
}
