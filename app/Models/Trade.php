<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'asset',
        'direction',
        'trade_type',
        'trade_date',
        'entry_time',
        'time_in_position',
        'setup',
        'daily_log_url',
        'news',
        'plan_followed',
        'session',
        'entry_pd_array',
        'entry_type',
        'lot_size',
        'risk_percent',
        'rr',
        'status',
        'pips',
        'outcome',
        'emotions',
        'pnl',
        'mistake',
        'entry_narrative',
        'notes',
        'improvement_idea'
    ];

    protected $casts = [
        'entry_pd_array' => 'array',
    ];

    public function screenshots()
    {
        return $this->hasMany(TradeScreenshot::class);
    }
}
