<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyInsight extends Model
{
    protected $fillable = [
        'user_id',
        'period',
        'insight',
        'trade_count',
        'total_pnl',
        'win_rate',
    ];

    protected $casts = [
        'total_pnl' => 'decimal:2',
        'win_rate' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
