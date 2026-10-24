<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Riwayat tarif uang saku. Tarif yang dipakai untuk suatu tanggal adalah
     * baris dengan berlaku_mulai terbaru yang tidak melewati tanggal itu,
     * sehingga perubahan tarif tidak mengubah rekap periode yang sudah lewat.
     */
    public function up(): void
    {
        Schema::create('tarif_uang_sakus', function (Blueprint $table) {
            $table->id();
            $table->date('berlaku_mulai')->unique();
            $table->unsignedInteger('tarif_hari_penuh');
            $table->unsignedInteger('tarif_setengah_hari');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Tarif awal diambil dari .env (UANG_SAKU_*), berlaku untuk semua tanggal lama.
        DB::table('tarif_uang_sakus')->insert([
            'berlaku_mulai' => '2000-01-01',
            'tarif_hari_penuh' => (int) config('absensi.uang_saku.hari_penuh', 50000),
            'tarif_setengah_hari' => (int) config('absensi.uang_saku.setengah_hari', 25000),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Hapus tabel tarif uang saku.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarif_uang_sakus');
    }
};
