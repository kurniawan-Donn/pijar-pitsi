<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Feedback peserta tentang program magang (sheet FEEDBACK MHS MAGANG).
     * Teks pertanyaan ada di config/feedback.php.
     */
    public function up(): void
    {
        Schema::create('feedback_pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_magang_id')->unique()->constrained('peserta_magangs')->cascadeOnDelete();
            $table->json('skor'); // {nomor pertanyaan: 1–5}
            $table->text('paling_disukai');
            $table->text('kendala');
            $table->text('perlu_diperbaiki');
            $table->text('saran');
            $table->string('rekomendasi', 20);
            $table->text('evaluasi_keseluruhan');
            $table->boolean('pernyataan')->default(false);
            $table->timestamp('dikirim_pada')->nullable();
            $table->unsignedSmallInteger('jumlah_kirim')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel feedback peserta.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_pesertas');
    }
};
