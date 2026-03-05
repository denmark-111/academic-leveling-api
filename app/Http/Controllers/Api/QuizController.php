<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return QuizResource::collection(Quiz::where('is_public', true)->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuizRequest $request)
    {
        $validated = $request->validated();

        $quiz = DB::transaction(function () use ($validated, $request) {

            $quiz = Quiz::create([
                'user_id' => $request->user()->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'is_public' => $validated['is_public'] ?? false,
            ]);

            foreach ($validated['questions'] as $index => $questionData) {

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

            return $quiz->load('questions.choices');
        });

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

        return QuizResource::make($quiz);
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

        $quiz->update($validated);

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
}
