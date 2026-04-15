<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Controller: UserDashboardController
 * 
 * Purpose: 
 * Manages the data payload served to the primary frontend user Dashboard. 
 * Aggregates statistics about total bids, active personal auctions, 
 * winnings, and expenditure metrics calculated cleanly.
 * 
 * Recent Changes:
 * Migrated to strict PSR-12 formatting syntax and cleaned up dangling dependency 
 * imports to strictly preserve clean scalable code architectural standards.
 */
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
