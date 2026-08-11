<?php

namespace Modules\Deriv\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Deriv\Models\DerivAccount;
use Modules\Deriv\Services\DerivSyncService;

class ProcessDerivTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected DerivAccount $account,
        protected array $payload
    ) {
    }

    public function handle(DerivSyncService $syncService): void
    {
        $syncService->processTransaction($this->account, $this->payload);
    }
}
