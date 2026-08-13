<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplineMonthPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year_month',
        'background_image',
        'layout_config',
        'monthly_activities',
        'is_locked',
    ];

    protected $casts = [
        'layout_config' => 'array',
        'monthly_activities' => 'array',
        'is_locked' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
