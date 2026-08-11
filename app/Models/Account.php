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
}
