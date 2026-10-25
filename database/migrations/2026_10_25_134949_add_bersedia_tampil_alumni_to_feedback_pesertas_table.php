<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Persetujuan peserta untuk ditampilkan sebagai alumni di website.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_pesertas', function (Blueprint $table) {
            $table->boolean('bersedia_tampil_alumni')->default(false)->after('pernyataan');
        });
    }

    public function down(): void
    {
        Schema::table('feedback_pesertas', function (Blueprint $table) {
            $table->dropColumn('bersedia_tampil_alumni');
        });
    }
};
