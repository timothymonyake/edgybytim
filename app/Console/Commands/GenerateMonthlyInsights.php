<?php

namespace App\Console\Commands;

use App\Models\Trade;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GenerateMonthlyInsights extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insights:monthly {--user_id= : Specific user ID to generate insights for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly AI insights for all users or a specific user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user_id');
        
        if ($userId) {
            $users = User::where('id', $userId)->get();
        } else {
            // Get all users with API keys
            $users = User::whereNotNull('openai_api_key')->get();
        }
        
        if ($users->isEmpty()) {
            $this->error('No users found with Groq API keys configured.');
            return 0;
        }
        
        $this->info("Generating monthly insights for {$users->count()} user(s)...");
        
        foreach ($users as $user) {
            $this->info("Processing user: {$user->name} (ID: {$user->id})");
            
            try {
                $result = $this->generateInsightForUser($user);
                
                // Save to database
                \App\Models\MonthlyInsight::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'period' => $result['period']
                    ],
                    [
                        'insight' => $result['insight'],
                        'trade_count' => $result['trade_count'],
                        'total_pnl' => $result['total_pnl'],
                        'win_rate' => $result['win_rate'],
                    ]
                );
                
                Log::info("Monthly insight generated for user {$user->id}", [
                    'user_id' => $user->id,
                    'period' => $result['period']
                ]);
                
                $this->info("✓ Successfully generated insights for {$user->name}");
                
            } catch (\Exception $e) {
                $this->error("✗ Failed for {$user->name}: " . $e->getMessage());
                Log::error("Failed to generate monthly insights for user {$user->id}", [
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->info('Monthly insights generation completed!');
        return 0;
    }
    
    private function generateInsightForUser(User $user)
    {
        // Get last month's data
        $startDate = Carbon::now()->subMonth()->startOfMonth();
        $endDate = Carbon::now()->subMonth()->endOfMonth();
        $period = $startDate->format('Y-m');
        
        $trades = Trade::whereBetween('trade_date', [$startDate, $endDate])->where('user_id', $user->id)->get();
        
        if ($trades->isEmpty()) {
            return null;
        }
        
        $analysis = $this->generateMonthlyAnalysis($trades);
        
        $prompt = "Analyze this monthly trading performance and provide:\n\n" .
                  "1. Key strengths and weaknesses\n" .
                  "2. Patterns in winning vs losing trades\n" .
                  "3. Specific actionable recommendations for next month\n" .
                  "4. Risk management observations\n" .
                  "5. Psychological insights based on the data\n\n" .
                  "Trading Data:\n" . $analysis;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $user->groq_api_key,
                'Content-Type' => 'application/json',
            ])->timeout(90)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert trading analyst specializing in performance review and improvement strategies. Provide detailed, actionable monthly insights.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 3000,
            ]);

            if (!$response->successful()) {
                Log::error("Groq API Error for user {$user->id}", ['body' => $response->body()]);
                return null;
            }

            $data = $response->json();
            $insight = $data['choices'][0]['message']['content'] ?? 'No response generated.';
            
            // Calculate stats for return
            $closedTrades = $trades->where('status', 'closed');
            $wonTrades = $closedTrades->where('outcome', 'win');
            $totalPnl = $closedTrades->sum('pnl');
            $winRate = $closedTrades->count() > 0 ? ($wonTrades->count() / $closedTrades->count()) * 100 : 0;

            return [
                'insight' => $insight,
                'period' => $period,
                'trade_count' => $closedTrades->count(),
                'total_pnl' => round($totalPnl, 2),
                'win_rate' => round($winRate, 2)
            ];

        } catch (\Exception $e) {
            Log::error("AI Analysis Exception for user {$user->id}", ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    private function generateMonthlyAnalysis($trades)
    {
        $closedTrades = $trades->where('status', 'closed');
        $wonTrades = $closedTrades->where('outcome', 'win');
        $lostTrades = $closedTrades->where('outcome', 'loss');
        
        $analysis = "Monthly Performance Analysis:\n\n";
        $analysis .= "Total Trades: " . $closedTrades->count() . "\n";
        $analysis .= "Wins: " . $wonTrades->count() . " | Losses: " . $lostTrades->count() . "\n";
        $analysis .= "Win Rate: " . round(($wonTrades->count() / max($closedTrades->count(), 1)) * 100, 2) . "%\n";
        $analysis .= "Total P&L: $" . $closedTrades->sum('pnl') . "\n";
        $analysis .= "Average P&L per Trade: $" . round($closedTrades->avg('pnl'), 2) . "\n";
        $analysis .= "Best Trade: $" . $closedTrades->max('pnl') . "\n";
        $analysis .= "Worst Trade: $" . $closedTrades->min('pnl') . "\n";
        $analysis .= "Average R:R: " . round($closedTrades->avg('rr'), 2) . "\n\n";
        
        $analysis .= "Breakdown by Asset:\n";
        $byAsset = $closedTrades->groupBy('asset');
        foreach ($byAsset as $asset => $assetTrades) {
            $wins = $assetTrades->where('outcome', 'win')->count();
            $total = $assetTrades->count();
            $winRate = round(($wins / max($total, 1)) * 100, 2);
            $pnl = $assetTrades->sum('pnl');
            $analysis .= "- $asset: $total trades, $winRate% WR, P&L: $$pnl\n";
        }
        
        $analysis .= "\nBreakdown by Session:\n";
        $bySession = $closedTrades->groupBy('session');
        foreach ($bySession as $session => $sessionTrades) {
            $wins = $sessionTrades->where('outcome', 'win')->count();
            $total = $sessionTrades->count();
            $winRate = round(($wins / max($total, 1)) * 100, 2);
            $analysis .= "- $session: $total trades, $winRate% WR\n";
        }
        
        $analysis .= "\nBreakdown by Direction:\n";
        $byDirection = $closedTrades->groupBy('direction');
        foreach ($byDirection as $direction => $dirTrades) {
            $wins = $dirTrades->where('outcome', 'win')->count();
            $total = $dirTrades->count();
            $winRate = round(($wins / max($total, 1)) * 100, 2);
            $analysis .= "- $direction: $total trades, $winRate% WR\n";
        }
        
        $analysis .= "\nCompliance Metrics:\n";
        $withChecklist = $trades->where('checklist', '!=', null)->count();
        $withPlan = $trades->where('plan', '!=', null)->count();
        $analysis .= "- Trades with Checklist: $withChecklist (" . round(($withChecklist / max($trades->count(), 1)) * 100, 2) . "%)\n";
        $analysis .= "- Trades with Plan: $withPlan (" . round(($withPlan / max($trades->count(), 1)) * 100, 2) . "%)\n";
        
        return $analysis;
    }
}
