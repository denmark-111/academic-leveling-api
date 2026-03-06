<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionFactory> */
    use HasFactory;

    use SoftDeletes;

    protected static function booted()
    {
        // When a question is deleted, also delete its choices
        static::deleting(function ($question) {
            $question->choices()->delete();
        });
    }
    
    protected $fillable = [
        'quiz_id',
        'question_text',
        'type',
        'correct_answer',
        'points',
        'order',
    ];

    public function choices()
    {
        return $this->hasMany(Choice::class);
    }
}
