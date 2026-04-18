<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'target_value',
        'reward_exp',
        'reward_coins',
        'icon'
    ];

    protected $casts = [
        'target_value' => 'integer',
        'reward_exp' => 'integer',
        'reward_coins' => 'integer',
    ];

    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class);
    }
}