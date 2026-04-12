<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuctionRequest;
use App\Models\Auction;
use App\Services\AuctionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AuctionController extends Controller
{
    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Derive a human-readable status ("live" | "upcoming" | "ended") from DB timestamps.
     */
    private function deriveStatus(Auction $auction): string
    {
        $now = Carbon::now();

        if ($auction->end_time && $now->greaterThan($auction->end_time)) {
            return 'ended';
        }

        if ($auction->start_time && $now->lessThan($auction->start_time)) {
            return 'upcoming';
        }

        return 'live';
    }

    /**
     * Format a diff of seconds into "Xh Ym" or "Xs" style string.
     */
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

    /**
     * Shape a single auction record for the listing page.
     */
    private function formatForListing(Auction $auction): array
    {
        $now    = Carbon::now();
        $status = $this->deriveStatus($auction);

        $timeLeft  = null;
        $startsIn  = null;

        if ($status === 'live' && $auction->end_time) {
            $timeLeft = $this->formatCountdown($now->diffInSeconds($auction->end_time, false));
        } elseif ($status === 'upcoming' && $auction->start_time) {
            $startsIn = $this->formatCountdown($now->diffInSeconds($auction->start_time, false));
        }

        return [
            'id'             => $auction->id,
            'slug'           => $auction->slug,
            'title'          => $auction->title,
            'image'          => $auction->image,
            'status'         => $status,
            'current_price'  => $auction->current_price,
            'starting_price' => $auction->starting_price,
            'end_time'       => $auction->end_time?->toIso8601String(),
            'start_time'     => $auction->start_time?->toIso8601String(),
            'timeLeft'       => $timeLeft,
            'startsIn'       => $startsIn,
            'user'           => $auction->user ? ['name' => $auction->user->name] : null,
        ];
    }

    // ─── Actions ──────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');   // all | live | upcoming | ended
        $sort   = $request->input('sort', 'newest');

        $now = Carbon::now();

        $query = Auction::with('user');

        // ── Search ────────────────────────────────────────────────────────────
        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        // ── Status filter (translate to real time-range conditions) ───────────
        if ($status && $status !== 'all') {
            match ($status) {
                'live'     => $query->where('start_time', '<=', $now)->where('end_time', '>=', $now),
                'upcoming' => $query->where('start_time', '>', $now),
                'ended'    => $query->where('end_time', '<', $now),
                default    => null,
            };
        }

        // ── Sort ──────────────────────────────────────────────────────────────
        match ($sort) {
            'ending'     => $query->orderBy('end_time', 'asc'),
            'price_asc'  => $query->orderBy('current_price', 'asc'),
            'price_desc' => $query->orderBy('current_price', 'desc'),
            default      => $query->orderBy('created_at', 'desc'),   // newest
        };

        $auctions = $query->get()->map(fn ($a) => $this->formatForListing($a))->values();

        return Inertia::render('Auctions/Index', [
            'auctions' => $auctions,
            'filters'  => [
                'search' => $search,
                'status' => $status ?? 'all',
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

        return redirect()->route('auctions.show', $auction->slug);
    }

    public function create()
    {
        return Inertia::render('Auctions/Create');
    }

    public function show(Auction $auction)
    {
        $auction->load(['user', 'bids.user']);

        return Inertia::render('Auctions/Show', [
            'auction' => [
                'id'             => $auction->id,
                'slug'           => $auction->slug,          // ← was missing → caused /auctions/undefined/bid
                'title'          => $auction->title,
                'description'    => $auction->description,
                'image'          => $auction->image,
                'current_price'  => $auction->current_price,
                'starting_price' => $auction->starting_price,
                'start_time'     => $auction->start_time,
                'end_time'       => $auction->end_time,
                'user_id'        => $auction->user_id,
                'user'           => $auction->user ? ['name' => $auction->user->name] : null,
                'bids'           => $auction->bids->map(fn ($bid) => [
                    'id'         => $bid->id,
                    'amount'     => $bid->amount,
                    'user_id'    => $bid->user_id,
                    'created_at' => $bid->created_at,
                    'user'       => ['name' => $bid->user->name],
                ])->sortByDesc('amount')->values(),
            ],
            'authUserId' => auth()->id(),
        ]);
    }
}
