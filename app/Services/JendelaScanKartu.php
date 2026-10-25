<?php

namespace App\Services;

use App\Models\PengaturanMagang;
use App\Models\PesertaMagang;
use App\Models\ScanKartuAbsensi;
use Carbon\CarbonImmutable;

/**
 * Kapan tombol scan kartu aktif, kartu bulan/sisi mana yang boleh di-scan,
 * dan sisa jatah scan peserta hari ini (diatur di Pengaturan Magang).
 *
 * Jendela yang melewati akhir bulan (mis. tanggal 28 s.d. 2) berarti: tanggal
 * 28–akhir bulan untuk kartu bulan ini, tanggal 1–2 untuk kartu bulan lalu.
 */
class JendelaScanKartu
{
    public function aktif(): bool
    {
        return PengaturanMagang::ambil()->scan_aktif;
    }

    /**
     * Kartu yang boleh di-scan peserta pada tanggal itu (hanya bulan yang beririsan dengan masa magang).
     *
     * @return array<int, array{sisi: string, tahun: int, bulan: int, label: string}>
     */
    public function pilihan(PesertaMagang $peserta, ?CarbonImmutable $hariIni = null): array
    {
        $hariIni ??= CarbonImmutable::today();

        if (! $this->aktif()) {
            return [];
        }

        $pilihan = [];

        foreach ($this->jendela() as [$sisi, $mulai, $selesai]) {
            $bulanKartu = $this->bulanKartu($hariIni, $mulai, $selesai);

            if ($bulanKartu && $this->bulanDalamMasaMagang($peserta, $bulanKartu)) {
                $pilihan[] = [
                    'sisi' => $sisi,
                    'tahun' => $bulanKartu->year,
                    'bulan' => $bulanKartu->month,
                    'label' => ScanKartuAbsensi::SISI[$sisi].' · '.$bulanKartu->translatedFormat('F Y'),
                ];
            }
        }

        return $pilihan;
    }

    public function bolehScan(PesertaMagang $peserta, string $sisi, int $tahun, int $bulan): bool
    {
        return collect($this->pilihan($peserta))
            ->contains(fn (array $p) => $p['sisi'] === $sisi && $p['tahun'] === $tahun && $p['bulan'] === $bulan);
    }

    /**
     * Sisa scan peserta hari ini (scan gagal ikut dihitung karena tetap memanggil AI).
     */
    public function sisaHariIni(PesertaMagang $peserta): int
    {
        $terpakai = ScanKartuAbsensi::where('peserta_magang_id', $peserta->id)
            ->where('created_at', '>=', CarbonImmutable::today())
            ->count();

        return max(0, PengaturanMagang::ambil()->scan_batas_harian - $terpakai);
    }

    /**
     * Tanggal terdekat jendela scan berikutnya dibuka (untuk keterangan tombol nonaktif).
     */
    public function bukaBerikutnya(?CarbonImmutable $hariIni = null): ?CarbonImmutable
    {
        $hariIni ??= CarbonImmutable::today();

        if (! $this->aktif()) {
            return null;
        }

        for ($i = 1; $i <= 62; $i++) {
            $tanggal = $hariIni->addDays($i);

            foreach ($this->jendela() as [, $mulai, $selesai]) {
                if ($this->bulanKartu($tanggal, $mulai, $selesai)) {
                    return $tanggal;
                }
            }
        }

        return null;
    }

    /**
     * Teks aturan jendela, mis. "Sisi 1–15: tanggal 14–17. Sisi 16–31: tanggal 28 s.d. 2 bulan berikutnya."
     */
    public function keterangan(): string
    {
        return collect($this->jendela())
            ->map(fn (array $j) => 'Sisi '.str_replace('-', '–', $j[0]).': tanggal '.($j[1] <= $j[2]
                ? $j[1].'–'.$j[2]
                : $j[1].' s.d. '.$j[2].' bulan berikutnya'))
            ->implode('. ').'.';
    }

    /**
     * @return array<int, array{string, int, int}> [sisi, tanggal mulai, tanggal selesai]
     */
    private function jendela(): array
    {
        $p = PengaturanMagang::ambil();

        return [
            [ScanKartuAbsensi::SISI_AWAL, $p->scan_jendela_1_mulai, $p->scan_jendela_1_selesai],
            [ScanKartuAbsensi::SISI_AKHIR, $p->scan_jendela_2_mulai, $p->scan_jendela_2_selesai],
        ];
    }

    /**
     * Bulan kartu yang di-scan bila tanggal berada di dalam jendela, atau null.
     */
    private function bulanKartu(CarbonImmutable $tanggal, int $mulai, int $selesai): ?CarbonImmutable
    {
        $hari = $tanggal->day;
        $bulanIni = $tanggal->startOfMonth();

        if ($mulai <= $selesai) {
            return $hari >= $mulai && $hari <= $selesai ? $bulanIni : null;
        }

        return match (true) {
            $hari >= $mulai => $bulanIni,
            $hari <= $selesai => $bulanIni->subMonthNoOverflow(),
            default => null,
        };
    }

    private function bulanDalamMasaMagang(PesertaMagang $peserta, CarbonImmutable $bulan): bool
    {
        if (! $peserta->periode_mulai || ! $peserta->periode_selesai) {
            return false;
        }

        return $bulan->startOfMonth()->lte($peserta->periode_selesai)
            && $bulan->endOfMonth()->gte($peserta->periode_mulai);
    }
}
