<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuctionRequest;
use App\Models\Auction;
use App\Services\AuctionService;
use App\Http\Resources\AuctionResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuctionController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only('search', 'status');
        $sort = $request->input('sort', 'newest');

        $auctions = Auction::with('user')
            ->filter($filters)
            ->sort($sort)
            ->get();

        return Inertia::render('Auctions/Index', [
            'auctions' => AuctionResource::collection($auctions)->resolve(),
            'filters'  => [
                'search' => $filters['search'] ?? null,
                'status' => $filters['status'] ?? 'all',
                'sort'   => $sort,
            ],
        ]);
    }

    public function store(StoreAuctionRequest $request, AuctionService $service)
    {
        $auction = $service->createAuction(
            $request->user(),
            $request->validated()
        );

        $startTime = \Carbon\Carbon::parse($auction->start_time);
        $diffForHumans = $startTime->diffForHumans();

        return redirect()->route('auctions.show', $auction->slug)
            ->with('success', "Auction created successfully! It starts {$diffForHumans}.");
    }

    public function create()
    {
        return Inertia::render('Auctions/Create');
    }

    public function show(Auction $auction)
    {
        $auction->load(['user', 'bids.user']);

        return Inertia::render('Auctions/Show', [
            'auction' => (new AuctionResource($auction))->resolve(),
            'authUserId' => auth()->id(),
        ]);
    }
}
