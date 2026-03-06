<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    /** @use HasFactory<\Database\Factories\QuizFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected static function booted()
    {
        // When a quiz is deleted, also delete its questions
        static::deleting(function ($quiz) {
            $quiz->questions->each->delete();
        });
    }

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

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
