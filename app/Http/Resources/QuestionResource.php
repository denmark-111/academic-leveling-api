<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'question_text' => $this->question_text,
            'type' => $this->type,
            'correct_answer' => $this->correct_answer,
            'points' => $this->points,
            'order' => $this->order,
            'choices' => ChoiceResource::collection($this->whenLoaded('choices')),
        ];
    }
}
