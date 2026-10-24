<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bukti kegiatan (opsional) per hari logbook, disimpan di disk privat.
     */
    public function up(): void
    {
        Schema::create('logbook_buktis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('logbook_harian_id')->constrained('logbook_harians')->cascadeOnDelete();
            $table->string('path');
            $table->string('nama_asli');
            $table->string('mime', 100);
            $table->unsignedInteger('ukuran');
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel bukti logbook.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_buktis');
    }
};
