<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menghubungkan peserta magang dengan pengajuan asalnya
     * supaya satu pengajuan tidak dibuatkan peserta dua kali.
     */
    public function up(): void
    {
        Schema::table('peserta_magangs', function (Blueprint $table) {
            $table->foreignId('pengajuan_magang_id')
                ->nullable()
                ->unique()
                ->constrained('pengajuan_magangs')
                ->nullOnDelete();
        });
    }

    /**
     * Menghapus hubungan peserta dengan pengajuan.
     */
    public function down(): void
    {
        Schema::table('peserta_magangs', function (Blueprint $table) {
            $table->dropUnique(['pengajuan_magang_id']);
        });

        Schema::table('peserta_magangs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pengajuan_magang_id');
        });
    }
};
