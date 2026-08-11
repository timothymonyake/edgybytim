<?php

namespace Modules\Deriv\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Deriv\Events\TradeReceived;
use Modules\Deriv\Models\DerivAccount;
use Modules\Deriv\Models\DerivTrade;

class DerivSyncService
{
    public function __construct(protected TradeMetricService $metricService)
    {
    }

    /**
     * Process a raw transaction from Deriv WebSocket.
     */
    public function processTransaction(DerivAccount $account, array $data): void
    {
        DB::transaction(function () use ($account, $data) {
            $contract = $data['contract_id'] ?? null;
            if (!$contract) return;

            // Simple logic to sync trade records
            $trade = DerivTrade::updateOrCreate(
                ['deriv_account_id' => $account->id, 'raw_payload->contract_id' => $contract],
                [
                    'symbol' => $data['symbol'] ?? 'Unknown',
                    'contract_type' => $this->parseContractType($data['contract_type'] ?? ''),
                    'entry_price' => $data['entry_tick'] ?? 0,
                    'exit_price' => $data['exit_tick'] ?? null,
                    'profit_loss' => $data['profit'] ?? 0,
                    'stake' => $data['buy_price'] ?? 0,
                    'opened_at' => Carbon::createFromTimestamp($data['purchase_time'] ?? time()),
                    'closed_at' => isset($data['exit_tick_time']) ? Carbon::createFromTimestamp($data['exit_tick_time']) : null,
                    'raw_payload' => $data,
                ]
            );

            // Update account balance and equity
            $account->update([
                'balance' => $data['balance'] ?? $account->balance,
            ]);

            // Sync daily performance snapshot
            $this->updateDailyPerformance($account);

            // Recalculate metrics
            $this->metricService->recalculateMetrics($account);

            // Dispatch event for real-time updates
            if ($trade->wasRecentlyCreated || $trade->wasChanged()) {
                event(new TradeReceived($trade));
            }
        });
    }

    /**
     * Update the account level metrics and performance snapshots.
     */
    protected function updateDailyPerformance(DerivAccount $account): void
    {
        $today = Carbon::today()->toDateString();
        
        $pnl = $account->trades()
            ->whereDate('closed_at', $today)
            ->sum('profit_loss');

        // This is a simplified version of starting balance calculation
        $startingBalance = $account->balance - $pnl;

        $account->dailyPerformances()->updateOrCreate(
            ['date' => $today],
            [
                'starting_balance' => $startingBalance,
                'ending_balance' => $account->balance,
                'pnl' => $pnl,
            ]
        );
    }

    protected function parseContractType(string $type): string
    {
        if (str_contains(strtolower($type), 'call') || str_contains(strtolower($type), 'rise') || str_contains(strtolower($type), 'buy')) {
            return 'BUY';
        }
        return 'SELL';
    }
}
