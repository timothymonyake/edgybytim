<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'asset_type_id',
        'name',
        'code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assetType()
    {
        return $this->belongsTo(AssetType::class);
    }

    public function trades()
    {
        return $this->hasMany(Trade::class);
    }
}
