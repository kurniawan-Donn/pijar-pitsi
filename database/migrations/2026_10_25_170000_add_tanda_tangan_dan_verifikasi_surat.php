<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Gambar tanda tangan (Mengetahui, Menyetujui) dan stempel di pengaturan magang,
     * serta kode verifikasi publik (QR) untuk surat keterangan. Surat yang sudah
     * terbit langsung diberi kode agar ikut bisa diverifikasi saat dicetak ulang.
     */
    public function up(): void
    {
        Schema::table('pengaturan_magangs', function (Blueprint $table) {
            $table->string('mengetahui_ttd_path')->nullable()->after('mengetahui_jabatan');
            $table->string('menyetujui_ttd_path')->nullable()->after('menyetujui_jabatan');
            $table->string('stempel_path')->nullable()->after('menyetujui_ttd_path');
        });

        Schema::table('surat_keterangans', function (Blueprint $table) {
            $table->string('kode_verifikasi', 40)->nullable()->unique()->after('nomor_surat');
        });

        DB::table('surat_keterangans')
            ->where('status', 'terbit')
            ->whereNull('kode_verifikasi')
            ->orderBy('id')
            ->pluck('id')
            ->each(fn (int $id) => DB::table('surat_keterangans')->where('id', $id)->update(['kode_verifikasi' => Str::random(32)]));
    }

    /**
     * Hapus kolom tanda tangan dan kode verifikasi.
     */
    public function down(): void
    {
        Schema::table('surat_keterangans', function (Blueprint $table) {
            $table->dropUnique(['kode_verifikasi']);
            $table->dropColumn('kode_verifikasi');
        });

        Schema::table('pengaturan_magangs', function (Blueprint $table) {
            $table->dropColumn(['mengetahui_ttd_path', 'menyetujui_ttd_path', 'stempel_path']);
        });
    }
};
