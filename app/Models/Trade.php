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
        'asset_id',
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
        'hin_day',
        'entry_narrative',
        'notes',
        'improvement_idea'
    ];

    protected $casts = [
        'entry_pd_array' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function associatedAsset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function screenshots()
    {
        return $this->hasMany(TradeScreenshot::class);
    }

    /**
     * Helper to get asset name from relationship or fallback to legacy column
     */
    public function getAssetName()
    {
        if ($this->asset_id && $this->associatedAsset) {
            return $this->associatedAsset->name;
        }
        return $this->attributes['asset'] ?? '-';
    }
}
