<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudySession extends Model
{
    protected $fillable = [
        'session_at',
        'duration',
    ];

    protected $casts = [
        'session_at' => 'datetime',
    ];
}
