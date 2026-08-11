<?php

namespace Modules\Deriv\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DerivTradeMetric extends Model
{
    protected $table = 'deriv_trade_metrics';

    protected $fillable = [
        'deriv_account_id',
        'win_rate',
        'total_trades',
        'wins',
        'losses',
        'breakeven',
        'avg_win',
        'avg_loss',
        'risk_reward_ratio',
        'profit_factor',
        'expectancy',
    ];

    protected $casts = [
        'win_rate' => 'decimal:2',
        'avg_win' => 'decimal:2',
        'avg_loss' => 'decimal:2',
        'risk_reward_ratio' => 'decimal:2',
        'profit_factor' => 'decimal:2',
        'expectancy' => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(DerivAccount::class, 'deriv_account_id');
    }
}
