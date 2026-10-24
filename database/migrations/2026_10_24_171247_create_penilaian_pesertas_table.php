<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Penilaian akhir, satu per peserta magang.
     */
    public function up(): void
    {
        Schema::create('penilaian_pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_magang_id')->unique()->constrained('peserta_magangs')->cascadeOnDelete();
            $table->decimal('nilai_akhir', 5, 2);
            $table->char('predikat', 1);
            $table->text('catatan_pembimbing')->nullable();
            $table->foreignId('dinilai_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dinilai_pada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel penilaian peserta.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_pesertas');
    }
};
