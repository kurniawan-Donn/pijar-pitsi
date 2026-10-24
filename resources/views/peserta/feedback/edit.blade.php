{{-- resources/views/peserta/feedback/edit.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Feedback Magang')
@section('page_title', 'Feedback Magang')
@section('page_subtitle', 'Penilaian Anda terhadap program magang')

@php
    $skala = config('feedback.skala');
@endphp

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .panel {
        max-width: 980px;

        margin-bottom: 18px;
        padding: 22px 24px;

        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel h3 {
        margin-bottom: 6px;

        color: #17324d;
        font-size: 15px;
    }

    .panel > p {
        margin-bottom: 14px;

        color: #7b8794;
        font-size: 12px;
        line-height: 1.6;
    }

    .terkunci {
        padding: 40px 24px;

        text-align: center;
    }

    .terkunci .ikon {
        font-size: 34px;
    }

    .terkunci strong {
        display: block;

        margin: 10px 0 6px;

        color: #17324d;
        font-size: 16px;
    }

    .terkunci p {
        color: #7b8794;
        font-size: 13px;
    }

    .skala-scroll {
        overflow-x: auto;
    }

    table.skala {
        width: 100%;
        min-width: 680px;
        border-collapse: collapse;

        font-size: 13px;
    }

    table.skala th {
        padding: 8px 6px;

        background: #f8fafc;
        color: #6b7c8d;

        font-size: 10.5px;
        font-weight: 800;
        text-align: center;
        text-transform: uppercase;
    }

    table.skala th:first-child {
        text-align: left;
    }

    table.skala td {
        padding: 10px 6px;

        border-top: 1px solid #edf1f5;

        color: #34495e;
        vertical-align: middle;
    }

    table.skala td.pilih {
        width: 74px;

        text-align: center;
    }

    table.skala input {
        width: 18px;
        height: 18px;

        accent-color: #1779c9;
        cursor: pointer;
    }

    table.skala tr.salah td {
        background: #fff6f6;
    }

    .rekomendasi {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .rekomendasi label {
        display: flex;
        align-items: center;
        gap: 8px;

        padding: 10px 14px;

        border: 1px solid #d7e0eb;
        border-radius: 10px;

        font-size: 13px;
        font-weight: 700;

        cursor: pointer;
    }

    .rekomendasi label:has(input:checked) {
        border-color: #1779c9;
        background: #eef6fd;
    }

    .rekomendasi input,
    .pernyataan input {
        accent-color: #1779c9;
    }

    .pernyataan {
        display: flex;
        align-items: flex-start;
        gap: 10px;

        font-size: 13px;
        line-height: 1.6;
        cursor: pointer;
    }

    .pernyataan input {
        flex: 0 0 18px;
        width: 18px;
        height: 18px;
        margin-top: 2px;
    }

    .actions {
        max-width: 980px;

        display: flex;
        justify-content: flex-end;
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 16px 30px;
        }

        .panel {
            padding: 18px 16px;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages :validation="false" />

    @if (! $dibuka)

        <section class="panel terkunci">
            <div class="ikon">🔒</div>
            <strong>Feedback belum dibuka</strong>
            <p>
                @if ($dibukaPada)
                    Feedback bisa diisi mulai {{ $dibukaPada->translatedFormat('l, d F Y') }}, sebulan sebelum masa magang Anda selesai.
                @else
                    Tanggal selesai magang Anda belum diatur admin.
                @endif
            </p>
        </section>

    @else

        @if ($errors->any())
            <x-alert type="error" title="Feedback belum terkirim:" :messages="$errors->all()" />
        @endif

        @if ($terkunci)
            <x-alert type="info" message="Surat keterangan Anda sudah diterbitkan, sehingga feedback tidak bisa diubah lagi. Terima kasih atas masukan Anda." />
        @elseif ($feedback->exists)
            <x-alert type="info" :message="'Feedback sudah dikirim '.$feedback->dikirim_pada?->translatedFormat('d F Y').($feedback->jumlah_kirim > 1 ? ' dan diperbarui '.$feedback->updated_at->translatedFormat('d F Y H:i') : '').'. Anda masih bisa mengubah dan mengirim ulang.'" />
        @endif

        <form method="POST" action="{{ route('peserta.feedback.update') }}">
            @csrf
            @method('PUT')

            <fieldset @disabled($terkunci) style="border: none; margin: 0; padding: 0; min-width: 0;">

            <section class="panel">
                <h3>Penilaian Program Magang</h3>
                <p>Berikan penilaian sesuai pengalaman Anda: 5 = {{ $skala[5] }} sampai 1 = {{ $skala[1] }}.</p>

                <div class="skala-scroll">
                    <table class="skala">
                        <thead>
                            <tr>
                                <th>Pertanyaan</th>
                                @foreach ($skala as $nilai => $label)
                                    <th>{{ $nilai }}<br>{{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (config('feedback.pertanyaan_skala') as $nomor => $pertanyaan)
                                @php
                                    $terpilih = (int) old("skor.{$nomor}", $feedback->skor[$nomor] ?? 0);
                                @endphp
                                <tr @class(['salah' => $errors->has("skor.{$nomor}")])>
                                    <td>{{ $nomor }}. {{ $pertanyaan }}</td>
                                    @foreach ($skala as $nilai => $label)
                                        <td class="pilih">
                                            <input type="radio" name="skor[{{ $nomor }}]" value="{{ $nilai }}" @checked($terpilih === $nilai) aria-label="Pertanyaan {{ $nomor }}: {{ $label }}" required>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel">
                <h3>Pertanyaan Terbuka</h3>
                <p>Jawab dengan jujur; masukan Anda dipakai untuk memperbaiki program magang.</p>

                @foreach (config('feedback.pertanyaan_terbuka') as $kolom => $pertanyaan)
                    <x-form.input :name="$kolom" type="textarea" :label="$pertanyaan" :value="$feedback->{$kolom}" rows="3" maxlength="2000" required />
                @endforeach

                <div class="x-form-group">
                    <span class="x-form-label">{{ config('feedback.pertanyaan_rekomendasi') }} <span class="x-form-required">*</span></span>
                    <div class="rekomendasi">
                        @foreach (config('feedback.rekomendasi') as $nilai => $label)
                            <label>
                                <input type="radio" name="rekomendasi" value="{{ $nilai }}" @checked(old('rekomendasi', $feedback->rekomendasi) === $nilai) required>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    @error('rekomendasi') <div class="x-form-error">{{ $message }}</div> @enderror
                </div>
            </section>

            <section class="panel">
                <h3>Evaluasi Keseluruhan</h3>

                <x-form.input name="evaluasi_keseluruhan" type="textarea" :label="config('feedback.pertanyaan_evaluasi')" :value="$feedback->evaluasi_keseluruhan" rows="4" maxlength="2000" required />

                <label class="pernyataan">
                    <input type="checkbox" name="pernyataan" value="1" @checked(old('pernyataan', $feedback->pernyataan)) required>
                    <span>{{ config('feedback.pernyataan') }}</span>
                </label>
                @error('pernyataan') <div class="x-form-error">{{ $message }}</div> @enderror
            </section>

            </fieldset>

            @unless ($terkunci)
                <div class="actions">
                    <x-button>{{ $feedback->exists ? 'Kirim Ulang Feedback' : 'Kirim Feedback' }}</x-button>
                </div>
            @endunless
        </form>

    @endif

</div>

@endsection
