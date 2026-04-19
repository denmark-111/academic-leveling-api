<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuizCompleted
{
    use Dispatchable, SerializesModels;

    public $userId;
    public $score;
    public $totalPoints;

    public function __construct($userId, $score, $totalPoints)
    {
        $this->userId = $userId;
        $this->score = $score;
        $this->totalPoints = $totalPoints;
    }
}