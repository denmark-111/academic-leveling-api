<?php

namespace App\Listeners;

use App\Events\QuestCompleted;
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
        app(ExperienceService::class)->gainExp(
            $event->userId,
            $event->quest->exp_reward
        );
    }
}
