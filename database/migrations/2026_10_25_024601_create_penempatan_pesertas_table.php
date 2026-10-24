<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Divisi & mentor peserta per periode (rotasi). Periode tanpa baris
     * memakai penempatan periode sebelumnya.
     */
    public function up(): void
    {
        Schema::create('penempatan_pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_magang_id')->constrained('peserta_magangs')->cascadeOnDelete();
            $table->char('periode', 7); // kunci periode Y-m (bulan tanggal akhir periode)
            $table->foreignId('divisi_id')->constrained('divisis')->restrictOnDelete();
            $table->string('mentor_nama')->nullable();
            $table->string('mentor_jabatan')->nullable();
            $table->timestamps();

            $table->unique(['peserta_magang_id', 'periode']);
        });
    }

    /**
     * Hapus tabel penempatan peserta.
     */
    public function down(): void
    {
        Schema::dropIfExists('penempatan_pesertas');
    }
};
