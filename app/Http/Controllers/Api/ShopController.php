<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShopItem;
use App\Models\UserItem;
use App\Services\CoinService;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $items = ShopItem::all();
        return response()->json(['data' => $items]);
    }

    public function buy(Request $request, $itemId)
    {
        $user = $request->user();
        $item = ShopItem::findOrFail($itemId);

        // Check stock
        if ($item->stock !== null && $item->stock <= 0) {
            return response()->json(['message' => 'Item out of stock'], 400);
        }

        // Check user coins
        if ($user->coins < $item->price) {
            return response()->json(['message' => 'Not enough coins'], 400);
        }

        // Deduct coins
        app(CoinService::class)->addCoins($user->id, -$item->price);

        // Add to user inventory
        $userItem = UserItem::firstOrNew([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        if ($userItem->exists) {
            $userItem->quantity += 1;
        } else {
            $userItem->quantity = 1;
            $userItem->purchased_at = now();
        }

        $userItem->save();

        // Decrease stock if finite
        if ($item->stock !== null) {
            $item->decrement('stock');
        }

        return response()->json([
            'message' => 'Purchase successful',
            'data' => [
                'item' => $item->name,
                'quantity' => $userItem->quantity,
                'remaining_coins' => $user->fresh()->coins,
            ]
        ]);
    }

    public function inventory(Request $request)
    {
        $user = $request->user();
        $inventory = $user->items()->with('item')->get();

        return response()->json(['data' => $inventory]);
    }

    public function useItem(Request $request, $userItemId)
    {
        $user = $request->user();
        $userItem = UserItem::where('user_id', $user->id)
            ->where('id', $userItemId)
            ->firstOrFail();

        if ($userItem->quantity <= 0) {
            return response()->json(['message' => 'No more of this item'], 400);
        }

        // Check expiration
        if ($userItem->expires_at && now()->gt($userItem->expires_at)) {
            return response()->json(['message' => 'Item expired'], 400);
        }

        // Decrement quantity
        $userItem->decrement('quantity');

        // If consumable and quantity becomes 0, delete the record
        if ($userItem->item->is_consumable && $userItem->quantity <= 0) {
            $userItem->delete();
        }

        // Return the effect so frontend can apply it
        return response()->json([
            'message' => 'Item used',
            'effect' => $userItem->item->effect,
        ]);
    }
}