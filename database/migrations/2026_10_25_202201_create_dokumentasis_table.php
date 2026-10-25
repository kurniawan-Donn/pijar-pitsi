<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Galeri dokumentasi kegiatan di beranda. Foto (besar + thumbnail) disimpan
     * di disk public karena memang untuk ditampilkan ke umum.
     */
    public function up(): void
    {
        Schema::create('dokumentasis', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 150);
            $table->string('keterangan', 300)->nullable();
            $table->date('tanggal_kegiatan')->nullable();
            $table->string('foto_path');
            $table->string('thumbnail_path');
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('tampil')->default(true);
            $table->timestamps();

            $table->index(['tampil', 'urutan']);
        });
    }

    /**
     * Hapus tabel dokumentasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumentasis');
    }
};
