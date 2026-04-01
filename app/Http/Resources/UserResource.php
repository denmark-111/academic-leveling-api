<?php

namespace App\Http\Resources;

use App\Services\ExperienceService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $service = app(ExperienceService::class);
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'level' => $this->level,
            'current_exp' => $this->exp,
            'exp_to_next_level' => $service->expToNextLevel($this->level),
            'progress_percent' => round(($this->exp / $service->expToNextLevel($this->level)) * 100, 2),
        ];
    }
}
