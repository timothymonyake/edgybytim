<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AIInsightsController extends Controller
{
    public function index()
    {
        return view('ai_insights.index');
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
        ]);

        $user = $request->user();

        if (!$user->groq_api_key) {
            return response()->json([
                'error' => 'Please set your Groq API key in your profile settings first.'
            ], 400);
        }

        // Fetch detailed trade data (CSV format)
        $trades = Trade::where('user_id', $user->id)
            ->orderBy('trade_date', 'desc')
            ->take(50)
            ->get();
        
        $csvData = "Date,Asset,Direction,Outcome,P&L,RR,Session,Entry Type,Plan Followed,Emotions,Mistakes\n";
        foreach ($trades as $trade) {
            $csvData .= "{$trade->trade_date},{$trade->asset},{$trade->direction},{$trade->outcome},{$trade->pnl},{$trade->rr},{$trade->session},{$trade->entry_type},{$trade->plan_followed},{$trade->emotions},{$trade->mistakes}\n";
        }

        // Get screenshots (limit to last 5 trades to avoid token limits)
        $screenshots = [];
        foreach ($trades->take(5) as $trade) {
            foreach ($trade->screenshots as $screenshot) {
                $screenshots[] = asset('storage/' . $screenshot->path);
            }
        }

        $prompt = $request->prompt;
        $prompt .= "\n\nHere is my recent trading data (CSV):\n" . $csvData;

        if (!empty($screenshots)) {
            $prompt .= "\n\nScreenshot Links (for context): " . implode(', ', $screenshots);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $user->groq_api_key,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert trading psychology and strategy coach. Analyze the user\'s query and data to provide actionable, specific advice. Be concise but insightful.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

            if (!$response->successful()) {
                \Log::error('Groq API Error', ['body' => $response->body()]);
                return response()->json([
                    'error' => 'AI service error: ' . ($response->json()['error']['message'] ?? 'Unknown error')
                ], 500);
            }

            $data = $response->json();
            $insight = $data['choices'][0]['message']['content'] ?? 'No response generated.';

            // Save to database
            \App\Models\SavedInsight::create([
                'user_id' => $user->id,
                'type' => 'quick',
                'title' => substr($request->prompt, 0, 50) . '...',
                'prompt' => $request->prompt,
                'response' => $insight,
                'metadata' => ['trade_count' => $trades->count()]
            ]);

            return response()->json([
                'insight' => $insight
            ]);

        } catch (\Exception $e) {
            \Log::error('AI Analysis Exception', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'An error occurred while communicating with the AI service.'
            ], 500);
        }
    }

    public function monthlyInsights(Request $request)
    {
        $user = $request->user();

        if (!$user->groq_api_key) {
            return response()->json([
                'error' => 'Please set your Groq API key in your profile settings first.'
            ], 400);
        }

        // Get last month's data
        $startDate = Carbon::now()->subMonth()->startOfMonth();
        $endDate = Carbon::now()->subMonth()->endOfMonth();
        $period = $startDate->format('Y-m');

        $trades = Trade::where('user_id', $user->id)
            ->whereBetween('trade_date', [$startDate, $endDate])
            ->get();

        if ($trades->isEmpty()) {
            return response()->json([
                'error' => 'No trade data available for last month.'
            ], 400);
        }

        $closedTrades = $trades->where('status', 'closed');
        $wonTrades = $closedTrades->where('outcome', 'win');
        $totalPnl = $closedTrades->sum('pnl');
        $winRate = $closedTrades->count() > 0 ? ($wonTrades->count() / $closedTrades->count()) * 100 : 0;

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
                Log::error('Groq API Error', ['body' => $response->body()]);
                return response()->json([
                    'error' => 'AI service error: ' . ($response->json()['error']['message'] ?? 'Unknown error')
                ], 500);
            }

            $data = $response->json();
            $insight = $data['choices'][0]['message']['content'] ?? 'No response generated.';

            // Save to database
            \App\Models\MonthlyInsight::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'period' => $period
                ],
                [
                    'insight' => $insight,
                    'trade_count' => $closedTrades->count(),
                    'total_pnl' => round($totalPnl, 2),
                    'win_rate' => round($winRate, 2),
                ]
            );

            return response()->json([
                'insight' => $insight,
                'period' => $period,
                'trade_count' => $closedTrades->count()
            ]);

        } catch (\Exception $e) {
            Log::error('AI Analysis Exception', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'An error occurred while communicating with the AI service.'
            ], 500);
        }
    }

    private function getTradeDataSummary()
    {
        $trades = Trade::where('user_id', auth()->id())
            ->where('status', 'closed')
            ->orderBy('trade_date', 'desc')
            ->take(50)
            ->get();

        $summary = "Recent Trading Summary (Last 50 trades):\n\n";
        $summary .= "Total Trades: " . $trades->count() . "\n";
        $summary .= "Win Rate: " . round(($trades->where('outcome', 'win')->count() / $trades->count()) * 100, 2) . "%\n";
        $summary .= "Total P&L: $" . $trades->sum('pnl') . "\n";
        $summary .= "Average R:R: " . round($trades->avg('rr'), 2) . "\n\n";

        $summary .= "Performance by Asset:\n";
        $byAsset = $trades->groupBy('asset');
        foreach ($byAsset as $asset => $assetTrades) {
            $winRate = round(($assetTrades->where('outcome', 'win')->count() / $assetTrades->count()) * 100, 2);
            $summary .= "- $asset: {$assetTrades->count()} trades, {$winRate}% win rate, P&L: $" . $assetTrades->sum('pnl') . "\n";
        }

        $summary .= "\nPerformance by Session:\n";
        $bySession = $trades->groupBy('session');
        foreach ($bySession as $session => $sessionTrades) {
            $winRate = round(($sessionTrades->where('outcome', 'win')->count() / $sessionTrades->count()) * 100, 2);
            $summary .= "- $session: {$sessionTrades->count()} trades, {$winRate}% win rate\n";
        }

        return $summary;
    }

    private function generateMonthlyAnalysis($trades)
    {
        $closedTrades = $trades->where('status', 'closed');
        $wonTrades = $closedTrades->where('outcome', 'win');
        $lostTrades = $closedTrades->where('outcome', 'loss');

        $analysis = "Monthly Performance Analysis:\n\n";
        $analysis .= "Total Trades: " . $closedTrades->count() . "\n";
        $analysis .= "Wins: " . $wonTrades->count() . " | Losses: " . $lostTrades->count() . "\n";
        $analysis .= "Win Rate: " . round(($wonTrades->count() / $closedTrades->count()) * 100, 2) . "%\n";
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
            $winRate = round(($wins / $total) * 100, 2);
            $pnl = $assetTrades->sum('pnl');
            $analysis .= "- $asset: $total trades, $winRate% WR, P&L: $$pnl\n";
        }

        $analysis .= "\nBreakdown by Session:\n";
        $bySession = $closedTrades->groupBy('session');
        foreach ($bySession as $session => $sessionTrades) {
            $wins = $sessionTrades->where('outcome', 'win')->count();
            $total = $sessionTrades->count();
            $winRate = round(($wins / $total) * 100, 2);
            $analysis .= "- $session: $total trades, $winRate% WR\n";
        }

        $analysis .= "\nBreakdown by Direction:\n";
        $byDirection = $closedTrades->groupBy('direction');
        foreach ($byDirection as $direction => $dirTrades) {
            $wins = $dirTrades->where('outcome', 'win')->count();
            $total = $dirTrades->count();
            $winRate = round(($wins / $total) * 100, 2);
            $analysis .= "- $direction: $total trades, $winRate% WR\n";
        }

        $analysis .= "\nCompliance Metrics:\n";
        $withChecklist = $trades->where('checklist', '!=', null)->count();
        $withPlan = $trades->where('plan', '!=', null)->count();
        $analysis .= "- Trades with Checklist: $withChecklist (" . round(($withChecklist / $trades->count()) * 100, 2) . "%)\n";
        $analysis .= "- Trades with Plan: $withPlan (" . round(($withPlan / $trades->count()) * 100, 2) . "%)\n";

        return $analysis;
    }

    public function history()
    {
        $quickInsights = \App\Models\SavedInsight::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($insight) {
                $insight->source = 'quick';
                return $insight;
            });

        $monthlyInsights = \App\Models\MonthlyInsight::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($insight) {
                $insight->source = 'monthly';
                $insight->title = 'Monthly Report - ' . $insight->period;
                $insight->response = $insight->insight; // Map insight to response
                return $insight;
            });

        $history = $quickInsights->concat($monthlyInsights)->sortByDesc('created_at');

        return view('ai_insights.history', compact('history'));
    }
}
