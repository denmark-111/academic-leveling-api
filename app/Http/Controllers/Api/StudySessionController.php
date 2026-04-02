<?php

namespace App\Http\Controllers\Api;

use App\Events\StudySessionCreated;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudySessionResource;
use App\Services\CoinService;
use App\Services\ExperienceService;
use Illuminate\Http\Request;

class StudySessionController extends Controller
{
    // List all study sessions for the user
    public function index(Request $request)
    {
        $sessions = $request->user()->studySessions()->latest('session_at')->get();

        return StudySessionResource::collection($sessions);
    }

    // Store a study session
    public function store(Request $request)
    {
        $validated = $request->validate([
            'session_at' => 'required|date',
            'duration' => 'required|integer|min:300', // Minimum 5 minutes (300 seconds)
        ]);

        $user = $request->user();

        $session = $user->studySessions()->create($validated);

        // fire study session created event
        event(new StudySessionCreated(
            $user->id,
            $session->duration
        ));

        // temporarily attach rewards to the session for response (not saved in DB)
        $session->rewards = [
            'exp' => app(ExperienceService::class)->calculateStudyExp($session->duration),
            'coins' => app(CoinService::class)->calculateStudyCoins($session->duration),
        ];

        return StudySessionResource::make($session)
            ->additional(['message' => 'Study session created successfully'])
            ->response()
            ->setStatusCode(201);
    }
}
