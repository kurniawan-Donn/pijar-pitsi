<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nilai per aspek. Nama & bobot aspek disalin saat dinilai supaya
     * nilai lama tidak berubah bila aspek diubah/dihapus belakangan.
     */
    public function up(): void
    {
        Schema::create('penilaian_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_peserta_id')->constrained('penilaian_pesertas')->cascadeOnDelete();
            $table->foreignId('aspek_penilaian_id')->nullable()->constrained('aspek_penilaians')->nullOnDelete();
            $table->string('nama_aspek');
            $table->unsignedTinyInteger('bobot');
            $table->unsignedTinyInteger('nilai');
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel detail penilaian.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_details');
    }
};
