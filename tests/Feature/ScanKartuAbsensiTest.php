<?php

namespace Tests\Feature;

use App\Models\IzinPeserta;
use App\Models\LogAktivitas;
use App\Models\LogbookHarian;
use App\Models\PengaturanMagang;
use App\Models\PesertaMagang;
use App\Models\ScanKartuAbsensi;
use App\Models\User;
use App\Services\BacaKartuAbsensi;
use App\Services\JendelaScanKartu;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Isi jam logbook dari foto kartu absensi (Gemini di-mock).
 *
 * Hari ini 29 Oktober 2026 (Kamis): jendela sisi 16–31 aktif untuk kartu Oktober.
 * Batas isi mundur 7 hari → 22 Oktober ke atas masih bisa diisi biasa.
 */
class ScanKartuAbsensiTest extends TestCase
{
    use RefreshDatabase;

    private User $akun;

    private PesertaMagang $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-29 09:00:00');
        Storage::fake('local');
        config(['services.gemini.key' => 'kunci-uji', 'services.gemini.model' => 'gemini-2.5-flash']);

        $this->akun = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $this->peserta = PesertaMagang::create([
            'nama' => 'Dony Kurniawan',
            'status' => 'aktif',
            'periode_mulai' => '2026-08-03',
            'periode_selesai' => '2026-11-27',
            'user_id' => $this->akun->id,
        ]);
    }

    /**
     * Respons Gemini palsu berisi baris-baris bacaan.
     *
     * @param  array<int, array<string, mixed>>  $baris
     */
    private function fakeGemini(array $baris, int $status = 200): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response($status === 200 ? [
                'candidates' => [['content' => ['parts' => [['text' => json_encode(['baris' => $baris])]]]]],
                'usageMetadata' => ['promptTokenCount' => 1800, 'candidatesTokenCount' => 400, 'thoughtsTokenCount' => 600],
                'modelVersion' => 'gemini-2.5-flash',
            ] : ['error' => ['message' => 'gagal']], $status),
        ]);
    }

    /**
     * Bacaan sisi belakang foto contoh (16–23 Oktober 2026).
     *
     * @return array<int, array<string, mixed>>
     */
    private function bacaanBelakang(): array
    {
        return [
            ['tanggal' => 16, 'jam_masuk' => '7:54', 'jam_keluar' => '16:54', 'keyakinan' => 0.97],
            ['tanggal' => 19, 'jam_masuk' => '07:58', 'jam_keluar' => '16:37', 'keyakinan' => 0.95],
            ['tanggal' => 20, 'jam_masuk' => '07:58', 'jam_keluar' => '16:35', 'keyakinan' => 0.95],
            ['tanggal' => 21, 'jam_masuk' => '08:08', 'jam_keluar' => '16:37', 'keyakinan' => 0.9],
            ['tanggal' => 22, 'jam_masuk' => '07:47', 'jam_keluar' => '16:45', 'keyakinan' => 0.93],
            ['tanggal' => 23, 'jam_masuk' => '08:05', 'jam_keluar' => null, 'keyakinan' => 0.62, 'catatan' => 'Jam keluar tidak terlihat.'],
        ];
    }

    private function unggah(string $kartu = '16-31|2026|10'): TestResponse
    {
        return $this->actingAs($this->akun)->post(route('peserta.scan-kartu.store'), [
            'kartu' => $kartu,
            'foto' => UploadedFile::fake()->image('kartu.jpg', 900, 1200),
        ]);
    }

    private function scanTerakhir(): ScanKartuAbsensi
    {
        return ScanKartuAbsensi::latest('id')->firstOrFail();
    }

    private function logbook(string $tanggal): ?LogbookHarian
    {
        return LogbookHarian::where('peserta_magang_id', $this->peserta->id)->whereDate('tanggal', $tanggal)->first();
    }

    // -----------------------------------------------------------------
    // Validasi hasil scan
    // -----------------------------------------------------------------

    public function test_validasi_hasil_ai_di_server(): void
    {
        $scan = ScanKartuAbsensi::factory()->create(['peserta_magang_id' => $this->peserta->id, 'sisi' => ScanKartuAbsensi::SISI_AKHIR]);

        $hasil = collect(app(BacaKartuAbsensi::class)->validasi([
            ['tanggal' => 16, 'jam_masuk' => '7:54', 'jam_keluar' => '16.54', 'keyakinan' => 0.97],
            ['tanggal' => 5, 'jam_masuk' => '08:00', 'jam_keluar' => '16:00', 'keyakinan' => 0.99],   // bukan sisi ini
            ['tanggal' => 32, 'jam_masuk' => '08:00', 'jam_keluar' => '16:00', 'keyakinan' => 0.99],  // tidak ada
            ['tanggal' => 19, 'jam_masuk' => '16:37', 'jam_keluar' => '07:58', 'keyakinan' => 0.95], // terbalik
            ['tanggal' => 20, 'jam_masuk' => '25:10', 'jam_keluar' => '16:35', 'keyakinan' => 0.95], // jam tidak sah
            ['tanggal' => 21, 'jam_masuk' => '08:08', 'jam_keluar' => '16:37', 'keyakinan' => 0.5],  // ragu
            ['tanggal' => 22, 'jam_masuk' => '07:47', 'jam_keluar' => '16:45', 'keyakinan' => 0.7],
            ['tanggal' => 22, 'jam_masuk' => '07:48', 'jam_keluar' => '16:45', 'keyakinan' => 0.9],  // ganda
            ['tanggal' => 23, 'jam_masuk' => null, 'jam_keluar' => null, 'keyakinan' => 0.9],        // kosong
        ], $scan))->keyBy('tanggal');

        $this->assertSame([16, 19, 20, 21, 22], $hasil->keys()->all());

        $this->assertSame(['07:54', '16:54', false], [$hasil[16]['jam_masuk'], $hasil[16]['jam_keluar'], $hasil[16]['ragu']]);
        $this->assertTrue($hasil[19]['ragu']);
        $this->assertContains('Jam masuk tidak lebih awal dari jam keluar.', $hasil[19]['catatan']);
        $this->assertNull($hasil[20]['jam_masuk']);
        $this->assertTrue($hasil[20]['ragu']);
        $this->assertTrue($hasil[21]['ragu']);
        $this->assertSame('07:48', $hasil[22]['jam_masuk']);
        $this->assertTrue($hasil[22]['ragu']);
    }

    // -----------------------------------------------------------------
    // Jendela scan
    // -----------------------------------------------------------------

    /**
     * @return array<string, array{string, array<int, string>}>
     */
    public static function tanggalJendela(): array
    {
        return [
            '14 Okt: sisi depan Oktober' => ['2026-10-14', ['1-15|2026|10']],
            '17 Okt: sisi depan Oktober' => ['2026-10-17', ['1-15|2026|10']],
            '18 Okt: tutup' => ['2026-10-18', []],
            '28 Okt: sisi belakang Oktober' => ['2026-10-28', ['16-31|2026|10']],
            '2 Nov: sisi belakang Oktober' => ['2026-11-02', ['16-31|2026|10']],
            '3 Nov: tutup' => ['2026-11-03', []],
            '1 Agt: Juli di luar masa magang' => ['2026-08-01', []],
        ];
    }

    /**
     * @param  array<int, string>  $harapan
     */
    #[DataProvider('tanggalJendela')]
    public function test_jendela_scan_menentukan_kartu_yang_bisa_discan(string $tanggal, array $harapan): void
    {
        $pilihan = app(JendelaScanKartu::class)->pilihan($this->peserta, CarbonImmutable::parse($tanggal));

        $this->assertSame($harapan, array_map(fn ($p) => "{$p['sisi']}|{$p['tahun']}|{$p['bulan']}", $pilihan));
    }

    public function test_di_luar_jendela_tombol_nonaktif_dan_scan_ditolak_tanpa_memanggil_ai(): void
    {
        Carbon::setTestNow('2026-10-20 09:00:00');
        $this->fakeGemini($this->bacaanBelakang());

        $this->actingAs($this->akun)
            ->get(route('peserta.logbook.index'))
            ->assertSee('Bisa dipakai mulai 28 Oktober');

        $this->unggah('16-31|2026|10')->assertSessionHasErrors('kartu');

        Http::assertNothingSent();
        $this->assertSame(0, ScanKartuAbsensi::count());
    }

    public function test_saklar_mematikan_fitur_scan(): void
    {
        PengaturanMagang::ambil()->update(['scan_aktif' => false]);
        $this->fakeGemini($this->bacaanBelakang());

        $this->actingAs($this->akun)->get(route('peserta.logbook.index'))->assertDontSee('Isi dari kartu absensi');
        $this->actingAs($this->akun)->get(route('peserta.scan-kartu.create'))->assertSee('Fitur scan kartu sedang dinonaktifkan');
        $this->unggah()->assertSessionHasErrors('kartu');

        Http::assertNothingSent();
    }

    public function test_batas_scan_per_hari_termasuk_scan_gagal(): void
    {
        PengaturanMagang::ambil()->update(['scan_batas_harian' => 2]);
        $this->fakeGemini([], 500);

        $this->unggah()->assertSessionHasErrors('foto');
        $this->unggah()->assertSessionHasErrors('foto');
        $this->assertSame(2, ScanKartuAbsensi::where('status', ScanKartuAbsensi::GAGAL)->count());

        $this->fakeGemini($this->bacaanBelakang());
        $this->unggah()->assertSessionHasErrors(['foto' => 'Batas scan hari ini sudah habis. Coba lagi besok atau isi jam secara manual.']);
        $this->assertSame(2, ScanKartuAbsensi::count());

        // Besok jatah kembali.
        Carbon::setTestNow('2026-10-30 08:00:00');
        $this->unggah()->assertRedirect();
        $this->assertSame(3, ScanKartuAbsensi::count());
    }

    // -----------------------------------------------------------------
    // Alur scan → pratinjau → simpan
    // -----------------------------------------------------------------

    public function test_scan_mengirim_foto_dan_kalender_ke_ai_lalu_menampilkan_pratinjau(): void
    {
        $this->fakeGemini($this->bacaanBelakang());

        $this->unggah()->assertRedirect(route('peserta.scan-kartu.show', $this->scanTerakhir()));

        Http::assertSent(function ($request) {
            $teks = $request['contents'][0]['parts'][0]['text'];

            return $request->hasHeader('x-goog-api-key', 'kunci-uji')
                && str_contains($request->url(), 'models/gemini-2.5-flash:generateContent')
                && $request['generationConfig']['responseMimeType'] === 'application/json'
                && $request['contents'][0]['parts'][1]['inline_data']['mime_type'] === 'image/jpeg'
                && str_contains($teks, 'tanggal 16–31 Oktober 2026')
                && str_contains($teks, '- 17: Sabtu (libur: akhir pekan');
        });

        $scan = $this->scanTerakhir();
        $this->assertSame(ScanKartuAbsensi::DIPRATINJAU, $scan->status);
        $this->assertSame(6, $scan->jumlah_terbaca);
        $this->assertSame([1800, 1000], [$scan->token_masuk, $scan->token_keluar]);
        Storage::disk('local')->assertExists($scan->foto_path);

        // Hasil scan tidak pernah langsung tersimpan ke logbook.
        $this->assertSame(0, LogbookHarian::count());

        $this->actingAs($this->akun)
            ->get(route('peserta.scan-kartu.show', $scan))
            ->assertOk()
            ->assertSee('Simpan ke Logbook')
            ->assertSee('name="baris[16][jam_masuk]" value="07:54"', false)
            ->assertSee('Jam keluar tidak terlihat.')
            ->assertSee('Tanggal belum tiba.')
            ->assertSee('Di luar batas isi mundur');

        $log = LogAktivitas::where('aksi', 'scan_kartu_absensi')->sole();
        $this->assertSame(['sisi' => '16-31', 'bulan' => '2026-10', 'terbaca' => 6, 'berhasil' => true], $log->data);
    }

    public function test_simpan_mengisi_jam_dan_menandai_sumber_serta_perubahan(): void
    {
        $this->fakeGemini($this->bacaanBelakang());
        $this->unggah();
        $scan = $this->scanTerakhir();

        $this->actingAs($this->akun)
            ->put(route('peserta.scan-kartu.simpan', $scan), ['baris' => [
                16 => ['pakai' => '1', 'jam_masuk' => '07:54', 'jam_keluar' => '16:54'],
                22 => ['pakai' => '1', 'jam_masuk' => '07:47', 'jam_keluar' => '16:45'],
                23 => ['pakai' => '1', 'jam_masuk' => '08:05', 'jam_keluar' => '16:30'], // keluar diisi peserta
                20 => ['jam_masuk' => '07:58', 'jam_keluar' => '16:35'],                // tidak dicentang
            ]])
            ->assertRedirect()
            ->assertSessionHas('success');

        $tanggal16 = $this->logbook('2026-10-16');
        $this->assertSame(LogbookHarian::HADIR, $tanggal16->kehadiran);
        $this->assertSame(['07:54', '16:54'], [$tanggal16->jam('pagi_masuk'), $tanggal16->jam('siang_keluar')]);
        $this->assertTrue($tanggal16->dariScan());
        $this->assertTrue($tanggal16->di_luar_batas_isi);
        $this->assertFalse($tanggal16->diubahSetelahScan());
        $this->assertTrue($tanggal16->activityBelumDiisi());
        $this->assertSame(LogbookHarian::MENUNGGU, $tanggal16->status_verifikasi);
        $this->assertSame($scan->id, $tanggal16->scan_kartu_absensi_id);

        $this->assertFalse($this->logbook('2026-10-22')->di_luar_batas_isi);
        $this->assertTrue($this->logbook('2026-10-23')->diubahSetelahScan());
        $this->assertSame(['pagi_masuk' => '08:05', 'siang_keluar' => ''], $this->logbook('2026-10-23')->jam_scan_asli);
        $this->assertNull($this->logbook('2026-10-20'));

        $scan->refresh();
        $this->assertSame([ScanKartuAbsensi::DISIMPAN, 3], [$scan->status, $scan->jumlah_disimpan]);

        // Tidak bisa disimpan dua kali.
        $this->actingAs($this->akun)->put(route('peserta.scan-kartu.simpan', $scan), ['baris' => [19 => ['pakai' => '1', 'jam_masuk' => '07:58']]])->assertForbidden();

        $log = LogAktivitas::where('aksi', 'simpan_scan_kartu_absensi')->sole();
        $this->assertSame(3, $log->data['disimpan']);
        $this->assertSame(1, $log->data['diubah_peserta']);
        $this->assertSame(1, $log->data['di_luar_batas']);
    }

    public function test_tanggal_diterima_belum_tiba_dan_izin_tidak_bisa_diubah_scan(): void
    {
        $diterima = LogbookHarian::create(['peserta_magang_id' => $this->peserta->id, 'tanggal' => '2026-10-19', 'kehadiran' => 'hadir', 'pagi_masuk' => '08:00', 'siang_keluar' => '16:00', 'activity' => 'Rapat', 'status_verifikasi' => LogbookHarian::DITERIMA]);
        $izin = IzinPeserta::create(['peserta_magang_id' => $this->peserta->id, 'jenis' => 'sakit', 'tanggal_mulai' => '2026-10-21', 'tanggal_selesai' => '2026-10-21', 'keterangan' => 'Demam', 'status' => LogbookHarian::MENUNGGU]);
        LogbookHarian::create(['peserta_magang_id' => $this->peserta->id, 'tanggal' => '2026-10-21', 'kehadiran' => 'sakit', 'izin_peserta_id' => $izin->id]);

        $this->fakeGemini([...$this->bacaanBelakang(), ['tanggal' => 30, 'jam_masuk' => '08:00', 'jam_keluar' => '16:00', 'keyakinan' => 0.9]]);
        $this->unggah();
        $scan = $this->scanTerakhir();

        $this->actingAs($this->akun)
            ->get(route('peserta.scan-kartu.show', $scan))
            ->assertSee('Sudah diterima admin, tidak bisa diubah.')
            ->assertSee('Tanggal ini sudah tercatat sakit; tidak diubah.')
            ->assertDontSee('name="baris[19][jam_masuk]"', false)
            ->assertDontSee('name="baris[30][jam_masuk]"', false);

        // Dipaksa lewat request pun tetap tidak berubah.
        $this->actingAs($this->akun)->put(route('peserta.scan-kartu.simpan', $scan), ['baris' => [
            19 => ['pakai' => '1', 'jam_masuk' => '07:58', 'jam_keluar' => '16:37'],
            21 => ['pakai' => '1', 'jam_masuk' => '08:08', 'jam_keluar' => '16:37'],
            30 => ['pakai' => '1', 'jam_masuk' => '08:00', 'jam_keluar' => '16:00'],
            22 => ['pakai' => '1', 'jam_masuk' => '07:47', 'jam_keluar' => '16:45'],
        ]])->assertSessionHasNoErrors();

        $this->assertSame('08:00', $diterima->fresh()->jam('pagi_masuk'));
        $this->assertSame('sakit', $this->logbook('2026-10-21')->kehadiran);
        $this->assertNull($this->logbook('2026-10-30'));
        $this->assertTrue($this->logbook('2026-10-22')->dariScan());
    }

    public function test_tanggal_yang_sudah_ada_jam_meminta_pilihan_lama_atau_baru(): void
    {
        LogbookHarian::create(['peserta_magang_id' => $this->peserta->id, 'tanggal' => '2026-10-22', 'kehadiran' => 'hadir', 'pagi_masuk' => '08:15', 'siang_keluar' => '16:20', 'activity' => 'Audit', 'status_verifikasi' => LogbookHarian::DITOLAK]);

        $this->fakeGemini($this->bacaanBelakang());
        $this->unggah();
        $scan = $this->scanTerakhir();

        $this->actingAs($this->akun)
            ->get(route('peserta.scan-kartu.show', $scan))
            ->assertSee('Pakai jam lama (08:15 – 16:20)')
            ->assertSee('Pakai jam dari kartu');

        $isi = fn (?string $pilihan) => ['baris' => [22 => array_filter(['pakai' => '1', 'jam_masuk' => '07:47', 'jam_keluar' => '16:45', 'pilihan' => $pilihan])]];

        $this->actingAs($this->akun)->put(route('peserta.scan-kartu.simpan', $scan), $isi(null))->assertSessionHasErrors('baris.22.pilihan');

        $this->actingAs($this->akun)->put(route('peserta.scan-kartu.simpan', $scan), $isi('baru'))->assertSessionHasNoErrors();

        $logbook = $this->logbook('2026-10-22');
        $this->assertSame(['07:47', '16:45'], [$logbook->jam('pagi_masuk'), $logbook->jam('siang_keluar')]);
        $this->assertSame('Audit', $logbook->activity);
        $this->assertSame(LogbookHarian::MENUNGGU, $logbook->status_verifikasi);
    }

    public function test_jam_tidak_valid_di_pratinjau_ditolak(): void
    {
        $this->fakeGemini($this->bacaanBelakang());
        $this->unggah();

        $this->actingAs($this->akun)
            ->put(route('peserta.scan-kartu.simpan', $this->scanTerakhir()), ['baris' => [22 => ['pakai' => '1', 'jam_masuk' => '16:45', 'jam_keluar' => '07:47']]])
            ->assertSessionHasErrors('baris.22.jam_keluar');

        $this->actingAs($this->akun)
            ->put(route('peserta.scan-kartu.simpan', $this->scanTerakhir()), ['baris' => []])
            ->assertSessionHasErrors('baris');

        $this->assertSame(0, LogbookHarian::count());
    }

    public function test_ai_gagal_atau_kunci_belum_diatur_menampilkan_pesan_ramah(): void
    {
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['candidates' => [['content' => ['parts' => [['text' => 'bukan json']]]]]])]);
        config(['services.gemini.key' => null]);

        $this->unggah()->assertSessionHasErrors(['foto' => 'Fitur scan belum siap: kunci API Gemini belum diatur. Hubungi admin.']);
        Http::assertNothingSent();

        config(['services.gemini.key' => 'kunci-uji']);

        $this->unggah()->assertSessionHasErrors('foto');
        $this->assertSame(ScanKartuAbsensi::GAGAL, $this->scanTerakhir()->status);
        $this->assertTrue(LogAktivitas::where('aksi', 'scan_kartu_absensi')->where('data->berhasil', false)->exists());
    }

    // -----------------------------------------------------------------
    // Akses foto & sisi admin
    // -----------------------------------------------------------------

    public function test_foto_kartu_hanya_untuk_pemilik_dan_admin(): void
    {
        $this->fakeGemini($this->bacaanBelakang());
        $this->unggah();
        $scan = $this->scanTerakhir();

        $this->actingAs($this->akun)->get(route('scan-kartu.foto', $scan))->assertOk();
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]))->get(route('scan-kartu.foto', $scan))->assertOk();

        $lain = User::factory()->create(['role' => User::ROLE_PESERTA]);
        PesertaMagang::create(['nama' => 'Peserta Lain', 'status' => 'aktif', 'periode_mulai' => '2026-08-03', 'periode_selesai' => '2026-11-27', 'user_id' => $lain->id]);

        $this->actingAs($lain)->get(route('scan-kartu.foto', $scan))->assertForbidden();
        $this->actingAs($lain)->get(route('peserta.scan-kartu.show', $scan))->assertForbidden();
        $this->actingAs($lain)->put(route('peserta.scan-kartu.simpan', $scan), ['baris' => [22 => ['pakai' => '1', 'jam_masuk' => '07:47']]])->assertForbidden();
    }

    public function test_admin_melihat_penanda_dan_tidak_bisa_menerima_logbook_tanpa_activity(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->fakeGemini($this->bacaanBelakang());
        $this->unggah();
        $scan = $this->scanTerakhir();
        $this->actingAs($this->akun)->put(route('peserta.scan-kartu.simpan', $scan), ['baris' => [
            16 => ['pakai' => '1', 'jam_masuk' => '07:54', 'jam_keluar' => '16:54'],
            22 => ['pakai' => '1', 'jam_masuk' => '07:50', 'jam_keluar' => '16:45'],
        ]]);

        $this->actingAs($admin)
            ->get(route('superadmin.peserta-magang.logbook-harian', [$this->peserta, 'periode' => '2026-10']))
            ->assertOk()
            ->assertSee('Scan kartu')
            ->assertSee('Diubah setelah scan (asli 07:47–16:45)')
            ->assertSee('Di luar batas isi mundur')
            ->assertSee('Activity belum diisi')
            ->assertSee(route('superadmin.scan-kartu.show', $scan), false);

        $this->actingAs($admin)
            ->get(route('superadmin.scan-kartu.show', $scan))
            ->assertOk()
            ->assertSee(route('scan-kartu.foto', $scan), false)
            ->assertSee('diubah peserta');

        $logbook = $this->logbook('2026-10-22');

        $this->actingAs($admin)
            ->put(route('superadmin.peserta-magang.logbook-harian.verifikasi', $this->peserta), ['logbook' => [$logbook->id], 'status' => LogbookHarian::DITERIMA])
            ->assertSessionHas('warning', '1 hari tidak diterima karena activity belum diisi peserta.');

        $this->assertSame(LogbookHarian::MENUNGGU, $logbook->fresh()->status_verifikasi);
    }

    public function test_peserta_bisa_melengkapi_activity_logbook_scan_di_luar_batas(): void
    {
        $this->fakeGemini($this->bacaanBelakang());
        $this->unggah();
        $this->actingAs($this->akun)->put(route('peserta.scan-kartu.simpan', $this->scanTerakhir()), ['baris' => [16 => ['pakai' => '1', 'jam_masuk' => '07:54', 'jam_keluar' => '16:54']]]);

        $this->actingAs($this->akun)
            ->get(route('peserta.dashboard'))
            ->assertSee('1 hari sudah ada jamnya tetapi activity belum diisi');

        $this->actingAs($this->akun)
            ->get(route('peserta.logbook.edit', '2026-10-16'))
            ->assertOk()
            ->assertSee('Jam diisi dari')
            ->assertSee('lihat kartu');

        $this->actingAs($this->akun)
            ->put(route('peserta.logbook.update', '2026-10-16'), [
                'kehadiran' => 'hadir',
                'pagi_masuk' => '07:54',
                'siang_keluar' => '16:54',
                'activity' => 'Merapikan berkas audit.',
            ])
            ->assertSessionHasNoErrors();

        $logbook = $this->logbook('2026-10-16');
        $this->assertSame('Merapikan berkas audit.', $logbook->activity);
        $this->assertTrue($logbook->dariScan());
        $this->assertFalse($logbook->diubahSetelahScan());

        // Tanggal lewat batas yang bukan dari scan tetap terkunci.
        $this->actingAs($this->akun)->get(route('peserta.logbook.edit', '2026-10-15'))->assertRedirect();
    }

    public function test_pengaturan_scan_bisa_diubah_admin(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('superadmin.pengaturan-magang.index'))
            ->assertSee('Scan Kartu Absensi')
            ->assertSee('Simpan Scan Kartu');

        $this->actingAs($admin)
            ->put(route('superadmin.pengaturan-magang.scan'), [
                'scan_aktif' => '1',
                'scan_jendela_1_mulai' => 13,
                'scan_jendela_1_selesai' => 18,
                'scan_jendela_2_mulai' => 27,
                'scan_jendela_2_selesai' => 3,
                'scan_batas_harian' => 8,
            ])
            ->assertSessionHasNoErrors();

        $p = PengaturanMagang::ambil();
        $this->assertSame([13, 18, 27, 3, 8], [$p->scan_jendela_1_mulai, $p->scan_jendela_1_selesai, $p->scan_jendela_2_mulai, $p->scan_jendela_2_selesai, $p->scan_batas_harian]);
        $this->assertSame('Sisi 1–15: tanggal 13–18. Sisi 16–31: tanggal 27 s.d. 3 bulan berikutnya.', app(JendelaScanKartu::class)->keterangan());

        $this->actingAs($admin)
            ->put(route('superadmin.pengaturan-magang.scan'), ['scan_jendela_1_mulai' => 40, 'scan_batas_harian' => 0])
            ->assertSessionHasErrorsIn('scan', ['scan_jendela_1_mulai', 'scan_batas_harian']);
    }
}
