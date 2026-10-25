<?php

namespace App\Services;

use RuntimeException;

/**
 * Foto kartu absensi gagal dibaca (AI tidak tersedia, kunci belum diatur, atau respons tidak sah).
 * Pesannya aman ditampilkan ke peserta.
 */
class GagalBacaKartuException extends RuntimeException {}
