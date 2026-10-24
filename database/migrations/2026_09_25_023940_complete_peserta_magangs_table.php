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
        Schema::table('peserta_magangs', function (Blueprint $table) {

            if (! Schema::hasColumn('peserta_magangs', 'nama')) {
                $table->string('nama')->nullable();
            }

            if (! Schema::hasColumn('peserta_magangs', 'nim')) {
                $table->string('nim')->nullable();
            }

            if (! Schema::hasColumn('peserta_magangs', 'asal_kampus')) {
                $table->string('asal_kampus')->nullable();
            }

            if (! Schema::hasColumn('peserta_magangs', 'program_studi')) {
                $table->string('program_studi')->nullable();
            }

            if (! Schema::hasColumn('peserta_magangs', 'periode_mulai')) {
                $table->date('periode_mulai')->nullable();
            }

            if (! Schema::hasColumn('peserta_magangs', 'periode_selesai')) {
                $table->date('periode_selesai')->nullable();
            }

            if (! Schema::hasColumn('peserta_magangs', 'pembimbing')) {
                $table->string('pembimbing')->nullable();
            }

            if (! Schema::hasColumn('peserta_magangs', 'status')) {
                $table->enum('status', [
                    'aktif',
                    'selesai',
                ])->default('aktif');
            }

            if (! Schema::hasColumn('peserta_magangs', 'google_sheet_url')) {
                $table->text('google_sheet_url')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta_magangs', function (Blueprint $table) {

            $columns = [
                'nama',
                'nim',
                'asal_kampus',
                'program_studi',
                'periode_mulai',
                'periode_selesai',
                'pembimbing',
                'status',
                'google_sheet_url',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('peserta_magangs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
