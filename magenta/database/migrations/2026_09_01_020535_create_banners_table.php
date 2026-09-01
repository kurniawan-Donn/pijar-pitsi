<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();

            // Judul utama banner
            $table->string('judul');

            // Deskripsi / teks pendukung
            $table->text('deskripsi')->nullable();

            // Lokasi file gambar banner
            $table->string('gambar')->nullable();

            // Tulisan tombol
            $table->string('tombol_teks')->nullable();

            // Link tujuan tombol
            $table->string('tombol_link')->nullable();

            // Status banner
            $table->enum('status', ['aktif', 'nonaktif'])
                ->default('aktif');

            // Urutan tampilan banner
            $table->integer('urutan')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};