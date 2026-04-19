<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'progress' => [
                'level' => $this->level,
                'current_exp' => $this->exp,
                'exp_to_next_level' => app(\App\Services\ExperienceService::class)->expToNextLevel($this->level),
                'progress_percent' => $this->level > 0 ? round(($this->exp / app(\App\Services\ExperienceService::class)->expToNextLevel($this->level)) * 100, 2) : 0,
            ],
            'coins' => $this->coins,
            'total_exp' => $this->total_exp, // add this line
        ];
    }
}
