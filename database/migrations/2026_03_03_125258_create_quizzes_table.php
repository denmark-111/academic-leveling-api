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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('quiz_code', 8)->unique()->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('subject')->nullable();
            $table->enum('grade_level', ['all','g7','g8','g9','g10','g11','g12','college'])->default('all');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            $table->enum('timer_mode', ['none', 'question', 'quiz'])->default('none');
            $table->boolean('is_question_shuffled')->default(false);
            $table->boolean('is_choices_shuffled')->default(false);
            $table->boolean('is_public')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
