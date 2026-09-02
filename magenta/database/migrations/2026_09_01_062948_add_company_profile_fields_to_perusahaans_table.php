<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perusahaans', function (Blueprint $table) {
            if (!Schema::hasColumn('perusahaans', 'nama')) {
                $table->string('nama')->nullable();
            }

            if (!Schema::hasColumn('perusahaans', 'alamat')) {
                $table->text('alamat')->nullable();
            }

            if (!Schema::hasColumn('perusahaans', 'kota')) {
                $table->string('kota', 100)->nullable();
            }

            if (!Schema::hasColumn('perusahaans', 'telepon')) {
                $table->string('telepon', 50)->nullable();
            }

            if (!Schema::hasColumn('perusahaans', 'email')) {
                $table->string('email')->nullable();
            }

            if (!Schema::hasColumn('perusahaans', 'website')) {
                $table->string('website')->nullable();
            }

            if (!Schema::hasColumn('perusahaans', 'nama_pembimbing')) {
                $table->string('nama_pembimbing')->nullable();
            }

            if (!Schema::hasColumn('perusahaans', 'jabatan_pembimbing')) {
                $table->string('jabatan_pembimbing')->nullable();
            }

            if (!Schema::hasColumn('perusahaans', 'deskripsi')) {
                $table->text('deskripsi')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('perusahaans', function (Blueprint $table) {
            if (Schema::hasColumn('perusahaans', 'deskripsi')) {
                $table->dropColumn('deskripsi');
            }

            if (Schema::hasColumn('perusahaans', 'website')) {
                $table->dropColumn('website');
            }

            if (Schema::hasColumn('perusahaans', 'telepon')) {
                $table->dropColumn('telepon');
            }

            if (Schema::hasColumn('perusahaans', 'nama')) {
                $table->dropColumn('nama');
            }
        });
    }
};