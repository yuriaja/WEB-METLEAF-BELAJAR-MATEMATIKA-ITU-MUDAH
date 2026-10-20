<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('assessment_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('attempt_number')->default(1);
            $table->decimal('score', 8, 2)->nullable();
            $table->string('status', 20)->default('in_progress');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['assessment_id', 'user_id', 'attempt_number'], 'attempts_unique');
        });
        Schema::create('assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_attempt_id')->constrained('assessment_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->text('answer_text')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('points_awarded', 8, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->unique(['assessment_attempt_id', 'question_id'], 'answers_unique');
        });
    }
    public function down(): void {
        Schema::dropIfExists('assessment_answers');
        Schema::dropIfExists('assessment_attempts');
    }
};
