<?php

namespace App\Listeners;

use App\Events\QuestCompleted;
use App\Services\CoinService;
use App\Services\ExperienceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleQuestCompleted
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function handle(QuestCompleted $event)
    {
        // EXP gain
        app(ExperienceService::class)->gainFromQuest(
            $event->userId,
            $event->quest
        );

        // Coin gain
        app(CoinService::class)->gainFromQuest(
            $event->userId,
            $event->quest
        );
    }
}
