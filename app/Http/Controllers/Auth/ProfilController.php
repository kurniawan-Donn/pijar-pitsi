<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

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
}
