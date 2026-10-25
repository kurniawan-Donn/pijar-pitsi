{{-- resources/views/superadmin/dokumentasi/index.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Dokumentasi')
@section('page_title', 'Dokumentasi Kegiatan')
@section('page_subtitle', 'Galeri foto kegiatan yang tampil di beranda website')

@push('styles')
<style>
    .panel {
        max-width: 1100px;
        padding: 22px 24px;
    }

    .panel-kepala {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;

        margin-bottom: 18px;
    }

    .panel-kepala h3 {
        margin-bottom: 4px;

        font-size: 15px;
    }

    .panel-kepala p {
        max-width: 620px;

        color: var(--teks-sekunder);
        font-size: 13px;
        line-height: 1.6;
    }

    .grid-dok {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
    }

    .kartu-dok {
        display: flex;
        flex-direction: column;

        overflow: hidden;

        border: 1px solid var(--garis);
        border-radius: var(--radius);

        background: var(--permukaan);

        scroll-margin-top: 90px;
    }

    .kartu-dok.redup .kartu-foto img {
        filter: grayscale(.8);
        opacity: .55;
    }

    .kartu-foto {
        position: relative;

        aspect-ratio: 16 / 10;

        background: var(--permukaan-redup);
    }

    .kartu-foto img {
        width: 100%;
        height: 100%;

        display: block;

        object-fit: cover;
    }

    .kartu-foto .nomor {
        position: absolute;
        top: 8px;
        left: 8px;

        min-width: 26px;
        height: 26px;
        padding: 0 7px;

        display: grid;
        place-items: center;

        border-radius: 999px;

        background: rgba(17, 24, 39, .65);
        color: #ffffff;

        font-size: 12px;
        font-weight: 700;
    }

    .kartu-isi {
        flex: 1;

        padding: 12px 14px 10px;
    }

    .kartu-isi strong {
        display: block;

        color: var(--teks);
        font-size: 14px;
        line-height: 1.4;
    }

    .kartu-isi .tanggal {
        display: flex;
        align-items: center;
        gap: 5px;

        margin-top: 4px;

        color: var(--teks-sekunder);
        font-size: 12px;
    }

    .kartu-isi p {
        margin-top: 6px;

        color: var(--teks-sekunder);
        font-size: 12.5px;
        line-height: 1.5;
    }

    .kartu-aksi {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;

        padding: 10px 14px 14px;

        border-top: 1px solid var(--garis);
    }

    .kartu-aksi form {
        margin: 0;
    }

    .tombol-ikon {
        width: 32px;
        height: 32px;

        display: inline-grid;
        place-items: center;

        border: 1px solid var(--garis-kuat);
        border-radius: var(--radius-kecil);

        background: var(--permukaan);
        color: var(--teks-sekunder);

        cursor: pointer;
    }

    .tombol-ikon:hover:not(:disabled) {
        background: var(--permukaan-redup);
        color: var(--teks);
    }

    .tombol-ikon:disabled {
        opacity: .35;
        cursor: default;
    }

    .tombol-ikon.bahaya {
        border-color: #fecdca;
        color: var(--bahaya);
    }

    .kartu-aksi .x-btn {
        min-width: 0;
        height: 32px;
        padding: 0 12px;
    }

    .kartu-aksi .dorong {
        margin-left: auto;
    }

    @media (max-width: 600px) {
        .panel {
            padding: 18px 16px;
        }

        .grid-dok {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages />

    <section class="panel">
        <div class="panel-kepala">
            <div>
                <h3>Galeri Dokumentasi ({{ $dokumentasis->count() }})</h3>
                <p>
                    Urutan di bawah sama dengan urutan di beranda: foto tersusun 2 baris ke bawah lalu memanjang ke kanan.
                    Judul, tanggal, dan keterangan muncul saat kursor diarahkan ke foto (di HP saat disentuh).
                </p>
            </div>

            <x-button :href="route('superadmin.dokumentasi.create')"><x-icon name="plus" :size="16" /> Tambah Dokumentasi</x-button>
        </div>

        @if ($dokumentasis->isEmpty())
            <x-empty icon="images" title="Belum ada dokumentasi" message="Tambahkan foto kegiatan magang agar tampil di beranda website." />
        @else
            <div class="grid-dok">
                @foreach ($dokumentasis as $dokumentasi)
                    <article @class(['kartu-dok', 'redup' => ! $dokumentasi->tampil]) id="dokumentasi-{{ $dokumentasi->id }}">
                        <div class="kartu-foto">
                            <img src="{{ $dokumentasi->urlThumbnail() }}" alt="{{ $dokumentasi->judul }}" loading="lazy">
                            <span class="nomor">{{ $loop->iteration }}</span>
                        </div>

                        <div class="kartu-isi">
                            <strong>{{ $dokumentasi->judul }}</strong>

                            @if ($dokumentasi->labelTanggal())
                                <span class="tanggal"><x-icon name="calendar" :size="13" /> {{ $dokumentasi->labelTanggal() }}</span>
                            @endif

                            @if ($dokumentasi->keterangan)
                                <p>{{ \Illuminate\Support\Str::limit($dokumentasi->keterangan, 110) }}</p>
                            @endif
                        </div>

                        <div class="kartu-aksi">
                            <form method="POST" action="{{ route('superadmin.dokumentasi.urutan', $dokumentasi) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="arah" value="naik">
                                <button type="submit" class="tombol-ikon" title="Geser ke depan" aria-label="Geser ke depan" @disabled($loop->first)><x-icon name="chevron-left" :size="16" /></button>
                            </form>
                            <form method="POST" action="{{ route('superadmin.dokumentasi.urutan', $dokumentasi) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="arah" value="turun">
                                <button type="submit" class="tombol-ikon" title="Geser ke belakang" aria-label="Geser ke belakang" @disabled($loop->last)><x-icon name="chevron-right" :size="16" /></button>
                            </form>

                            <form method="POST" action="{{ route('superadmin.dokumentasi.tampil', $dokumentasi) }}">
                                @csrf
                                @method('PUT')
                                <x-status-badge :status="$dokumentasi->tampil ? 'aktif' : 'nonaktif'" :label="$dokumentasi->tampil ? 'Tampil' : 'Disembunyikan'" />
                                <button type="submit" class="tombol-ikon" title="{{ $dokumentasi->tampil ? 'Sembunyikan dari beranda' : 'Tampilkan di beranda' }}" aria-label="{{ $dokumentasi->tampil ? 'Sembunyikan dari beranda' : 'Tampilkan di beranda' }}">
                                    <x-icon :name="$dokumentasi->tampil ? 'eye' : 'eye-off'" :size="16" />
                                </button>
                            </form>

                            <x-button variant="secondary" :href="route('superadmin.dokumentasi.edit', $dokumentasi)" class="dorong"><x-icon name="pencil" :size="14" /> Ubah</x-button>

                            <form method="POST" action="{{ route('superadmin.dokumentasi.destroy', $dokumentasi) }}" onsubmit="return confirm('Hapus dokumentasi ini beserta fotonya?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="tombol-ikon bahaya" title="Hapus" aria-label="Hapus dokumentasi"><x-icon name="trash" :size="16" /></button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

</div>

@endsection
