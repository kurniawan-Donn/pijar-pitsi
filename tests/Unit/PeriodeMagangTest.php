<?php

namespace Tests\Unit;

use App\Services\PeriodeMagang;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PeriodeMagangTest extends TestCase
{
    /**
     * Contoh dari buku kerja: sheet JULI = 28 Juni–27 Juli, AGUSTUS = 28 Juli–27 Agustus.
     *
     * @return array<string, array{string, string}>
     */
    public static function tanggalDanKunci(): array
    {
        return [
            '27 Juli masih periode Juli' => ['2026-07-27', '2026-07'],
            '28 Juli masuk periode Agustus' => ['2026-07-28', '2026-08'],
            '31 Juli masuk periode Agustus' => ['2026-07-31', '2026-08'],
            '1 Agustus periode Agustus' => ['2026-08-01', '2026-08'],
            '28 Desember masuk periode Januari tahun berikutnya' => ['2026-12-28', '2027-01'],
            '27 Februari periode Februari' => ['2027-02-27', '2027-02'],
            '28 Februari masuk periode Maret' => ['2027-02-28', '2027-03'],
        ];
    }

    #[DataProvider('tanggalDanKunci')]
    public function test_kunci_periode_untuk_tanggal(string $tanggal, string $kunci): void
    {
        $this->assertSame($kunci, (new PeriodeMagang)->kunciUntuk(CarbonImmutable::parse($tanggal)));
    }

    public function test_rentang_dan_label_periode(): void
    {
        $periode = new PeriodeMagang;

        $agustus = $periode->rentang('2026-08');
        $this->assertSame('2026-07-28', $agustus['mulai']->toDateString());
        $this->assertSame('2026-08-27', $agustus['selesai']->toDateString());

        $januari = $periode->rentang('2027-01');
        $this->assertSame('2026-12-28', $januari['mulai']->toDateString());
        $this->assertSame('2027-01-27', $januari['selesai']->toDateString());

        $this->assertSame('Agustus 2026', $periode->label('2026-08'));
    }

    public function test_tanggal_awal_1_berarti_bulan_kalender(): void
    {
        $periode = new PeriodeMagang(1);

        $this->assertSame('2026-07', $periode->kunciUntuk(CarbonImmutable::parse('2026-07-31')));

        $rentang = $periode->rentang('2027-02');
        $this->assertSame('2027-02-01', $rentang['mulai']->toDateString());
        $this->assertSame('2027-02-28', $rentang['selesai']->toDateString());
    }

    public function test_periode_selama_magang_dipotong_di_awal_dan_akhir(): void
    {
        // Magang 3 Juni – 30 November 2026 (seperti contoh buku kerja).
        $daftar = (new PeriodeMagang)->antara(CarbonImmutable::parse('2026-06-03'), CarbonImmutable::parse('2026-11-30'));

        $this->assertSame(
            ['2026-06', '2026-07', '2026-08', '2026-09', '2026-10', '2026-11', '2026-12'],
            array_column($daftar, 'kunci')
        );

        $this->assertSame('2026-06-03', $daftar[0]['mulai']->toDateString());
        $this->assertSame('2026-06-27', $daftar[0]['selesai']->toDateString());
        $this->assertSame('2026-06-28', $daftar[1]['mulai']->toDateString());
        $this->assertSame('2026-07-27', $daftar[1]['selesai']->toDateString());
        $this->assertSame('2026-11-28', $daftar[6]['mulai']->toDateString());
        $this->assertSame('2026-11-30', $daftar[6]['selesai']->toDateString());
    }

    public function test_rentang_terbalik_kosong(): void
    {
        $this->assertSame([], (new PeriodeMagang)->antara(CarbonImmutable::parse('2026-08-10'), CarbonImmutable::parse('2026-08-01')));
    }

    public function test_tanggal_awal_tidak_valid_ditolak(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PeriodeMagang(29);
    }
}
