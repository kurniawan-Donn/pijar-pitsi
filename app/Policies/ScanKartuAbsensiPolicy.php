<?php

namespace App\Policies;

use App\Models\ScanKartuAbsensi;
use App\Models\User;

/**
 * Foto dan hasil scan kartu absensi hanya untuk pemiliknya, admin, dan super admin.
 */
class ScanKartuAbsensiPolicy
{
    public function view(User $user, ScanKartuAbsensi $scanKartuAbsensi): bool
    {
        if (! $user->isPeserta()) {
            return true;
        }

        return $user->pesertaMagang?->id === $scanKartuAbsensi->peserta_magang_id;
    }

    /**
     * Menyimpan hasil scan ke logbook: hanya pemilik, dan hanya sekali.
     */
    public function update(User $user, ScanKartuAbsensi $scanKartuAbsensi): bool
    {
        return $user->isPeserta()
            && $user->pesertaMagang?->id === $scanKartuAbsensi->peserta_magang_id
            && ! $scanKartuAbsensi->sudahDisimpan();
    }
}
