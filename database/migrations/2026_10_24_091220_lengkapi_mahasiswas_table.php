<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Melengkapi data mahasiswa untuk form pendaftaran website:
     * - asal kampus (mahasiswa bisa berasal dari kampus mana pun)
     * - fakultas boleh kosong
     */
    public function up(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->string('asal_kampus')->nullable()->after('email');
            $table->string('fakultas')->nullable()->change();
        });
    }

    /**
     * Mengembalikan struktur tabel mahasiswa.
     */
    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropColumn('asal_kampus');
        });
    }
};
