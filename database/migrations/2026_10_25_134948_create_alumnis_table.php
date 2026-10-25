<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Alumni magang yang ditampilkan di website publik.
 *
 * - Dari peserta (peserta_magang_id terisi): hanya tampil bila peserta
 *   bersedia (feedback) dan tampil = true; rating dari feedback no. 10.
 * - Manual (peserta_magang_id kosong): alumni sebelum sistem ini ada;
 *   rating diisi admin.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_magang_id')->nullable()->unique()->constrained('peserta_magangs')->nullOnDelete();
            $table->string('nama');
            $table->string('foto_path')->nullable();
            $table->string('universitas');
            $table->string('program_studi')->nullable();
            $table->string('divisi')->nullable();
            $table->string('periode', 100)->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->string('judul_project', 150);
            $table->text('deskripsi_project');
            $table->text('testimoni')->nullable();
            $table->unsignedInteger('urutan')->default(0)->index();
            $table->boolean('tampil')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnis');
    }
};
