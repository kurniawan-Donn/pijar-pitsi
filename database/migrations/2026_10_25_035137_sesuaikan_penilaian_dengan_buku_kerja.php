<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Penilaian mengikuti sheet PENILAIAN MHS MAGANG:
     * - aspek masuk kelompok (bobot per kelompok, aspek dirata-rata sama rata);
     * - predikat berupa teks (tabel predikat_penilaians);
     * - kesimpulan: kekuatan, aspek perbaikan, langkah perbaikan.
     *
     * Aspek lama dinonaktifkan (tidak dihapus) supaya penilaian lama utuh;
     * nilai lama juga sudah tersimpan sebagai salinan di penilaian_details.
     */
    public function up(): void
    {
        Schema::table('aspek_penilaians', function (Blueprint $table) {
            $table->foreignId('kelompok_penilaian_id')->nullable()->constrained('kelompok_penilaians')->nullOnDelete();
        });

        Schema::table('penilaian_pesertas', function (Blueprint $table) {
            $table->string('predikat', 50)->change();
            $table->text('kekuatan')->nullable();
            $table->text('aspek_perbaikan')->nullable();
            $table->text('langkah_perbaikan')->nullable();
        });

        Schema::table('penilaian_details', function (Blueprint $table) {
            $table->string('kelompok', 100)->nullable();
            $table->unsignedTinyInteger('bobot_kelompok')->nullable();
        });

        DB::table('aspek_penilaians')->update(['aktif' => false]);

        $kelompok = DB::table('kelompok_penilaians')->pluck('id', 'nama');
        $sekarang = now();
        $urutan = (int) DB::table('aspek_penilaians')->max('urutan');

        $aspekBaru = [
            'Hard Skill' => [
                'Pemahaman terkait Profile dan Proses Bisnis perusahaan',
                'Pemahaman terkait Layanan Consulting & Assurance',
                'Kemampuan dalam melaksanakan tugas',
                'Kualitas Hasil Pekerjaan',
                'Kemampuan Analisis',
                'Kemampuan Problem Solving',
                'Penguasaan terhadap tools/Aplikasi',
            ],
            'Soft Skill' => [
                'Kedisiplinan',
                'Tanggung Jawab',
                'Kolaborasi Tim',
                'Manajemen Waktu',
                'Inisiatif',
                'Kejujuran',
                'Etika dan Sikap Profesional',
                'Komunikasi',
            ],
        ];

        foreach ($aspekBaru as $namaKelompok => $daftar) {
            foreach ($daftar as $nama) {
                DB::table('aspek_penilaians')->insert([
                    'nama' => $nama,
                    'bobot' => 0, // bobot kini per kelompok
                    'urutan' => ++$urutan,
                    'aktif' => true,
                    'kelompok_penilaian_id' => $kelompok[$namaKelompok],
                    'created_at' => $sekarang,
                    'updated_at' => $sekarang,
                ]);
            }
        }
    }

    /**
     * Kembalikan struktur lama (aspek baru dihapus, aspek lama diaktifkan).
     */
    public function down(): void
    {
        DB::table('aspek_penilaians')->whereNotNull('kelompok_penilaian_id')->delete();
        DB::table('aspek_penilaians')->update(['aktif' => true]);

        Schema::table('penilaian_details', function (Blueprint $table) {
            $table->dropColumn(['kelompok', 'bobot_kelompok']);
        });

        Schema::table('penilaian_pesertas', function (Blueprint $table) {
            $table->dropColumn(['kekuatan', 'aspek_perbaikan', 'langkah_perbaikan']);
        });

        Schema::table('aspek_penilaians', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kelompok_penilaian_id');
        });
    }
};
