<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', [
                'quiz_count',
                'perfect_score',
                'study_duration',
                'quest_completed',
                'total_exp',
                'level_reached'
            ]);
            $table->integer('target_value');
            $table->integer('reward_exp')->default(0);
            $table->integer('reward_coins')->default(0);
            $table->string('icon')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('achievements');
    }
};