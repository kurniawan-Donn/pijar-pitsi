<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
                'string',
                'min:8',
                'confirmed',
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
                'string',
                'min:8',
                'confirmed',
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
        | Password hanya diperbarui jika diisi.
        |--------------------------------------------------------------
        */
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

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

        $user->delete();

        return redirect()
            ->route('superadmin.pengguna.index')
            ->with(
                'success',
                'Pengguna berhasil dihapus.'
            );
    }
}