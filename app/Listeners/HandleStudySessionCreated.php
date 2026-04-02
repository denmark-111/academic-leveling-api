<?php

namespace App\Listeners;

use App\Events\StudySessionCreated;
use App\Services\CoinService;
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
        app(ExperienceService::class)->gainFromStudy(
            $event->userId,
            $event->duration
        );

        // Coin gain
        app(CoinService::class)->gainFromStudy(
            $event->userId,
            $event->duration
        );
    }
}
