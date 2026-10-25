<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Peta lokasi kantor di bagian Kontak beranda. Bila tautan sematan kosong,
     * peta dibuat otomatis dari alamat.
     */
    public function up(): void
    {
        Schema::table('perusahaans', function (Blueprint $table) {
            $table->boolean('tampilkan_peta')->default(true)->after('deskripsi');
            $table->text('peta_embed_url')->nullable()->after('tampilkan_peta');
        });
    }

    /**
     * Hapus kolom peta lokasi.
     */
    public function down(): void
    {
        Schema::table('perusahaans', function (Blueprint $table) {
            $table->dropColumn(['tampilkan_peta', 'peta_embed_url']);
        });
    }
};
