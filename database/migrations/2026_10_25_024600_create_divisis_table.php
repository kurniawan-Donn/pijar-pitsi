<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Daftar divisi tempat peserta ditempatkan.
     */
    public function up(): void
    {
        Schema::create('divisis', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel divisi.
     */
    public function down(): void
    {
        Schema::dropIfExists('divisis');
    }
};
