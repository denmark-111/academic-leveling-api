<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserItem extends Model
{
    protected $fillable = [
        'user_id',
        'item_id',
        'quantity',
        'purchased_at',
        'expires_at'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'purchased_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(ShopItem::class, 'item_id');
    }
}