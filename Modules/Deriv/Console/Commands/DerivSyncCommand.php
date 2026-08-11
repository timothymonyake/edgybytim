<?php

namespace Modules\Deriv\Console\Commands;

use Illuminate\Console\Command;
use Modules\Deriv\Models\DerivAccount;
use Modules\Deriv\Services\DerivSyncService;
use Modules\Deriv\Services\TradeMetricService;

class DerivSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'deriv:sync {account_id?}';

    /**
     * The console command description.
     */
    protected $description = 'Trigger manual synchronization and metric recalculation for Deriv accounts.';

    /**
     * Execute the console command.
     */
    public function handle(DerivSyncService $syncService, TradeMetricService $metricService): void
    {
        $accountId = $this->argument('account_id');
        
        $accounts = $accountId 
            ? DerivAccount::where('account_id', $accountId)->get() 
            : DerivAccount::all();

        if ($accounts->isEmpty()) {
            $this->error("No Deriv accounts found.");
            return;
        }

        foreach ($accounts as $account) {
            $this->info("Syncing metrics for Account ID: {$account->account_id}...");
            $metricService->recalculateMetrics($account);
            $this->info("Account synced successfully.");
        }
    }
}
