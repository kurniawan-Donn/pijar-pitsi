<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pengaturan program magang (satu baris), diatur admin dari halaman
     * Pengaturan Magang.
     */
    public function up(): void
    {
        Schema::create('pengaturan_magangs', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('batas_isi_mundur_hari')->default(7);
            $table->unsignedTinyInteger('tanggal_awal_periode')->default(28);
            $table->string('kota_surat', 100)->nullable();
            $table->string('mengetahui_nama')->nullable();
            $table->string('mengetahui_jabatan')->nullable();
            $table->string('menyetujui_nama')->nullable();
            $table->string('menyetujui_jabatan')->nullable();
            $table->string('format_nomor_surat', 100)->default('{urut}/SKM/{bulan_romawi}/{tahun}');
            $table->timestamps();
        });

        DB::table('pengaturan_magangs')->insert([
            'batas_isi_mundur_hari' => 7,
            'tanggal_awal_periode' => 28,
            'mengetahui_jabatan' => 'Staff GA & HRD',
            'menyetujui_jabatan' => 'Manager GA & FA',
            'format_nomor_surat' => '{urut}/SKM/{bulan_romawi}/{tahun}',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Hapus tabel pengaturan magang.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_magangs');
    }
};
