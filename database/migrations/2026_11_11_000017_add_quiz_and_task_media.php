<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->text('resource_url')->nullable();
        });
        Schema::table('activities', function (Blueprint $table) {
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->text('resource_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('activities', fn (Blueprint $table) => $table->dropColumn([
            'attachment_path', 'attachment_name', 'resource_url',
        ]));
        Schema::table('questions', fn (Blueprint $table) => $table->dropColumn([
            'attachment_path', 'attachment_name', 'resource_url',
        ]));
    }
};
