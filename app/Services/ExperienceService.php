<?php

namespace App\Services;

use App\Events\LevelUp;
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
            event(new LevelUp($user->id, $user->level));
        }

        $user->save();
    }

    public function expToNextLevel(int $level): int
    {
        // Level 1 -> 1500 exp, each subsequent level requires 500 more exp than the previous
        return 1500 + (($level - 1) * 500);
    }

    // Quiz EXP gain logic
    public function gainFromQuiz($userId, $score)
    {
        $exp = $this->calculateQuizExp($score);

        $this->gainExp($userId, $exp);
    }

    // Quiz Exp formula
    public function calculateQuizExp($score): int
    {
        return $score * 5;
    }

    // Study EXP gain logic
    public function gainFromStudy($userId, $duration)
    {
        $exp = $this->calculateStudyExp($duration);

        $this->gainExp($userId, $exp);
    }

    // Study Exp formula
    public function calculateStudyExp($duration): int
    {
        return floor($duration / 60);
    }

    // Quest EXP logic
    public function gainFromQuest($userId, $quest)
    {
        $this->gainExp($userId, $quest->exp_reward);
    }
}