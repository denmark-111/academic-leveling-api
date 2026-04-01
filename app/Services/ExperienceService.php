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

    private function expToNextLevel($level)
    {
        return 100 + ($level * 20);
    }
}