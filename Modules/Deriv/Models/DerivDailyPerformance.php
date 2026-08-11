<?php

namespace Modules\Deriv\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DerivDailyPerformance extends Model
{
    protected $table = 'deriv_daily_performance';

    protected $fillable = [
        'deriv_account_id',
        'date',
        'starting_balance',
        'ending_balance',
        'pnl',
    ];

    protected $casts = [
        'date' => 'date',
        'starting_balance' => 'decimal:2',
        'ending_balance' => 'decimal:2',
        'pnl' => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(DerivAccount::class, 'deriv_account_id');
    }
}
