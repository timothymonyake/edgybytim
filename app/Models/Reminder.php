<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = [
        'user_id', 
        'title', 
        'content', 
        'remind_at', 
        'frequency', 
        'recurrence_days', 
        'is_active', 
        'last_reminded_at',
        'expires_at'
    ];

    protected $casts = [
        'remind_at' => 'datetime',
        'recurrence_days' => 'array',
        'is_active' => 'boolean',
        'last_reminded_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
