<?php

namespace App\Listeners;

use App\Events\LevelUp;
use App\Services\AchievementService;

class UpdateAchievementsOnLevelUp
{
    protected $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function handle(LevelUp $event)
    {
        // Set level_reached achievement to the new level (not cumulative)
        $this->achievementService->setProgress($event->userId, 'level_reached', $event->newLevel);
    }
}