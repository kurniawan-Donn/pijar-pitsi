<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\User;
use App\Services\FotoProfil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Profil & password milik pengguna yang sedang login.
 */
class ProfilController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profil.edit', ['user' => $request->user()]);
    }

    /**
     * Perbarui nama & email.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update($validated);

        return redirect()->route('profil.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Ganti password (wajib memasukkan password saat ini).
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::min(8)->letters()->numbers()],
        ], [
            'password.different' => 'Password baru harus berbeda dari password saat ini.',
        ]);

        $dariPasswordSementara = $request->user()->wajib_ganti_password;

        $request->user()->update([
            'password' => $validated['password'],
            'wajib_ganti_password' => false,
        ]);

        // Token sesi baru setelah password berubah.
        $request->session()->regenerate();

        LogAktivitas::catat('ubah_password', $request->user(), 'Mengganti password akun sendiri');

        // Setelah password sementara diganti, langsung lanjut ke dashboard.
        $tujuan = $dariPasswordSementara ? $request->user()->routeDashboard() : null;

        return redirect()
            ->route($tujuan ?? 'profil.edit')
            ->with('success', 'Password berhasil diganti.');
    }

    /**
     * Unggah / ganti foto profil. Untuk peserta, foto ini sekaligus pas foto
     * biodata dan boleh diganti kapan saja tanpa persetujuan ulang.
     */
    public function updateFoto(Request $request, FotoProfil $fotoProfil): RedirectResponse
    {
        $request->validate(['foto' => FotoProfil::aturan()], FotoProfil::pesan());

        $user = $request->user();
        $sudahAda = (bool) $user->foto_path;

        $fotoProfil->simpan($user, $request->file('foto'));

        LogAktivitas::catat('ganti_foto_profil', $user, $sudahAda ? 'Mengganti foto profil' : 'Mengunggah foto profil');

        return back()->with('success', 'Foto profil berhasil disimpan.');
    }

    public function hapusFoto(Request $request, FotoProfil $fotoProfil): RedirectResponse
    {
        $user = $request->user();

        if ($user->foto_path) {
            $fotoProfil->hapus($user);
            LogAktivitas::catat('hapus_foto_profil', $user, 'Menghapus foto profil');
        }

        return back()->with('success', 'Foto profil dihapus.');
    }

    /**
     * Tampilkan foto profil (pemilik, admin, super admin).
     */
    public function foto(User $user): StreamedResponse
    {
        Gate::authorize('lihat-foto-profil', $user);

        abort_unless($user->foto_path && Storage::disk('local')->exists($user->foto_path), 404);

        return Storage::disk('local')->response($user->foto_path, 'foto-profil.jpg', [
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }
}
