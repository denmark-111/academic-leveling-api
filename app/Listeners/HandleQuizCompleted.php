<?php

namespace App\Listeners;

use App\Events\QuizCompleted;
use App\Services\ExperienceService;
use App\Services\QuestService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleQuizCompleted
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function handle(QuizCompleted $event)
    {
        // Quest progress
        app(QuestService::class)->updateProgress(
            $event->userId,
            'quiz_count',
            1
        );

        // EXP gain
        $exp = $event->score * 5;

        app(ExperienceService::class)->gainExp(
            $event->userId,
            $exp
        );
    }
}
