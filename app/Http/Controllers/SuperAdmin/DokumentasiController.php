<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SimpanDokumentasiRequest;
use App\Models\Dokumentasi;
use App\Models\LogAktivitas;
use App\Services\FotoDokumentasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Kelola galeri dokumentasi kegiatan di beranda: unggah, ubah, urutkan,
 * tampilkan/sembunyikan, dan hapus. Untuk admin dan super admin.
 */
class DokumentasiController extends Controller
{
    public function __construct(private FotoDokumentasi $foto) {}

    public function index(): View
    {
        return view('superadmin.dokumentasi.index', [
            'dokumentasis' => Dokumentasi::berurutan()->get(),
        ]);
    }

    public function create(): View
    {
        return view('superadmin.dokumentasi.form', [
            'dokumentasi' => new Dokumentasi(['tampil' => true]),
        ]);
    }

    public function store(SimpanDokumentasiRequest $request): RedirectResponse
    {
        $dokumentasi = new Dokumentasi($request->dataDokumentasi() + ['urutan' => Dokumentasi::urutanBerikutnya()]);

        // Menyimpan foto sekaligus menyimpan baris baru (kolom foto wajib terisi).
        $this->foto->simpan($dokumentasi, $request->file('foto'));

        LogAktivitas::catat('simpan_dokumentasi', $dokumentasi, "Dokumentasi \"{$dokumentasi->judul}\" ditambahkan");

        return redirect()
            ->to(route('superadmin.dokumentasi.index').'#dokumentasi-'.$dokumentasi->id)
            ->with('success', 'Dokumentasi berhasil ditambahkan.');
    }

    public function edit(Dokumentasi $dokumentasi): View
    {
        return view('superadmin.dokumentasi.form', ['dokumentasi' => $dokumentasi]);
    }

    public function update(SimpanDokumentasiRequest $request, Dokumentasi $dokumentasi): RedirectResponse
    {
        $dokumentasi->fill($request->dataDokumentasi());
        $berubah = $dokumentasi->getDirty();
        $dokumentasi->save();

        if ($request->hasFile('foto')) {
            $this->foto->simpan($dokumentasi, $request->file('foto'));
            $berubah['foto'] = 'diganti';
        }

        if ($berubah) {
            LogAktivitas::catat('simpan_dokumentasi', $dokumentasi, "Dokumentasi \"{$dokumentasi->judul}\" diperbarui", $berubah);
        }

        return redirect()
            ->to(route('superadmin.dokumentasi.index').'#dokumentasi-'.$dokumentasi->id)
            ->with('success', 'Dokumentasi berhasil disimpan.');
    }

    public function destroy(Dokumentasi $dokumentasi): RedirectResponse
    {
        LogAktivitas::catat('hapus_dokumentasi', $dokumentasi, "Dokumentasi \"{$dokumentasi->judul}\" dihapus");

        $this->foto->hapus($dokumentasi);
        $dokumentasi->delete();

        return redirect()
            ->route('superadmin.dokumentasi.index')
            ->with('success', 'Dokumentasi berhasil dihapus.');
    }

    /**
     * Tampilkan / sembunyikan dari beranda tanpa membuka form.
     */
    public function tampil(Dokumentasi $dokumentasi): RedirectResponse
    {
        $dokumentasi->update(['tampil' => ! $dokumentasi->tampil]);

        LogAktivitas::catat('simpan_dokumentasi', $dokumentasi, "Dokumentasi \"{$dokumentasi->judul}\" ".($dokumentasi->tampil ? 'ditampilkan' : 'disembunyikan'));

        return redirect()
            ->to(route('superadmin.dokumentasi.index').'#dokumentasi-'.$dokumentasi->id)
            ->with('success', $dokumentasi->tampil ? 'Dokumentasi ditampilkan di beranda.' : 'Dokumentasi disembunyikan dari beranda.');
    }

    /**
     * Geser satu dokumentasi naik/turun satu posisi, lalu nomori ulang 1..n.
     */
    public function urutan(Request $request, Dokumentasi $dokumentasi): RedirectResponse
    {
        $arah = $request->validate(['arah' => ['required', Rule::in(['naik', 'turun'])]])['arah'];

        $daftar = Dokumentasi::berurutan()->get()->values();
        $posisi = $daftar->search(fn (Dokumentasi $item) => $item->is($dokumentasi));
        $tujuan = $arah === 'naik' ? $posisi - 1 : $posisi + 1;

        if ($tujuan >= 0 && $tujuan < $daftar->count()) {
            $urut = $daftar->all();
            [$urut[$posisi], $urut[$tujuan]] = [$urut[$tujuan], $urut[$posisi]];

            DB::transaction(function () use ($urut) {
                foreach (array_values($urut) as $index => $item) {
                    $item->update(['urutan' => $index + 1]);
                }
            });
        }

        return redirect()->to(route('superadmin.dokumentasi.index').'#dokumentasi-'.$dokumentasi->id);
    }
}
