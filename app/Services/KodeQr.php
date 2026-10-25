<?php

namespace App\Services;

use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Kode QR sebagai data URI PNG (dibuat di server, tanpa layanan luar) untuk disisipkan ke PDF.
 */
class KodeQr
{
    public function dataUri(string $isi): string
    {
        $options = new QROptions([
            'outputType' => QROutputInterface::GDIMAGE_PNG,
            'outputBase64' => true,
            'scale' => 4,
            'quietzoneSize' => 1,
        ]);

        return (new QRCode($options))->render($isi);
    }
}
