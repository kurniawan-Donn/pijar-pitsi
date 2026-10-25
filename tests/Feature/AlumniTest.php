<?php

namespace Tests\Feature;

use App\Models\Alumni;
use App\Models\BiodataPeserta;
use App\Models\Divisi;
use App\Models\Faq;
use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Alumni magang: persetujuan di feedback, kelola admin, foto publik, dan
 * carousel + popup di beranda.
 */
class AlumniTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $akun;

    private PesertaMagang $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        // Feedback terbuka sebulan sebelum 30 November.
        Carbon::setTestNow('2026-11-02 09:00:00');
        Storage::fake('local');
        Storage::fake('public');

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->akun = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $this->akun->forceFill(['foto_path' => 'foto-profil/rina.jpg'])->save();
        Storage::disk('local')->put('foto-profil/rina.jpg', 'jpg-profil-rina');

        $this->peserta = PesertaMagang::create([
            'nama' => 'Rina',
            'asal_kampus' => 'Kampus Lama',
            'program_studi' => 'Akuntansi',
            'status' => 'aktif',
            'periode_mulai' => '2026-06-01',
            'periode_selesai' => '2026-11-30',
            'user_id' => $this->akun->id,
        ]);

        BiodataPeserta::create([
            'peserta_magang_id' => $this->peserta->id,
            'nama_lengkap' => 'Rina Kurniasari',
            'perguruan_tinggi' => 'Universitas Gadjah Mada',
            'fakultas_jurusan' => 'Ekonomi / Akuntansi',
            'status' => BiodataPeserta::STATUS_DISETUJUI,
        ]);

        $umum = Divisi::create(['nama' => 'Unit Umum']);
        $konsultan = Divisi::create(['nama' => 'Divisi Konsultan']);
        $this->peserta->penempatans()->create(['periode' => '2026-06', 'divisi_id' => $umum->id]);
        $this->peserta->penempatans()->create(['periode' => '2026-09', 'divisi_id' => $konsultan->id]);
    }

    private function kirimFeedback(bool $bersedia, int $skorKepuasan = 4): TestResponse
    {
        return $this->actingAs($this->akun->fresh())->put(route('peserta.feedback.update'), [
            'skor' => array_fill_keys(range(1, 9), 5) + [10 => $skorKepuasan],
            'paling_disukai' => 'Belajar audit langsung dengan tim.',
            'kendala' => 'a',
            'perlu_diperbaiki' => 'b',
            'saran' => 'c',
            'rekomendasi' => 'bersedia',
            'evaluasi_keseluruhan' => 'Bagus',
            'pernyataan' => '1',
            'bersedia_tampil_alumni' => $bersedia ? '1' : null,
        ]);
    }

    /**
     * Peserta selesai + bersedia, lalu admin menambahkannya sebagai alumni.
     */
    private function alumniDariPeserta(array $ubah = []): Alumni
    {
        $this->kirimFeedback(true);
        $this->peserta->update(['status' => 'selesai']);

        $this->actingAs($this->admin)
            ->post(route('superadmin.alumni.store'), $ubah + [
                'peserta_magang_id' => $this->peserta->id,
                'nama' => 'Rina Kurniasari',
                'universitas' => 'Universitas Gadjah Mada',
                'divisi' => 'Divisi Konsultan',
                'periode' => 'Juni – November 2026',
                'judul_project' => 'Dashboard audit internal',
                'deskripsi_project' => 'Membangun dashboard temuan audit.',
                'testimoni' => 'Belajar audit langsung dengan tim.',
                'tampil' => '1',
            ])
            ->assertSessionHasNoErrors();

        return Alumni::firstOrFail();
    }

    public function test_form_feedback_punya_centang_alumni_dan_tersimpan(): void
    {
        $this->actingAs($this->akun)
            ->get(route('peserta.feedback.edit'))
            ->assertSee('Saya bersedia ditampilkan sebagai alumni');

        $this->kirimFeedback(true)->assertSessionHas('success');
        $this->assertTrue($this->peserta->fresh()->feedback->bersedia_tampil_alumni);

        $this->kirimFeedback(false);
        $this->assertFalse($this->peserta->fresh()->feedback->bersedia_tampil_alumni);
    }

    public function test_hanya_peserta_selesai_dan_bersedia_yang_bisa_dipilih(): void
    {
        $this->kirimFeedback(false);
        $this->peserta->update(['status' => 'selesai']);

        $this->actingAs($this->admin)
            ->get(route('superadmin.alumni.index'))
            ->assertSee('Dari peserta (0 bersedia)');

        $this->actingAs($this->admin)
            ->get(route('superadmin.alumni.create', ['peserta' => $this->peserta->id]))
            ->assertRedirect(route('superadmin.alumni.index'))
            ->assertSessionHas('error');

        $this->actingAs($this->admin)
            ->post(route('superadmin.alumni.store'), [
                'peserta_magang_id' => $this->peserta->id,
                'nama' => 'Rina',
                'universitas' => 'UGM',
                'judul_project' => 'X',
                'deskripsi_project' => 'Y',
            ])
            ->assertSessionHasErrors(['peserta_magang_id' => 'Hanya peserta yang sudah selesai dan bersedia ditampilkan sebagai alumni yang bisa dipilih.']);
    }

    public function test_pilih_peserta_mengisi_data_otomatis(): void
    {
        $this->kirimFeedback(true);
        $this->peserta->update(['status' => 'selesai']);

        $this->actingAs($this->admin)
            ->get(route('superadmin.alumni.index'))
            ->assertSee('Dari peserta (1 bersedia)')
            ->assertSee('Rina · Kampus Lama');

        $this->actingAs($this->admin)
            ->get(route('superadmin.alumni.create', ['peserta' => $this->peserta->id]))
            ->assertOk()
            ->assertSee('value="Rina Kurniasari"', false)
            ->assertSee('value="Universitas Gadjah Mada"', false)
            ->assertSee('value="Ekonomi / Akuntansi"', false)
            ->assertSee('value="Divisi Konsultan"', false)
            ->assertSee('value="Juni – November 2026"', false)
            ->assertSee('Belajar audit langsung dengan tim.')
            ->assertSee('★★★★☆', false)
            ->assertDontSee('name="rating"', false);
    }

    public function test_alumni_dari_peserta_rating_dari_feedback_dan_foto_disalin_ke_publik(): void
    {
        $alumni = $this->alumniDariPeserta();

        $this->assertSame($this->peserta->id, $alumni->peserta_magang_id);
        $this->assertSame(4, $alumni->nilaiRating());
        $this->assertNull($alumni->rating);
        Storage::disk('public')->assertExists($alumni->foto_path);
        $this->assertSame('jpg-profil-rina', Storage::disk('public')->get($alumni->foto_path));
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'simpan_alumni']);
    }

    public function test_rating_dan_foto_alumni_dari_peserta_tidak_bisa_diisi_admin(): void
    {
        $this->kirimFeedback(true);
        $this->peserta->update(['status' => 'selesai']);

        $this->actingAs($this->admin)
            ->post(route('superadmin.alumni.store'), [
                'peserta_magang_id' => $this->peserta->id,
                'nama' => 'Rina',
                'universitas' => 'UGM',
                'judul_project' => 'X',
                'deskripsi_project' => 'Y',
                'rating' => 5,
            ])
            ->assertSessionHasErrors('rating');
    }

    public function test_sembunyikan_menghapus_foto_publik_dan_tampilkan_lagi_menyalin_ulang(): void
    {
        $alumni = $this->alumniDariPeserta();
        $foto = $alumni->foto_path;

        $data = $alumni->only(['nama', 'universitas', 'judul_project', 'deskripsi_project']);

        $this->actingAs($this->admin)->put(route('superadmin.alumni.update', $alumni), $data)->assertSessionHasNoErrors();

        $this->assertFalse($alumni->fresh()->tampil);
        $this->assertNull($alumni->fresh()->foto_path);
        Storage::disk('public')->assertMissing($foto);

        $this->actingAs($this->admin)->put(route('superadmin.alumni.update', $alumni), $data + ['tampil' => '1']);

        Storage::disk('public')->assertExists($alumni->fresh()->foto_path);
    }

    public function test_peserta_menarik_persetujuan_alumni_langsung_disembunyikan(): void
    {
        $alumni = $this->alumniDariPeserta();
        $foto = $alumni->foto_path;

        $this->get(route('home'))->assertSee('Rina Kurniasari');

        $this->kirimFeedback(false)->assertSessionHas('success');

        $alumni->refresh();
        $this->assertFalse($alumni->tampil);
        $this->assertNull($alumni->foto_path);
        Storage::disk('public')->assertMissing($foto);
        $this->get(route('home'))->assertDontSee('Rina Kurniasari');

        $this->actingAs($this->admin)
            ->get(route('superadmin.alumni.index'))
            ->assertSee('Persetujuan ditarik');
    }

    public function test_alumni_manual_dengan_rating_dan_foto_unggahan(): void
    {
        $this->actingAs($this->admin)
            ->post(route('superadmin.alumni.store'), [
                'nama' => 'Budi Lama',
                'universitas' => 'Universitas Diponegoro',
                'program_studi' => 'Manajemen',
                'divisi' => 'Unit Umum',
                'periode' => 'Januari – Juni 2024',
                'rating' => 5,
                'judul_project' => 'Arsip digital',
                'deskripsi_project' => 'Memindahkan arsip ke sistem.',
                'tampil' => '1',
                'foto' => UploadedFile::fake()->image('budi.png', 600, 400),
            ])
            ->assertRedirect(route('superadmin.alumni.index'));

        $alumni = Alumni::firstOrFail();
        $this->assertFalse($alumni->dariPeserta());
        $this->assertSame(5, $alumni->nilaiRating());

        [$lebar, $tinggi] = getimagesizefromstring(Storage::disk('public')->get($alumni->foto_path));
        $this->assertSame([400, 400], [$lebar, $tinggi]);

        $this->actingAs($this->admin)->delete(route('superadmin.alumni.destroy', $alumni));
        $this->assertModelMissing($alumni);
        Storage::disk('public')->assertMissing($alumni->foto_path);
    }

    public function test_url_foto_alumni_mengikuti_alamat_yang_dibuka_bukan_app_url(): void
    {
        config(['app.url' => 'http://localhost:8000']);
        $alumni = Alumni::factory()->create(['tampil' => true, 'foto_path' => 'alumni/budi.jpg']);

        $this->actingAs($this->admin)
            ->get('http://pilara.test/superadmin/alumni')
            ->assertSee('src="http://pilara.test/storage/alumni/budi.jpg"', false)
            ->assertDontSee('localhost:8000/storage', false);

        $this->get('http://pilara.test/')->assertSee('http://pilara.test/storage/alumni/budi.jpg', false);
        $this->assertNotNull($alumni->fresh()->urlFoto());
    }

    public function test_form_alumni_punya_pratinjau_foto_sebelum_disimpan(): void
    {
        $this->actingAs($this->admin)
            ->get(route('superadmin.alumni.create'))
            ->assertSee('id="pratinjau-foto-alumni"', false)
            ->assertSee('data-pratinjau="#pratinjau-foto-alumni"', false);
    }

    public function test_urutan_alumni(): void
    {
        $a = Alumni::factory()->create(['nama' => 'Alfa']);
        $b = Alumni::factory()->create(['nama' => 'Beta']);

        $this->actingAs($this->admin)->put(route('superadmin.alumni.urutan', $b), ['arah' => 'naik']);

        $this->assertSame(['Beta', 'Alfa'], Alumni::berurutan()->pluck('nama')->all());
        $this->get(route('home'))->assertSeeInOrder(['Beta', 'Alfa']);
    }

    public function test_beranda_carousel_dan_popup_hanya_alumni_yang_tampil(): void
    {
        Alumni::factory()->create([
            'nama' => 'Citra Tampil',
            'rating' => 4,
            'judul_project' => 'Sistem arsip',
            'deskripsi_project' => "Baris satu\nBaris dua",
            'testimoni' => 'Mentornya sabar.',
        ]);
        Alumni::factory()->tersembunyi()->create(['nama' => 'Dedi Sembunyi']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="alumni"', false)
            ->assertSee('Citra Tampil')
            ->assertSee('★★★★☆', false)
            ->assertSee('Sistem arsip')
            ->assertSee('<dialog class="alumni-dialog"', false)
            ->assertSee('Baris satu<br />', false)
            ->assertSee('Mentornya sabar.')
            ->assertDontSee('Dedi Sembunyi');
    }

    public function test_navbar_publik_baru(): void
    {
        Alumni::factory()->create();
        Faq::factory()->create();

        $this->get(route('home'))
            ->assertSeeInOrder(['>Beranda<', '>Tentang<', '>Alumni<', '>FAQ<', '>Daftar Magang<', '>Cek Status<', '>Login<'], false)
            ->assertSee('id="tentang"', false)
            ->assertDontSee('<li><a href="#pengumuman">', false)
            ->assertDontSee('<li><a href="#kontak">', false);
    }

    public function test_menu_alumni_dan_faq_hilang_bila_tidak_ada_data(): void
    {
        Faq::query()->delete();

        $this->get(route('home'))
            ->assertDontSee('<li><a href="#alumni">', false)
            ->assertDontSee('<li><a href="#faq">', false)
            ->assertDontSee('id="alumni"', false)
            ->assertSee('<li><a href="#tentang">Tentang</a></li>', false);
    }
}
