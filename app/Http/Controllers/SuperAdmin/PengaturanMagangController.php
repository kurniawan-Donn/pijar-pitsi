<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use App\Models\LogAktivitas;
use App\Models\PengaturanMagang;
use App\Models\TarifUangSaku;
use App\Services\TandaTanganDokumen;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pengaturan program magang: aturan umum, riwayat tarif uang saku,
 * dan daftar hari libur nasional / cuti bersama.
 */
class PengaturanMagangController extends Controller
{
    /**
     * Rentang hari libur maksimal sekali tambah.
     */
    private const MAKS_HARI_LIBUR = 31;

    public function index(Request $request): View
    {
        $tahun = (int) $request->query('tahun', now()->year);
        $tahun = $tahun >= 2000 && $tahun <= 2100 ? $tahun : now()->year;

        return view('superadmin.pengaturan-magang.index', [
            'pengaturan' => PengaturanMagang::ambil(),
            'tarifs' => TarifUangSaku::with('pembuat')->orderByDesc('berlaku_mulai')->get(),
            'tarifBerlaku' => TarifUangSaku::berlakuPada(now()),
            'hariLiburs' => HariLibur::whereYear('tanggal', $tahun)->orderBy('tanggal')->get(),
            'tahun' => $tahun,
        ]);
    }

    /**
     * Simpan aturan umum, penanda tangan, serta gambar tanda tangan dan stempel.
     */
    public function update(Request $request, TandaTanganDokumen $tandaTangan): RedirectResponse
    {
        $validated = $request->validate([
            'batas_isi_mundur_hari' => ['required', 'integer', 'min:0', 'max:60'],
            'tanggal_awal_periode' => ['required', 'integer', 'min:1', 'max:28'],
            'kota_surat' => ['nullable', 'string', 'max:100'],
            'mengetahui_nama' => ['nullable', 'string', 'max:255'],
            'mengetahui_jabatan' => ['nullable', 'string', 'max:255'],
            'menyetujui_nama' => ['nullable', 'string', 'max:255'],
            'menyetujui_jabatan' => ['nullable', 'string', 'max:255'],
            'format_nomor_surat' => ['required', 'string', 'max:100', 'regex:/\{urut\}/'],
            ...collect(TandaTanganDokumen::KOLOM)->keys()->mapWithKeys(fn (string $jenis) => [
                $jenis => TandaTanganDokumen::aturan(),
                'hapus_'.$jenis => ['nullable', 'boolean'],
            ])->all(),
        ], [
            'format_nomor_surat.regex' => 'Format nomor surat wajib memuat {urut}.',
            ...collect(TandaTanganDokumen::KOLOM)->keys()->flatMap(fn (string $jenis) => [
                "{$jenis}.mimes" => 'Gambar harus berformat PNG atau JPG.',
                "{$jenis}.max" => 'Ukuran gambar maksimal 1 MB.',
                "{$jenis}.dimensions" => 'Ukuran gambar minimal 80 × 40 piksel dan maksimal 4000 piksel.',
            ])->all(),
        ], [
            'ttd_mengetahui' => 'tanda tangan Mengetahui',
            'ttd_menyetujui' => 'tanda tangan Menyetujui',
            'stempel' => 'stempel',
            'batas_isi_mundur_hari' => 'batas isi mundur',
            'tanggal_awal_periode' => 'tanggal awal periode',
            'format_nomor_surat' => 'format nomor surat',
        ]);

        $pengaturan = PengaturanMagang::ambil();
        $pengaturan->fill(collect($validated)->only($pengaturan->getFillable())->all());
        $berubah = $pengaturan->getDirty();
        $pengaturan->save();

        foreach (array_keys(TandaTanganDokumen::KOLOM) as $jenis) {
            if ($request->hasFile($jenis)) {
                $tandaTangan->simpan($pengaturan, $jenis, $request->file($jenis));
                $berubah[$jenis] = 'diunggah';
            } elseif ($request->boolean('hapus_'.$jenis) && $pengaturan->{TandaTanganDokumen::KOLOM[$jenis]}) {
                $tandaTangan->hapus($pengaturan, $jenis);
                $berubah[$jenis] = 'dihapus';
            }
        }

        if ($berubah) {
            LogAktivitas::catat('ubah_pengaturan_magang', $pengaturan, 'Pengaturan magang diperbarui', $berubah);
        }

        return redirect()
            ->route('superadmin.pengaturan-magang.index')
            ->with('success', 'Pengaturan magang berhasil disimpan.');
    }

    /**
     * Pengaturan scan kartu absensi (admin & super admin): saklar, jendela tanggal, batas harian.
     */
    public function simpanScan(Request $request): RedirectResponse
    {
        $hari = ['required', 'integer', 'min:1', 'max:31'];

        $validated = $request->validateWithBag('scan', [
            'scan_jendela_1_mulai' => $hari,
            'scan_jendela_1_selesai' => $hari,
            'scan_jendela_2_mulai' => $hari,
            'scan_jendela_2_selesai' => $hari,
            'scan_batas_harian' => ['required', 'integer', 'min:1', 'max:50'],
        ], [], [
            'scan_jendela_1_mulai' => 'awal jendela sisi 1–15',
            'scan_jendela_1_selesai' => 'akhir jendela sisi 1–15',
            'scan_jendela_2_mulai' => 'awal jendela sisi 16–31',
            'scan_jendela_2_selesai' => 'akhir jendela sisi 16–31',
            'scan_batas_harian' => 'batas scan per hari',
        ]);

        $pengaturan = PengaturanMagang::ambil();
        $pengaturan->fill($validated + ['scan_aktif' => $request->boolean('scan_aktif')]);
        $berubah = $pengaturan->getDirty();
        $pengaturan->save();

        if ($berubah) {
            LogAktivitas::catat('ubah_pengaturan_magang', $pengaturan, 'Pengaturan scan kartu absensi diperbarui', $berubah);
        }

        return redirect()
            ->to(route('superadmin.pengaturan-magang.index').'#scan')
            ->with('success', 'Pengaturan scan kartu absensi disimpan.');
    }

    /**
     * Gambar tanda tangan / stempel untuk pratinjau di halaman pengaturan (khusus super admin).
     */
    public function gambar(string $jenis): Response
    {
        abort_unless(array_key_exists($jenis, TandaTanganDokumen::KOLOM), 404);

        $path = PengaturanMagang::ambil()->{TandaTanganDokumen::KOLOM[$jenis]};

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, null, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Tambah tarif uang saku baru yang berlaku mulai tanggal tertentu.
     */
    public function simpanTarif(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('tarif', [
            'berlaku_mulai' => ['required', 'date'],
            'tarif_hari_penuh' => ['required', 'integer', 'min:0', 'max:10000000'],
            'tarif_setengah_hari' => ['required', 'integer', 'min:0', 'max:10000000'],
        ], [], [
            'berlaku_mulai' => 'tanggal berlaku',
            'tarif_hari_penuh' => 'tarif hari penuh',
            'tarif_setengah_hari' => 'tarif setengah hari',
        ]);

        $berlakuMulai = CarbonImmutable::parse($validated['berlaku_mulai'])->toDateString();

        // whereDate: kolom bisa tersimpan sebagai "Y-m-d 00:00:00".
        if (TarifUangSaku::whereDate('berlaku_mulai', $berlakuMulai)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['berlaku_mulai' => 'Sudah ada tarif yang berlaku mulai tanggal tersebut. Hapus dulu bila ingin mengganti.'], 'tarif');
        }

        $tarif = TarifUangSaku::create([
            'berlaku_mulai' => $berlakuMulai,
            'tarif_hari_penuh' => $validated['tarif_hari_penuh'],
            'tarif_setengah_hari' => $validated['tarif_setengah_hari'],
            'dibuat_oleh' => $request->user()->id,
        ]);

        LogAktivitas::catat('tambah_tarif_uang_saku', $tarif, 'Tarif uang saku berlaku mulai '.$tarif->berlaku_mulai->translatedFormat('d F Y'), [
            'tarif_hari_penuh' => $tarif->tarif_hari_penuh,
            'tarif_setengah_hari' => $tarif->tarif_setengah_hari,
        ]);

        return redirect()
            ->to(route('superadmin.pengaturan-magang.index').'#tarif')
            ->with('success', 'Tarif uang saku berhasil ditambahkan.');
    }

    /**
     * Hapus satu tarif. Minimal satu tarif harus tersisa.
     */
    public function hapusTarif(TarifUangSaku $tarifUangSaku): RedirectResponse
    {
        if (TarifUangSaku::count() <= 1) {
            return redirect()
                ->to(route('superadmin.pengaturan-magang.index').'#tarif')
                ->with('error', 'Tarif terakhir tidak dapat dihapus. Tambahkan tarif lain lebih dulu.');
        }

        LogAktivitas::catat('hapus_tarif_uang_saku', $tarifUangSaku, 'Tarif uang saku berlaku mulai '.$tarifUangSaku->berlaku_mulai->translatedFormat('d F Y').' dihapus', [
            'tarif_hari_penuh' => $tarifUangSaku->tarif_hari_penuh,
            'tarif_setengah_hari' => $tarifUangSaku->tarif_setengah_hari,
        ]);

        $tarifUangSaku->delete();

        return redirect()
            ->to(route('superadmin.pengaturan-magang.index').'#tarif')
            ->with('success', 'Tarif uang saku berhasil dihapus.');
    }

    /**
     * Tambah hari libur untuk satu tanggal atau satu rentang (cuti bersama).
     * Tanggal yang sudah terdaftar dilewati.
     */
    public function simpanLibur(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('libur', [
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'keterangan' => ['required', 'string', 'max:255'],
        ], [], [
            'tanggal_mulai' => 'tanggal',
            'tanggal_selesai' => 'tanggal selesai',
        ]);

        $mulai = CarbonImmutable::parse($validated['tanggal_mulai']);
        $selesai = CarbonImmutable::parse($validated['tanggal_selesai'] ?? $validated['tanggal_mulai']);

        if ($mulai->diffInDays($selesai) + 1 > self::MAKS_HARI_LIBUR) {
            return back()
                ->withInput()
                ->withErrors(['tanggal_selesai' => 'Rentang hari libur maksimal '.self::MAKS_HARI_LIBUR.' hari.'], 'libur');
        }

        $sudahAda = HariLibur::whereDate('tanggal', '>=', $mulai->toDateString())
            ->whereDate('tanggal', '<=', $selesai->toDateString())
            ->get()
            ->map(fn (HariLibur $libur) => $libur->tanggal->toDateString())
            ->all();

        $ditambah = 0;

        foreach (CarbonPeriod::create($mulai, $selesai) as $tanggal) {
            if (in_array($tanggal->toDateString(), $sudahAda, true)) {
                continue;
            }

            HariLibur::create(['tanggal' => $tanggal->toDateString(), 'keterangan' => $validated['keterangan']]);
            $ditambah++;
        }

        if ($ditambah > 0) {
            LogAktivitas::catat('tambah_hari_libur', null, "{$ditambah} hari libur ditambahkan: {$validated['keterangan']}", [
                'dari' => $mulai->toDateString(),
                'sampai' => $selesai->toDateString(),
            ]);
        }

        $pesan = $ditambah > 0
            ? "{$ditambah} hari libur berhasil ditambahkan."
            : 'Tidak ada hari libur baru: semua tanggal sudah terdaftar.';

        if ($ditambah > 0 && count($sudahAda) > 0) {
            $pesan .= ' '.count($sudahAda).' tanggal sudah terdaftar dan dilewati.';
        }

        return redirect()
            ->to(route('superadmin.pengaturan-magang.index', ['tahun' => $mulai->year]).'#libur')
            ->with($ditambah > 0 ? 'success' : 'warning', $pesan);
    }

    public function hapusLibur(HariLibur $hariLibur): RedirectResponse
    {
        LogAktivitas::catat('hapus_hari_libur', $hariLibur, 'Hari libur '.$hariLibur->tanggal->translatedFormat('d F Y')." ({$hariLibur->keterangan}) dihapus");

        $tahun = $hariLibur->tanggal->year;
        $hariLibur->delete();

        return redirect()
            ->to(route('superadmin.pengaturan-magang.index', ['tahun' => $tahun]).'#libur')
            ->with('success', 'Hari libur berhasil dihapus.');
    }
}
