<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SimpanAlumniRequest;
use App\Models\Alumni;
use App\Models\LogAktivitas;
use App\Models\PesertaMagang;
use App\Services\FotoAlumni;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Kelola alumni magang yang tampil di website: pilih dari peserta yang
 * sudah selesai dan bersedia, atau input manual untuk alumni lama.
 */
class AlumniController extends Controller
{
    public function __construct(private FotoAlumni $fotoAlumni) {}

    public function index(): View
    {
        return view('superadmin.alumni.index', [
            'alumnis' => Alumni::with('pesertaMagang.feedback')->berurutan()->get(),
            'pesertaBersedia' => $this->pesertaBersedia(),
        ]);
    }

    /**
     * Form tambah. Dengan ?peserta=ID, isian terisi otomatis dari data peserta.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $alumni = new Alumni(['tampil' => true]);

        if ($request->filled('peserta')) {
            $peserta = $this->pesertaBersedia()->firstWhere('id', $request->integer('peserta'));

            if (! $peserta) {
                return redirect()
                    ->route('superadmin.alumni.index')
                    ->with('error', 'Peserta tidak ditemukan, belum selesai, belum bersedia ditampilkan, atau sudah menjadi alumni.');
            }

            $alumni->fill(Alumni::isianDariPeserta($peserta));
            $alumni->setRelation('pesertaMagang', $peserta);
        }

        return view('superadmin.alumni.form', ['alumni' => $alumni]);
    }

    public function store(SimpanAlumniRequest $request): RedirectResponse
    {
        $alumni = DB::transaction(function () use ($request) {
            $alumni = Alumni::create($request->dataAlumni() + ['urutan' => Alumni::urutanBerikutnya()]);

            if ($request->hasFile('foto')) {
                $this->fotoAlumni->simpanUnggahan($alumni, $request->file('foto'));
            }

            return $alumni;
        });

        $this->fotoAlumni->sinkron($alumni);

        LogAktivitas::catat('simpan_alumni', $alumni, "Alumni {$alumni->nama} ditambahkan".($alumni->dariPeserta() ? ' dari data peserta' : ' (manual)'));

        return redirect()
            ->route('superadmin.alumni.index')
            ->with('success', 'Alumni berhasil ditambahkan.');
    }

    public function edit(Alumni $alumni): View
    {
        return view('superadmin.alumni.form', ['alumni' => $alumni->load('pesertaMagang.feedback', 'pesertaMagang.user')]);
    }

    public function update(SimpanAlumniRequest $request, Alumni $alumni): RedirectResponse
    {
        $alumni->fill($request->dataAlumni());
        $berubah = $alumni->getDirty();
        $alumni->save();

        if ($request->hasFile('foto')) {
            $this->fotoAlumni->simpanUnggahan($alumni, $request->file('foto'));
            $berubah['foto'] = 'diganti';
        } elseif ($request->boolean('hapus_foto') && ! $alumni->dariPeserta()) {
            $this->fotoAlumni->hapus($alumni);
            $berubah['foto'] = 'dihapus';
        }

        $this->fotoAlumni->sinkron($alumni);

        if ($berubah) {
            LogAktivitas::catat('simpan_alumni', $alumni, "Alumni {$alumni->nama} diperbarui", $berubah);
        }

        return redirect()
            ->route('superadmin.alumni.index')
            ->with('success', 'Alumni berhasil disimpan.');
    }

    public function destroy(Alumni $alumni): RedirectResponse
    {
        LogAktivitas::catat('hapus_alumni', $alumni, "Alumni {$alumni->nama} dihapus");

        $this->fotoAlumni->hapus($alumni);
        $alumni->delete();

        return redirect()
            ->route('superadmin.alumni.index')
            ->with('success', 'Alumni berhasil dihapus.');
    }

    /**
     * Geser satu alumni naik/turun satu posisi, lalu nomori ulang 1..n.
     */
    public function urutan(Request $request, Alumni $alumni): RedirectResponse
    {
        $arah = $request->validate(['arah' => ['required', Rule::in(['naik', 'turun'])]])['arah'];

        $daftar = Alumni::berurutan()->get()->values();
        $posisi = $daftar->search(fn (Alumni $item) => $item->is($alumni));
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

        return redirect()->to(route('superadmin.alumni.index').'#alumni-'.$alumni->id);
    }

    /**
     * Peserta selesai yang bersedia tampil dan belum menjadi alumni.
     *
     * @return Collection<int, PesertaMagang>
     */
    private function pesertaBersedia(): Collection
    {
        return PesertaMagang::with(['biodata', 'feedback', 'penempatans.divisi'])
            ->where('status', 'selesai')
            ->whereHas('feedback', fn (Builder $query) => $query->where('bersedia_tampil_alumni', true))
            ->whereDoesntHave('alumni')
            ->orderBy('nama')
            ->get();
    }
}
