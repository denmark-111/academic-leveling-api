<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    /** @use HasFactory<\Database\Factories\QuizFactory> */
    use HasFactory;

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'is_public',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
