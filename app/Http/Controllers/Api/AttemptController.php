<?php

namespace App\Http\Controllers\Api;

use App\Events\QuizCompleted;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttemptResource;
use App\Models\Attempt;
use App\Models\Question;
use App\Models\Quiz;
use App\Services\CoinService;
use App\Services\ExperienceService;
use Illuminate\Http\Request;

class AttemptController extends Controller
{
    // List all attempts of the authenticated user (history)
    public function index(Request $request)
    {
        $attempts = $request->user()->attempts()
            ->with('quiz')
            ->whereNotNull('completed_at')
            ->paginate(10);

        return AttemptResource::collection($attempts);
    }

    // Show details of a specific attempt
    public function show(Request $request, Attempt $attempt)
    {
        if ($attempt->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$attempt->completed_at) {
            return response()->json(['message' => 'Attempt not completed yet'], 404);
        }

        return AttemptResource::make($attempt->load('quiz', 'answers.question'));
    }

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

        $isCorrect = null; // Determine correctness
        $answerText = null; // For answer snapshot
        $correctAnswerSnapshot = null; // For correct answer snapshot

        if ($question->type === 'multiple_choice' || $question->type === 'true_false') {
            $selectedChoice = $question->choices()->where('id', $validated['choice_id'] ?? null)->first();

            $correctChoice = $question->choices()->where('is_correct', true)->first();

            $isCorrect = $correctChoice && $selectedChoice && $correctChoice->id === $selectedChoice->id;

            $answerText = $selectedChoice?->choice_text;
            $correctAnswerSnapshot = $correctChoice?->choice_text;
        } elseif ($question->type === 'identification') {
            $answerText = $validated['answer_text'] ?? null;

            $isCorrect = $answerText && strtolower(trim($answerText)) === strtolower(trim($question->correct_answer));
            $correctAnswerSnapshot = $question->correct_answer;
        }

        // Save or update answer
        // since it doenst check if the question already has been answered, user can answer the same question multiple times and update their previous answer
        $attempt->answers()->updateOrCreate(
            [
                'question_id' => $question->id,
            ],
            [
                'choice_id' => $validated['choice_id'] ?? null,
                'answer_text' => $answerText,
                'is_correct' => $isCorrect,
                'correct_answer_snapshot' => $correctAnswerSnapshot,
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

        // Fire quiz completed event
        event(new QuizCompleted(
            $attempt->user_id,
            $score
        ));

        return response()->json([
            'message' => 'Quiz submitted successfully',
            'data' => [
                'score' => $score,
                'total_questions' => $attempt->quiz->questions()->count(),
                'correct_answers' => $attempt->answers()->where('is_correct', true)->count(),
                'rewards' => [
                    'exp' => app(ExperienceService::class)->calculateQuizExp($score),
                    'coins' => app(CoinService::class)->calculateQuizCoins($score),
                ],
            ]
        ]);
    }

    // Submit all answers at once
    public function submitAll(Request $request, $attemptId)
    {
        $attempt = Attempt::with('quiz.questions.choices')->findOrFail($attemptId);

        if ($attempt->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($attempt->completed_at) {
            return response()->json(['message' => 'Attempt already submitted'], 400);
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.choice_id' => 'nullable|exists:choices,id',
            'answers.*.answer_text' => 'nullable|string'
        ]);

        $score = 0;
        $correctCount = 0;

        foreach ($validated['answers'] as $input) {

            $question = $attempt->quiz->questions
                ->where('id', $input['question_id'])
                ->first();

            if (!$question) {
                continue; // safety: skip invalid question
            }

            $isCorrect = null; // Determine correctness
            $answerText = null; // For answer snapshot
            $correctAnswerSnapshot = null; // For correct answer snapshot

            if (in_array($question->type, ['multiple_choice', 'true_false'])) {
                $selectedChoice = $question->choices->where('id', $input['choice_id'] ?? null)->first();

                $correctChoice = $question->choices->where('is_correct', true)->first();

                $isCorrect = $correctChoice && $selectedChoice && $correctChoice->id === $selectedChoice->id;

                $answerText = $selectedChoice?->choice_text;
                $correctAnswerSnapshot = $correctChoice?->choice_text;
            } elseif ($question->type === 'identification') {
                $answerText = $input['answer_text'] ?? null;

                $isCorrect = $answerText && strtolower(trim($answerText)) === strtolower(trim($question->correct_answer));
                $correctAnswerSnapshot = $question->correct_answer;
            }

            // Save (no updateOrCreate needed since it's one-shot)
            $attempt->answers()->create([
                'question_id' => $question->id,
                'choice_id' => $input['choice_id'] ?? null,
                'answer_text' => $answerText,
                'is_correct' => $isCorrect,
                'correct_answer_snapshot' => $correctAnswerSnapshot,
            ]);

            if ($isCorrect) {
                $score += $question->points;
                $correctCount++;
            }
        }

        // Finalize attempt
        $attempt->update([
            'score' => $score,
            'completed_at' => now(),
        ]);

        // fire quiz completed event
        event(new QuizCompleted(
            $attempt->user_id,
            $score
        ));

        return response()->json([
            'message' => 'Quiz submitted successfully',
            'data' => [
                'score' => $score,
                'total_questions' => $attempt->quiz->questions->count(),
                'correct_answers' => $correctCount,
                'rewards' => [
                    'exp' => app(ExperienceService::class)->calculateQuizExp($score),
                    'coins' => app(CoinService::class)->calculateQuizCoins($score),
                ],
            ]
        ]);
    }
}
