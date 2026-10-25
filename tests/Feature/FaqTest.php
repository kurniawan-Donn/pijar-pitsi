<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Kelola FAQ oleh admin & super admin dan tampilan accordion di beranda.
 */
class FaqTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    /**
     * FAQ awal dari migrasi: [kategori => jumlah].
     *
     * @var array<string, int>
     */
    private array $faqAwal;

    protected function setUp(): void
    {
        parent::setUp();

        // Migrasi membuat 5 FAQ awal; test lain mulai dari kosong.
        $this->faqAwal = Faq::tampil()->get()->countBy('kategori')->all();
        Faq::query()->delete();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    public function test_migrasi_membuat_faq_awal(): void
    {
        $this->assertSame(['Pendaftaran' => 3, 'Selama Magang' => 2], $this->faqAwal);
    }

    public function test_tambah_faq_ditaruh_paling_bawah_dan_tercatat_di_log(): void
    {
        Faq::factory()->create(['urutan' => 4]);

        $this->actingAs($this->admin)
            ->post(route('superadmin.faq.store'), [
                'pertanyaan' => 'Apakah magang dibayar?',
                'jawaban' => 'Ya, ada uang saku harian.',
                'kategori' => '  Selama Magang ',
                'tampil' => '1',
            ])
            ->assertSessionHas('success');

        $faq = Faq::where('pertanyaan', 'Apakah magang dibayar?')->firstOrFail();
        $this->assertSame(5, $faq->urutan);
        $this->assertSame('Selama Magang', $faq->kategori);
        $this->assertTrue($faq->tampil);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'simpan_faq']);
    }

    public function test_validasi_pertanyaan_dan_jawaban_wajib(): void
    {
        $this->actingAs($this->admin)
            ->from(route('superadmin.faq.index'))
            ->post(route('superadmin.faq.store'), ['pertanyaan' => '', 'jawaban' => ''])
            ->assertSessionHasErrorsIn('baru', ['pertanyaan', 'jawaban']);

        $this->assertSame(0, Faq::count());
    }

    public function test_ubah_dan_sembunyikan_faq(): void
    {
        $faq = Faq::factory()->create(['kategori' => 'Lama']);

        $this->actingAs($this->admin)
            ->put(route('superadmin.faq.update', $faq), [
                'pertanyaan' => 'Pertanyaan baru?',
                'jawaban' => 'Jawaban baru.',
                'kategori' => '',
            ])
            ->assertSessionHas('success');

        $faq->refresh();
        $this->assertSame('Pertanyaan baru?', $faq->pertanyaan);
        $this->assertNull($faq->kategori);
        // Checkbox tidak dicentang = disembunyikan.
        $this->assertFalse($faq->tampil);
    }

    public function test_geser_urutan_naik_dan_turun(): void
    {
        [$a, $b, $c] = [
            Faq::factory()->create(['pertanyaan' => 'A?', 'urutan' => 1]),
            Faq::factory()->create(['pertanyaan' => 'B?', 'urutan' => 2]),
            Faq::factory()->create(['pertanyaan' => 'C?', 'urutan' => 2]),
        ];

        $this->actingAs($this->admin)->put(route('superadmin.faq.urutan', $c), ['arah' => 'naik']);
        $this->assertSame(['A?', 'C?', 'B?'], Faq::berurutan()->pluck('pertanyaan')->all());
        $this->assertSame([1, 2, 3], Faq::berurutan()->pluck('urutan')->all());

        $this->actingAs($this->admin)->put(route('superadmin.faq.urutan', $a), ['arah' => 'turun']);
        $this->assertSame(['C?', 'A?', 'B?'], Faq::berurutan()->pluck('pertanyaan')->all());

        // Paling atas tidak bisa naik lagi.
        $this->actingAs($this->admin)->put(route('superadmin.faq.urutan', $c), ['arah' => 'naik']);
        $this->assertSame(['C?', 'A?', 'B?'], Faq::berurutan()->pluck('pertanyaan')->all());

        $this->actingAs($this->admin)
            ->put(route('superadmin.faq.urutan', $b), ['arah' => 'samping'])
            ->assertSessionHasErrors('arah');
    }

    public function test_hapus_faq(): void
    {
        $faq = Faq::factory()->create();

        $this->actingAs($this->admin)->delete(route('superadmin.faq.destroy', $faq))->assertSessionHas('success');

        $this->assertModelMissing($faq);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'hapus_faq']);
    }

    public function test_halaman_admin_dan_menu_sidebar(): void
    {
        Faq::factory()->create(['pertanyaan' => 'Tampil?']);
        Faq::factory()->tersembunyi()->create(['pertanyaan' => 'Rahasia?']);

        $this->actingAs($this->admin)
            ->get(route('superadmin.faq.index'))
            ->assertOk()
            ->assertSee('Daftar FAQ (2)')
            ->assertSeeInOrder(['Tampil?', 'Rahasia?', 'Disembunyikan'])
            ->assertSeeInOrder(['Konten Website', '<span>FAQ</span>'], false);
    }

    public function test_beranda_menampilkan_faq_tampil_berurutan_per_kategori(): void
    {
        Faq::factory()->create(['pertanyaan' => 'Kedua?', 'kategori' => 'Pendaftaran', 'urutan' => 2]);
        Faq::factory()->create(['pertanyaan' => 'Pertama?', 'kategori' => 'Pendaftaran', 'urutan' => 1, 'jawaban' => "Baris satu\nBaris <b>dua</b>"]);
        Faq::factory()->create(['pertanyaan' => 'Ketiga?', 'kategori' => 'Selama Magang', 'urutan' => 3]);
        Faq::factory()->tersembunyi()->create(['pertanyaan' => 'Tersembunyi?']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="faq"', false)
            ->assertSeeInOrder(['Pendaftaran', 'Pertama?', 'Kedua?', 'Selama Magang', 'Ketiga?'])
            // Jawaban di-escape, baris baru dipertahankan.
            ->assertSee('Baris satu<br />', false)
            ->assertSee('Baris &lt;b&gt;dua&lt;/b&gt;', false)
            ->assertDontSee('Tersembunyi?');
    }

    public function test_bagian_faq_tidak_tampil_bila_kosong(): void
    {
        Faq::factory()->tersembunyi()->create();

        $this->get(route('home'))->assertOk()->assertDontSee('id="faq"', false);
    }
}
