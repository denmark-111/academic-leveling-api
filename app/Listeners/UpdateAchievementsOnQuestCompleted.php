<?php

namespace App\Listeners;

use App\Events\QuestCompleted;
use App\Services\AchievementService;

class UpdateAchievementsOnQuestCompleted
{
    protected $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function handle(QuestCompleted $event)
    {
        // Increment quest_completed by 1
        $this->achievementService->updateProgress($event->userId, 'quest_completed', 1);
    }
}