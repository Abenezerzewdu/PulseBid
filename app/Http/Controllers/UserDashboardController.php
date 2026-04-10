<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserDashboardController extends Controller
{
    public function index(){
    $user=auth()->user();
    //auctions created by the user that haven't ended
      $activeAuctions = $user->auctions()
        ->where('end_time', '>', now())
        ->count();
    //total bids of the user
    $totalBidsPlaced=$user->bids()->count();
    // total auctions the user won
    $auctionsWon=Auction::where('winner_id',$user->id)->count();
    //total amount user spent
    $totalSpent=Auction::where('winner_id',$user->id)->sum('current_price');
    return Inertia::render('Dashboard',[
    'user'=>$user,
      "totalBidsPlaced"=>$totalBidsPlaced,
      'auctionsWon'=>$auctionsWon,
      'totalSpent'=>$totalSpent,
      'activeAuctions'=>$activeAuctions

    ]);
    }
}
