<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Melengkapi pengajuan magang:
     * - perusahaan_id boleh kosong (pendaftar melamar ke perusahaan pemilik website)
     * - dokumen_path untuk surat pengantar yang diunggah
     * - catatan_admin agar catatan admin tidak menimpa keterangan pendaftar
     */
    public function up(): void
    {
        Schema::table('pengajuan_magangs', function (Blueprint $table) {
            $table->foreignId('perusahaan_id')->nullable()->change();
            $table->string('dokumen_path')->nullable()->after('keterangan');
            $table->text('catatan_admin')->nullable()->after('dokumen_path');
        });
    }

    /**
     * Mengembalikan struktur tabel pengajuan magang.
     */
    public function down(): void
    {
        Schema::table('pengajuan_magangs', function (Blueprint $table) {
            $table->dropColumn(['dokumen_path', 'catatan_admin']);
        });
    }
};
