<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Milestone extends Model
{
    protected $fillable = ['user_id', 'date', 'img_path', 'text'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
