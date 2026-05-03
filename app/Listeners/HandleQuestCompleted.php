<?php

namespace App\Listeners;

use App\Events\QuestCompleted;
use App\Models\Quest;
use App\Services\QuestService;
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
        $completedQuest = $event->quest;

        // Prevent meta quests from triggering themselves
        if ($completedQuest->type === 'quest_completion_count') {
            return;
        }

        // Find all meta quests that depend on this quest's period
        $metaQuests = Quest::where('type', 'quest_completion_count')
            ->where('is_active', true)
            ->where('source_period', $completedQuest->period) // only meta quests that track the same period as the completed quest
            ->get();

        foreach ($metaQuests as $metaQuest) {
            app(QuestService::class)->updateProgressForQuest(
                $event->userId,
                $metaQuest->id,
                1
            );
        }
    }
}
