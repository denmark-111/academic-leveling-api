<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return QuizResource::collection(
            Quiz::with('user')
                ->where(function ($query) use ($request) {
                    $query->where('is_public', true)
                        ->orWhere('user_id', $request->user()->id);
                })
                ->paginate(10)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuizRequest $request)
    {
        $validated = $request->validated();
        $user = $request->user();

        $quiz = DB::transaction(function () use ($validated, $user) {
            
            // Generate code only if public
            $quizCode = ($validated['is_public'] ?? false) ? $this->generateUniqueQuizCode() : null;

            $quiz = $user->quizzes()->create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'subject' => $validated['subject'] ?? null,
                'grade_level' => $validated['grade_level'] ?? 'all',
                'difficulty' => $validated['difficulty'] ?? 'easy',
                'timer_mode' => $validated['timer_mode'] ?? 'none',
                'is_question_shuffled' => $validated['is_question_shuffled'] ?? false,
                'is_choices_shuffled' => $validated['is_choices_shuffled'] ?? false,
                'is_public' => $validated['is_public'] ?? false,
                'quiz_code' => $quizCode,
            ]);

            $this->createQuestions($quiz, $validated['questions']);

            return $quiz;
        });

        $quiz->load(['questions.choices', 'user']);

        return QuizResource::make($quiz)
            ->additional(['message' => 'Quiz created successfully'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Quiz $quiz)
    {
        if (!$quiz->is_public && $quiz->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return QuizResource::make($quiz->load('questions.choices', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuizRequest $request, Quiz $quiz)
    {
        if ($quiz->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validated();

        $quiz = DB::transaction(function () use ($validated, $quiz) {

            $quiz->update([
                'title' => $validated['title'] ?? $quiz->title,
                'description' => $validated['description'] ?? $quiz->description,
                'is_public' => $validated['is_public'] ?? $quiz->is_public,
            ]);

            if (isset($validated['questions'])) {

                // delete existing questions and choices (choices will be deleted automatically via model events)
                $quiz->questions->each->delete();

                // create new questions and choices
                $this->createQuestions($quiz, $validated['questions']);
            }

            return $quiz->load('questions.choices', 'user');
        });

        return QuizResource::make($quiz)
            ->additional(['message' => 'Quiz updated successfully']);   
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Quiz $quiz)
    {
        if ($quiz->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $quiz->delete();

        return response()->noContent();
    }

    // Quizzes owned by the user
    public function myQuizzes(Request $request)
    {
        return QuizResource::collection(
            Quiz::with('user')
                ->where('user_id', $request->user()->id)
                ->paginate(10)
        );
    }

    // Helper method to create questions and choices
    private function createQuestions($quiz, array $questions)
    {
        foreach ($questions as $index => $questionData) {

            $question = $quiz->questions()->create([
                'question_text' => $questionData['question_text'],
                'type' => $questionData['type'],
                'correct_answer' => $questionData['correct_answer'] ?? null,
                'points' => $questionData['points'] ?? 1,
                'order' => $questionData['order'] ?? $index,
            ]);

            if (!empty($questionData['choices'])) {
                foreach ($questionData['choices'] as $choice) {
                    $question->choices()->create([
                        'choice_text' => $choice['choice_text'],
                        'is_correct' => $choice['is_correct'],
                    ]);
                }
            }
        }
    }

    // Generate a unique 8-character quiz code.
    private function generateUniqueQuizCode(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (Quiz::where('quiz_code', $code)->exists());

        return $code;
    }
}
