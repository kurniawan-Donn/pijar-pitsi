<?php

namespace App\Policies;

use App\Models\IzinPeserta;
use App\Models\User;

/**
 * Izin/sakit (beserta buktinya) hanya boleh dibuka pemiliknya dan admin.
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
}
