<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'score',
        'started_at',
        'completed_at',
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
