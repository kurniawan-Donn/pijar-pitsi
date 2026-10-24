{{-- resources/views/superadmin/feedback/index.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Feedback Magang')
@section('page_title', 'Feedback Magang')
@section('page_subtitle', 'Rekap penilaian peserta terhadap program magang')

@php
    $bersedia = ($hasil['rekomendasi']['sangat_bersedia'] ?? 0) + ($hasil['rekomendasi']['bersedia'] ?? 0);
    $angka = fn (float $nilai) => number_format($nilai, 2, ',', '.');
    $judulTerbuka = config('feedback.pertanyaan_terbuka') + ['evaluasi_keseluruhan' => config('feedback.pertanyaan_evaluasi')];
@endphp

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;

        margin-bottom: 16px;
    }

    .toolbar form {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 10px;
    }

    .toolbar label {
        display: block;

        margin-bottom: 5px;

        color: #52646e;
        font-size: 12px;
        font-weight: 700;
    }

    .toolbar input[type="date"] {
        height: 40px;

        padding: 0 10px;

        border: 1px solid #d7e0eb;
        border-radius: 8px;

        font-family: inherit;
    }

    .stat-row {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;

        margin-bottom: 18px;
    }

    .grid {
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }

    .panel {
        margin-bottom: 18px;
        padding: 20px 22px;

        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel h3 {
        margin-bottom: 14px;

        color: #17324d;
        font-size: 15px;
    }

    .bar-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 140px 40px;
        align-items: center;
        gap: 10px;

        padding: 7px 0;

        border-top: 1px solid #f0f3f6;

        font-size: 12.5px;
    }

    .bar-row:first-of-type {
        border-top: none;
    }

    .track {
        height: 8px;

        border-radius: 999px;

        background: #edf1f5;

        overflow: hidden;
    }

    .track span {
        display: block;

        height: 100%;

        border-radius: 999px;

        background: linear-gradient(90deg, #1779c9, #19a974);
    }

    .bar-row strong {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    table.daftar {
        width: 100%;
        border-collapse: collapse;

        font-size: 13px;
    }

    table.daftar th {
        padding: 8px 10px;

        background: #f8fafc;
        color: #6b7c8d;

        font-size: 11px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
    }

    table.daftar td {
        padding: 9px 10px;

        border-top: 1px solid #edf1f5;
    }

    table.daftar a {
        color: #1779c9;
        font-weight: 700;
        text-decoration: none;
    }

    details.terbuka {
        padding: 10px 0;

        border-top: 1px solid #edf1f5;
    }

    details.terbuka summary {
        color: #17324d;
        font-size: 13px;
        font-weight: 700;

        cursor: pointer;
    }

    details.terbuka ul {
        margin: 10px 0 0;
        padding-left: 18px;

        color: #34495e;
        font-size: 13px;
        line-height: 1.6;
    }

    details.terbuka li {
        margin-bottom: 8px;

        white-space: pre-line;
    }

    details.terbuka small {
        color: #8a99a8;
    }

    .empty {
        padding: 24px;

        color: #8a99a8;
        text-align: center;
    }

    @media (max-width: 1000px) {
        .grid,
        .stat-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 16px 30px;
        }

        .bar-row {
            grid-template-columns: minmax(0, 1fr) 80px 36px;
        }

        .tabel-scroll {
            overflow-x: auto;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages />

    <div class="toolbar">
        <form method="GET" action="{{ route('superadmin.feedback.index') }}">
            <div>
                <label for="dari">Selesai magang dari</label>
                <input type="date" id="dari" name="dari" value="{{ $filter['dari'] ?? '' }}">
            </div>
            <div>
                <label for="sampai">sampai</label>
                <input type="date" id="sampai" name="sampai" value="{{ $filter['sampai'] ?? '' }}">
            </div>
            <x-button variant="secondary">Tampilkan</x-button>
        </form>

        <x-button variant="secondary" :href="route('superadmin.laporan.index', array_filter(['jenis' => 'feedback'] + $filter))">⬇ Export (Laporan)</x-button>
    </div>

    <div class="stat-row">
        <x-stat-card label="Feedback Terkumpul" :value="$hasil['jumlah']" icon="✎" />
        <x-stat-card label="Rata-rata Kepuasan" :value="$angka($hasil['rata_rata']).' / 5'" icon="★" hint="Rata-rata 10 pertanyaan skala" />
        <x-stat-card label="Bersedia Merekomendasikan" :value="$bersedia.' / '.$hasil['jumlah']" icon="👍" hint="Sangat bersedia + bersedia" />
    </div>

    @if ($hasil['jumlah'] === 0)
        <section class="panel empty">Belum ada feedback pada rentang ini.</section>
    @else

        <div class="grid">
            <section class="panel">
                <h3>Rata-rata per Pertanyaan</h3>
                @foreach (config('feedback.pertanyaan_skala') as $nomor => $pertanyaan)
                    <div class="bar-row">
                        <span>{{ $nomor }}. {{ $pertanyaan }}</span>
                        <div class="track"><span style="width: {{ $hasil['per_pertanyaan'][$nomor] / 5 * 100 }}%"></span></div>
                        <strong>{{ $angka($hasil['per_pertanyaan'][$nomor]) }}</strong>
                    </div>
                @endforeach
            </section>

            <section class="panel">
                <h3>Kesediaan Merekomendasikan</h3>
                @foreach (config('feedback.rekomendasi') as $kunci => $label)
                    <div class="bar-row">
                        <span>{{ $label }}</span>
                        <div class="track"><span style="width: {{ $hasil['rekomendasi'][$kunci] / max(1, $hasil['jumlah']) * 100 }}%"></span></div>
                        <strong>{{ $hasil['rekomendasi'][$kunci] }}</strong>
                    </div>
                @endforeach
            </section>
        </div>

        <section class="panel">
            <h3>Feedback per Peserta</h3>
            <div class="tabel-scroll">
                <table class="daftar">
                    <thead>
                        <tr>
                            <th>Peserta</th>
                            <th>Selesai Magang</th>
                            <th>Rata-rata</th>
                            <th>Rekomendasi</th>
                            <th>Dikirim</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftar as $feedback)
                            <tr>
                                <td>{{ $feedback->pesertaMagang->nama }}</td>
                                <td>{{ $feedback->pesertaMagang->periode_selesai?->translatedFormat('d M Y') ?? '—' }}</td>
                                <td>{{ $angka($feedback->rataRata()) }}</td>
                                <td>{{ $feedback->labelRekomendasi() }}</td>
                                <td>{{ $feedback->updated_at->translatedFormat('d M Y') }}{{ $feedback->jumlah_kirim > 1 ? ' (diperbarui)' : '' }}</td>
                                <td><a href="{{ route('superadmin.peserta-magang.feedback', $feedback->pesertaMagang) }}">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel">
            <h3>Jawaban Terbuka</h3>
            @foreach ($judulTerbuka as $kolom => $pertanyaan)
                <details class="terbuka">
                    <summary>{{ $pertanyaan }} ({{ count($hasil['terbuka'][$kolom]) }})</summary>
                    <ul>
                        @foreach ($hasil['terbuka'][$kolom] as $jawaban)
                            <li>{{ $jawaban['jawaban'] }} <small>— {{ $jawaban['nama'] }}</small></li>
                        @endforeach
                    </ul>
                </details>
            @endforeach
        </section>

    @endif

</div>

@endsection
