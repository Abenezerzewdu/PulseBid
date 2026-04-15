<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessEndedAuctions extends Command
{
    protected $signature = 'auctions:process-ended';
    protected $description = 'Process ended auctions and assign winners';

    public function handle()
    {
        Auction::whereNull('winner_id')
            ->where('end_time', '<=', now())
            ->chunk(100, function ($endedAuctions) {
                foreach ($endedAuctions as $auction) {
                    DB::transaction(function () use ($auction) {
                        $highestBid = $auction->bids()
                            ->orderByDesc('amount')
                            ->first();

                        // If no bids → skip
                        if (!$highestBid) {
                            return;
                        }

                        // Set winner
                        $auction->update([
                            'winner_id' => $highestBid->user_id
                        ]);

                        // Create transaction
                        Transaction::create([
                            'auction_id' => $auction->id,
                            'seller_id' => $auction->user_id,
                            'buyer_id' => $highestBid->user_id,
                            'status' => 'pending',
                        ]);
                    });
                }
            });

        $this->info('Ended auctions processed successfully.');
    }
}
