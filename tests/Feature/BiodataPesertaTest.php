<?php

namespace Tests\Feature;

use App\Models\BiodataPeserta;
use App\Models\PengajuanMagang;
use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class BiodataPesertaTest extends TestCase
{
    use RefreshDatabase;

    private User $akun;

    private PesertaMagang $peserta;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->akun = User::factory()->create(['role' => User::ROLE_PESERTA, 'email' => 'rina@contoh.test']);
        // Pas foto (= foto profil) wajib sebelum biodata dikirim.
        $this->akun->forceFill(['foto_path' => 'foto-profil/rina.jpg'])->save();
        $this->peserta = PesertaMagang::create([
            'nama' => 'Rina',
            'nim' => '111',
            'asal_kampus' => 'Kampus Lama',
            'status' => 'aktif',
            'periode_mulai' => '2026-06-01',
            'periode_selesai' => '2026-11-30',
            'user_id' => $this->akun->id,
        ]);
        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    /**
     * Isian lengkap yang lolos syarat kirim.
     *
     * @param  array<string, mixed>  $ubah
     * @return array<string, mixed>
     */
    private function lengkap(array $ubah = []): array
    {
        return array_merge([
            'aksi' => 'kirim',
            'nama_lengkap' => 'Rina Kurniasari',
            'nama_panggilan' => 'Rina',
            'jenis_kelamin' => 'perempuan',
            'tempat_lahir' => 'Sleman',
            'tanggal_lahir' => '2005-01-12',
            'nik' => '3404011201050001',
            'alamat_ktp' => 'Jl. Contoh No. 1, Sleman',
            'alamat_domisili' => 'Yogyakarta',
            'no_hp' => '0812-3456-7890',
            'email' => 'rina@contoh.test',
            'perguruan_tinggi' => 'Universitas Contoh',
            'fakultas_jurusan' => 'Teknik / Informatika',
            'nim' => '2205001',
            'semester' => 5,
            'ipk' => '3,45',
            'dosen_pembimbing_nama' => 'Dosen Contoh',
            'dosen_pembimbing_kontak' => '081200000000',
            'keterampilan' => ['Laravel', '', 'Kerja tim'],
            'bahasa' => ['Indonesia', 'Inggris (dasar)'],
            'riwayat_kesehatan' => [''],
            'no_rekening' => '1234567890',
            'nama_bank' => 'BCA',
            'atas_nama_rekening' => 'Rina Kurniasari',
            'pernyataan_setuju' => '1',
            'pernyataan_tempat' => 'Yogyakarta',
        ], $ubah);
    }

    private function kirim(array $ubah = []): TestResponse
    {
        return $this->actingAs($this->akun)->put(route('peserta.biodata.update'), $this->lengkap($ubah));
    }

    public function test_form_terisi_otomatis_dari_data_pendaftaran(): void
    {
        $pengajuan = PengajuanMagang::factory()->disetujui()->create();
        $this->peserta->update(['pengajuan_magang_id' => $pengajuan->id]);

        $this->actingAs($this->akun)
            ->get(route('peserta.biodata.edit'))
            ->assertOk()
            ->assertSee('value="Rina"', false)
            ->assertSee('value="'.$pengajuan->mahasiswa->email.'"', false)
            ->assertSee('Belum dikirim');

        $this->assertNull($this->peserta->fresh()->biodata);
    }

    public function test_simpan_draft_sebagian_dan_data_sensitif_terenkripsi(): void
    {
        $this->actingAs($this->akun)
            ->put(route('peserta.biodata.update'), [
                'aksi' => 'simpan',
                'nama_lengkap' => 'Rina K',
                'nik' => '3404 0112 0105 0001',
                'no_rekening' => '1234567890',
                'riwayat_kesehatan' => ['Alergi debu'],
                'keterampilan' => ['', 'Laravel', ''],
            ])
            ->assertRedirect(route('peserta.biodata.edit'))
            ->assertSessionHas('success', 'Draft biodata berhasil disimpan.');

        $biodata = $this->peserta->fresh()->biodata;

        $this->assertSame(BiodataPeserta::STATUS_DRAFT, $biodata->status);
        $this->assertSame('3404011201050001', $biodata->nik);
        $this->assertSame(['Laravel'], $biodata->keterampilan);
        $this->assertSame(['Alergi debu'], $biodata->riwayat_kesehatan);

        $mentah = DB::table('biodata_pesertas')->first();
        $this->assertStringNotContainsString('3404011201050001', $mentah->nik);
        $this->assertStringNotContainsString('1234567890', $mentah->no_rekening);
        $this->assertStringNotContainsString('Alergi', $mentah->riwayat_kesehatan);
    }

    public function test_kirim_ditolak_bila_data_wajib_belum_lengkap(): void
    {
        $this->kirim(['nik' => '', 'keterampilan' => [''], 'pernyataan_setuju' => '0'])
            ->assertSessionHasErrors([
                'nik' => 'NIK wajib diisi sebelum biodata dikirim.',
                'keterampilan' => 'Isi minimal satu keterampilan sebelum biodata dikirim.',
                'pernyataan_setuju' => 'Centang pernyataan kebenaran data sebelum mengirim biodata.',
            ]);

        $this->assertNull($this->peserta->fresh()->biodata);
    }

    public function test_validasi_format(): void
    {
        $this->kirim(['nik' => '12345', 'no_hp' => 'abc', 'ipk' => '4,5', 'tautan_portofolio' => 'bukan-url'])
            ->assertSessionHasErrors(['nik' => 'NIK harus 16 digit angka.', 'no_hp', 'ipk', 'tautan_portofolio']);
    }

    public function test_kirim_lengkap_mengunci_biodata(): void
    {
        $this->kirim()
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Biodata berhasil dikirim. Admin akan meninjau data Anda.');

        $biodata = $this->peserta->fresh()->biodata;

        $this->assertSame(BiodataPeserta::STATUS_DIAJUKAN, $biodata->status);
        $this->assertNotNull($biodata->diajukan_pada);
        $this->assertSame(now()->toDateString(), $biodata->pernyataan_tanggal->toDateString());
        $this->assertSame('3.45', (string) $biodata->ipk);
        $this->assertSame('081234567890', $biodata->no_hp);
        $this->assertSame(['Laravel', 'Kerja tim'], $biodata->keterampilan);
        $this->assertSame(100, $biodata->progres()['persen']);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'ajukan_biodata', 'user_id' => $this->akun->id]);

        $this->actingAs($this->akun)
            ->put(route('peserta.biodata.update'), $this->lengkap(['aksi' => 'simpan', 'nama_lengkap' => 'Diubah']))
            ->assertSessionHas('error');

        $this->assertSame('Rina Kurniasari', $biodata->fresh()->nama_lengkap);
    }

    public function test_peserta_tidak_bisa_mengubah_detail_program(): void
    {
        $this->kirim(['posisi_dilamar' => 'Direktur', 'status' => 'disetujui', 'durasi_bulan' => 12]);

        $biodata = $this->peserta->fresh()->biodata;

        $this->assertNull($biodata->posisi_dilamar);
        $this->assertNull($biodata->durasi_bulan);
        $this->assertSame(BiodataPeserta::STATUS_DIAJUKAN, $biodata->status);
    }

    public function test_admin_menyiapkan_detail_program(): void
    {
        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.biodata.program', $this->peserta), [
                'jenis_program' => 'lainnya',
            ])
            ->assertSessionHasErrorsIn('program', ['jenis_program_lainnya' => 'Isi keterangan jenis program lainnya.']);

        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.biodata.program', $this->peserta), [
                'posisi_dilamar' => 'Divisi Konsultan',
                'jenis_program' => 'wajib_kampus',
                'durasi_bulan' => 6,
                'mentor_magang' => 'Mentor Contoh',
            ])
            ->assertSessionHas('success');

        $biodata = $this->peserta->fresh()->biodata;
        $this->assertSame('Divisi Konsultan', $biodata->posisi_dilamar);
        $this->assertSame(BiodataPeserta::STATUS_DRAFT, $biodata->status);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'ubah_program_biodata']);

        $this->actingAs($this->akun)
            ->get(route('peserta.biodata.edit'))
            ->assertSee('Divisi Konsultan')
            ->assertSee('Mahasiswa Magang Wajib dari Kampus')
            ->assertSee('6 bulan');
    }

    public function test_admin_minta_revisi_lalu_peserta_memperbaiki_dan_kirim_ulang(): void
    {
        $this->kirim();

        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.biodata.tinjau', $this->peserta), ['keputusan' => 'revisi'])
            ->assertSessionHasErrorsIn('tinjau', 'catatan_revisi');

        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.biodata.tinjau', $this->peserta), [
                'keputusan' => 'revisi',
                'catatan_revisi' => 'Nomor rekening harus atas nama sendiri.',
            ])
            ->assertSessionHas('success');

        $this->assertSame(BiodataPeserta::STATUS_PERLU_REVISI, $this->peserta->fresh()->biodata->status);

        $this->actingAs($this->akun)
            ->get(route('peserta.biodata.edit'))
            ->assertSee('Nomor rekening harus atas nama sendiri.');

        $this->kirim(['no_rekening' => '9999999999'])->assertSessionHasNoErrors();

        $biodata = $this->peserta->fresh()->biodata;
        $this->assertSame(BiodataPeserta::STATUS_DIAJUKAN, $biodata->status);
        $this->assertSame('9999999999', $biodata->no_rekening);
    }

    public function test_admin_menyetujui_dan_data_peserta_diperbarui(): void
    {
        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.biodata.tinjau', $this->peserta), ['keputusan' => 'setujui'])
            ->assertSessionHas('error');

        $this->kirim();

        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.biodata.tinjau', $this->peserta), ['keputusan' => 'setujui'])
            ->assertSessionHas('success');

        $peserta = $this->peserta->fresh();
        $this->assertSame(BiodataPeserta::STATUS_DISETUJUI, $peserta->biodata->status);
        $this->assertSame($this->admin->id, $peserta->biodata->ditinjau_oleh);
        $this->assertSame('Rina Kurniasari', $peserta->nama);
        $this->assertSame('2205001', $peserta->nim);
        $this->assertSame('Universitas Contoh', $peserta->asal_kampus);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'tinjau_biodata']);

        // Biodata yang disetujui bisa dibuka kembali untuk revisi.
        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.biodata.tinjau', $this->peserta), [
                'keputusan' => 'revisi',
                'catatan_revisi' => 'Perbarui semester.',
            ]);

        $this->assertTrue($this->peserta->fresh()->biodata->bisaDiubahPeserta());
    }

    public function test_halaman_admin_menyamarkan_nik_dan_menampilkan_biodata(): void
    {
        $this->kirim();

        $this->actingAs($this->admin)
            ->get(route('superadmin.peserta-magang.biodata', $this->peserta))
            ->assertOk()
            ->assertSee('3404********0001')
            ->assertSee('Kerja tim')
            ->assertSee('Menunggu ditinjau');

        $this->actingAs($this->admin)
            ->get(route('superadmin.peserta-magang.show', $this->peserta))
            ->assertSee('Menunggu ditinjau · 100% data wajib terisi');
    }

    public function test_progres_dan_field_kosong(): void
    {
        $biodata = new BiodataPeserta(['nama_lengkap' => 'A', 'keterampilan' => ['', ' '], 'bahasa' => ['Indonesia']]);

        $this->assertSame(2, $biodata->progres()['terisi']);
        $this->assertArrayHasKey('keterampilan', $biodata->fieldWajibKosong());
        $this->assertArrayNotHasKey('bahasa', $biodata->fieldWajibKosong());
    }

    public function test_dashboard_peserta_menampilkan_progres_biodata(): void
    {
        $this->actingAs($this->akun)
            ->get(route('peserta.dashboard'))
            ->assertOk()
            ->assertSeeInOrder(['<h3>Biodata</h3>', 'Belum dikirim'], false)
            ->assertSee(route('peserta.biodata.edit'));
    }

    public function test_peserta_selesai_tidak_bisa_mengubah_biodata(): void
    {
        $this->peserta->update(['status' => 'selesai']);

        $this->actingAs($this->akun)
            ->put(route('peserta.biodata.update'), $this->lengkap(['aksi' => 'simpan']))
            ->assertSessionHas('error', 'Masa magang Anda sudah selesai. Data hanya dapat dilihat.');

        $this->assertNull($this->peserta->fresh()->biodata);
    }
}
