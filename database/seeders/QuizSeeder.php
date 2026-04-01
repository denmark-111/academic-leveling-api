<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the test user
        $user = User::where('email', 'test@example.com')->first();

        if (!$user) {
            $this->command->error("Test user not found. Run DatabaseSeeder first.");
            return;
        }

        // Create or update the quiz for the user
        $quiz = $user->quizzes()->updateOrCreate(
            ['title' => 'Basic Programming Quiz'],
            [
                'description' => 'Test your knowledge about programming basics',
                'subject' => 'Fundamentals of Programming',
                'grade_level' => 'college',
                'type' => 'mixed',
                'difficulty' => 'medium',
                'timer_mode' => 'quiz',
                'is_question_shuffled' => true,
                'is_choices_shuffled' => true,
                'is_public' => true,
                'quiz_code' => Str::upper(Str::random(8)),
            ]
        );

        // Clean old questions to avoid stacking duplicates
        $quiz->questions()->delete();

        // Insert the Questions & Choices
        $this->seedQuestions($quiz);
    }

    private function seedQuestions($quiz)
    {
        $questions = [
            [
                'question_text' => 'What does HTML stand for?',
                'type' => 'multiple_choice',
                'points' => 1,
                'choices' => [
                    ['choice_text' => 'Hyper Text Markup Language', 'is_correct' => true],
                    ['choice_text' => 'High Text Machine Language', 'is_correct' => false],
                    ['choice_text' => 'Hyperlinks Text Mark Language', 'is_correct' => false],
                ]
            ],
            [
                'question_text' => 'PHP is a programming language.',
                'type' => 'true_false',
                'points' => 1,
                'choices' => [
                    ['choice_text' => 'True', 'is_correct' => true],
                    ['choice_text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'question_text' => 'Who created Laravel?',
                'type' => 'identification',
                'points' => 1,
                'correct_answer' => 'Taylor Otwell'
            ]
        ];

        foreach ($questions as $index => $qData) {
            $question = $quiz->questions()->create([
                'question_text' => $qData['question_text'],
                'type' => $qData['type'],
                'points' => $qData['points'],
                'order' => $index + 1,
                'correct_answer' => $qData['correct_answer'] ?? null,
            ]);

            if (isset($qData['choices'])) {
                foreach ($qData['choices'] as $cData) {
                    $question->choices()->create($cData);
                }
            }
        }
    }
}
