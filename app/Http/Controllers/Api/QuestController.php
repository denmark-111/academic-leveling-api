<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quest;
use App\Models\UserQuest;
use App\Services\CoinService;
use App\Services\ExperienceService;
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

            $data = [
                'id' => $quest->id,
                'title' => $quest->title,
                'description' => $quest->description,
                'type' => $quest->type,
                'progress' => $progress,
                'target' => $quest->target,
                'completed_at' => $userQuest->completed_at ?? null,
                'percentage' => $quest->target > 0
                    ? min(100, ($progress / $quest->target) * 100)
                    : 0,
                'rewards' => [
                    'exp' => $quest->exp_reward,
                    'coins' => $quest->coin_reward,
                    'claimed_at' => $userQuest->claimed_at ?? null,
                ],
            ];

            $result[$quest->period][] = $data;
        }

        return response()->json(['data' => $result]);
    }

    // Claim quest rewards
    public function claim(Request $request, $questId)
    {
        $user = $request->user();

        $quest = Quest::findOrFail($questId);

        $periodStart = $quest->period === 'daily'
            ? now()->startOfDay()
            : now()->startOfWeek();

        $userQuest = UserQuest::where('user_id', $user->id)
            ->where('quest_id', $quest->id)
            ->where('period_start', $periodStart)
            ->first();

        if (!$userQuest || !$userQuest->completed_at) {
            return response()->json(['message' => 'Quest not completed'], 400);
        }

        if ($userQuest->claimed_at) {
            return response()->json(['message' => 'Already claimed'], 400);
        }

        // Grant rewards
        app(ExperienceService::class)
            ->gainFromQuest($user->id, $quest);

        app(CoinService::class)
            ->gainFromQuest($user->id, $quest);

        // Mark as claimed
        $userQuest->claimed_at = now();
        $userQuest->save();

        return response()->json([
            'message' => 'Rewards claimed successfully',
            'data' => [
                'exp_gained' => $quest->exp_reward,
                'coins_gained' => $quest->coin_reward,
            ]
        ]);
    }
}
