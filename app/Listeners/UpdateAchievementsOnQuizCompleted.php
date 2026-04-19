<?php

namespace App\Listeners;

use App\Events\QuizCompleted;
use App\Services\AchievementService;

class UpdateAchievementsOnQuizCompleted
{
    protected $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function handle(QuizCompleted $event)
    {
        // Increment quiz count
        $this->achievementService->updateProgress($event->userId, 'quiz_count', 1);

        // Check for perfect score
        $this->achievementService->updatePerfectScore($event->userId, $event->score, $event->totalPoints);
    }
}