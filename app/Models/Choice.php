<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    /** @use HasFactory<\Database\Factories\ChoiceFactory> */
    use HasFactory;

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    protected $fillable = [
        'question_id',
        'choice_text',
        'is_correct',
    ];
}
