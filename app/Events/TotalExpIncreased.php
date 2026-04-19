<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TotalExpIncreased
{
    use Dispatchable, SerializesModels;

    public $userId;
    public $newTotalExp;

    public function __construct($userId, $newTotalExp)
    {
        $this->userId = $userId;
        $this->newTotalExp = $newTotalExp;
    }
}