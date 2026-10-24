<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kelompok aspek penilaian dengan bobot (%). Bawaan mengikuti sheet
     * PENILAIAN MHS MAGANG: Hard Skill 60%, Soft Skill 40%.
     */
    public function up(): void
    {
        Schema::create('kelompok_penilaians', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->unsignedTinyInteger('bobot');
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });

        DB::table('kelompok_penilaians')->insert([
            ['nama' => 'Hard Skill', 'bobot' => 60, 'urutan' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Soft Skill', 'bobot' => 40, 'urutan' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Hapus tabel kelompok penilaian.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelompok_penilaians');
    }
};
