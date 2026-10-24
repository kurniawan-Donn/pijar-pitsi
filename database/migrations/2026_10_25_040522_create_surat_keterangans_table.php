<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Surat keterangan magang (sheet SKET. MAGANG). Draft dibuat otomatis saat
     * peserta selesai; nomor diberikan saat diterbitkan dan tidak berubah
     * saat diterbitkan ulang. Isi surat disalin ke kolom data (snapshot).
     */
    public function up(): void
    {
        Schema::create('surat_keterangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_magang_id')->unique()->constrained('peserta_magangs')->cascadeOnDelete();
            $table->string('status', 10)->default('draft'); // draft | terbit
            $table->string('nomor_surat', 100)->nullable()->unique();
            $table->unsignedSmallInteger('tahun')->nullable();
            $table->unsignedInteger('urut')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->boolean('tampilkan_nilai')->default(false);
            $table->json('data')->nullable();
            $table->foreignId('diterbitkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diterbitkan_pada')->nullable();
            $table->timestamps();

            $table->unique(['tahun', 'urut']);
        });
    }

    /**
     * Hapus tabel surat keterangan.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keterangans');
    }
};
