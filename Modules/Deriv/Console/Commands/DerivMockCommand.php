<?php

namespace Modules\Deriv\Console\Commands;

use Illuminate\Console\Command;
use Modules\Deriv\Models\DerivAccount;
use Modules\Deriv\Services\DerivSyncService;
use Carbon\Carbon;

class DerivMockCommand extends Command
{
    protected $signature = 'deriv:mock {account_id?}';
    protected $description = 'Generate mock trading data for testing the dashboard visualizations.';

    public function handle(DerivSyncService $syncService): void
    {
        $accountId = $this->argument('account_id');
        $accounts = $accountId 
            ? DerivAccount::where('account_id', $accountId)->get() 
            : DerivAccount::all();

        if ($accounts->isEmpty()) {
            $this->error("No accounts found to mock.");
            return;
        }

        foreach ($accounts as $account) {
            $this->info("Generating 20 mock trades for {$account->account_id}...");
            
            $symbols = ['R_10', 'R_25', 'R_50', 'R_75', 'R_100'];
            $baseBalance = 10000;

            for ($i = 0; $i < 20; $i++) {
                $isWin = rand(0, 100) < 60; // 60% win rate
                $profit = $isWin ? rand(10, 50) : rand(-40, -10);
                
                $mockData = [
                    'contract_id' => rand(1000000, 9999999),
                    'symbol' => $symbols[array_rand($symbols)],
                    'contract_type' => rand(0, 1) ? 'CALL' : 'PUT',
                    'entry_tick' => rand(1000, 5000) / 100,
                    'exit_tick' => rand(1000, 5000) / 100,
                    'profit' => $profit,
                    'buy_price' => 10,
                    'purchase_time' => Carbon::now()->subDays(20 - $i)->timestamp,
                    'exit_tick_time' => Carbon::now()->subDays(20 - $i)->addMinutes(rand(1, 60))->timestamp,
                    'balance' => $baseBalance + $profit,
                ];

                $syncService->processTransaction($account, $mockData);
                $baseBalance += $profit;
            }

            $this->info("Mock data generated successfully.");
        }
    }
}
