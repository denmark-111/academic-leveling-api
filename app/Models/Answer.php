<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable =[
        'question_id',
        'choice_id',
        'answer_text',
        'is_correct',
        'correct_answer_snapshot'
    ];
    
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
