<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\QuizCompleted::class => [
            \App\Listeners\UpdateAchievementsOnQuizCompleted::class,
        ],
        \App\Events\StudySessionCreated::class => [
            \App\Listeners\UpdateAchievementsOnStudySessionCreated::class,
        ],
        \App\Events\QuestCompleted::class => [
            \App\Listeners\UpdateAchievementsOnQuestCompleted::class,
        ],
        \App\Events\LevelUp::class => [
            \App\Listeners\UpdateAchievementsOnLevelUp::class,
        ],
        \App\Events\TotalExpIncreased::class => [
            \App\Listeners\UpdateAchievementsOnTotalExpIncreased::class,
        ],
    ];
}