<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event: BidPlaced
 * 
 * Purpose: 
 * Resolves the real-time interaction for active auctions. Instructs the application 
 * to instantly broadcast (via ShouldBroadcastNow) newly placed bids pushed to the database 
 * out to the Echo / WebSockets server so that frontend Vue clients receive them dynamically 
 * without page refreshes.
 */
class BidPlaced implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $auctionId;
    public $bid;

    /**
     * Create a new event instance.
     */
    public function __construct(int $auctionId, array $bid)
    {
        $this->auctionId = $auctionId;
        $this->bid = $bid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('auction.' . $this->auctionId),
        ];
    }
    
    /**
     * Data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'auctionId' => $this->auctionId,
            'bid' => $this->bid,
        ];
    }
}
