<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\StudySession;
use App\Models\Attempt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display the authenticated user's profile.
     */
    public function show(Request $request)
    {
        return UserResource::make($request->user());
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'username' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->update($validated);

        return UserResource::make($user)
            ->additional(['message' => 'Profile updated successfully']);
    }

    /**
     * Get user's study statistics.
     */
    public function stats(Request $request)
    {
        $user = $request->user();

        // Calculate total study session duration in minutes
        $totalDuration = StudySession::where('user_id', $user->id)
            ->sum('duration') / 60; // Convert seconds to minutes

        // Count total quizzes completed (attempts with completed_at)
        $totalQuizzesCompleted = Attempt::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->count();

        // Calculate streak (consecutive days with quiz or study session)
        $streak = $this->calculateStreak($user->id);

        return response()->json([
            'total_study_duration_minutes' => (int) $totalDuration,
            'total_quizzes_completed' => $totalQuizzesCompleted,
            'streak' => $streak,
        ]);
    }

    /**
     * Calculate the user's streak of consecutive days with activity.
     */
    private function calculateStreak($userId)
    {
        // Get all dates from study sessions
        $studyDates = StudySession::where('user_id', $userId)
            ->selectRaw("DATE(session_at) as activity_date")
            ->distinct()
            ->pluck('activity_date');

        // Get all dates from completed attempts
        $quizDates = Attempt::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->selectRaw("DATE(completed_at) as activity_date")
            ->distinct()
            ->pluck('activity_date');

        // Merge and get unique dates, sorted in descending order
        $uniqueDates = collect($studyDates)
            ->merge($quizDates)
            ->unique()
            ->map(fn($date) => Carbon::parse($date)->startOfDay())
            ->sort()
            ->reverse()
            ->values();

        if ($uniqueDates->isEmpty()) {
            return 0;
        }

        // Count consecutive days from today backwards
        $streak = 0;
        $currentDate = Carbon::today()->startOfDay();

        foreach ($uniqueDates as $activityDate) {
            if ($currentDate->isSameDay($activityDate)) {
                $streak++;
                $currentDate = $currentDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }
}