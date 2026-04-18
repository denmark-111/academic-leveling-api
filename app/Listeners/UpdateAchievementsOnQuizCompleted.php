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
        // Increment quiz_count by 1
        $this->achievementService->updateProgress($event->userId, 'quiz_count', 1);

        // Check for perfect score (if score is max possible? But we don't have max score here.
        // We'll need to pass max score. For now, assume perfect if score == total points.
        // We'll leave perfect_score for later enhancement.
        // Optionally, we can pass the score and total questions, but we don't have total here.
        // So we skip perfect_score for now.
    }
}