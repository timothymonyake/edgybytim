<?php

namespace Modules\Deriv\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DerivAccount extends Model
{
    protected $fillable = [
        'user_id',
        'api_token',
        'account_id',
        'balance',
        'equity',
        'currency',
    ];

    protected $casts = [
        'api_token' => 'encrypted',
        'balance' => 'decimal:2',
        'equity' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trades(): HasMany
    {
        return $this->hasMany(DerivTrade::class, 'deriv_account_id');
    }

    public function dailyPerformances(): HasMany
    {
        return $this->hasMany(DerivDailyPerformance::class, 'deriv_account_id');
    }

    public function metrics(): HasOne
    {
        return $this->hasOne(DerivTradeMetric::class, 'deriv_account_id')->withDefault([
            'win_rate' => 0,
            'total_trades' => 0,
            'wins' => 0,
            'losses' => 0,
            'avg_win' => 0,
            'avg_loss' => 0,
            'profit_factor' => 0,
            'expectancy' => 0,
        ]);
    }
}
