<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Aspek penilaian akhir peserta magang beserta bobotnya (%).
     */
    public function up(): void
    {
        Schema::create('aspek_penilaians', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->unsignedTinyInteger('bobot');
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Aspek bawaan (total bobot 100). Bisa diubah Super Admin.
        $sekarang = now();

        DB::table('aspek_penilaians')->insert(collect([
            ['Kedisiplinan', 20],
            ['Tanggung Jawab', 20],
            ['Kemampuan Teknis', 25],
            ['Kerja Sama', 15],
            ['Inisiatif', 10],
            ['Komunikasi', 10],
        ])->map(fn (array $aspek, int $index) => [
            'nama' => $aspek[0],
            'bobot' => $aspek[1],
            'urutan' => $index + 1,
            'aktif' => true,
            'created_at' => $sekarang,
            'updated_at' => $sekarang,
        ])->all());
    }

    /**
     * Hapus tabel aspek penilaian.
     */
    public function down(): void
    {
        Schema::dropIfExists('aspek_penilaians');
    }
};
