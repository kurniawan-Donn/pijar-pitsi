<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan judul bagian Persyaratan & Alur Magang.
     *
     * Kolom ini sudah dipakai di Kelola Konten dan halaman utama,
     * tetapi belum pernah dibuat oleh migration sebelumnya.
     * Guard hasColumn() menjaga database lama yang sudah memilikinya.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('pengaturan_websites', 'persyaratan_judul')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->string('persyaratan_judul')
                    ->default('Persyaratan Magang');
            });
        }

        if (! Schema::hasColumn('pengaturan_websites', 'alur_judul')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->string('alur_judul')
                    ->default('Alur Magang');
            });
        }
    }

    /**
     * Menghapus judul bagian Persyaratan & Alur Magang.
     */
    public function down(): void
    {
        $existingColumns = array_values(array_filter(
            ['persyaratan_judul', 'alur_judul'],
            fn (string $column) => Schema::hasColumn('pengaturan_websites', $column)
        ));

        if (! empty($existingColumns)) {
            Schema::table('pengaturan_websites', function (Blueprint $table) use ($existingColumns) {
                $table->dropColumn($existingColumns);
            });
        }
    }
};
