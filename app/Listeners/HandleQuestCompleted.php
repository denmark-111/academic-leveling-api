<?php

namespace App\Listeners;

use App\Events\QuestCompleted;
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
        //
    }
}
