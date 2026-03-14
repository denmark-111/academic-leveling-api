<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
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
            'user' => $this->whenLoaded('user', function () {
                return[
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
            'quiz_code' => $this->quiz_code,
            'title' => $this->title,
            'description' => $this->description,
            'subject' => $this->subject,
            'grade_level' => $this->grade_level,
            'difficulty' => $this->difficulty,
            'timer_mode' => $this->timer_mode,
            'is_question_shuffled' => $this->is_question_shuffled,
            'is_choices_shuffled' => $this->is_choices_shuffled,
            'is_public' => $this->is_public,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'questions' => QuestionResource::collection($this->whenLoaded('questions')),
        ];
    }
}
