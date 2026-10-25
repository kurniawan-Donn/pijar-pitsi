<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Isi jam logbook dari foto kartu absensi (Revisi 6):
     * - riwayat scan beserta foto kartu (disk privat) dan hasil yang sudah divalidasi;
     * - penanda sumber jam di logbook (manual/scan), nilai asli scan, dan isian di luar batas;
     * - pengaturan fitur scan (saklar, jendela tanggal, batas per hari).
     */
    public function up(): void
    {
        Schema::create('scan_kartu_absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_magang_id')->constrained('peserta_magangs')->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('bulan');
            $table->string('sisi', 5); // 1-15 | 16-31
            $table->string('foto_path');
            $table->string('status', 12)->default('dipratinjau'); // dipratinjau | disimpan
            $table->json('hasil')->nullable();
            $table->unsignedTinyInteger('jumlah_terbaca')->default(0);
            $table->unsignedTinyInteger('jumlah_disimpan')->default(0);
            $table->string('model', 80)->nullable();
            $table->unsignedInteger('token_masuk')->nullable();
            $table->unsignedInteger('token_keluar')->nullable();
            $table->timestamp('disimpan_pada')->nullable();
            $table->timestamps();

            $table->index(['peserta_magang_id', 'created_at']);
        });

        Schema::table('logbook_harians', function (Blueprint $table) {
            $table->string('sumber_jam', 10)->default('manual')->after('sumber'); // manual | scan
            $table->foreignId('scan_kartu_absensi_id')->nullable()->after('sumber_jam')->constrained('scan_kartu_absensis')->nullOnDelete();
            $table->json('jam_scan_asli')->nullable()->after('scan_kartu_absensi_id');
            $table->boolean('di_luar_batas_isi')->default(false)->after('jam_scan_asli');
        });

        Schema::table('pengaturan_magangs', function (Blueprint $table) {
            $table->boolean('scan_aktif')->default(true);
            $table->unsignedTinyInteger('scan_jendela_1_mulai')->default(14);
            $table->unsignedTinyInteger('scan_jendela_1_selesai')->default(17);
            $table->unsignedTinyInteger('scan_jendela_2_mulai')->default(28);
            $table->unsignedTinyInteger('scan_jendela_2_selesai')->default(2);
            $table->unsignedTinyInteger('scan_batas_harian')->default(5);
        });
    }

    /**
     * Kembalikan skema sebelum fitur scan kartu.
     */
    public function down(): void
    {
        Schema::table('pengaturan_magangs', function (Blueprint $table) {
            $table->dropColumn(['scan_aktif', 'scan_jendela_1_mulai', 'scan_jendela_1_selesai', 'scan_jendela_2_mulai', 'scan_jendela_2_selesai', 'scan_batas_harian']);
        });

        Schema::table('logbook_harians', function (Blueprint $table) {
            $table->dropConstrainedForeignId('scan_kartu_absensi_id');
            $table->dropColumn(['sumber_jam', 'jam_scan_asli', 'di_luar_batas_isi']);
        });

        Schema::dropIfExists('scan_kartu_absensis');
    }
};
