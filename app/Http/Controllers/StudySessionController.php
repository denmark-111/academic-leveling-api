<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudySessionResource;
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
            'duration' => 'required|integer|min:1',
        ]);

        $user = $request->user();

        $session = $user->studySessions()->create($validated);

        return StudySessionResource::make($session)
            ->additional(['message' => 'Study session created successfully'])
            ->response()
            ->setStatusCode(201);
    }
}
