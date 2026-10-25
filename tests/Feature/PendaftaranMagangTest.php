<?php

namespace Tests\Feature;

use App\Models\Mahasiswa;
use App\Models\PengajuanMagang;
use App\Models\PengaturanWebsite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PendaftaranMagangTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    /**
     * Data form pendaftaran yang valid.
     *
     * @return array<string, mixed>
     */
    private function dataPendaftaran(array $ubah = []): array
    {
        return array_merge([
            'nama' => 'Rina Pratiwi',
            'nim' => '2201000001',
            'email' => 'rina@contoh.test',
            'no_hp' => '081234567890',
            'asal_kampus' => 'Universitas Contoh',
            'program_studi' => 'Sistem Informasi',
            'fakultas' => 'Ilmu Komputer',
            'posisi' => 'Web Developer',
            'tanggal_mulai' => now()->addWeek()->toDateString(),
            'tanggal_selesai' => now()->addMonths(3)->toDateString(),
            'keterangan' => 'Ingin belajar pengembangan web.',
            'dokumen' => UploadedFile::fake()->create('surat.pdf', 300, 'application/pdf'),
            'persetujuan' => '1',
        ], $ubah);
    }

    public function test_halaman_utama_menampilkan_form_pendaftaran(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="form-pendaftaran"', false)
            ->assertSee(route('pengajuan.store'))
            ->assertSee(route('pengajuan.status'));
    }

    public function test_tombol_google_form_hanya_tampil_bila_link_diatur(): void
    {
        $this->get(route('home'))->assertDontSee('Buka Google Form');

        PengaturanWebsite::create(['google_form_url' => 'https://forms.gle/contoh']);

        $this->get(route('home'))
            ->assertSee('Buka Google Form')
            ->assertSee('https://forms.gle/contoh');
    }

    public function test_pendaftaran_valid_tersimpan_beserta_dokumen(): void
    {
        $response = $this->post(route('pengajuan.store'), $this->dataPendaftaran());

        $response->assertRedirect(route('home').'#daftar');
        $response->assertSessionHas('pendaftaran_berhasil.nomor', 'MG-000001');

        $mahasiswa = Mahasiswa::where('nim', '2201000001')->first();
        $this->assertNotNull($mahasiswa);
        $this->assertSame('Universitas Contoh', $mahasiswa->asal_kampus);

        $pengajuan = $mahasiswa->pengajuanMagangs()->first();
        $this->assertSame(PengajuanMagang::STATUS_MENUNGGU, $pengajuan->status);
        $this->assertSame('Ingin belajar pengembangan web.', $pengajuan->keterangan);
        Storage::disk('local')->assertExists($pengajuan->dokumen_path);
    }

    public function test_halaman_sukses_menampilkan_nomor_pengajuan(): void
    {
        $this->followingRedirects()
            ->post(route('pengajuan.store'), $this->dataPendaftaran())
            ->assertSee('Pendaftaran Terkirim!')
            ->assertSee('MG-000001');
    }

    public function test_field_wajib_divalidasi_dengan_pesan_indonesia(): void
    {
        $response = $this->post(route('pengajuan.store'), [
            'nama' => '',
            'email' => 'bukan-email',
        ]);

        $response->assertRedirect(route('home').'#daftar');
        $response->assertSessionHasErrors([
            'nama' => 'Nama lengkap wajib diisi.',
            'email' => 'Format Email tidak valid.',
            'dokumen' => 'Surat pengantar wajib diunggah.',
        ]);

        $this->assertDatabaseCount('pengajuan_magangs', 0);
    }

    public function test_dokumen_harus_pdf_atau_gambar_maksimal_2mb(): void
    {
        $this->post(route('pengajuan.store'), $this->dataPendaftaran([
            'dokumen' => UploadedFile::fake()->create('surat.docx', 100),
        ]))->assertSessionHasErrors('dokumen');

        $this->post(route('pengajuan.store'), $this->dataPendaftaran([
            'dokumen' => UploadedFile::fake()->create('surat.pdf', 3000, 'application/pdf'),
        ]))->assertSessionHasErrors('dokumen');
    }

    public function test_tanggal_tidak_boleh_lampau_dan_selesai_setelah_mulai(): void
    {
        $this->post(route('pengajuan.store'), $this->dataPendaftaran([
            'tanggal_mulai' => now()->subDay()->toDateString(),
            'tanggal_selesai' => now()->subDays(2)->toDateString(),
        ]))->assertSessionHasErrors(['tanggal_mulai', 'tanggal_selesai']);
    }

    public function test_nim_dengan_pengajuan_menunggu_tidak_bisa_mendaftar_lagi(): void
    {
        $this->post(route('pengajuan.store'), $this->dataPendaftaran());

        $this->post(route('pengajuan.store'), $this->dataPendaftaran())
            ->assertSessionHasErrors('nim');

        $this->assertDatabaseCount('pengajuan_magangs', 1);
    }

    public function test_nim_yang_pernah_ditolak_boleh_mendaftar_lagi(): void
    {
        $mahasiswa = Mahasiswa::factory()->create(['nim' => '2201000001', 'email' => 'rina@contoh.test']);
        PengajuanMagang::factory()->ditolak()->for($mahasiswa)->create();

        $this->post(route('pengajuan.store'), $this->dataPendaftaran())
            ->assertSessionHasNoErrors();

        $this->assertSame(2, $mahasiswa->pengajuanMagangs()->count());
    }

    public function test_email_milik_nim_lain_ditolak(): void
    {
        Mahasiswa::factory()->create(['nim' => '9999', 'email' => 'rina@contoh.test']);

        $this->post(route('pengajuan.store'), $this->dataPendaftaran())
            ->assertSessionHasErrors(['email' => 'Email ini sudah terdaftar untuk NIM lain.']);
    }

    public function test_pendaftaran_dibatasi_tiga_kali_per_menit(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->post(route('pengajuan.store'), ['nama' => '']);
        }

        $this->post(route('pengajuan.store'), $this->dataPendaftaran())
            ->assertRedirect(route('home').'#daftar')
            ->assertSessionHasErrors(['nim' => 'Terlalu banyak pengiriman pendaftaran. Silakan coba lagi dalam 1 menit.']);

        $this->assertDatabaseCount('pengajuan_magangs', 0);
    }

    public function test_cek_status_menampilkan_status_dan_catatan_admin(): void
    {
        $mahasiswa = Mahasiswa::factory()->create(['nim' => '2201000001', 'email' => 'rina@contoh.test']);
        $pengajuan = PengajuanMagang::factory()->ditolak()->for($mahasiswa)->create();

        $response = $this->post(route('pengajuan.status'), [
            'status_kode' => strtolower($pengajuan->nomor()),
            'status_email' => 'RINA@contoh.test',
        ]);

        $response->assertRedirect(route('home').'#status');
        $response->assertSessionHas('status_pengajuan.0.nomor', $pengajuan->nomor());
        $response->assertSessionHas('status_pengajuan.0.status', 'ditolak');

        $this->followingRedirects()
            ->post(route('pengajuan.status'), [
                'status_kode' => $pengajuan->nomor(),
                'status_email' => 'rina@contoh.test',
            ])
            ->assertSee($pengajuan->nomor())
            ->assertSee('Kuota bidang ini sudah penuh.');
    }

    public function test_cek_status_hanya_menampilkan_pengajuan_dengan_kode_itu(): void
    {
        $mahasiswa = Mahasiswa::factory()->create(['email' => 'rina@contoh.test']);
        $lama = PengajuanMagang::factory()->ditolak()->for($mahasiswa)->create();
        $baru = PengajuanMagang::factory()->for($mahasiswa)->create();

        $this->post(route('pengajuan.status'), [
            'status_kode' => str_replace('-', '', $baru->nomor()),
            'status_email' => 'rina@contoh.test',
        ])
            ->assertSessionHas('status_pengajuan', fn (array $hasil) => count($hasil) === 1 && $hasil[0]['nomor'] === $baru->nomor())
            ->assertSessionHas('status_pengajuan.0.nomor', fn (string $nomor) => $nomor !== $lama->nomor());
    }

    public function test_cek_status_dengan_email_salah_tidak_membocorkan_data(): void
    {
        $mahasiswa = Mahasiswa::factory()->create(['nim' => '2201000001', 'email' => 'rina@contoh.test']);
        $pengajuan = PengajuanMagang::factory()->for($mahasiswa)->create();

        $this->post(route('pengajuan.status'), [
            'status_kode' => $pengajuan->nomor(),
            'status_email' => 'orang-lain@contoh.test',
        ])
            ->assertSessionHas('status_tidak_ditemukan')
            ->assertSessionMissing('status_pengajuan');
    }

    public function test_cek_status_menolak_kode_tidak_sah(): void
    {
        $this->post(route('pengajuan.status'), [
            'status_kode' => '2201000001',
            'status_email' => 'rina@contoh.test',
        ])->assertSessionHasErrors(['status_kode' => 'Format kode pengajuan seperti MG-000012.']);
    }
}
