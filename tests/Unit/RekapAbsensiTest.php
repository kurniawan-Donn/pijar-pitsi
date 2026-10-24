<?php

namespace Tests\Unit;

use App\Services\RekapAbsensi;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RekapAbsensiTest extends TestCase
{
    public function test_rekap_menghitung_status_dan_jenis_kehadiran(): void
    {
        $rows = [
            // Hari penuh: pagi & siang lengkap.
            ['pagi_masuk' => '08:00', 'pagi_keluar' => '12:00', 'siang_masuk' => '13:00', 'siang_keluar' => '16.00'],
            // Setengah hari: hanya pagi lengkap.
            ['pagi_masuk' => '08:00', 'pagi_keluar' => '12:00', 'activity' => 'Rapat'],
            // Setengah hari: hanya siang lengkap.
            ['siang_masuk' => '13:00', 'siang_keluar' => '16:00'],
            // Tidak lengkap: hanya jam masuk.
            ['pagi_masuk' => '08:00'],
            ['activity' => 'Izin acara keluarga'],
            ['activity' => 'IJIN kampus'],
            // Sakit tetap sakit walau ada jam.
            ['activity' => 'Sakit demam', 'pagi_masuk' => '08:00', 'pagi_keluar' => '09:00'],
            ['tanggal' => '2026-10-20', 'activity' => ''],
        ];

        $this->assertSame([
            'tercatat' => 8,
            'hadir' => 4,
            'izin' => 2,
            'sakit' => 1,
            'belum_diisi' => 1,
            'hari_penuh' => 1,
            'setengah_hari' => 2,
            'tidak_lengkap' => 1,
        ], RekapAbsensi::hitung($rows));
    }

    public function test_uang_saku_dari_hari_penuh_dan_setengah_hari(): void
    {
        $rekap = ['hari_penuh' => 10, 'setengah_hari' => 3];

        $this->assertSame(575000, RekapAbsensi::uangSaku($rekap, 50000, 25000));
    }

    public function test_rekap_kosong(): void
    {
        $rekap = RekapAbsensi::hitung([]);

        $this->assertSame(0, $rekap['tercatat']);
        $this->assertSame(0, RekapAbsensi::uangSaku($rekap, 50000, 25000));
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
