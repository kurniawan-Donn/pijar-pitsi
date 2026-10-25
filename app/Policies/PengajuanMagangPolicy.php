<?php

namespace App\Policies;

use App\Models\PengajuanMagang;
use App\Models\User;

/**
 * Pengajuan magang beserta dokumennya hanya boleh dibuka admin dan super admin.
 *
 * Lapisan kedua setelah middleware role. Pendaftar membuka dokumennya sendiri
 * lewat signed URL dari halaman cek status, bukan lewat policy ini.
 */
class PengajuanMagangPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->admin($user);
    }

    public function view(User $user, PengajuanMagang $pengajuanMagang): bool
    {
        return $this->admin($user);
    }

    public function update(User $user, PengajuanMagang $pengajuanMagang): bool
    {
        return $this->admin($user);
    }

    public function delete(User $user, PengajuanMagang $pengajuanMagang): bool
    {
        return $this->admin($user);
    }

    private function admin(User $user): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN], true);
    }
}
