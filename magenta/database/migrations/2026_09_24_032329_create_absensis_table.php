<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('peserta_magang_id')
                ->constrained('peserta_magangs')
                ->cascadeOnDelete();

            $table->date('tanggal');

            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();

            $table->enum('status', [
                'hadir',
                'izin',
                'sakit',
                'alpa',
            ])->default('hadir');

            $table->text('keterangan')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Satu peserta hanya boleh memiliki satu absensi
            | untuk satu tanggal.
            |--------------------------------------------------------------------------
            */
            $table->unique([
                'peserta_magang_id',
                'tanggal',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};