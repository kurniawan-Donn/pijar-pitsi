<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel predikat nilai akhir (sheet PENILAIAN MHS MAGANG). Predikat
     * berlaku untuk nilai >= batas_bawah.
     */
    public function up(): void
    {
        Schema::create('predikat_penilaians', function (Blueprint $table) {
            $table->id();
            $table->decimal('batas_bawah', 5, 2)->unique();
            $table->string('nama', 50);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        $sekarang = now();

        DB::table('predikat_penilaians')->insert(array_map(fn (array $predikat) => [
            'batas_bawah' => $predikat[0],
            'nama' => $predikat[1],
            'keterangan' => $predikat[2],
            'created_at' => $sekarang,
            'updated_at' => $sekarang,
        ], [
            [0, 'Perlu Pembinaan', 'Belum memenuhi kompetensi yang diharapkan sehingga memerlukan pendampingan dan pengembangan lebih lanjut.'],
            [20.01, 'Cukup', 'Memenuhi kompetensi dasar yang diharapkan, namun masih memerlukan pembinaan dan peningkatan pada beberapa indikator penilaian.'],
            [40.01, 'Baik', 'Memiliki kompetensi yang cukup baik, mampu melaksanakan tugas sesuai ketentuan, namun masih memerlukan bimbingan pada beberapa aspek.'],
            [60.01, 'Memuaskan', 'Menunjukkan penguasaan kompetensi yang baik, mampu menyelesaikan pekerjaan sesuai standar perusahaan dengan arahan yang minimal.'],
            [80.01, 'Sangat Memuaskan', 'Menunjukkan penguasaan kompetensi yang sangat baik, memiliki etika kerja yang tinggi, mampu bekerja secara mandiri, dan memberikan hasil kerja yang melebihi harapan.'],
        ]));
    }

    /**
     * Hapus tabel predikat penilaian.
     */
    public function down(): void
    {
        Schema::dropIfExists('predikat_penilaians');
    }
};
