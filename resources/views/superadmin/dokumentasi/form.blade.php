{{-- resources/views/superadmin/dokumentasi/form.blade.php (tambah & ubah) --}}

@extends('layouts.dashboard')

@php
    $baru = ! $dokumentasi->exists;
@endphp

@section('title', $baru ? 'Tambah Dokumentasi' : 'Ubah Dokumentasi')
@section('page_title', $baru ? 'Tambah Dokumentasi' : 'Ubah Dokumentasi')
@section('page_subtitle', 'Foto kegiatan untuk galeri di beranda website')

@push('styles')
<style>
    .panel {
        max-width: 900px;
        margin-bottom: 18px;
        padding: 22px 24px;
    }

    .panel h3 {
        margin-bottom: 14px;

        font-size: 15px;
    }

    .foto-baris {
        display: grid;
        grid-template-columns: 260px minmax(0, 1fr);
        gap: 20px;
        align-items: start;
    }

    .pratinjau {
        width: 100%;
        aspect-ratio: 4 / 3;

        display: grid;
        place-items: center;

        overflow: hidden;

        border: 1px dashed var(--garis-kuat);
        border-radius: var(--radius);

        background: var(--permukaan-redup);
        color: var(--teks-redup);

        font-size: 12.5px;
        text-align: center;

        object-fit: cover;
    }

    .fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0 16px;
    }

    .penuh {
        grid-column: 1 / -1;
    }

    .check {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        font-size: 13px;
        font-weight: 600;
    }

    .actions {
        display: flex;
        justify-content: space-between;
        gap: 10px;

        max-width: 900px;
    }

    @media (max-width: 700px) {
        .panel {
            padding: 18px 16px;
        }

        .foto-baris,
        .fields {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages />

    <form method="POST" action="{{ $baru ? route('superadmin.dokumentasi.store') : route('superadmin.dokumentasi.update', $dokumentasi) }}" enctype="multipart/form-data">
        @csrf
        @unless ($baru)
            @method('PUT')
        @endunless

        <section class="panel">
            <h3>Foto</h3>

            <div class="foto-baris">
                @if ($baru)
                    <span class="pratinjau" id="pratinjau-dokumentasi">Pratinjau foto muncul di sini</span>
                @else
                    <img src="{{ $dokumentasi->urlThumbnail() }}" alt="{{ $dokumentasi->judul }}" class="pratinjau" id="pratinjau-dokumentasi">
                @endif

                <div>
                    <x-form.input
                        name="foto"
                        type="file"
                        :label="$baru ? 'Unggah Foto' : 'Ganti Foto (opsional)'"
                        accept="image/jpeg,image/png"
                        data-pratinjau="#pratinjau-dokumentasi"
                        data-maks-mb="5"
                        :required="$baru"
                        hint="JPG/PNG maksimal 5 MB, minimal 300 × 200 piksel. Foto otomatis diperkecil dan data lokasi kamera dihapus."
                    />
                </div>
            </div>
        </section>

        <section class="panel">
            <h3>Keterangan</h3>

            <div class="fields">
                <x-form.input name="judul" label="Judul Kegiatan" :value="$dokumentasi->judul" maxlength="150" required placeholder="mis. Pembekalan peserta magang batch Agustus" />
                <x-form.input name="tanggal_kegiatan" type="date" label="Tanggal Kegiatan" :value="$dokumentasi->tanggal_kegiatan?->toDateString()" :max="now()->toDateString()" />
                <div class="penuh">
                    <x-form.input name="keterangan" type="textarea" label="Penjelasan Singkat" :value="$dokumentasi->keterangan" rows="3" maxlength="300" data-hitung hint="Tampil di atas foto saat kursor diarahkan." />
                </div>
            </div>

            <label class="check">
                <input type="checkbox" name="tampil" value="1" @checked(old('tampil', $dokumentasi->tampil))>
                Tampilkan di beranda
            </label>
        </section>

        <div class="actions">
            <x-button variant="secondary" :href="route('superadmin.dokumentasi.index')">Batal</x-button>
            <x-button>{{ $baru ? 'Simpan Dokumentasi' : 'Simpan Perubahan' }}</x-button>
        </div>
    </form>

</div>

@endsection
