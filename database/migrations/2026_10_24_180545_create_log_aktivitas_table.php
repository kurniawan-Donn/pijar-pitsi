<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catatan aksi penting: siapa melakukan apa, terhadap data apa, dan kapan.
     */
    public function up(): void
    {
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            // Nama pengguna disalin supaya log tetap terbaca walau akun dihapus.
            $table->string('nama_pengguna')->nullable();
            $table->string('aksi', 50)->index();
            $table->nullableMorphs('subjek');
            $table->string('keterangan');
            $table->json('data')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    /**
     * Hapus tabel log aktivitas.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};
