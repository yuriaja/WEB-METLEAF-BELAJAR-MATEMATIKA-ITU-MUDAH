<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('siswa')->index(); // siswa, guru, admin
            $table->string('student_number', 40)->nullable()->unique();
            $table->string('avatar_path')->nullable();
        });
    }
    public function down(): void {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['role', 'student_number', 'avatar_path']));
    }
};
