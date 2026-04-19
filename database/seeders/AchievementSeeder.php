<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    public function run()
    {
        $achievements = [
            [
                'name' => 'Quiz Novice',
                'description' => 'Complete 5 quizzes',
                'type' => 'quiz_count',
                'target_value' => 5,
                'reward_exp' => 100,
                'reward_coins' => 50,
            ],
            [
                'name' => 'Quiz Master',
                'description' => 'Complete 25 quizzes',
                'type' => 'quiz_count',
                'target_value' => 25,
                'reward_exp' => 500,
                'reward_coins' => 200,
            ],
            [
                'name' => 'Study Streak Starter',
                'description' => 'Study for 600 seconds (10 min) total',
                'type' => 'study_duration',
                'target_value' => 600,
                'reward_exp' => 150,
                'reward_coins' => 75,
            ],
            [
                'name' => 'Dedicated Scholar',
                'description' => 'Study for 3600 seconds (1 hour) total',
                'type' => 'study_duration',
                'target_value' => 3600,
                'reward_exp' => 1000,
                'reward_coins' => 500,
            ],
            [
                'name' => 'Quest Beginner',
                'description' => 'Complete 3 quests',
                'type' => 'quest_completed',
                'target_value' => 3,
                'reward_exp' => 200,
                'reward_coins' => 100,
            ],
            [
                'name' => 'Level 5',
                'description' => 'Reach level 5',
                'type' => 'level_reached',
                'target_value' => 5,
                'reward_exp' => 300,
                'reward_coins' => 150,
            ],
            [
                'name' => 'Perfect Start',
                'description' => 'Get a perfect score on any quiz',
                'type' => 'perfect_score',
                'target_value' => 1,
                'reward_exp' => 200,
                'reward_coins' => 100,
            ],
            [
                'name' => 'EXP Accumulator',
                'description' => 'Earn 5000 total EXP',
                'type' => 'total_exp',
                'target_value' => 5000,
                'reward_exp' => 0,
                'reward_coins' => 500,
            ],
            [
                'name' => 'EXP Master',
                'description' => 'Earn 20000 total EXP',
                'type' => 'total_exp',
                'target_value' => 20000,
                'reward_exp' => 0,
                'reward_coins' => 2000,
            ],
        ];

        foreach ($achievements as $ach) {
            // Use firstOrCreate to avoid duplicates
            Achievement::firstOrCreate(
                ['name' => $ach['name']],  // check by name
                $ach                       // data to create if not found
            );
        }
    }
}