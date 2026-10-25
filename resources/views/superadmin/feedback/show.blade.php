{{-- resources/views/superadmin/feedback/show.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Feedback Peserta')
@section('page_title', 'Feedback Peserta')
@section('page_subtitle', $pesertaMagang->nama)

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .top {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;

        margin-bottom: 16px;
    }

    .panel {
        max-width: 980px;

        margin-bottom: 18px;
        padding: 20px 22px;

        background: #ffffff;

        border: 1px solid var(--garis);
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel h3 {
        margin-bottom: 12px;

        color: var(--utama-gelap);
        font-size: 15px;
    }

    .skor-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 150px;
        gap: 12px;

        padding: 8px 0;

        border-top: 1px solid var(--garis);

        font-size: 13px;
    }

    .skor-row:first-of-type {
        border-top: none;
    }

    .skor-row strong {
        color: var(--utama-gelap);
        text-align: right;
    }

    .jawaban {
        padding: 10px 0;

        border-top: 1px solid var(--garis);

        font-size: 13px;
        line-height: 1.6;
    }

    .jawaban:first-of-type {
        border-top: none;
    }

    .jawaban span {
        display: block;

        color: var(--teks-sekunder);
        font-size: 12px;
        font-weight: 700;
    }

    .jawaban p {
        margin: 4px 0 0;

        color: var(--utama-gelap);
        white-space: pre-line;
    }

    .empty {
        padding: 30px;

        color: var(--teks-sekunder);
        text-align: center;
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 16px 30px;
        }

        .skor-row {
            grid-template-columns: 1fr;
        }

        .skor-row strong {
            text-align: left;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <div class="top">
        <x-button variant="secondary" :href="route('superadmin.peserta-magang.show', $pesertaMagang)">← Detail peserta</x-button>
        <x-button variant="secondary" :href="route('superadmin.feedback.index')">Rekap feedback</x-button>
    </div>

    @if (! $feedback)
        <section class="panel empty">
            Peserta belum mengirim feedback.
            @if ($dibukaPada)
                Feedback dibuka untuk peserta mulai {{ $dibukaPada->translatedFormat('d F Y') }}.
            @endif
        </section>
    @else
        <section class="panel">
            <h3>Penilaian Program · rata-rata {{ number_format($feedback->rataRata(), 2, ',', '.') }} / 5</h3>
            @foreach (config('feedback.pertanyaan_skala') as $nomor => $pertanyaan)
                @php
                    $nilai = (int) ($feedback->skor[$nomor] ?? 0);
                @endphp
                <div class="skor-row">
                    <span>{{ $nomor }}. {{ $pertanyaan }}</span>
                    <strong>{{ $nilai }} · {{ config('feedback.skala')[$nilai] ?? '-' }}</strong>
                </div>
            @endforeach
        </section>

        <section class="panel">
            <h3>Jawaban Terbuka</h3>
            @foreach (config('feedback.pertanyaan_terbuka') as $kolom => $pertanyaan)
                <div class="jawaban">
                    <span>{{ $pertanyaan }}</span>
                    <p>{{ $feedback->{$kolom} }}</p>
                </div>
            @endforeach
            <div class="jawaban">
                <span>{{ config('feedback.pertanyaan_rekomendasi') }}</span>
                <p>{{ $feedback->labelRekomendasi() }}</p>
            </div>
            <div class="jawaban">
                <span>{{ config('feedback.pertanyaan_evaluasi') }}</span>
                <p>{{ $feedback->evaluasi_keseluruhan }}</p>
            </div>
            <div class="jawaban">
                <span>Dikirim</span>
                <p>
                    {{ $feedback->dikirim_pada?->translatedFormat('d F Y H:i') }}
                    @if ($feedback->jumlah_kirim > 1)
                        · diperbarui {{ $feedback->updated_at->translatedFormat('d F Y H:i') }} ({{ $feedback->jumlah_kirim }}× dikirim)
                    @endif
                </p>
            </div>
        </section>
    @endif

</div>

@endsection
