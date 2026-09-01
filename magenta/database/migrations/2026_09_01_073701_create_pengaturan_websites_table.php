<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel pengaturan website.
     */
    public function up(): void
    {
        Schema::create('pengaturan_websites', function (Blueprint $table) {
            $table->id();

            // Judul dan deskripsi bagian Tentang Sistem
            $table->string('tentang_judul')->nullable();
            $table->text('tentang_deskripsi')->nullable();

            // Kartu 1
            $table->string('tentang_card1_judul')->nullable();
            $table->text('tentang_card1_deskripsi')->nullable();

            // Kartu 2
            $table->string('tentang_card2_judul')->nullable();
            $table->text('tentang_card2_deskripsi')->nullable();

            // Kartu 3
            $table->string('tentang_card3_judul')->nullable();
            $table->text('tentang_card3_deskripsi')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Menghapus tabel pengaturan website.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_websites');
    }
};