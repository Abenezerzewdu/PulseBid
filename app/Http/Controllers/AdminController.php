<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        $now = now();
        
        $stats = [
            'total_users' => User::count(),
            'total_auctions' => Auction::count(),
            'live_auctions' => Auction::where('start_time', '<=', $now)
                ->where('end_time', '>', $now)
                ->count(),
            'total_bids' => Bid::count(),
            'total_revenue' => Transaction::sum('amount') ?? 0,
        ];

        $health = [
            'database' => $this->getDatabaseStatus(),
            'php_version' => PHP_VERSION,
            'environment' => config('app.env'),
            'reverb' => $this->getReverbStatus(),
            'server_time' => $now->toDateTimeString(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'health' => $health,
        ]);
    }

    private function getDatabaseStatus()
    {
        try {
            DB::connection()->getPdo();
            return 'Connected';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    private function getReverbStatus()
    {
        $host = config('reverb.servers.reverb.host') === '0.0.0.0' ? '127.0.0.1' : config('reverb.servers.reverb.host');
        $port = config('reverb.servers.reverb.port');

        $connection = @fsockopen($host, $port, $errno, $errstr, 2);

        if (is_resource($connection)) {
            fclose($connection);
            return 'Online';
        }

        return 'Offline';
    }
}
