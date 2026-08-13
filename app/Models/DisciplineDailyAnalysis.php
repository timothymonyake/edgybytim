<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplineDailyAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'analysis_date',
        'phase',
        'chart_url',
        'chart_preview_url',
        'timeframe',
        'symbol',
        'bias',
        'key_levels',
        'narrative',
        'forex_week_url',
        'sort_order',
    ];

    protected $casts = [
        'analysis_date' => 'date:Y-m-d',
        'key_levels'    => 'array',
    ];

    /**
     * Derive an embeddable image URL from a TradingView snapshot URL.
     * Handles both: https://www.tradingview.com/x/HASH/ and https://s3.tradingview.com/...
     */
    public static function resolveTradingViewPreviewUrl(?string $url): ?string
    {
        if (!$url) return null;

        // Already a direct image URL
        if (preg_match('/\.(png|jpg|jpeg|gif|webp)(\?.*)?$/i', $url)) {
            return $url;
        }

        // TradingView snapshot → embed image URL
        // https://www.tradingview.com/x/AbCdEfGh/  → https://s3.tradingview.com/snapshots/a/AbCdEfGh.png
        if (preg_match('/tradingview\.com\/x\/([a-zA-Z0-9]+)\/?$/', $url, $m)) {
            $hash  = $m[1];
            $first = strtolower($hash[0]);
            return "https://s3.tradingview.com/snapshots/{$first}/{$hash}.png";
        }

        return $url; // return as-is so we can still link out
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
