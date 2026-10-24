<?php

namespace App\Services;

use RuntimeException;

/**
 * Tab bulan yang diminta tidak ada di spreadsheet peserta.
 */
class TabBulanTidakDitemukanException extends RuntimeException
{
    /**
     * @param  array<int, string>  $kandidat  nama tab yang dicari
     * @param  array<int, string>  $tersedia  nama tab yang ada di spreadsheet
     */
    public function __construct(
        public readonly array $kandidat,
        public readonly array $tersedia,
    ) {
        parent::__construct(
            'Tab '.implode(' / ', $kandidat).' tidak ditemukan. Tab yang tersedia: '
            .($tersedia ? implode(', ', $tersedia) : '(tidak ada)')
        );
    }
}
