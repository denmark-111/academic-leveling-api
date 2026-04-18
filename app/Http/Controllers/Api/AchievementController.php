<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Services\CoinService;
use App\Services\ExperienceService;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $achievements = Achievement::all();

        $result = [];
        foreach ($achievements as $achievement) {
            $userAchievement = UserAchievement::firstOrCreate([
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
            ]);

            $result[] = [
                'id' => $achievement->id,
                'name' => $achievement->name,
                'description' => $achievement->description,
                'icon' => $achievement->icon,
                'target' => $achievement->target_value,
                'progress' => $userAchievement->progress,
                'completed_at' => $userAchievement->completed_at,
                'claimed_at' => $userAchievement->claimed_at,
                'rewards' => [
                    'exp' => $achievement->reward_exp,
                    'coins' => $achievement->reward_coins,
                ],
            ];
        }

        return response()->json(['data' => $result]);
    }

    public function claim(Request $request, $achievementId)
    {
        $user = $request->user();
        $achievement = Achievement::findOrFail($achievementId);

        $userAchievement = UserAchievement::where('user_id', $user->id)
            ->where('achievement_id', $achievementId)
            ->first();

        if (!$userAchievement || !$userAchievement->completed_at) {
            return response()->json(['message' => 'Achievement not completed'], 400);
        }

        if ($userAchievement->claimed_at) {
            return response()->json(['message' => 'Already claimed'], 400);
        }

        // Grant rewards
        app(ExperienceService::class)->gainExp($user->id, $achievement->reward_exp);
        app(CoinService::class)->addCoins($user->id, $achievement->reward_coins);

        $userAchievement->claimed_at = now();
        $userAchievement->save();

        return response()->json([
            'message' => 'Rewards claimed',
            'data' => [
                'exp_gained' => $achievement->reward_exp,
                'coins_gained' => $achievement->reward_coins,
            ]
        ]);
    }
}