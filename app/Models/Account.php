<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'broker',
        'account_number',
        'account_size',
        'currency',
        'platform',
        'phase',
        'status',
        'initial_balance',
        'current_balance',
        'profit_target',
        'max_daily_loss',
        'max_total_loss',
        'has_consistency_rule',
        'consistency_rule_percent',
        'consistency_rule_type',
        'leverage',
        'timezone',
        'color',
        'notes',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'account_size' => 'float',
        'initial_balance' => 'float',
        'current_balance' => 'float',
        'profit_target' => 'float',
        'max_daily_loss' => 'float',
        'max_total_loss' => 'float',
        'has_consistency_rule' => 'boolean',
        'consistency_rule_percent' => 'float',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trades()
    {
        return $this->hasMany(Trade::class);
    }

    /**
     * Get calculated current balance based on initial balance + closed trade PnL
     */
    public function getCalculatedBalanceAttribute()
    {
        $netPnl = $this->trades()->where('status', 'closed')->sum('pnl');
        return $this->initial_balance + $netPnl;
    }

    /**
     * Get net PnL from closed trades
     */
    public function getNetPnlAttribute()
    {
        return $this->trades()->where('status', 'closed')->sum('pnl');
    }

    /**
     * Get net RR from closed trades
     */
    public function getNetRrAttribute()
    {
        return $this->trades()->where('status', 'closed')->sum('rr');
    }

    /**
     * Get Win Rate percentage
     */
    public function getWinRateAttribute()
    {
        $closed = $this->trades()->where('status', 'closed')->get();
        $total = $closed->count();
        if ($total === 0) {
            return 0;
        }
        $wins = $closed->where('outcome', 'win')->count();
        return round(($wins / $total) * 100, 1);
    }

    /**
     * Get total trades count
     */
    public function getTradeCountAttribute()
    {
        return $this->trades()->count();
    }

    /**
     * Get Consistency Rule compliance statistics
     */
    public function getConsistencyStatsAttribute()
    {
        $rulePercent = (float) ($this->consistency_rule_percent ?: 50.0);
        $ruleType = $this->consistency_rule_type ?: 'day';
        
        $closedTrades = $this->trades()->where('status', 'closed')->get();
        $totalPnl = (float) $closedTrades->sum('pnl');

        // Group by trade_date for day calculations
        $days = $closedTrades->groupBy(function($t) {
            return $t->trade_date ? substr($t->trade_date, 0, 10) : 'unknown';
        })->map(function($tradesOnDay, $date) {
            $pnl = (float) $tradesOnDay->sum('pnl');
            $tradesCount = $tradesOnDay->count();
            return [
                'date' => $date,
                'pnl' => $pnl,
                'trades_count' => $tradesCount,
            ];
        })->values();

        $bestDayProfit = (float) $days->where('pnl', '>', 0)->max('pnl') ?: 0.0;
        $bestTradeProfit = (float) $closedTrades->where('pnl', '>', 0)->max('pnl') ?: 0.0;

        $outlierValue = ($ruleType === 'trade') ? $bestTradeProfit : $bestDayProfit;

        $currentConsistencyPct = 0.0;
        if ($totalPnl > 0 && $outlierValue > 0) {
            $currentConsistencyPct = round(($outlierValue / $totalPnl) * 100, 2);
        } elseif ($outlierValue > 0 && $totalPnl <= 0) {
            $currentConsistencyPct = 100.0; // In deficit or break-even with a winning day
        }

        $targetTotalProfit = 0.0;
        if ($outlierValue > 0 && $rulePercent > 0) {
            $targetTotalProfit = round($outlierValue / ($rulePercent / 100.0), 2);
        }

        $additionalProfitNeeded = 0.0;
        if ($targetTotalProfit > $totalPnl) {
            $additionalProfitNeeded = round($targetTotalProfit - $totalPnl, 2);
        }

        $isCompliant = ($totalPnl > 0 && $outlierValue > 0 && $currentConsistencyPct <= $rulePercent);
        if ($outlierValue == 0 && $totalPnl >= 0) {
            $isCompliant = true; // No winning trades yet, compliant by default
        }

        // Remaining to account profit target if defined
        $accountProfitTarget = (float) ($this->profit_target ?: 0.0);
        $remainingToTarget = max(0.0, round($accountProfitTarget - $totalPnl, 2));

        return [
            'has_rule' => (bool) $this->has_consistency_rule,
            'rule_percent' => $rulePercent,
            'rule_type' => $ruleType,
            'total_pnl' => round($totalPnl, 2),
            'best_day_profit' => round($bestDayProfit, 2),
            'best_trade_profit' => round($bestTradeProfit, 2),
            'outlier_value' => round($outlierValue, 2),
            'current_consistency_pct' => $currentConsistencyPct,
            'target_total_profit' => $targetTotalProfit,
            'additional_profit_needed' => $additionalProfitNeeded,
            'is_compliant' => $isCompliant,
            'account_profit_target' => $accountProfitTarget,
            'remaining_to_target' => $remainingToTarget,
            'days_breakdown' => $days->sortByDesc('date')->values()->toArray(),
        ];
    }
}

