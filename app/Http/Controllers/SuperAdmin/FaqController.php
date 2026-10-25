<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Kelola FAQ website publik: tambah, ubah, hapus, urutkan, sembunyikan.
 */
class FaqController extends Controller
{
    public function index(): View
    {
        return view('superadmin.faq.index', [
            'faqs' => Faq::berurutan()->get(),
            'kategoris' => Faq::whereNotNull('kategori')->distinct()->orderBy('kategori')->pluck('kategori'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $faq = Faq::create($this->validasi($request, 'baru') + ['urutan' => Faq::urutanBerikutnya()]);

        LogAktivitas::catat('simpan_faq', $faq, 'FAQ ditambahkan: '.Str::limit($faq->pertanyaan, 80));

        return redirect()
            ->to(route('superadmin.faq.index').'#faq-'.$faq->id)
            ->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $faq->fill($this->validasi($request, 'faq'.$faq->id));
        $berubah = $faq->getDirty();
        $faq->save();

        if ($berubah) {
            LogAktivitas::catat('simpan_faq', $faq, 'FAQ diperbarui: '.Str::limit($faq->pertanyaan, 80), $berubah);
        }

        return redirect()
            ->to(route('superadmin.faq.index').'#faq-'.$faq->id)
            ->with('success', 'FAQ berhasil disimpan.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        LogAktivitas::catat('hapus_faq', $faq, 'FAQ dihapus: '.Str::limit($faq->pertanyaan, 80));

        $faq->delete();

        return redirect()
            ->route('superadmin.faq.index')
            ->with('success', 'FAQ berhasil dihapus.');
    }

    /**
     * Geser satu FAQ naik/turun satu posisi, lalu nomori ulang 1..n.
     */
    public function urutan(Request $request, Faq $faq): RedirectResponse
    {
        $arah = $request->validate(['arah' => ['required', Rule::in(['naik', 'turun'])]])['arah'];

        $daftar = Faq::berurutan()->get()->values();
        $posisi = $daftar->search(fn (Faq $item) => $item->is($faq));
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

        return redirect()->to(route('superadmin.faq.index').'#faq-'.$faq->id);
    }

    /**
     * @return array{pertanyaan: string, jawaban: string, kategori: ?string, tampil: bool}
     */
    private function validasi(Request $request, string $bag): array
    {
        $validated = $request->validateWithBag($bag, [
            'pertanyaan' => ['required', 'string', 'max:255'],
            'jawaban' => ['required', 'string', 'max:5000'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'tampil' => ['nullable', 'boolean'],
        ], [], [
            'pertanyaan' => 'pertanyaan',
            'jawaban' => 'jawaban',
            'kategori' => 'kategori',
        ]);

        return [
            'pertanyaan' => $validated['pertanyaan'],
            'jawaban' => $validated['jawaban'],
            'kategori' => filled($validated['kategori'] ?? null) ? trim($validated['kategori']) : null,
            'tampil' => $request->boolean('tampil'),
        ];
    }
}
