<?php

namespace App\Services;

use App\Models\FeedbackPeserta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Rekap feedback peserta: rata-rata per pertanyaan, sebaran rekomendasi,
 * dan jawaban terbuka. Filter berdasarkan tanggal selesai magang.
 */
class RekapFeedback
{
    /**
     * @return Collection<int, FeedbackPeserta>
     */
    public function daftar(?string $dari = null, ?string $sampai = null): Collection
    {
        return FeedbackPeserta::with('pesertaMagang')
            ->whereHas('pesertaMagang', fn (Builder $query) => $query
                // whereDate: kolom tersimpan sebagai "Y-m-d 00:00:00".
                ->when($dari, fn (Builder $q) => $q->whereDate('periode_selesai', '>=', $dari))
                ->when($sampai, fn (Builder $q) => $q->whereDate('periode_selesai', '<=', $sampai)))
            ->get()
            ->sortBy(fn (FeedbackPeserta $feedback) => $feedback->pesertaMagang->nama)
            ->values();
    }

    /**
     * @param  Collection<int, FeedbackPeserta>  $daftar
     * @return array{jumlah: int, rata_rata: float, per_pertanyaan: array<int, float>, rekomendasi: array<string, int>, terbuka: array<string, array<int, array{nama: string, jawaban: string}>>}
     */
    public function hitung(Collection $daftar): array
    {
        $perPertanyaan = [];

        foreach (array_keys(config('feedback.pertanyaan_skala')) as $nomor) {
            $nilai = $daftar->map(fn (FeedbackPeserta $feedback) => (int) ($feedback->skor[$nomor] ?? 0))->filter();
            $perPertanyaan[$nomor] = $nilai->isEmpty() ? 0.0 : round($nilai->avg(), 2);
        }

        $rekomendasi = array_map(fn () => 0, config('feedback.rekomendasi'));

        foreach ($daftar as $feedback) {
            if (isset($rekomendasi[$feedback->rekomendasi])) {
                $rekomendasi[$feedback->rekomendasi]++;
            }
        }

        $terbuka = [];

        foreach (array_keys(config('feedback.pertanyaan_terbuka')) + ['evaluasi_keseluruhan' => 'evaluasi_keseluruhan'] as $kolom) {
            $terbuka[$kolom] = $daftar
                ->filter(fn (FeedbackPeserta $feedback) => trim((string) $feedback->{$kolom}) !== '')
                ->map(fn (FeedbackPeserta $feedback) => ['nama' => $feedback->pesertaMagang->nama, 'jawaban' => $feedback->{$kolom}])
                ->values()
                ->all();
        }

        return [
            'jumlah' => $daftar->count(),
            'rata_rata' => $daftar->isEmpty() ? 0.0 : round($daftar->avg(fn (FeedbackPeserta $feedback) => $feedback->rataRata()), 2),
            'per_pertanyaan' => $perPertanyaan,
            'rekomendasi' => $rekomendasi,
            'terbuka' => $terbuka,
        ];
    }
}
