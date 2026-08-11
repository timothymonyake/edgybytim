<?php

namespace Modules\Deriv\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Deriv\Models\DerivAccount;
use WebSocket\Client;
use Phrity\Net\Uri;

class DerivWebSocketService
{
    protected string $baseUrl = "wss://ws.derivws.com/websockets/v3";

    /**
     * Connect and authenticate with the Deriv API.
     */
    public function connect(DerivAccount $account, callable $onMessage): void
    {
        $appId = config('services.deriv.app_id', '1089');
        $uri = new Uri("{$this->baseUrl}?app_id={$appId}");
        
        Log::info("Establish real connection to Deriv for: {$account->account_id}");

        try {
            $client = new Client($uri);
            
            // 1. Authorize
            $client->text(json_encode([
                'authorize' => $account->api_token
            ]));
            
            $authResponse = json_decode($client->receive()->getContent(), true);
            if (isset($authResponse['error'])) {
                throw new Exception("Auth Error: " . $authResponse['error']['message']);
            }

            Log::info("Deriv Account Authorized: {$account->account_id}");

            // 2. Subscribe to transactions
            $client->text(json_encode([
                'subscribe' => 1,
                'transaction' => 1
            ]));

            // 3. Main Loop
            while (true) {
                try {
                    $message = $client->receive();
                    if ($message) {
                        $data = json_decode($message->getContent(), true);
                        $onMessage($data);
                    }
                } catch (Exception $e) {
                    Log::error("Receive Error for {$account->account_id}: " . $e->getMessage());
                    break; // Exit loop to trigger reconnection logic in the command
                }
            }
        } catch (Exception $e) {
            Log::error("Deriv Connection Error for {$account->account_id}: " . $e->getMessage());
            throw $e; // Re-throw to inform the calling command
        }
    }

    /**
     * Process a single incoming message from the WebSocket.
     */
    public function handleIncoming(array $data, DerivAccount $account, DerivSyncService $syncService): void
    {
        $type = $data['msg_type'] ?? null;

        if ($type === 'transaction') {
            $syncService->processTransaction($account, $data['transaction']);
        } elseif ($type === 'balance') {
            $account->update(['balance' => $data['balance']['balance']]);
        }
    }
}
