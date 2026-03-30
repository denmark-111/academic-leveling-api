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
                'type' => 'study_duration',
                'target' => 3600,
                'period' => 'daily',
            ],
            [
                'title' => 'Answer 3 quizzes',
                'type' => 'quiz_count',
                'target' => 3,
                'period' => 'daily',
            ],

            // WEEKLY
            [
                'title' => 'Study for 10 hours',
                'type' => 'study_duration',
                'target' => 36000,
                'period' => 'weekly',
            ],
            [
                'title' => 'Answer 10 quizzes',
                'type' => 'quiz_count',
                'target' => 10,
                'period' => 'weekly',
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
