<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->nullable()->constrained('classes')->cascadeOnDelete();
            $table->foreignId('material_id')->nullable()->constrained('materials')->nullOnDelete();
            $table->string('title');
            $table->unsignedTinyInteger('fpbl_stage'); // 1 mengamati, 2 eksplorasi, 3 investigasi, 4 presentasi
            $table->string('mode', 20)->default('asinkron');
            $table->text('instructions')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamps();
        });
        Schema::create('study_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->unique(['class_id', 'name']);
        });
        Schema::create('study_group_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_group_id')->constrained('study_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['study_group_id', 'user_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('study_group_user');
        Schema::dropIfExists('study_groups');
        Schema::dropIfExists('activities');
    }
};
