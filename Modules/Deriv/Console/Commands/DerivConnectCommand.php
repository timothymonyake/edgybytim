<?php

namespace Modules\Deriv\Console\Commands;

use Illuminate\Console\Command;
use Modules\Deriv\Models\DerivAccount;
use Modules\Deriv\Services\DerivWebSocketService;
use Modules\Deriv\Services\DerivSyncService;
use Illuminate\Support\Facades\Log;

class DerivConnectCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'deriv:connect {account_id?}';

    /**
     * The console command description.
     */
    protected $description = 'Connect to Deriv WebSocket and stream real-time transactions.';

    /**
     * Execute the console command.
     */
    public function handle(DerivWebSocketService $wsService, DerivSyncService $syncService): void
    {
        $accountId = $this->argument('account_id');
        
        $accounts = $accountId 
            ? DerivAccount::where('account_id', $accountId)->get() 
            : DerivAccount::all();

        if ($accounts->isEmpty()) {
            $this->error("No Deriv accounts found.");
            return;
        }

        $this->info("Starting Deriv WebSocket listeners for " . $accounts->count() . " accounts...");

        foreach ($accounts as $account) {
            $this->output->writeln("<info>Connecting to Account ID: {$account->account_id}</info>");
            
            // This is a daemon-style call. In a real environment, you'd run this per account 
            // via a supervisor-managed process or dynamic queues.
            
            $wsService->connect($account, function (array $data) use ($account, $syncService, $wsService) {
                $wsService->handleIncoming($data, $account, $syncService);
                $this->output->writeln("<comment>Received message type: " . ($data['msg_type'] ?? 'unknown') . "</comment>");
            });
        }
    }
}
