<?php

namespace Tests\Unit;

use App\Services\RekapAbsensi;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RekapAbsensiTest extends TestCase
{
    /**
     * @return array<string, array{array<string, string>, string}>
     */
    public static function contohSesi(): array
    {
        return [
            'empat kolom' => [['pagi_masuk' => '08:00', 'pagi_keluar' => '12:00', 'siang_masuk' => '13:00', 'siang_keluar' => '16:30'], 'hari_penuh'],
            'masuk pagi pulang sore' => [['pagi_masuk' => '08:00', 'siang_keluar' => '16:30'], 'hari_penuh'],
            'pulang siang' => [['pagi_masuk' => '08:00', 'pagi_keluar' => '12:00'], 'setengah_hari'],
            'datang siang' => [['siang_masuk' => '13:00', 'siang_keluar' => '16:30'], 'setengah_hari'],
            'hanya masuk pagi' => [['pagi_masuk' => '08:00'], 'tidak_lengkap'],
            'hanya keluar sore' => [['siang_keluar' => '16:30'], 'tidak_lengkap'],
            'masuk siang tanpa keluar' => [['pagi_keluar' => '12:00', 'siang_masuk' => '13:00'], 'tidak_lengkap'],
        ];
    }

    /**
     * @param  array<string, string>  $row
     */
    #[DataProvider('contohSesi')]
    public function test_jenis_kehadiran(array $row, string $jenis): void
    {
        $this->assertSame($jenis, RekapAbsensi::jenisKehadiran($row));
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
