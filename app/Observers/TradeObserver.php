<?php

namespace App\Observers;

use App\Models\Trade;
use App\Models\ActivityLog;

class TradeObserver
{
    /**
     * Handle the Trade "created" event.
     */
    public function created(Trade $trade): void
    {
        ActivityLog::create([
            'user_id' => $trade->user_id,
            'action' => 'created_trade',
            'description' => "Opened a new {$trade->direction} trade on " . strtoupper($trade->asset),
            'subject_type' => Trade::class,
            'subject_id' => $trade->id,
            'url' => route('trades.index', ['trade_id' => $trade->id]),
        ]);
    }

    /**
     * Handle the Trade "updated" event.
     */
    public function updated(Trade $trade): void
    {
        // Only log significant updates, e.g., closing a trade
        if ($trade->wasChanged('status') && $trade->status === 'closed') {
             $pnl = number_format($trade->pnl, 2);
             $outcome = strtoupper($trade->outcome);
             
             ActivityLog::create([
                'user_id' => $trade->user_id,
                'action' => 'closed_trade',
                'description' => "Closed {$trade->direction} trade on " . strtoupper($trade->asset) . " ({$outcome}: \${$pnl})",
                'subject_type' => Trade::class,
                'subject_id' => $trade->id,
                'url' => route('trades.index', ['trade_id' => $trade->id]),
            ]);
        }
    }

    /**
     * Handle the Trade "deleted" event.
     */
    public function deleted(Trade $trade): void
    {
        ActivityLog::create([
            'user_id' => $trade->user_id,
            'action' => 'deleted_trade',
            'description' => "Deleted trade on " . strtoupper($trade->asset),
            'subject_type' => Trade::class,
            'subject_id' => $trade->id,
            'url' => null,
        ]);
    }
}
