<?php

namespace App\Http\Resources;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttemptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'score' => $this->score,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'quiz' => QuizResource::make($this->whenLoaded('quiz')),
            'answers' => $this->whenLoaded('answers', function () {
                return $this->answers->map(function ($answer) {
                    return [
                        'question_text' => $answer->question->question_text,
                        'correct_answer' => $answer->correct_answer_snapshot, // correct answer snapshot at the time of attempt
                        'answer_text' => $answer->answer_text,
                        'is_correct' => $answer->is_correct,
                    ];
                });
            }),
        ];
    }
}
