<?php

namespace Modules\Deriv\Services;

use Modules\Deriv\Models\DerivAccount;
use Modules\Deriv\Models\DerivTrade;
use Modules\Deriv\Models\DerivTradeMetric;

class TradeMetricService
{
    /**
     * Recalculate KPIs for a specific account.
     */
    public function recalculateMetrics(DerivAccount $account): DerivTradeMetric
    {
        $trades = $account->trades()->whereNotNull('closed_at')->get();
        
        $totalTrades = $trades->count();
        if ($totalTrades === 0) {
            return $account->metrics()->updateOrCreate(['deriv_account_id' => $account->id], [
                'total_trades' => 0,
                'win_rate' => 0,
            ]);
        }

        $wins = $trades->where('profit_loss', '>', 0);
        $losses = $trades->where('profit_loss', '<', 0);
        $breakevens = $trades->where('profit_loss', '==', 0);

        $winCount = $wins->count();
        $lossCount = $losses->count();
        $breakevenCount = $breakevens->count();

        $grossProfit = $wins->sum('profit_loss');
        $grossLoss = abs($losses->sum('profit_loss'));

        $avgWin = $winCount > 0 ? $grossProfit / $winCount : 0;
        $avgLoss = $lossCount > 0 ? $grossLoss / $lossCount : 0;

        $winRate = ($winCount / $totalTrades) * 100;
        $profitFactor = $grossLoss > 0 ? $grossProfit / $grossLoss : $grossProfit;
        
        // Risk to Reward (Average Win / Average Loss)
        $rrRatio = $avgLoss > 0 ? $avgWin / $avgLoss : 0;

        // Expectancy = (Win Rate * Avg Win) - (Loss Rate * Avg Loss)
        $lossRate = $lossCount / $totalTrades;
        $expectancy = (($winCount / $totalTrades) * $avgWin) - ($lossRate * $avgLoss);

        return $account->metrics()->updateOrCreate(['deriv_account_id' => $account->id], [
            'win_rate' => $winRate,
            'total_trades' => $totalTrades,
            'wins' => $winCount,
            'losses' => $lossCount,
            'breakeven' => $breakevenCount,
            'avg_win' => $avgWin,
            'avg_loss' => $avgLoss,
            'risk_reward_ratio' => $rrRatio,
            'profit_factor' => $profitFactor,
            'expectancy' => $expectancy,
        ]);
    }
}
