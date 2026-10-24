<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hari libur nasional & cuti bersama (Sabtu/Minggu tidak perlu dicatat).
     */
    public function up(): void
    {
        Schema::create('hari_liburs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel hari libur.
     */
    public function down(): void
    {
        Schema::dropIfExists('hari_liburs');
    }
};
