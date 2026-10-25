{{-- resources/views/peserta/surat/show.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Surat Keterangan')
@section('page_title', 'Surat Keterangan Magang')
@section('page_subtitle', 'Diterbitkan admin setelah masa magang selesai')

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .panel {
        max-width: 720px;

        padding: 34px 28px;

        background: #ffffff;

        border: 1px solid var(--garis);
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);

        text-align: center;
    }

    .ikon {
        font-size: 38px;
    }

    .panel strong {
        display: block;

        margin: 10px 0 6px;

        color: var(--utama-gelap);
        font-size: 17px;
    }

    .panel p {
        margin-bottom: 18px;

        color: var(--teks-sekunder);
        font-size: 13px;
        line-height: 1.7;
    }

    .syarat {
        display: inline-block;

        margin: 0 0 18px;
        padding: 0;

        list-style: none;

        color: var(--teks-sekunder);
        font-size: 13px;
        text-align: left;
    }

    .syarat li {
        display: flex;
        align-items: center;
        gap: 8px;

        padding: 3px 0;
    }

    .syarat .ok .x-icon {
        color: var(--sukses);
    }

    .syarat .belum .x-icon {
        color: var(--teks-redup);
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 16px 30px;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages :validation="false" />

    <section class="panel">
        @if ($surat?->sudahTerbit())
            <div class="ikon"><x-icon name="file-check" :size="28" /></div>
            <strong>Surat keterangan sudah terbit</strong>
            <p>
                Nomor {{ $surat->nomor_surat }}, tanggal {{ $surat->tanggal_surat?->translatedFormat('d F Y') }}.
                Simpan dan lampirkan surat ini untuk keperluan kampus.
            </p>
            <x-button :href="route('peserta.surat.unduh')"><x-icon name="download" :size="16" /> Unduh Surat (PDF)</x-button>
        @else
            <div class="ikon"><x-icon name="hourglass" :size="28" /></div>
            <strong>Surat keterangan belum terbit</strong>
            <p>
                Surat disiapkan admin setelah masa magang selesai
                @if ($peserta->periode_selesai)
                    ({{ $peserta->periode_selesai->translatedFormat('d F Y') }})
                @endif
                dan semua syarat berikut terpenuhi:
            </p>
            <ul class="syarat">
                @foreach ([
                    'Biodata disetujui admin' => $peserta->biodata?->status === 'disetujui',
                    'Penilaian akhir dari pembimbing' => (bool) $peserta->penilaian,
                    'Feedback magang sudah dikirim' => (bool) $peserta->feedback,
                ] as $label => $ok)
                    <li class="{{ $ok ? 'ok' : 'belum' }}"><x-icon :name="$ok ? 'circle-check' : 'clock'" :size="16" /> {{ $label }}</li>
                @endforeach
            </ul>
        @endif
    </section>

</div>

@endsection
