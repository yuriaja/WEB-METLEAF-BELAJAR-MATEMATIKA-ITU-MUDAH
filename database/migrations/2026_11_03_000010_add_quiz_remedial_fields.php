<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('assessments', 'passing_score')) {
            Schema::table('assessments', function (Blueprint $table) {
                $table->unsignedTinyInteger('passing_score')->default(70);
            });
        }
        if (!Schema::hasColumn('questions', 'material_id')) {
            Schema::table('questions', function (Blueprint $table) {
                // Nullable agar soal lama tetap valid. Akses materi divalidasi di controller.
                $table->unsignedBigInteger('material_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('questions', 'material_id')) {
            Schema::table('questions', fn (Blueprint $table) => $table->dropColumn('material_id'));
        }
        if (Schema::hasColumn('assessments', 'passing_score')) {
            Schema::table('assessments', fn (Blueprint $table) => $table->dropColumn('passing_score'));
        }
    }
};
