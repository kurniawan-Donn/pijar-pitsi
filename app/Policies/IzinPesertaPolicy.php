<?php

namespace App\Policies;

use App\Models\IzinPeserta;
use App\Models\User;

/**
 * Izin/sakit (beserta buktinya) hanya boleh dibuka pemiliknya dan admin.
 * Yang boleh membatalkan hanya pemiliknya.
 */
class IzinPesertaPolicy
{
    public function view(User $user, IzinPeserta $izinPeserta): bool
    {
        if (! $user->isPeserta()) {
            return true;
        }

        return $user->pesertaMagang?->id === $izinPeserta->peserta_magang_id;
    }

    public function delete(User $user, IzinPeserta $izinPeserta): bool
    {
        return $user->isPeserta()
            && $user->pesertaMagang?->id === $izinPeserta->peserta_magang_id;
    }
}
