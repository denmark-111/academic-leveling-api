<?php

namespace App\Services;

use App\Models\User;

class CoinService
{
    public function addCoins($userId, $amount)
    {
        $user = User::findOrFail($userId);
        $user->increment('coins', $amount);
    }

    // Quiz coins gain logic
    public function gainFromQuiz($userId, $score)
    {
        $coins = $this->calculateQuizCoins($score);
        $this->addCoins($userId, $coins);
    }

    // Quiz coin formula
    private function calculateQuizCoins($score): int
    {
        return floor($score * 3);
    }

    // Study coins gain logic
    public function gainFromStudy($userId, $duration)
    {
        $coins = $this->calculateStudyCoins($duration);
        $this->addCoins($userId, $coins);
    }

    // Study coin formula
    private function calculateStudyCoins($duration): int
    {
        return floor($duration / 120);
    }

    // Quest coins
    public function gainFromQuest($userId, $quest)
    {
        $this->addCoins($userId, $quest->coin_reward);
    }
}