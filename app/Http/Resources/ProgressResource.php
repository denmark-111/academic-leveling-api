<?php

namespace App\Http\Resources;

use App\Services\ExperienceService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $expToNextLevel = app(ExperienceService::class)->expToNextLevel($this->level);
        
        return [
            'level' => $this->level,
            'current_exp' => $this->exp,
            'exp_to_next_level' => $expToNextLevel,
            'progress_percent' => round(($this->exp / $expToNextLevel) * 100, 2),
        ];
    }
}
