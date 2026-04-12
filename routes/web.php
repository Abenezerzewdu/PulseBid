<?php

use App\Http\Controllers\AuctionController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index']);

Route::get('/dashboard', [UserDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ── Public routes ─────────────────────────────────────────────────────────────
// CRITICAL ORDER: literal segments must come before the {auction} wildcard.
// /auctions/create must be registered BEFORE /auctions/{auction}, otherwise
// Laravel treats "create" as a slug and throws a 404 model-not-found error.
Route::get('/auctions', [AuctionController::class, 'index'])->name('auctions.index');
Route::get('/auctions/create', [AuctionController::class, 'create'])->name('auctions.create');
Route::get('/auctions/{auction}', [AuctionController::class, 'show'])->name('auctions.show');

Route::get('/how', function () {
    return Inertia::render('HowItWorks');
});

// ── Authenticated routes ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Auction write operations (creating & storing require login)
    Route::post('/auctions', [AuctionController::class, 'store'])->name('auctions.store');

    // Bidding — protected: guests are shown a "Login to bid" notice on the Show page
    Route::post('/auctions/{auction}/bid', [BidController::class, 'store'])->name('auctions.bid');
});

require __DIR__.'/auth.php';
