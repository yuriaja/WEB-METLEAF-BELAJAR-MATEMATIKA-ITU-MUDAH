<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration kelas MathLeaf, aman jika percobaan migration sebelumnya
 * sudah menambahkan sebagian kolom di MySQL.
 *
 * Perbaikan utama: jangan memanggil ->index() setelah ->constrained().
 * Gunakan nama foreign key eksplisit agar MySQL tidak mendapat nama "1".
 */
return new class extends Migration {
    public function up(): void
    {
        // MySQL dapat menyimpan kolom yang berhasil dibuat sebelum migration gagal.
        // Karena itu setiap kolom diperiksa sebelum ditambahkan.
        if (! Schema::hasColumn('materials', 'class_id')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->foreignId('class_id')->nullable()->after('author_id');
            });
        }

        if (! Schema::hasColumn('materials', 'file_path')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->string('file_path')->nullable();
            });
        }

        if (! Schema::hasColumn('materials', 'original_filename')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->string('original_filename')->nullable();
            });
        }

        if (! Schema::hasColumn('assessments', 'class_id')) {
            Schema::table('assessments', function (Blueprint $table) {
                $table->foreignId('class_id')->nullable();
            });
        }

        if (! Schema::hasColumn('assessments', 'author_id')) {
            Schema::table('assessments', function (Blueprint $table) {
                $table->foreignId('author_id')->nullable();
            });
        }

        // MySQL mendukung penambahan FOREIGN KEY melalui ALTER TABLE.
        // SQLite tidak mendukung ALTER TABLE ADD CONSTRAINT untuk tabel yang sudah ada.
        // Kolom tetap ditambahkan pada SQLite; FK perlu ditangani melalui migrasi
        // rebuild tabel terpisah jika ingin enforced di lingkungan SQLite.
        if (DB::getDriverName() === 'mysql') {
            $this->ensureForeignKey('materials', 'class_id', 'classes', 'materials_class_id_fk');
            $this->ensureForeignKey('assessments', 'class_id', 'classes', 'assessments_class_id_fk');
            $this->ensureForeignKey('assessments', 'author_id', 'users', 'assessments_author_id_fk');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            $this->dropForeignKeyIfExists('assessments', 'author_id');
            $this->dropForeignKeyIfExists('assessments', 'class_id');
            $this->dropForeignKeyIfExists('materials', 'class_id');
        }

        foreach (['author_id', 'class_id'] as $column) {
            if (Schema::hasColumn('assessments', $column)) {
                Schema::table('assessments', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        foreach (['class_id', 'file_path', 'original_filename'] as $column) {
            if (Schema::hasColumn('materials', $column)) {
                Schema::table('materials', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    private function ensureForeignKey(string $table, string $column, string $references, string $constraint): void
    {
        $existing = $this->foreignKeyName($table, $column);

        // Kode migration sebelumnya sempat menciptakan foreign key bernama `1`.
        // Ganti dengan nama eksplisit, tanpa menghapus kolom atau datanya.
        if ($existing === '1') {
            Schema::table($table, function (Blueprint $blueprint) use ($existing) {
                $blueprint->dropForeign($existing);
            });
            $existing = null;
        }

        if ($existing !== null) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column, $references, $constraint) {
            $blueprint->foreign($column, $constraint)
                ->references('id')
                ->on($references)
                ->nullOnDelete();
        });
    }

    private function dropForeignKeyIfExists(string $table, string $column): void
    {
        $name = $this->foreignKeyName($table, $column);

        if ($name !== null) {
            Schema::table($table, function (Blueprint $blueprint) use ($name) {
                $blueprint->dropForeign($name);
            });
        }
    }

    private function foreignKeyName(string $table, string $column): ?string
    {
        // Project pengguna memakai MySQL. Periksa constraint aktual karena
        // percobaan migration sebelumnya mungkin sudah menjalankan sebagian DDL.
        $record = DB::selectOne(
            'SELECT CONSTRAINT_NAME AS name
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL
             LIMIT 1',
            [$table, $column]
        );

        return $record?->name;
    }
};
