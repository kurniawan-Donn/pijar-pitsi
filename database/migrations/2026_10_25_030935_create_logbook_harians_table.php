<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Logbook harian peserta, format mengikuti sheet bulanan September
     * (Pagi/Siang masuk-keluar, Activity, Output, Lokasi, Action).
     */
    public function up(): void
    {
        Schema::create('logbook_harians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_magang_id')->constrained('peserta_magangs')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('kehadiran', 10); // hadir | izin | sakit | libur
            $table->time('pagi_masuk')->nullable();
            $table->time('pagi_keluar')->nullable();
            $table->time('siang_masuk')->nullable();
            $table->time('siang_keluar')->nullable();
            $table->text('activity')->nullable();
            $table->text('output')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('status_verifikasi', 20)->default('menunggu'); // menunggu | diterima | ditolak | review
            $table->text('catatan_verifikasi')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->foreignId('izin_peserta_id')->nullable()->constrained('izin_pesertas')->cascadeOnDelete();
            $table->string('sumber', 10)->default('aplikasi'); // aplikasi | impor
            $table->timestamps();

            $table->unique(['peserta_magang_id', 'tanggal']);
        });
    }

    /**
     * Hapus tabel logbook harian.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_harians');
    }
};
