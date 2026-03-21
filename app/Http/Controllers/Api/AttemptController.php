<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;

class AttemptController extends Controller
{

    // Start the quiz
    public function start(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);

        if (!$quiz->is_public && $quiz->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $attempt = $quiz->attempts()->create([
            'user_id' => $request->user()->id,
            'started_at' => now()
        ]);

        return response()->json([
            'message' => 'Quiz started successfully',
            'attempt_id' => $attempt->id
        ]);
    }

    // Answer a question
    public function saveAnswer(Request $request, $attemptId)
    {
        $attempt = Attempt::findOrFail($attemptId);

        if ($attempt->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($attempt->completed_at) {
            return response()->json(['message' => 'Attempt already submitted'], 400);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'choice_id' => 'nullable|exists:choices,id',
            'answer_text' => 'nullable|string'
        ]);

        $question = Question::findOrFail($validated['question_id']);

        // Make sure question belongs to the quiz
        if ($question->quiz_id !== $attempt->quiz_id) {
            return response()->json(['message' => 'Question does not belong to this quiz'], 403);
        }

        // Determine correctness
        $isCorrect = null;

        if ($question->type === 'multiple_choice' || $question->type === 'true_false') {
            $correctChoice = $question->choices()->where('is_correct', true)->first();
            $isCorrect = $correctChoice && $correctChoice->id == $validated['choice_id'];
        } elseif ($question->type === 'identification') {
            $isCorrect = isset($validated['answer_text'])
                && strtolower(trim($validated['answer_text'])) === strtolower(trim($question->correct_answer));
        }

        // Save or update answer
        // since it doenst check if the question already has been answered, user can answer the same question multiple times and update their previous answer
        $attempt->answers()->updateOrCreate(
            [
                'question_id' => $question->id,
            ],
            [
                'choice_id' => $validated['choice_id'] ?? null,
                'answer_text' => $validated['answer_text'] ?? null,
                'is_correct' => $isCorrect,
            ]
        );

        return response()->json([
            'message' => 'Answer saved',
            'is_correct' => $isCorrect
        ]);
    }

    // Submit the quiz
    public function submit(Request $request, $attemptId)
    {
        $attempt = Attempt::with('answers.question')->findOrFail($attemptId);

        if ($attempt->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($attempt->completed_at) {
            return response()->json(['message' => 'Attempt already submitted'], 400);
        }

        // Calculate score
        $score = 0;

        foreach ($attempt->answers as $answer) {
            // Use the points from the question
            if ($answer->is_correct) {
                $score += $answer->question->points;
            }
        }

        // Mark the attempt as completed
        $attempt->update([
            'score' => $score,
            'completed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Quiz submitted successfully',
            'score' => $score,
            'total_questions' => $attempt->quiz->questions()->count(),
            'correct_answers' => $attempt->answers()->where('is_correct', true)->count(),
        ]);
    }
}
