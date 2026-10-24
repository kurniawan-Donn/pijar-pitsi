<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\HariLibur;
use App\Models\LogAktivitas;
use App\Models\PengaturanMagang;
use App\Models\PesertaMagang;
use App\Models\TarifUangSaku;
use App\Models\User;
use App\Services\PeriodeMagang;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengaturanMagangTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    /**
     * @param  array<string, mixed>  $ubah
     * @return array<string, mixed>
     */
    private function dataUmum(array $ubah = []): array
    {
        return array_merge([
            'batas_isi_mundur_hari' => 5,
            'tanggal_awal_periode' => 28,
            'kota_surat' => 'Yogyakarta',
            'mengetahui_nama' => 'Petugas HRD',
            'mengetahui_jabatan' => 'Staff GA & HRD',
            'menyetujui_nama' => 'Manajer',
            'menyetujui_jabatan' => 'Manager GA & FA',
            'format_nomor_surat' => '{urut}/SKM/{bulan_romawi}/{tahun}',
        ], $ubah);
    }

    public function test_halaman_pengaturan_tampil_dengan_nilai_bawaan(): void
    {
        $this->actingAs($this->admin)
            ->get(route('superadmin.pengaturan-magang.index'))
            ->assertOk()
            ->assertSee('Tarif Uang Saku')
            ->assertSee('Rp 50.000')
            ->assertSee('Staff GA &amp; HRD', false);

        $this->assertSame(7, PengaturanMagang::ambil()->batas_isi_mundur_hari);
        $this->assertSame(28, PengaturanMagang::ambil()->tanggal_awal_periode);
    }

    public function test_simpan_aturan_umum_dan_tercatat_di_log(): void
    {
        $this->actingAs($this->admin)
            ->put(route('superadmin.pengaturan-magang.update'), $this->dataUmum())
            ->assertRedirect(route('superadmin.pengaturan-magang.index'))
            ->assertSessionHas('success');

        $pengaturan = PengaturanMagang::ambil();
        $this->assertSame(5, $pengaturan->batas_isi_mundur_hari);
        $this->assertSame('Petugas HRD', $pengaturan->mengetahui_nama);

        $log = LogAktivitas::firstWhere('aksi', 'ubah_pengaturan_magang');
        $this->assertSame(5, $log->data['batas_isi_mundur_hari']);
    }

    public function test_validasi_aturan_umum(): void
    {
        $this->actingAs($this->admin)
            ->put(route('superadmin.pengaturan-magang.update'), $this->dataUmum([
                'tanggal_awal_periode' => 31,
                'batas_isi_mundur_hari' => -1,
                'format_nomor_surat' => 'SKM/{tahun}',
            ]))
            ->assertSessionHasErrors([
                'tanggal_awal_periode',
                'batas_isi_mundur_hari',
                'format_nomor_surat' => 'Format nomor surat wajib memuat {urut}.',
            ]);
    }

    public function test_tanggal_awal_periode_dipakai_service_periode(): void
    {
        $this->assertSame('2026-08', app(PeriodeMagang::class)->kunciUntuk(CarbonImmutable::parse('2026-07-28')));

        PengaturanMagang::ambil()->update(['tanggal_awal_periode' => 1]);

        $this->assertSame('2026-07', app(PeriodeMagang::class)->kunciUntuk(CarbonImmutable::parse('2026-07-28')));
    }

    public function test_tarif_baru_berlaku_mulai_tanggalnya_saja(): void
    {
        $this->actingAs($this->admin)
            ->post(route('superadmin.pengaturan-magang.tarif.store'), [
                'berlaku_mulai' => '2026-09-01',
                'tarif_hari_penuh' => 60000,
                'tarif_setengah_hari' => 30000,
            ])
            ->assertSessionHas('success');

        $this->assertSame(50000, TarifUangSaku::berlakuPada(CarbonImmutable::parse('2026-08-31'))->tarif_hari_penuh);
        $this->assertSame(60000, TarifUangSaku::berlakuPada(CarbonImmutable::parse('2026-09-01'))->tarif_hari_penuh);
        $this->assertSame(30000, TarifUangSaku::berlakuPada(CarbonImmutable::parse('2027-01-01'))->tarif_setengah_hari);

        $tarif = TarifUangSaku::latest('id')->first();
        $this->assertSame($this->admin->id, $tarif->dibuat_oleh);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'tambah_tarif_uang_saku']);
    }

    public function test_tarif_dengan_tanggal_sama_ditolak(): void
    {
        $data = ['berlaku_mulai' => '2026-09-01', 'tarif_hari_penuh' => 60000, 'tarif_setengah_hari' => 30000];

        $this->actingAs($this->admin)->post(route('superadmin.pengaturan-magang.tarif.store'), $data);

        $this->actingAs($this->admin)
            ->post(route('superadmin.pengaturan-magang.tarif.store'), $data)
            ->assertSessionHasErrorsIn('tarif', 'berlaku_mulai');

        $this->assertSame(2, TarifUangSaku::count());
    }

    public function test_tarif_terakhir_tidak_bisa_dihapus(): void
    {
        $satuSatunya = TarifUangSaku::firstOrFail();

        $this->actingAs($this->admin)
            ->delete(route('superadmin.pengaturan-magang.tarif.destroy', $satuSatunya))
            ->assertSessionHas('error');

        $this->assertModelExists($satuSatunya);

        $baru = TarifUangSaku::create(['berlaku_mulai' => '2026-09-01', 'tarif_hari_penuh' => 60000, 'tarif_setengah_hari' => 30000]);

        $this->actingAs($this->admin)
            ->delete(route('superadmin.pengaturan-magang.tarif.destroy', $baru))
            ->assertSessionHas('success');

        $this->assertModelMissing($baru);
    }

    public function test_tambah_hari_libur_rentang_melewati_tanggal_yang_sudah_ada(): void
    {
        HariLibur::create(['tanggal' => '2026-12-25', 'keterangan' => 'Natal']);

        $this->actingAs($this->admin)
            ->post(route('superadmin.pengaturan-magang.libur.store'), [
                'tanggal_mulai' => '2026-12-24',
                'tanggal_selesai' => '2026-12-26',
                'keterangan' => 'Cuti Bersama Natal',
            ])
            ->assertSessionHas('success', '2 hari libur berhasil ditambahkan. 1 tanggal sudah terdaftar dan dilewati.');

        $this->assertSame(3, HariLibur::count());
        $this->assertSame('Natal', HariLibur::whereDate('tanggal', '2026-12-25')->value('keterangan'));

        $this->actingAs($this->admin)
            ->get(route('superadmin.pengaturan-magang.index', ['tahun' => 2026]))
            ->assertSee('Cuti Bersama Natal');
    }

    public function test_rentang_hari_libur_dibatasi(): void
    {
        $this->actingAs($this->admin)
            ->post(route('superadmin.pengaturan-magang.libur.store'), [
                'tanggal_mulai' => '2026-01-01',
                'tanggal_selesai' => '2026-03-01',
                'keterangan' => 'Terlalu panjang',
            ])
            ->assertSessionHasErrorsIn('libur', 'tanggal_selesai');

        $this->assertSame(0, HariLibur::count());
    }

    public function test_hapus_hari_libur(): void
    {
        $libur = HariLibur::create(['tanggal' => '2026-08-17', 'keterangan' => 'HUT RI']);

        $this->actingAs($this->admin)
            ->delete(route('superadmin.pengaturan-magang.libur.destroy', $libur))
            ->assertSessionHas('success');

        $this->assertModelMissing($libur);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'hapus_hari_libur']);
    }

    public function test_kelola_divisi(): void
    {
        $this->actingAs($this->admin)
            ->post(route('superadmin.divisi.store'), ['nama' => 'Divisi Konsultan'])
            ->assertSessionHas('success');

        $divisi = Divisi::firstWhere('nama', 'Divisi Konsultan');
        $this->assertTrue($divisi->aktif);

        $this->actingAs($this->admin)
            ->post(route('superadmin.divisi.store'), ['nama' => 'Divisi Konsultan'])
            ->assertSessionHasErrorsIn('baru', 'nama');

        $this->actingAs($this->admin)
            ->put(route('superadmin.divisi.update', $divisi), ['nama' => 'Konsultan', 'aktif' => 0])
            ->assertSessionHas('success');

        $this->assertSame('Konsultan', $divisi->fresh()->nama);
        $this->assertFalse($divisi->fresh()->aktif);

        $this->actingAs($this->admin)->get(route('superadmin.divisi.index'))->assertOk()->assertSee('Konsultan');
    }

    public function test_divisi_yang_dipakai_tidak_bisa_dihapus(): void
    {
        $dipakai = Divisi::create(['nama' => 'Marketing']);
        $kosong = Divisi::create(['nama' => 'Unit Umum']);
        $peserta = PesertaMagang::create(['nama' => 'Peserta', 'status' => 'aktif']);
        $peserta->penempatans()->create(['periode' => '2026-07', 'divisi_id' => $dipakai->id]);

        $this->actingAs($this->admin)
            ->delete(route('superadmin.divisi.destroy', $dipakai))
            ->assertSessionHas('error');
        $this->assertModelExists($dipakai);

        $this->actingAs($this->admin)
            ->delete(route('superadmin.divisi.destroy', $kosong))
            ->assertSessionHas('success');
        $this->assertModelMissing($kosong);
    }

    public function test_penempatan_per_periode_dan_rotasi(): void
    {
        $umum = Divisi::create(['nama' => 'Unit Umum']);
        $marketing = Divisi::create(['nama' => 'Marketing']);
        $konsultan = Divisi::create(['nama' => 'Konsultan']);

        $peserta = PesertaMagang::create([
            'nama' => 'Peserta Rotasi',
            'status' => 'aktif',
            'periode_mulai' => '2026-06-03',
            'periode_selesai' => '2026-09-27',
        ]);

        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.penempatan.update', $peserta), [
                'penempatan' => [
                    '2026-06' => ['divisi_id' => $umum->id, 'mentor_nama' => 'Mentor Umum'],
                    '2026-07' => ['divisi_id' => $marketing->id],
                    '2026-08' => ['divisi_id' => $konsultan->id, 'mentor_nama' => 'Mentor Konsultan', 'mentor_jabatan' => 'Konsultan Senior'],
                    '2026-09' => ['divisi_id' => ''],
                    '2030-01' => ['divisi_id' => $umum->id], // di luar masa magang, diabaikan
                ],
            ])
            ->assertRedirect(route('superadmin.peserta-magang.show', $peserta).'#penempatan')
            ->assertSessionHas('penempatan_status', 'Penempatan divisi berhasil disimpan.');

        $peserta->load('penempatans');

        $this->assertSame(3, $peserta->penempatans->count());
        $this->assertSame($marketing->id, $peserta->penempatanUntuk('2026-07')->divisi_id);
        // September tidak diisi: ikut Agustus.
        $this->assertSame('Mentor Konsultan', $peserta->penempatanUntuk('2026-09')->mentor_nama);
        $this->assertNull($peserta->penempatanUntuk('2026-05'));

        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'ubah_penempatan', 'subjek_id' => $peserta->id]);

        $this->actingAs($this->admin)
            ->get(route('superadmin.peserta-magang.show', $peserta))
            ->assertOk()
            ->assertSee('Divisi &amp; Mentor per Periode', false)
            ->assertSee('September 2026')
            ->assertSee('ikut sebelumnya (Konsultan)');
    }

    public function test_mengosongkan_divisi_kembali_ikut_periode_sebelumnya(): void
    {
        $umum = Divisi::create(['nama' => 'Unit Umum']);
        $marketing = Divisi::create(['nama' => 'Marketing']);
        $peserta = PesertaMagang::create([
            'nama' => 'Peserta',
            'status' => 'aktif',
            'periode_mulai' => '2026-06-03',
            'periode_selesai' => '2026-08-27',
        ]);
        $peserta->penempatans()->create(['periode' => '2026-06', 'divisi_id' => $umum->id]);
        $peserta->penempatans()->create(['periode' => '2026-07', 'divisi_id' => $marketing->id]);

        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.penempatan.update', $peserta), [
                'penempatan' => ['2026-07' => ['divisi_id' => '']],
            ]);

        $this->assertSame($umum->id, $peserta->fresh()->penempatanUntuk('2026-07')->divisi_id);
    }

    public function test_detail_peserta_tanpa_periode_memberi_petunjuk(): void
    {
        $peserta = PesertaMagang::create(['nama' => 'Tanpa Periode', 'status' => 'aktif']);

        $this->actingAs($this->admin)
            ->get(route('superadmin.peserta-magang.show', $peserta))
            ->assertOk()
            ->assertSee('Isi tanggal mulai dan selesai magang');
    }

    public function test_peserta_tidak_bisa_membuka_pengaturan(): void
    {
        $akun = User::factory()->create(['role' => User::ROLE_PESERTA]);
        PesertaMagang::create(['nama' => 'Peserta', 'status' => 'aktif', 'user_id' => $akun->id]);

        $this->actingAs($akun)->get(route('superadmin.pengaturan-magang.index'))->assertForbidden();
        $this->actingAs($akun)->get(route('superadmin.divisi.index'))->assertForbidden();
    }
}
