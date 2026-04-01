<?php

namespace App\Services;

use App\Models\User;

class ExperienceService
{
    public function gainExp($userId, $amount)
    {
        $user = User::findOrFail($userId);

        $user->exp += $amount;

        // Level up loop
        while ($user->exp >= $this->expToNextLevel($user->level)) {
            $user->exp -= $this->expToNextLevel($user->level);
            $user->level++;
        }

        $user->save();
    }

    public function expToNextLevel(int $level): int
    {
        // Level 1 -> 1500 exp, each subsequent level requires 500 more exp than the previous
        return 1500 + (($level - 1) * 500);
    }
}