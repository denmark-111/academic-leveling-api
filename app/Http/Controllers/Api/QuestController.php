<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quest;
use App\Models\UserQuest;
use Illuminate\Http\Request;

class QuestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $quests = Quest::where('is_active', true)->get();

        $result = [
            'daily' => [],
            'weekly' => []
        ];

        foreach ($quests as $quest) {

            $periodStart = $quest->period === 'daily'
                ? now()->startOfDay()
                : now()->startOfWeek();

            $userQuest = UserQuest::where('user_id', $user->id)
                ->where('quest_id', $quest->id)
                ->where('period_start', $periodStart)
                ->first();

            $progress = $userQuest->progress ?? 0;
            $completedAt = $userQuest->completed_at ?? null;

            $data = [
                'id' => $quest->id,
                'title' => $quest->title,
                'description' => $quest->description,
                'type' => $quest->type,
                'progress' => $progress,
                'target' => $quest->target,
                'completed' => !is_null($completedAt),
                'completed_at' => $completedAt,
                'percentage' => $quest->target > 0
                    ? min(100, ($progress / $quest->target) * 100)
                    : 0,
            ];

            $result[$quest->period][] = $data;
        }

        return response()->json(['data' => $result]);
    }
}
