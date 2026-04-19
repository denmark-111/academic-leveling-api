<?php

namespace App\Listeners;

use App\Events\StudySessionCreated;
use App\Services\AchievementService;

class UpdateAchievementsOnStudySessionCreated
{
    protected $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function handle(StudySessionCreated $event)
    {
        // Add duration in seconds to study_duration achievement
        $this->achievementService->updateProgress($event->userId, 'study_duration', $event->duration);
    }
}