<?php

namespace Tests\Feature;

use App\Services\ParserSheetLogbook;
use DateTimeImmutable;
use Tests\TestCase;

/**
 * Parser sheet bulanan buku kerja lama (contoh baris diambil dari pola
 * buku kerja asli, dengan isi kegiatan disamarkan).
 */
class ParserSheetLogbookTest extends TestCase
{
    /**
     * Tab format lama seperti dibaca dari file .xlsx (tanggal & jam berupa objek).
     *
     * @return array<int, array<int, mixed>>
     */
    private function tabLama(): array
    {
        $jam = fn (string $hhmm) => new DateTimeImmutable("1899-12-30 {$hhmm}:00");
        $tgl = fn (string $tanggal) => new DateTimeImmutable($tanggal);

        return [
            ['', 'DAFTAR HADIR PESERTA MAGANG'],
            [],
            ['', 'Nama : Peserta', '', '', '', '', '', 'BULAN : AGUSTUS'],
            ['', 'No', 'Tanggal', 'Pagi', '', 'Siang', '', 'Keterangan ', 'Paraf'],
            ['', '', '', 'Masuk', 'Keluar', 'Masuk', 'Keluar'],
            ['', 1, $tgl('2026-07-28'), '07:56', '', '', $jam('16:56'), 'Menyusun laporan', 'v'],
            ['', 2, $tgl('2026-07-29'), '08.00', '', '', '16.30', "\u{2060}Mempelajari regulasi", ''],
            ['', 3, $tgl('2026-08-01'), '', '', '', '', 'Hari Sabtu', ''],
            ['', 4, $tgl('2026-08-04'), '??', '', '', $jam('16:48'), 'Belajar SOP', 'v'],
            ['', 5, $tgl('2026-08-05'), $tgl('2026-04-08'), '', '', '16.59', 'Jam diketik 08.04', ''],
            ['', 6, $tgl('2026-08-06'), '8;10', '', '', '16;48', 'Titik koma', ''],
            ['', 7, $tgl('2026-08-07'), '', '', '', '', 'Tidak hadir dikarenakan sakit', ''],
            ['', 8, $tgl('2026-08-10'), '', '', '', '', 'izin tidak masuk karena urusan kampus', ''],
            ['', 9, $tgl('2026-08-11'), '', '', '', '', 'Cuti Bersama Maulid Nabi', ''],
            ['', 10, $tgl('2026-08-12'), '08:00', '12:00', '', '', 'Pulang siang', 'v'],
            ['', 11, $tgl('2026-08-13'), '', '', '', '', '', ''],
            ['', 12],
            ['', 'Total Masuk Full', '', ': 20 hari'],
            ['', 13, $tgl('2026-08-20'), '08:00', '', '', '16:00', 'Setelah total, diabaikan', ''],
        ];
    }

    public function test_format_lama_dari_xlsx(): void
    {
        $hasil = (new ParserSheetLogbook)->parse($this->tabLama());
        $baris = collect($hasil['baris'])->keyBy('tanggal');

        $this->assertSame('lama', $hasil['format']);
        $this->assertCount(10, $baris);
        $this->assertFalse($baris->has('2026-08-13'));
        $this->assertFalse($baris->has('2026-08-20'));

        $this->assertSame(['hadir', '07:56', null, null, '16:56', 'diterima'], [
            $baris['2026-07-28']['kehadiran'], $baris['2026-07-28']['pagi_masuk'], $baris['2026-07-28']['pagi_keluar'],
            $baris['2026-07-28']['siang_masuk'], $baris['2026-07-28']['siang_keluar'], $baris['2026-07-28']['status_verifikasi'],
        ]);
        $this->assertSame('Mempelajari regulasi', $baris['2026-07-29']['activity']);
        $this->assertSame('menunggu', $baris['2026-07-29']['status_verifikasi']);

        $this->assertSame('libur', $baris['2026-08-01']['kehadiran']);
        $this->assertSame('diterima', $baris['2026-08-01']['status_verifikasi']);
        $this->assertSame('sakit', $baris['2026-08-07']['kehadiran']);
        $this->assertSame('izin', $baris['2026-08-10']['kehadiran']);
        $this->assertSame('libur', $baris['2026-08-11']['kehadiran']);

        // "??" dikosongkan, tanggal salah ketik ditafsirkan jam, titik koma diterima.
        $this->assertNull($baris['2026-08-04']['pagi_masuk']);
        $this->assertSame('08:04', $baris['2026-08-05']['pagi_masuk']);
        $this->assertSame('08:10', $baris['2026-08-06']['pagi_masuk']);
        $this->assertSame('16:48', $baris['2026-08-06']['siang_keluar']);
        $this->assertSame('12:00', $baris['2026-08-12']['pagi_keluar']);

        $peringatan = implode("\n", $hasil['peringatan']);
        $this->assertStringContainsString('04 Agt 2026: jam pagi masuk "??" tidak terbaca', $peringatan);
        $this->assertStringContainsString('04 Agt 2026: jam hadir tidak lengkap', $peringatan);
        $this->assertStringContainsString('05 Agt 2026: jam pagi masuk tersimpan sebagai tanggal, ditafsirkan 08:04', $peringatan);
    }

    public function test_format_baru_dari_google_sheets_nilai_mentah(): void
    {
        // Google Sheets UNFORMATTED_VALUE + SERIAL_NUMBER: tanggal = angka seri, jam = pecahan hari.
        $rows = [
            ['', 'DAILY AKTIVITY MAHASISWA MAGANG'],
            ['', 'Bulan', ': SEPTEMBER'],
            ['', 'No', 'Tanggal', 'Pagi', '', 'Siang', '', 'Activity', 'Output', 'Lokasi', 'Action'],
            ['', '', '', 'Masuk', 'Keluar', 'Masuk', 'Keluar'],
            ['', 1, 46262, '08.00', '', '', '16.30', 'Finalisasi mockup', '', 'Kantor Jogja', 'Diterima'],
            ['', 2, 46263, '', '', '', '', '', '', '', ' .......'],
            ['', 3, 46266, 0.3326388888888889, '', '', 0.6993055555555555, 'Prototype', 'Draft', 'Kantor Jogja', 'Review'],
            ['', 4, 46269, '', '', '', '', 'Izin istirahat setelah perjalanan', '', '', ' .......'],
            ['', 5, 46270, 46120, '', '', '16.30', 'Jam jadi tanggal', '', '', ''],
        ];

        $hasil = (new ParserSheetLogbook)->parse($rows);
        $baris = collect($hasil['baris'])->keyBy('tanggal');

        $this->assertSame('baru', $hasil['format']);
        $this->assertSame(['2026-08-28', '2026-09-01', '2026-09-04', '2026-09-05'], $baris->keys()->all());
        $this->assertSame('diterima', $baris['2026-08-28']['status_verifikasi']);
        $this->assertSame('Kantor Jogja', $baris['2026-08-28']['lokasi']);
        $this->assertSame(['07:59', '16:47', 'review', 'Draft'], [
            $baris['2026-09-01']['pagi_masuk'], $baris['2026-09-01']['siang_keluar'],
            $baris['2026-09-01']['status_verifikasi'], $baris['2026-09-01']['output'],
        ]);
        $this->assertSame('izin', $baris['2026-09-04']['kehadiran']);
        $this->assertSame('08:04', $baris['2026-09-05']['pagi_masuk']);
        $this->assertSame('menunggu', $baris['2026-09-05']['status_verifikasi']);
    }

    public function test_tab_bukan_sheet_bulanan_dikenali(): void
    {
        $hasil = (new ParserSheetLogbook)->parse([
            ['', 'PROFILE MAHASISWA MAGANG'],
            ['', '', '', 'Nama Lengkap', ':', 'Contoh'],
        ]);

        $this->assertNull($hasil['format']);
        $this->assertSame([], $hasil['baris']);
    }
}
