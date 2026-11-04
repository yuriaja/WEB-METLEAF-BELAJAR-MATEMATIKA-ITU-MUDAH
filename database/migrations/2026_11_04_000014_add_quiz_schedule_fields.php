<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('assessments', 'starts_at')) {
            Schema::table('assessments', fn (Blueprint $t) => $t->timestamp('starts_at')->nullable()->index());
        }
        if (!Schema::hasColumn('assessments', 'due_at')) {
            Schema::table('assessments', fn (Blueprint $t) => $t->timestamp('due_at')->nullable()->index());
        }
    }
    public function down(): void
    {
        if (Schema::hasColumn('assessments', 'due_at')) {
            Schema::table('assessments', fn (Blueprint $t) => $t->dropColumn('due_at'));
        }
        if (Schema::hasColumn('assessments', 'starts_at')) {
            Schema::table('assessments', fn (Blueprint $t) => $t->dropColumn('starts_at'));
        }
    }
};
