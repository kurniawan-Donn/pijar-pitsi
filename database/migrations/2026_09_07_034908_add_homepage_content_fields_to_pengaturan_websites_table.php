<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | INFORMASI TERBARU
        |--------------------------------------------------------------------------
        */

        if (!Schema::hasColumn('pengaturan_websites', 'informasi_terbaru_judul')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->string('informasi_terbaru_judul')
                    ->default('Informasi Terbaru');
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'informasi_terbaru_deskripsi')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->text('informasi_terbaru_deskripsi')
                    ->nullable();
            });
        }


        /*
        |--------------------------------------------------------------------------
        | PENGUMUMAN
        |--------------------------------------------------------------------------
        */

        if (!Schema::hasColumn('pengaturan_websites', 'pengumuman_judul')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->string('pengumuman_judul')
                    ->default('Pengumuman');
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'pengumuman_deskripsi')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->text('pengumuman_deskripsi')
                    ->nullable();
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'pengumuman_card1_judul')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->string('pengumuman_card1_judul')
                    ->default('Persiapkan Dokumen');
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'pengumuman_card1_deskripsi')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->text('pengumuman_card1_deskripsi')
                    ->nullable();
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'pengumuman_card2_judul')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->string('pengumuman_card2_judul')
                    ->default('Pendaftaran Online');
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'pengumuman_card2_deskripsi')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->text('pengumuman_card2_deskripsi')
                    ->nullable();
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'pengumuman_card3_judul')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->string('pengumuman_card3_judul')
                    ->default('Informasi Perusahaan');
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'pengumuman_card3_deskripsi')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->text('pengumuman_card3_deskripsi')
                    ->nullable();
            });
        }


        /*
        |--------------------------------------------------------------------------
        | PERSYARATAN MAGANG
        |--------------------------------------------------------------------------
        |
        | persyaratan_judul sudah dibuat oleh migration sebelumnya.
        |
        */

        if (!Schema::hasColumn('pengaturan_websites', 'persyaratan_deskripsi')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->text('persyaratan_deskripsi')
                    ->nullable();
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'persyaratan1_judul')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->string('persyaratan1_judul')
                    ->default('Surat Pengantar');
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'persyaratan1_deskripsi')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->text('persyaratan1_deskripsi')
                    ->nullable();
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'persyaratan2_judul')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->string('persyaratan2_judul')
                    ->default('Data Mahasiswa');
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'persyaratan2_deskripsi')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->text('persyaratan2_deskripsi')
                    ->nullable();
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'persyaratan3_judul')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->string('persyaratan3_judul')
                    ->default('Dokumen Pendukung');
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'persyaratan3_deskripsi')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->text('persyaratan3_deskripsi')
                    ->nullable();
            });
        }


        /*
        |--------------------------------------------------------------------------
        | ALUR MAGANG
        |--------------------------------------------------------------------------
        |
        | alur_judul sudah dibuat oleh migration sebelumnya.
        |
        */

        if (!Schema::hasColumn('pengaturan_websites', 'alur_deskripsi')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->text('alur_deskripsi')
                    ->nullable();
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'alur1_judul')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->string('alur1_judul')
                    ->default('Persiapkan');
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'alur1_deskripsi')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->text('alur1_deskripsi')
                    ->nullable();
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'alur2_judul')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->string('alur2_judul')
                    ->default('Daftar');
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'alur2_deskripsi')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->text('alur2_deskripsi')
                    ->nullable();
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'alur3_judul')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->string('alur3_judul')
                    ->default('Verifikasi');
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'alur3_deskripsi')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->text('alur3_deskripsi')
                    ->nullable();
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'alur4_judul')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->string('alur4_judul')
                    ->default('Pelaksanaan');
            });
        }

        if (!Schema::hasColumn('pengaturan_websites', 'alur4_deskripsi')) {
            Schema::table('pengaturan_websites', function (Blueprint $table) {
                $table->text('alur4_deskripsi')
                    ->nullable();
            });
        }
    }


    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Hapus hanya kolom yang memang ditambahkan oleh migration ini.
        |--------------------------------------------------------------------------
        */

        $columns = [
            'informasi_terbaru_judul',
            'informasi_terbaru_deskripsi',

            'pengumuman_judul',
            'pengumuman_deskripsi',

            'pengumuman_card1_judul',
            'pengumuman_card1_deskripsi',

            'pengumuman_card2_judul',
            'pengumuman_card2_deskripsi',

            'pengumuman_card3_judul',
            'pengumuman_card3_deskripsi',

            'persyaratan_deskripsi',

            'persyaratan1_judul',
            'persyaratan1_deskripsi',

            'persyaratan2_judul',
            'persyaratan2_deskripsi',

            'persyaratan3_judul',
            'persyaratan3_deskripsi',

            'alur_deskripsi',

            'alur1_judul',
            'alur1_deskripsi',

            'alur2_judul',
            'alur2_deskripsi',

            'alur3_judul',
            'alur3_deskripsi',

            'alur4_judul',
            'alur4_deskripsi',
        ];

        $existingColumns = [];

        foreach ($columns as $column) {
            if (Schema::hasColumn('pengaturan_websites', $column)) {
                $existingColumns[] = $column;
            }
        }

        if (!empty($existingColumns)) {
            Schema::table('pengaturan_websites', function (Blueprint $table) use ($existingColumns) {
                $table->dropColumn($existingColumns);
            });
        }
    }
};