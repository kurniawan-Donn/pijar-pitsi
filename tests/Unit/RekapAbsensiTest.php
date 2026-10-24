<?php

namespace Tests\Unit;

use App\Services\RekapAbsensi;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RekapAbsensiTest extends TestCase
{
    public function test_rekap_menghitung_hadir_izin_sakit_dan_belum_diisi(): void
    {
        $rows = [
            ['pagi_masuk' => '08:00', 'activity' => 'Membuat laporan'],
            ['pagi_masuk' => '', 'siang_keluar' => '16.00', 'activity' => ''],
            ['activity' => 'Izin acara keluarga'],
            ['activity' => 'IJIN kampus'],
            ['activity' => 'Sakit demam', 'pagi_masuk' => '08:00'],
            ['tanggal' => '2026-10-20', 'activity' => ''],
        ];

        $this->assertSame([
            'tercatat' => 6,
            'hadir' => 2,
            'izin' => 2,
            'sakit' => 1,
            'belum_diisi' => 1,
        ], RekapAbsensi::hitung($rows));
    }

    public function test_rekap_kosong(): void
    {
        $this->assertSame(
            ['tercatat' => 0, 'hadir' => 0, 'izin' => 0, 'sakit' => 0, 'belum_diisi' => 0],
            RekapAbsensi::hitung([])
        );
    }

    /**
     * @return array<string, array{mixed, string}>
     */
    public static function contohJam(): array
    {
        return [
            'titik' => ['07.30', '07:30'],
            'satu digit' => ['7:05', '07:05'],
            'dengan detik' => ['08:00:00', '08:00'],
            'spasi' => ['  13:45 ', '13:45'],
            'kosong' => ['', ''],
            'null' => [null, ''],
            'jam tidak valid' => ['25:00', ''],
            'teks' => ['pagi', ''],
        ];
    }

    #[DataProvider('contohJam')]
    public function test_normalisasi_jam(mixed $masukan, string $hasil): void
    {
        $this->assertSame($hasil, RekapAbsensi::normalisasiJam($masukan));
    }
}
