<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Akun login peserta magang, dikelola admin dari halaman detail peserta.
 *
 * Password sementara hanya ditampilkan sekali (flash session) dan wajib
 * diganti peserta saat login pertama (middleware WajibGantiPassword).
 */
class AkunPesertaController extends Controller
{
    /**
     * Buat akun login untuk peserta.
     */
    public function store(Request $request, PesertaMagang $pesertaMagang): RedirectResponse
    {
        if ($pesertaMagang->user) {
            return $this->kembali($pesertaMagang)->with('akun_error', 'Peserta ini sudah memiliki akun login.');
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ], [], [
            'email' => 'Email login',
        ]);

        $password = $this->passwordSementara();

        $user = DB::transaction(function () use ($pesertaMagang, $validated, $password) {
            $user = User::create([
                'name' => $pesertaMagang->nama,
                'email' => $validated['email'],
                'password' => $password,
                'role' => User::ROLE_PESERTA,
                'wajib_ganti_password' => true,
                'aktif' => true,
            ]);

            $pesertaMagang->update(['user_id' => $user->id]);

            return $user;
        });

        LogAktivitas::catat('buat_akun_peserta', $pesertaMagang, "Akun login {$user->email} dibuat untuk peserta {$pesertaMagang->nama}");

        return $this->kembali($pesertaMagang)
            ->with('akun_status', 'Akun login berhasil dibuat.')
            ->with('password_sementara', $password);
    }

    /**
     * Buat password sementara baru (peserta lupa password).
     */
    public function resetPassword(PesertaMagang $pesertaMagang): RedirectResponse
    {
        $user = $pesertaMagang->user;

        if (! $user) {
            return $this->kembali($pesertaMagang)->with('akun_error', 'Peserta ini belum memiliki akun login.');
        }

        $password = $this->passwordSementara();

        $user->update([
            'password' => $password,
            'wajib_ganti_password' => true,
        ]);

        LogAktivitas::catat('reset_password_peserta', $pesertaMagang, "Password akun {$user->email} di-reset");

        return $this->kembali($pesertaMagang)
            ->with('akun_status', 'Password sementara baru berhasil dibuat.')
            ->with('password_sementara', $password);
    }

    /**
     * Aktifkan / nonaktifkan akun. Akun nonaktif langsung dikeluarkan
     * pada request berikutnya (middleware peserta.aktif).
     */
    public function updateStatus(PesertaMagang $pesertaMagang): RedirectResponse
    {
        $user = $pesertaMagang->user;

        if (! $user) {
            return $this->kembali($pesertaMagang)->with('akun_error', 'Peserta ini belum memiliki akun login.');
        }

        $user->update(['aktif' => ! $user->aktif]);

        $status = $user->aktif ? 'diaktifkan' : 'dinonaktifkan';

        LogAktivitas::catat('ubah_status_akun_peserta', $pesertaMagang, "Akun {$user->email} {$status}", [
            'aktif' => $user->aktif,
        ]);

        return $this->kembali($pesertaMagang)->with('akun_status', "Akun login berhasil {$status}.");
    }

    /**
     * Hapus akun login. Data peserta tetap ada.
     */
    public function destroy(PesertaMagang $pesertaMagang): RedirectResponse
    {
        $user = $pesertaMagang->user;

        if (! $user) {
            return $this->kembali($pesertaMagang)->with('akun_error', 'Peserta ini belum memiliki akun login.');
        }

        DB::transaction(function () use ($pesertaMagang, $user) {
            $pesertaMagang->update(['user_id' => null]);
            $user->delete();
        });

        LogAktivitas::catat('hapus_akun_peserta', $pesertaMagang, "Akun login {$user->email} milik peserta {$pesertaMagang->nama} dihapus");

        return $this->kembali($pesertaMagang)->with('akun_status', 'Akun login berhasil dihapus.');
    }

    /**
     * Password acak 10 karakter berisi huruf & angka (memenuhi aturan password aplikasi).
     */
    private function passwordSementara(): string
    {
        return Str::password(10, symbols: false);
    }

    private function kembali(PesertaMagang $pesertaMagang): RedirectResponse
    {
        return redirect()->to(route('superadmin.peserta-magang.show', $pesertaMagang).'#akun');
    }
}
