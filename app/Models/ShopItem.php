<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopItem extends Model
{
    protected $table = 'shop_items';

    protected $fillable = [
        'name',
        'description',
        'price',
        'type',
        'effect',
        'icon',
        'is_consumable',
        'stock'
    ];

    protected $casts = [
        'price' => 'integer',
        'effect' => 'array',
        'is_consumable' => 'boolean',
        'stock' => 'integer',
    ];

    public function userItems()
    {
        return $this->hasMany(UserItem::class);
    }
}