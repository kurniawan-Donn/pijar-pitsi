<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Http\Requests\SimpanFeedbackRequest;
use App\Models\FeedbackPeserta;
use App\Models\LogAktivitas;
use App\Services\FotoAlumni;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Feedback program magang oleh peserta, dibuka sebulan sebelum selesai.
 * Bisa diedit dan dikirim ulang, termasuk setelah status magang selesai,
 * sampai surat keterangan diterbitkan.
 */
class FeedbackController extends Controller
{
    public function edit(Request $request): View
    {
        $peserta = $request->user()->pesertaMagang->load('feedback');

        return view('peserta.feedback.edit', [
            'peserta' => $peserta,
            'feedback' => $peserta->feedback ?? new FeedbackPeserta(['skor' => []]),
            'dibuka' => FeedbackPeserta::sudahDibuka($peserta),
            'terkunci' => FeedbackPeserta::terkunci($peserta),
            'dibukaPada' => FeedbackPeserta::dibukaPada($peserta),
        ]);
    }

    public function update(SimpanFeedbackRequest $request, FotoAlumni $fotoAlumni): RedirectResponse
    {
        $peserta = $request->user()->pesertaMagang->load(['feedback', 'alumni']);

        if (! FeedbackPeserta::sudahDibuka($peserta)) {
            return redirect()->route('peserta.feedback.edit')->with('error', 'Feedback belum dibuka.');
        }

        if (FeedbackPeserta::terkunci($peserta)) {
            return redirect()->route('peserta.feedback.edit')->with('error', 'Feedback sudah dikunci karena surat keterangan telah diterbitkan.');
        }

        $feedback = $peserta->feedback ?? new FeedbackPeserta(['peserta_magang_id' => $peserta->id]);
        $kirimUlang = $feedback->exists;

        $feedback->fill([
            'skor' => $request->skor(),
            ...$request->safe()->only(array_keys(config('feedback.pertanyaan_terbuka'))),
            'rekomendasi' => $request->input('rekomendasi'),
            'evaluasi_keseluruhan' => $request->input('evaluasi_keseluruhan'),
            'pernyataan' => true,
            'bersedia_tampil_alumni' => $request->boolean('bersedia_tampil_alumni'),
            'dikirim_pada' => $feedback->dikirim_pada ?? now(),
            'jumlah_kirim' => $feedback->jumlah_kirim + 1,
        ])->save();

        LogAktivitas::catat('kirim_feedback', $peserta, ($kirimUlang ? 'Feedback dikirim ulang oleh ' : 'Feedback dikirim oleh ').$peserta->nama);

        // Persetujuan alumni ditarik: alumni langsung disembunyikan dan foto publiknya dihapus.
        if ($peserta->alumni && ! $feedback->bersedia_tampil_alumni && $peserta->alumni->tampil) {
            $peserta->alumni->update(['tampil' => false]);
            LogAktivitas::catat('simpan_alumni', $peserta->alumni, "Alumni {$peserta->alumni->nama} disembunyikan karena peserta menarik persetujuan");
        }

        if ($peserta->alumni) {
            $fotoAlumni->sinkron($peserta->alumni->fresh());
        }

        return redirect()
            ->route('peserta.feedback.edit')
            ->with('success', $kirimUlang ? 'Perubahan feedback berhasil dikirim. Terima kasih!' : 'Feedback berhasil dikirim. Terima kasih!');
    }
}
