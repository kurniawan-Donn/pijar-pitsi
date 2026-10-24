<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tanggal logbook yang dibuka admin walau sudah melewati batas isi mundur.
     */
    public function up(): void
    {
        Schema::create('buka_kunci_logbooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_magang_id')->constrained('peserta_magangs')->cascadeOnDelete();
            $table->date('tanggal');
            $table->date('berlaku_sampai');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['peserta_magang_id', 'tanggal']);
        });
    }

    /**
     * Hapus tabel buka kunci logbook.
     */
    public function down(): void
    {
        Schema::dropIfExists('buka_kunci_logbooks');
    }
};
