<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom profil perusahaan.
     */
    public function up(): void
    {
        Schema::table('perusahaans', function (Blueprint $table) {

            $table->string('nama')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            $table->string('nama_pembimbing')->nullable();
            $table->string('jabatan_pembimbing')->nullable();

            $table->text('deskripsi')->nullable();

        });
    }

    /**
     * Hapus kolom profil perusahaan.
     */
    public function down(): void
    {
        Schema::table('perusahaans', function (Blueprint $table) {

            $table->dropColumn([
                'nama',
                'alamat',
                'kota',
                'telepon',
                'email',
                'website',
                'nama_pembimbing',
                'jabatan_pembimbing',
                'deskripsi',
            ]);

        });
    }
};