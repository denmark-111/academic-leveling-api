<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['quiz_count', 'study_duration', 'quest_completion_count']); // what to track
            $table->integer('target'); // e.g. 3 quizzes OR 3600 seconds
            $table->enum('period', ['daily', 'weekly']);
            $table->enum('source_period', ['daily', 'weekly'])->nullable(); // for tracking progress across periods (e.g. daily quest completions counting as a progress towards a weekly meta quest)
            $table->boolean('is_active')->default(true);
            $table->integer('exp_reward')->default(0); // EXP reward for completing the quest
            $table->integer('coin_reward')->default(0); // Coin reward for completing the quest
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quests');
    }
};
