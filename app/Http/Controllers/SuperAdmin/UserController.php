<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna.
     */
    public function index()
    {
        $users = User::orderBy('role', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view(
            'superadmin.pengguna.index',
            compact('users')
        );
    }

    /**
     * Menampilkan form tambah pengguna.
     */
    public function create()
    {
        return view('superadmin.pengguna.create');
    }

    /**
     * Menyimpan pengguna baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],

            'role' => [
                'required',
                Rule::in([
                    'admin',
                    'super_admin',
                ]),
            ],
        ]);

        User::create($validated);

        return redirect()
            ->route('superadmin.pengguna.index')
            ->with(
                'success',
                'Pengguna berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan form edit pengguna.
     */
    public function edit(User $user)
    {
        return view(
            'superadmin.pengguna.edit',
            compact('user')
        );
    }

    /**
     * Memperbarui data pengguna.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ],

            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],

            'role' => [
                'required',
                Rule::in([
                    'admin',
                    'super_admin',
                ]),
            ],
        ]);

        /*
        |--------------------------------------------------------------
        | Role akun sendiri dan Super Admin terakhir tidak boleh turun,
        | supaya sistem tidak pernah kehilangan Super Admin.
        |--------------------------------------------------------------
        */
        $turunDariSuperAdmin = $user->role === 'super_admin' && $validated['role'] !== 'super_admin';

        if ($turunDariSuperAdmin && $user->id === auth()->id()) {
            return back()->withInput()->with('error', 'Role akun yang sedang digunakan tidak dapat diturunkan.');
        }

        if ($turunDariSuperAdmin && $this->superAdminTerakhir($user)) {
            return back()->withInput()->with('error', 'Minimal harus ada satu Super Admin. Tambahkan Super Admin lain terlebih dahulu.');
        }

        /*
        |--------------------------------------------------------------
        | Password hanya diperbarui jika diisi.
        |--------------------------------------------------------------
        */
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $roleLama = $user->role;

        $user->update($validated);

        LogAktivitas::catat(
            'ubah_pengguna',
            $user,
            "Akun {$user->email} diperbarui",
            array_filter([
                'role' => $roleLama !== $user->role ? ['dari' => $roleLama, 'ke' => $user->role] : null,
                'password_diganti' => isset($validated['password']) ?: null,
            ])
        );

        return redirect()
            ->route('superadmin.pengguna.index')
            ->with(
                'success',
                'Data pengguna berhasil diperbarui.'
            );
    }

    /**
     * Menghapus pengguna.
     */
    public function destroy(User $user)
    {
        /*
        |--------------------------------------------------------------
        | Super Admin tidak boleh menghapus akun dirinya sendiri.
        |--------------------------------------------------------------
        */
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('superadmin.pengguna.index')
                ->with(
                    'error',
                    'Akun yang sedang digunakan tidak dapat dihapus.'
                );
        }

        if ($user->role === 'super_admin' && $this->superAdminTerakhir($user)) {
            return redirect()
                ->route('superadmin.pengguna.index')
                ->with('error', 'Super Admin terakhir tidak dapat dihapus.');
        }

        LogAktivitas::catat('hapus_pengguna', $user, "Akun {$user->email} ({$user->role}) dihapus");

        $user->delete();

        return redirect()
            ->route('superadmin.pengguna.index')
            ->with(
                'success',
                'Pengguna berhasil dihapus.'
            );
    }

    /**
     * Apakah pengguna ini satu-satunya Super Admin.
     */
    private function superAdminTerakhir(User $user): bool
    {
        return ! User::where('role', 'super_admin')->whereKeyNot($user->id)->exists();
    }
}
