<?php

namespace App\Listeners;

use App\Events\StudySessionCreated;
use App\Services\ExperienceService;
use App\Services\QuestService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleStudySessionCreated
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function handle(StudySessionCreated $event)
    {
        // Quest progress
        app(QuestService::class)->updateProgress(
            $event->userId,
            'study_duration',
            $event->duration
        );

        // EXP gain
        $exp = floor($event->duration / 60);

        app(ExperienceService::class)->gainExp(
            $event->userId,
            $exp
        );
    }
}
