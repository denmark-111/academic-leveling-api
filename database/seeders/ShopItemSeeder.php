<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShopItem;

class ShopItemSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'name' => 'Hint Token',
                'description' => 'Reveals one correct answer',
                'price' => 50,
                'type' => 'powerup',
                'effect' => json_encode(['hint' => true]),
                'is_consumable' => true,
                'stock' => null,
            ],
            [
                'name' => 'Extra Time',
                'description' => 'Adds 30 seconds to quiz timer',
                'price' => 100,
                'type' => 'powerup',
                'effect' => json_encode(['extra_time' => 30]),
                'is_consumable' => true,
                'stock' => null,
            ],
            [
                'name' => 'Cosmic Avatar',
                'description' => 'Unlock a special avatar',
                'price' => 500,
                'type' => 'cosmetic',
                'effect' => json_encode(['avatar' => 'cosmic']),
                'is_consumable' => false,
                'stock' => 10,
            ],
        ];

        foreach ($items as $item) {
            ShopItem::create($item);
        }
    }
}