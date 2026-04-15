<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Auctions created by the user that haven't ended
        $activeAuctions = $user->auctions()
            ->where('end_time', '>', now())
            ->count();

        // Total bids placed by the user
        $totalBidsPlaced = $user->bids()->count();

        // Total auctions the user won
        $auctionsWon = Auction::where('winner_id', $user->id)->count();

        // Total amount user spent on won auctions
        $totalSpent = Auction::where('winner_id', $user->id)->sum('current_price');

        return Inertia::render('Dashboard', [
            'user'            => $user,
            'totalBidsPlaced' => $totalBidsPlaced,
            'auctionsWon'     => $auctionsWon,
            'totalSpent'      => $totalSpent,
            'activeAuctions'  => $activeAuctions,
        ]);
    }
}
