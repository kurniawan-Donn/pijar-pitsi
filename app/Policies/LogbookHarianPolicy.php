<?php

namespace App\Policies;

use App\Models\LogbookHarian;
use App\Models\User;

/**
 * Logbook (beserta buktinya) hanya boleh dibuka pemiliknya dan admin.
 */
class LogbookHarianPolicy
{
    public function view(User $user, LogbookHarian $logbookHarian): bool
    {
        if (! $user->isPeserta()) {
            return true;
        }

        return $user->pesertaMagang?->id === $logbookHarian->peserta_magang_id;
    }
}
