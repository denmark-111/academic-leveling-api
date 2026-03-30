<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserQuest extends Model
{
    protected $fillable = [
        'user_id',
        'quest_id',
        'progress',
        'is_completed',
        'period_start',
    ];
}
