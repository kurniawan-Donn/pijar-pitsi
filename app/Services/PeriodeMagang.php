<?php

namespace App\Services;

use App\Models\PesertaMagang;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use InvalidArgumentException;

/**
 * Periode bulanan magang (bukan bulan kalender).
 *
 * Dengan tanggal awal 28, periode berjalan dari tanggal 28 sampai tanggal 27
 * bulan berikutnya dan dinamai menurut bulan tanggal akhirnya, mis.
 * 28 Juli – 27 Agustus 2026 = periode "Agustus 2026" (kunci "2026-08").
 * Tanggal awal 1 berarti periode = bulan kalender.
 */
class PeriodeMagang
{
    public function __construct(private int $tanggalAwal = 28)
    {
        if ($tanggalAwal < 1 || $tanggalAwal > 28) {
            throw new InvalidArgumentException('Tanggal awal periode harus 1–28.');
        }
    }

    public function tanggalAwal(): int
    {
        return $this->tanggalAwal;
    }

    /**
     * Kunci periode (Y-m) untuk suatu tanggal.
     */
    public function kunciUntuk(DateTimeInterface $tanggal): string
    {
        $tanggal = CarbonImmutable::instance($tanggal);

        if ($this->tanggalAwal > 1 && $tanggal->day >= $this->tanggalAwal) {
            $tanggal = $tanggal->startOfMonth()->addMonth();
        }

        return $tanggal->format('Y-m');
    }

    /**
     * Tanggal mulai & selesai satu periode penuh.
     *
     * @return array{mulai: CarbonImmutable, selesai: CarbonImmutable}
     */
    public function rentang(string $kunci): array
    {
        $bulan = $this->bulanDariKunci($kunci);

        if ($this->tanggalAwal === 1) {
            return ['mulai' => $bulan->startOfMonth(), 'selesai' => $bulan->endOfMonth()->startOfDay()];
        }

        return [
            'mulai' => $bulan->subMonth()->setDay($this->tanggalAwal),
            'selesai' => $bulan->setDay($this->tanggalAwal - 1),
        ];
    }

    /**
     * Nama periode, mis. "Agustus 2026".
     */
    public function label(string $kunci): string
    {
        return $this->bulanDariKunci($kunci)->locale('id')->translatedFormat('F Y');
    }

    /**
     * Semua periode selama masa magang peserta. Periode pertama dimulai dari
     * tanggal mulai magang dan periode terakhir berhenti di tanggal selesai.
     *
     * @return array<int, array{kunci: string, label: string, mulai: CarbonImmutable, selesai: CarbonImmutable}>
     */
    public function untukPeserta(PesertaMagang $peserta): array
    {
        if (! $peserta->periode_mulai || ! $peserta->periode_selesai) {
            return [];
        }

        return $this->antara($peserta->periode_mulai, $peserta->periode_selesai);
    }

    /**
     * Periode-periode yang mencakup rentang tanggal, dipotong di kedua ujung.
     *
     * @return array<int, array{kunci: string, label: string, mulai: CarbonImmutable, selesai: CarbonImmutable}>
     */
    public function antara(DateTimeInterface $mulai, DateTimeInterface $selesai): array
    {
        $mulai = CarbonImmutable::instance($mulai)->startOfDay();
        $selesai = CarbonImmutable::instance($selesai)->startOfDay();

        if ($selesai->lt($mulai)) {
            return [];
        }

        $hasil = [];
        $kunci = $this->kunciUntuk($mulai);
        $kunciAkhir = $this->kunciUntuk($selesai);

        while ($kunci <= $kunciAkhir) {
            $rentang = $this->rentang($kunci);

            $hasil[] = [
                'kunci' => $kunci,
                'label' => $this->label($kunci),
                'mulai' => $rentang['mulai']->max($mulai),
                'selesai' => $rentang['selesai']->min($selesai),
            ];

            $kunci = $this->bulanDariKunci($kunci)->addMonth()->format('Y-m');
        }

        return $hasil;
    }

    private function bulanDariKunci(string $kunci): CarbonImmutable
    {
        if (! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $kunci)) {
            throw new InvalidArgumentException("Kunci periode tidak valid: {$kunci}");
        }

        return CarbonImmutable::createFromFormat('!Y-m', $kunci);
    }
}
