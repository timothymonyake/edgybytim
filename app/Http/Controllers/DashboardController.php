<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Start with base query
        $query = Trade::query();
        
        // Apply Filters
        $this->applyFilters($query, $request);
        
        // Get filtered trades
        $trades = $query->get();
        $closedTrades = $trades->where('status', 'closed');
        
        // Calculate KPIs
        $kpis = $this->calculateKPIs($trades, $closedTrades);
        
        // Get chart data
        $chartData = $this->getChartData($trades, $closedTrades);
        
        // Get behavioral analytics
        $behavioral = $this->getBehavioralAnalytics($trades);
        
        // Get best and worst trades
        $bestWorst = $this->getBestWorstTrades($closedTrades);
        
        // Calculate Streaks
        $streaks = $this->calculateStreaks($closedTrades);
        
        if ($request->ajax()) {
            return response()->json([
                'kpis' => $kpis,
                'chartData' => $chartData,
                'behavioral' => $behavioral,
                'bestWorst' => $bestWorst,
                'streaks' => $streaks
            ]);
        }
        
        return view('dashboard.analytics', compact('kpis', 'chartData', 'behavioral', 'bestWorst', 'streaks'));
    }
    
    private function applyFilters($query, $request)
    {
        // Default to current month if no date range is provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $start = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
                $end = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
                $query->whereBetween('trade_date', [$start, $end]);
            } catch (\Exception $e) {
                // Ignore date parse errors
            }
        } else {
            // Default to current month
            $start = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end = Carbon::now()->endOfMonth()->format('Y-m-d');
            $query->whereBetween('trade_date', [$start, $end]);
        }

        if ($request->filled('market') && $request->market != '0') {
            $query->where('asset', $request->market);
        }

        if ($request->filled('direction') && $request->direction != '0') {
            $query->where('direction', $request->direction);
        }

        if ($request->filled('session') && $request->session != '0') {
            $query->where('session', $request->session);
        }

        if ($request->filled('outcome') && $request->outcome != '0') {
            $query->where('outcome', $request->outcome);
        }
        
        if ($request->filled('hin_day') && $request->hin_day != '0') {
            $query->where('hin_day', $request->hin_day == 'yes' ? 1 : 0);
        }

        if ($request->filled('plan_followed') && $request->plan_followed != '0') {
            $query->where('plan_followed', $request->plan_followed == 'yes' ? 1 : 0);
        }
    }
    
    private function calculateStreaks($trades)
    {
        $sortedTrades = $trades->sortBy('trade_date');
        
        $currentWinStreak = 0;
        $currentLossStreak = 0;
        $maxWinStreak = 0;
        $maxLossStreak = 0;
        
        $tempWinStreak = 0;
        $tempLossStreak = 0;
        
        foreach ($sortedTrades as $trade) {
            if ($trade->outcome === 'win') {
                $tempWinStreak++;
                $tempLossStreak = 0; // Reset loss streak
                
                if ($tempWinStreak > $maxWinStreak) {
                    $maxWinStreak = $tempWinStreak;
                }
            } elseif ($trade->outcome === 'loss') {
                $tempLossStreak++;
                $tempWinStreak = 0; // Reset win streak
                
                if ($tempLossStreak > $maxLossStreak) {
                    $maxLossStreak = $tempLossStreak;
                }
            } else {
                // Reset both on breakeven or other outcomes if desired, 
                // or just ignore. Let's reset to be strict.
                $tempWinStreak = 0;
                $tempLossStreak = 0;
            }
        }
        
        // Current streaks are the final values
        $currentWinStreak = $tempWinStreak;
        $currentLossStreak = $tempLossStreak;
        
        return [
            'current_win_streak' => $currentWinStreak,
            'current_loss_streak' => $currentLossStreak,
            'max_win_streak' => $maxWinStreak,
            'max_loss_streak' => $maxLossStreak
        ];
    }
    
    private function calculateKPIs($trades, $closedTrades)
    {
        $wonTrades = $closedTrades->where('outcome', 'win')->count();
        $totalClosed = $closedTrades->count();
        
        $winRate = $totalClosed > 0 ? ($wonTrades / $totalClosed) * 100 : 0;
        
        // Monthly Stats (Current Month) - Note: If filtered, this should probably reflect the filter range?
        // But the label says "This Month". I'll keep it as "This Month" relative to the filtered data 
        // OR just calculate stats for the filtered dataset.
        // Given the filter is "Date Range", the "Monthly Stats" tile might be confusing if I select "Last Year".
        // I will update the logic to calculate stats for the *filtered* dataset, effectively making it a "Selected Period Stats" tile.
        
        $periodPnL = $closedTrades->sum('pnl');
        $periodCount = $closedTrades->count();
        $periodWins = $closedTrades->where('outcome', 'win')->count();
        $periodLosses = $closedTrades->where('outcome', 'loss')->count();
        $periodWinRate = $periodCount > 0 ? ($periodWins / $periodCount) * 100 : 0;
        $periodAvgRR = $closedTrades->avg('rr') ?? 0;
        $periodTotalRR = $closedTrades->sum('rr') ?? 0;
        
        // Best and Worst Day (Lifetime/Filtered)
        $dailyPnL = $closedTrades->groupBy(function($trade) {
            return Carbon::parse($trade->trade_date)->format('Y-m-d');
        })->map(function($dayTrades) {
            return $dayTrades->sum('pnl');
        });
        
        $bestDay = $dailyPnL->max() ?? 0;
        $worstDay = $dailyPnL->min() ?? 0;
        
        // Compliance Score
        $complianceScore = $this->calculateComplianceScore($trades);
        
        // Lifetime Win Rate (Exempt from filters)
        $allClosedTrades = Trade::where('status', 'closed')->get();
        $lifetimeWon = $allClosedTrades->where('outcome', 'win')->count();
        $lifetimeTotal = $allClosedTrades->count();
        $lifetimeWinRate = $lifetimeTotal > 0 ? ($lifetimeWon / $lifetimeTotal) * 100 : 0;
        
        return [
            'win_rate' => round($lifetimeWinRate, 1), // Now Lifetime Win Rate
            'filtered_win_rate' => round($winRate, 1), // Filtered Win Rate (if needed)
            'monthly_roi' => round($periodPnL, 2),
            'monthly_trades' => $periodCount,
            'monthly_wins' => $periodWins,
            'monthly_losses' => $periodLosses,
            'monthly_win_rate' => round($periodWinRate, 1),
            'monthly_avg_rr' => round($periodAvgRR, 2),
            'monthly_total_rr' => round($periodTotalRR, 2),
            'best_day' => round($bestDay, 2),
            'worst_day' => round($worstDay, 2),
            'compliance_score' => round($complianceScore, 1)
        ];
    }
    
    private function calculateComplianceScore($trades)
    {
        if ($trades->count() == 0) return 0;
        
        $score = 0;
        $total = $trades->count();
        
        foreach ($trades as $trade) {
            $points = 0;
            if ($trade->checklist) $points += 33.33;
            if ($trade->plan) $points += 33.33;
            if ($trade->killzone) $points += 33.33;
            $score += $points;
        }
        
        return $total > 0 ? $score / $total : 0;
    }
    
    private function getChartData($trades, $closedTrades)
    {
        // Equity Curve
        $equityCurve = $this->getEquityCurve($closedTrades);
        
        // Top 3 Instruments
        $topInstruments = $trades->groupBy('asset')
            ->map(function($pairTrades, $asset) {
                $wins = $pairTrades->where('outcome', 'win')->count();
                $losses = $pairTrades->where('outcome', 'loss')->count();
                $total = $wins + $losses;
                $winRate = $total > 0 ? ($wins / $total) * 100 : 0;
                
                return [
                    'pair' => $asset, // Keeping key 'pair' for frontend compatibility or change to 'asset'
                    'wins' => $wins,
                    'losses' => $losses,
                    'total' => $total,
                    'win_rate' => round($winRate, 1)
                ];
            })
            ->sortByDesc('total')
            ->take(3)
            ->values();
            
        // Top 3 Performing Sessions (by Win Rate)
        $sessionWinRates = $trades->groupBy('session')
            ->map(function($sessionTrades, $session) {
                $wins = $sessionTrades->where('outcome', 'win')->count();
                $total = $sessionTrades->where('status', 'closed')->count();
                $winRate = $total > 0 ? ($wins / $total) * 100 : 0;
                
                return [
                    'session' => $session ? ucwords(str_replace('_', ' ', $session)) : 'Unknown',
                    'win_rate' => round($winRate, 1),
                    'total' => $total
                ];
            })
            ->sortByDesc('win_rate') // Sort by Win Rate
            ->take(3) // Take Top 3
            ->values();
            
        // Daily Performance (Mon-Fri)
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
        $dailyPerformance = [];
        
        foreach ($days as $day) {
            $dayTrades = $closedTrades->filter(function($trade) use ($day) {
                return Carbon::parse($trade->trade_date)->format('D') === $day;
            });
            
            $profit = $dayTrades->where('pnl', '>', 0)->sum('pnl');
            $loss = abs($dayTrades->where('pnl', '<', 0)->sum('pnl'));
            $net = $dayTrades->sum('pnl');
            
            $dailyPerformance[] = [
                'day' => $day,
                'profit' => round($profit, 2),
                'loss' => round($loss, 2),
                'net' => round($net, 2)
            ];
        }
        
        // Monthly Performance (Histogram)
        $monthlyPerformance = $closedTrades->groupBy(function($trade) {
            return Carbon::parse($trade->trade_date)->format('Y-m');
        })->map(function($monthTrades, $month) {
            return [
                'label' => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                'value' => round($monthTrades->sum('pnl'), 2),
                'date' => $month
            ];
        })->sortBy('date')->values();
        
        return [
            'equity_curve' => $equityCurve,
            'top_instruments' => $topInstruments,
            'session_win_rates' => $sessionWinRates,
            'daily_performance' => $dailyPerformance,
            'monthly_performance' => $monthlyPerformance
        ];
    }
    
    private function getEquityCurve($closedTrades)
    {
        $sorted = $closedTrades->sortBy('trade_date');
        $equity = 0;
        $curve = [];
        
        foreach ($sorted as $trade) {
            $equity += $trade->pnl;
            $curve[] = [
                'date' => $trade->trade_date,
                'equity' => round($equity, 2)
            ];
        }
        
        return $curve;
    }
    
    private function getBehavioralAnalytics($trades)
    {
        // Revenge trading (trades taken within 1 hour of a loss)
        $revengeTrades = 0;
        
        // Impulse trades (no checklist or plan)
        $impulseTrades = $trades->filter(function($trade) {
            return !$trade->checklist && !$trade->plan;
        })->count();
        
        $impulsePercentage = $trades->count() > 0 ? ($impulseTrades / $trades->count()) * 100 : 0;
        
        // Missed trades (from notes/journal)
        $missedTrades = 0; // This would need a separate tracking system
        
        // Patience score (trades with proper setup vs impulse)
        $patienceScore = 100 - $impulsePercentage;
        
        return [
            'revenge_trades' => $revengeTrades,
            'impulse_percentage' => round($impulsePercentage, 1),
            'missed_trades' => $missedTrades,
            'patience_score' => round($patienceScore, 1)
        ];
    }
    
    private function getBestWorstTrades($closedTrades)
    {
        $best = $closedTrades->sortByDesc('pnl')->first();
        $worst = $closedTrades->sortBy('pnl')->first();
        
        return [
            'best' => $best,
            'worst' => $worst
        ];
    }
}
