<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplineDailyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'log_date',
        'completed_activities',
        'notes',
        'score',
    ];

    protected $casts = [
        'completed_activities' => 'array',
        'log_date' => 'date:Y-m-d',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
