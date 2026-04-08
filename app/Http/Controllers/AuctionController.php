<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuctionRequest;
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
}
