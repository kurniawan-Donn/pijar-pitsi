<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Akun login peserta (satu peserta paling banyak satu akun) dan
     * nomor peserta format MG{tahun}{bulan}{urut 3 digit}, mis. MG202606020.
     */
    public function up(): void
    {
        Schema::table('peserta_magangs', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->unique()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('nomor_peserta', 20)->nullable()->unique();
        });

        // Isi nomor untuk peserta yang sudah ada, urut per bulan mulai magang.
        $urutPerBulan = [];

        DB::table('peserta_magangs')->orderBy('id')->get()->each(function (object $peserta) use (&$urutPerBulan) {
            $bulan = Carbon::parse($peserta->periode_mulai ?? $peserta->created_at ?? 'now')->format('Ym');
            $urutPerBulan[$bulan] = ($urutPerBulan[$bulan] ?? 0) + 1;

            DB::table('peserta_magangs')
                ->where('id', $peserta->id)
                ->update(['nomor_peserta' => sprintf('MG%s%03d', $bulan, $urutPerBulan[$bulan])]);
        });
    }

    /**
     * Hapus akun & nomor peserta.
     */
    public function down(): void
    {
        Schema::table('peserta_magangs', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
            $table->dropUnique(['nomor_peserta']);
        });

        Schema::table('peserta_magangs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('nomor_peserta');
        });
    }
};
