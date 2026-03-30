<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserQuest extends Model
{
    protected $fillable = [
        'user_id',
        'quest_id',
        'progress',
        'completed_at',
        'period_start',
    ];
}
