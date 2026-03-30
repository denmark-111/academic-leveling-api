<?php

namespace App\Services;

use App\Models\Quest;
use App\Models\UserQuest;

class QuestService
{
    public function updateProgress($userId, $type, $value)
    {
        $quests = Quest::where('type', $type)
            ->where('is_active', true)
            ->get();

        foreach ($quests as $quest) {

            $periodStart = $quest->period === 'daily'
                ? now()->startOfDay()
                : now()->startOfWeek();

            $userQuest = UserQuest::firstOrCreate([
                'user_id' => $userId,
                'quest_id' => $quest->id,
                'period_start' => $periodStart,
            ]);

            $userQuest->progress += $value;

            if (!$userQuest->completed_at && $userQuest->progress >= $quest->target) {
                $userQuest->completed_at = now();
            }

            $userQuest->save();
        }
    }
}