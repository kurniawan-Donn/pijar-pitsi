<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pengajuan izin / sakit untuk satu rentang tanggal dengan satu bukti
     * (mis. surat dokter). Setiap tanggal dalam rentang dibuatkan baris
     * logbook_harians dengan kehadiran izin/sakit.
     */
    public function up(): void
    {
        Schema::create('izin_pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_magang_id')->constrained('peserta_magangs')->cascadeOnDelete();
            $table->string('jenis', 10); // izin | sakit
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('keterangan');
            $table->string('bukti_path')->nullable();
            $table->string('bukti_nama')->nullable();
            $table->string('bukti_mime', 100)->nullable();
            $table->unsignedInteger('bukti_ukuran')->nullable();
            $table->string('status', 20)->default('menunggu'); // menunggu | diterima | ditolak
            $table->text('catatan_verifikasi')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel izin peserta.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_pesertas');
    }
};
