<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Events\AchievementCompleted;
use Illuminate\Support\Facades\Log;

class AchievementService
{
    /**
     * Update progress for achievements of a given type.
     */
    public function updateProgress($userId, $type, $value)
    {
        // Find all achievements of this type that are not yet completed by the user
        $achievements = Achievement::where('type', $type)->get();

        foreach ($achievements as $achievement) {
            $userAchievement = UserAchievement::firstOrCreate([
                'user_id' => $userId,
                'achievement_id' => $achievement->id,
            ]);

            // If already completed, skip
            if ($userAchievement->completed_at) {
                continue;
            }

            // Add progress
            $userAchievement->progress += $value;

            // Check if target reached
            if ($userAchievement->progress >= $achievement->target_value) {
                $userAchievement->completed_at = now();
                // Optionally fire an event for achievement completion (can be used for notifications)
                // event(new AchievementCompleted($userId, $achievement));
            }

            $userAchievement->save();
        }
    }

    public function updatePerfectScore($userId, $score, $totalPoints)
    {
        if ($score == $totalPoints && $totalPoints > 0) {
            $this->updateProgress($userId, 'perfect_score', 1);
        }
    }
}