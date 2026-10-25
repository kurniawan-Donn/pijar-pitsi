{{-- resources/views/superadmin/alumni/index.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Alumni')
@section('page_title', 'Alumni Magang')
@section('page_subtitle', 'Kartu alumni yang tampil di website publik')

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .panel {
        max-width: 1000px;
        margin-bottom: 18px;
        padding: 22px 24px;

        background: #ffffff;

        border: 1px solid var(--garis);
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel h3 {
        margin-bottom: 4px;

        color: var(--utama-gelap);
        font-size: 15px;
    }

    .panel > p {
        margin-bottom: 14px;

        color: var(--teks-sekunder);
        font-size: 13px;
        line-height: 1.6;
    }

    .tambah {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 12px;
    }

    .tambah label {
        display: block;

        margin-bottom: 6px;

        color: var(--utama-gelap);
        font-size: 12px;
        font-weight: 700;
    }

    .tambah select {
        min-width: 300px;
        height: 40px;

        padding: 0 10px;

        border: 1px solid var(--garis-kuat);
        border-radius: 8px;

        background: #ffffff;

        font-family: inherit;
        font-size: 13px;
    }

    .atau {
        color: var(--teks-sekunder);
        font-size: 12px;
        line-height: 40px;
    }

    .daftar {
        display: flex;
        flex-direction: column;
    }

    .baris {
        display: flex;
        align-items: center;
        gap: 14px;

        padding: 12px 0;

        border-top: 1px solid var(--garis);
    }

    .baris:first-child {
        border-top: none;
    }

    .baris.redup {
        opacity: .6;
    }

    .foto {
        flex-shrink: 0;

        width: 48px;
        height: 48px;

        border-radius: 12px;

        object-fit: cover;
    }

    .foto-kosong {
        display: grid;
        place-items: center;

        background: var(--utama-muda);
        color: var(--utama);

        font-weight: 800;
    }

    .isi {
        flex: 1;
        min-width: 0;
    }

    .isi strong {
        color: var(--utama-gelap);
        font-size: 14px;
    }

    .isi span {
        display: block;

        color: var(--teks-sekunder);
        font-size: 12px;
    }

    .bintang {
        color: var(--peringatan);
        letter-spacing: 1px;
    }

    .badge {
        display: inline-block;

        padding: 2px 8px;

        border-radius: 999px;

        font-size: 10.5px;
        font-weight: 800;
        white-space: nowrap;
    }

    .b-hijau { background: var(--sukses-muda); color: var(--sukses); }
    .b-oranye { background: var(--peringatan-muda); color: var(--peringatan); }
    .b-abu { background: var(--garis); color: var(--teks-sekunder); }
    .b-biru { background: var(--utama-muda); color: var(--utama-gelap); }

    .aksi {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .aksi form {
        margin: 0;
    }

    .geser {
        width: 30px;
        height: 30px;

        border: 1px solid var(--garis-kuat);
        border-radius: 7px;

        background: #ffffff;
        color: var(--teks-sekunder);

        cursor: pointer;
    }

    .geser:disabled {
        opacity: .35;
        cursor: default;
    }

    .x-btn {
        min-width: 0;
        min-height: 34px;
    }

    .empty {
        padding: 24px;

        color: var(--teks-redup);
        text-align: center;
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 16px 30px;
        }

        .baris {
            flex-wrap: wrap;
        }

        .tambah select {
            min-width: 0;
            width: 100%;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages />

    {{-- TAMBAH --}}
    <section class="panel">
        <h3>Tambah Alumni</h3>
        <p>
            Pilih peserta yang sudah selesai dan mencentang "bersedia ditampilkan sebagai alumni" di feedback.
            Data terisi otomatis; lengkapi judul dan deskripsi project. Rating diambil dari feedback peserta
            (kepuasan keseluruhan), bukan dari nilai penilaian.
        </p>

        <div class="tambah">
            <form method="GET" action="{{ route('superadmin.alumni.create') }}" class="tambah">
                <div>
                    <label for="peserta">Dari peserta ({{ $pesertaBersedia->count() }} bersedia)</label>
                    <select id="peserta" name="peserta" required @disabled($pesertaBersedia->isEmpty())>
                        @forelse ($pesertaBersedia as $peserta)
                            <option value="{{ $peserta->id }}">{{ $peserta->nama }}{{ $peserta->asal_kampus ? ' · '.$peserta->asal_kampus : '' }}</option>
                        @empty
                            <option value="">Belum ada peserta yang bersedia</option>
                        @endforelse
                    </select>
                </div>
                <x-button :disabled="$pesertaBersedia->isEmpty()">Pilih &amp; Lengkapi</x-button>
            </form>

            <span class="atau">atau</span>

            <x-button variant="secondary" :href="route('superadmin.alumni.create')">+ Input Manual (alumni lama)</x-button>
        </div>
    </section>

    {{-- DAFTAR --}}
    <section class="panel">
        <h3>Daftar Alumni ({{ $alumnis->count() }})</h3>
        <p>Urutan di bawah sama dengan urutan kartu di website. Gunakan tombol panah naik/turun untuk menggeser.</p>

        @if ($alumnis->isEmpty())
            <div class="empty">Belum ada alumni.</div>
        @else
            <div class="daftar">
                @foreach ($alumnis as $alumni)
                    @php
                        $rating = $alumni->nilaiRating();
                    @endphp

                    <div class="baris {{ $alumni->tampilDiWebsite() ? '' : 'redup' }}" id="alumni-{{ $alumni->id }}">
                        @if ($alumni->urlFoto())
                            <img src="{{ $alumni->urlFoto() }}" alt="" class="foto">
                        @else
                            <span class="foto foto-kosong">{{ mb_strtoupper(mb_substr($alumni->nama, 0, 1)) }}</span>
                        @endif

                        <div class="isi">
                            <strong>{{ $alumni->nama }}</strong>
                            @if ($rating)
                                <span class="bintang" title="Rating {{ $rating }}/5" style="display: inline;">{{ str_repeat('★', $rating) }}{{ str_repeat('☆', 5 - $rating) }}</span>
                            @endif
                            <span>{{ $alumni->judul_project }}</span>
                            <span>{{ $alumni->universitas }}{{ $alumni->divisi ? ' · '.$alumni->divisi : '' }}{{ $alumni->periode ? ' · '.$alumni->periode : '' }}</span>
                        </div>

                        <span class="badge {{ $alumni->dariPeserta() ? 'b-biru' : 'b-abu' }}">{{ $alumni->dariPeserta() ? 'Dari peserta' : 'Manual' }}</span>

                        @if ($alumni->tampilDiWebsite())
                            <span class="badge b-hijau">Tampil</span>
                        @elseif (! $alumni->disetujuiPeserta())
                            <span class="badge b-oranye" title="Peserta membatalkan persetujuan di feedback">Persetujuan ditarik</span>
                        @else
                            <span class="badge b-abu">Disembunyikan</span>
                        @endif

                        <div class="aksi">
                            <form method="POST" action="{{ route('superadmin.alumni.urutan', $alumni) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="arah" value="naik">
                                <button type="submit" class="geser" title="Naikkan" aria-label="Naikkan urutan" @disabled($loop->first)><x-icon name="chevron-up" :size="16" /></button>
                            </form>
                            <form method="POST" action="{{ route('superadmin.alumni.urutan', $alumni) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="arah" value="turun">
                                <button type="submit" class="geser" title="Turunkan" aria-label="Turunkan urutan" @disabled($loop->last)><x-icon name="chevron-down" :size="16" /></button>
                            </form>
                            <x-button variant="secondary" :href="route('superadmin.alumni.edit', $alumni)">Ubah</x-button>
                            <form method="POST" action="{{ route('superadmin.alumni.destroy', $alumni) }}" onsubmit="return confirm('Hapus alumni ini dari website?')">
                                @csrf
                                @method('DELETE')
                                <x-button variant="danger">Hapus</x-button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

</div>

@endsection
