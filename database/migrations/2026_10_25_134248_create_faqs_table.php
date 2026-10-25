<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pertanyaan yang sering diajukan (FAQ) di website publik, dikelola admin.
 * Lima FAQ awal ikut dibuat agar halaman publik tidak kosong setelah deploy.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('pertanyaan');
            $table->text('jawaban');
            $table->string('kategori', 100)->nullable();
            $table->unsignedInteger('urutan')->default(0)->index();
            $table->boolean('tampil')->default(true);
            $table->timestamps();
        });

        $sekarang = now();

        DB::table('faqs')->insert(collect([
            ['Pendaftaran', 'Siapa yang bisa mendaftar magang?', 'Mahasiswa aktif D3/D4/S1 dari perguruan tinggi mana pun yang membawa surat pengantar resmi dari kampus.'],
            ['Pendaftaran', 'Bagaimana cara mengetahui hasil pendaftaran?', 'Buka bagian Cek Status di website ini, lalu masukkan kode pengajuan (contoh MG-000012) dan email yang dipakai saat mendaftar.'],
            ['Pendaftaran', 'Dokumen apa yang perlu disiapkan?', 'Surat pengantar dari kampus dalam format PDF, JPG, atau PNG dengan ukuran maksimal 2 MB.'],
            ['Selama Magang', 'Bagaimana peserta mengisi kehadiran dan kegiatan harian?', 'Setelah diterima, admin membuatkan akun login. Peserta mengisi logbook harian (jam kerja dan kegiatan) langsung di website.'],
            ['Selama Magang', 'Apakah peserta mendapat surat keterangan magang?', 'Ya. Surat keterangan diterbitkan setelah magang selesai, biodata disetujui, penilaian diisi, dan peserta mengirim feedback.'],
        ])->map(fn (array $faq, int $index) => [
            'kategori' => $faq[0],
            'pertanyaan' => $faq[1],
            'jawaban' => $faq[2],
            'urutan' => $index + 1,
            'tampil' => true,
            'created_at' => $sekarang,
            'updated_at' => $sekarang,
        ])->all());
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
