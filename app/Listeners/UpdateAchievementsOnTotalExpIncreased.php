<?php

namespace App\Listeners;

use App\Events\TotalExpIncreased;
use App\Services\AchievementService;

class UpdateAchievementsOnTotalExpIncreased
{
    protected $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function handle(TotalExpIncreased $event)
    {
        $this->achievementService->setProgress($event->userId, 'total_exp', $event->newTotalExp);
    }
}