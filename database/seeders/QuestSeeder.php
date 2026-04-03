<?php

namespace Database\Seeders;

use App\Models\Quest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quests = [
            // DAILY
            [
                'title' => 'Study for 1 hour',
                'description' => 'Study for at least 1 hour',
                'type' => 'study_duration',
                'target' => 3600,
                'period' => 'daily',
                'is_active' => true,
                'exp_reward' => 100,
                'coin_reward' => 50,
            ],
            [
                'title' => 'Answer 3 quizzes',
                'description' => 'Answer at least 3 quizzes',
                'type' => 'quiz_count',
                'target' => 3,
                'period' => 'daily',
                'is_active' => true,
                'exp_reward' => 100,
                'coin_reward' => 50,
            ],

            // WEEKLY
            [
                'title' => 'Study for 10 hours',
                'description' => 'Study for at least 10 hours',
                'type' => 'study_duration',
                'target' => 36000,
                'period' => 'weekly',
                'is_active' => true,
                'exp_reward' => 500,
                'coin_reward' => 250,
            ],
            [
                'title' => 'Answer 10 quizzes',
                'description' => 'Answer at least 10 quizzes',
                'type' => 'quiz_count',
                'target' => 10,
                'period' => 'weekly',
                'is_active' => true,
                'exp_reward' => 500,
                'coin_reward' => 250,
            ],
        ];

        foreach ($quests as $quest) {
            Quest::updateOrCreate(
                [
                    'title' => $quest['title'],
                    'period' => $quest['period'],
                ],
                $quest
            );
        }
    }
}
