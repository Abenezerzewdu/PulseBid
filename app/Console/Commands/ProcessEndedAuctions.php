<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Console Command: ProcessEndedAuctions
 * 
 * Purpose: 
 * Fired periodically by the strict cron scheduler. Detects any auctions whose `end_time`
 * has passed and securely computes the winner, locks down the bidding records, and inserts 
 * a new Transaction record so the Buyer and Seller can interface via Messaging.
 */
class ProcessEndedAuctions extends Command
{
    protected $signature = 'auctions:process-ended';
    protected $description = 'Process ended auctions and assign winners';

    public function handle()
    {
        // PERFORMANCE FIX: Utilized `->chunk(100)` to efficiently paginate through resolved 
        // auctions instead of indiscriminately pulling thousands rows via `->get()`. 
        // This stops severe memory bloat and protects the server's RAM from crashing 
        // when scaling up processing. 
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
