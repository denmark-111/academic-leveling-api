<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudySessionResource extends JsonResource
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
            'session_at' => $this->session_at,
            'duration' => $this->duration, // Duration in seconds
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'rewards' => $this->when(isset($this->rewards), $this->rewards), // Include rewards if they are set (from controller)
        ];
    }
}
