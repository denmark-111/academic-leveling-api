<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LevelUp
{
    use Dispatchable, SerializesModels;

    public $userId;
    public $newLevel;

    public function __construct($userId, $newLevel)
    {
        $this->userId = $userId;
        $this->newLevel = $newLevel;
    }
}