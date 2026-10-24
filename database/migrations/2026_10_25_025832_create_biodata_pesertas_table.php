<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Biodata peserta mengikuti sheet PROFIL MAHASISWA buku kerja magang,
     * ditambah data rekening uang saku. NIK, nomor rekening, dan riwayat
     * kesehatan disimpan terenkripsi (kolom text).
     */
    public function up(): void
    {
        Schema::create('biodata_pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_magang_id')->unique()->constrained('peserta_magangs')->cascadeOnDelete();

            // Data pribadi
            $table->string('nama_lengkap')->nullable();
            $table->string('nama_panggilan', 100)->nullable();
            $table->string('jenis_kelamin', 20)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('nik')->nullable();
            $table->text('alamat_ktp')->nullable();
            $table->text('alamat_domisili')->nullable();
            $table->string('tautan_maps_domisili', 500)->nullable();
            $table->string('no_hp', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('tautan_portofolio', 500)->nullable();
            $table->string('tautan_instagram', 500)->nullable();

            // Data akademik
            $table->string('perguruan_tinggi')->nullable();
            $table->string('fakultas_jurusan')->nullable();
            $table->string('nim', 50)->nullable();
            $table->unsignedTinyInteger('semester')->nullable();
            $table->decimal('ipk', 3, 2)->nullable();
            $table->string('dosen_pembimbing_nama')->nullable();
            $table->string('dosen_pembimbing_kontak', 30)->nullable();
            $table->string('dosen_pembimbing_email')->nullable();

            // Detail program (disiapkan admin)
            $table->string('posisi_dilamar')->nullable();
            $table->string('jenis_program', 30)->nullable();
            $table->string('jenis_program_lainnya')->nullable();
            $table->unsignedTinyInteger('durasi_bulan')->nullable();
            $table->string('mentor_magang')->nullable();

            // Keterampilan & kesehatan (daftar, bisa lebih dari satu baris)
            $table->json('keterampilan')->nullable();
            $table->json('bahasa')->nullable();
            $table->text('riwayat_kesehatan')->nullable();

            // Rekening uang saku
            $table->text('no_rekening')->nullable();
            $table->string('nama_bank', 100)->nullable();
            $table->string('atas_nama_rekening')->nullable();

            // Pernyataan kebenaran data
            $table->boolean('pernyataan_setuju')->default(false);
            $table->string('pernyataan_tempat', 100)->nullable();
            $table->date('pernyataan_tanggal')->nullable();

            // Alur tinjau
            $table->string('status', 20)->default('draft');
            $table->text('catatan_revisi')->nullable();
            $table->timestamp('diajukan_pada')->nullable();
            $table->foreignId('ditinjau_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ditinjau_pada')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Hapus tabel biodata peserta.
     */
    public function down(): void
    {
        Schema::dropIfExists('biodata_pesertas');
    }
};
