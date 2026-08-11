<?php

namespace Modules\Deriv\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DerivTrade extends Model
{
    protected $fillable = [
        'deriv_account_id',
        'symbol',
        'contract_type',
        'entry_price',
        'exit_price',
        'profit_loss',
        'stake',
        'opened_at',
        'closed_at',
        'raw_payload',
        'notes',
        'tags',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'raw_payload' => 'array',
        'tags' => 'array',
        'profit_loss' => 'decimal:2',
        'stake' => 'decimal:2',
        'entry_price' => 'decimal:5',
        'exit_price' => 'decimal:5',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(DerivAccount::class, 'deriv_account_id');
    }
}
