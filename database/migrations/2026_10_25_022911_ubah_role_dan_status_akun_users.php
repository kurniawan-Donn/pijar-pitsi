<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Role menjadi string (nilai valid dijaga aplikasi lewat User::ROLES)
     * supaya role baru "peserta" tidak perlu mengubah enum di tiap database.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('admin')->change();
            $table->boolean('wajib_ganti_password')->default(false);
            $table->boolean('aktif')->default(true);
        });
    }

    /**
     * Kembalikan role ke enum lama. Akun peserta dihapus lebih dulu
     * karena nilainya tidak ada di enum lama.
     */
    public function down(): void
    {
        DB::table('users')->where('role', 'peserta')->delete();

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['wajib_ganti_password', 'aktif']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'super_admin'])->default('admin')->change();
        });
    }
};
