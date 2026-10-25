{{-- resources/views/superadmin/alumni/form.blade.php (tambah & ubah) --}}

@extends('layouts.dashboard')

@php
    $baru = ! $alumni->exists;
    $dariPeserta = $alumni->peserta_magang_id !== null;
    $rating = $alumni->nilaiRating();
@endphp

@section('title', $baru ? 'Tambah Alumni' : 'Ubah Alumni')
@section('page_title', $baru ? 'Tambah Alumni' : 'Ubah Alumni')
@section('page_subtitle', $dariPeserta ? 'Dari data peserta magang' : 'Input manual untuk alumni sebelum sistem ini ada')

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .panel {
        max-width: 900px;
        margin-bottom: 18px;
        padding: 22px 24px;

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

    .fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0 16px;
    }

    .penuh {
        grid-column: 1 / -1;
    }

    .info-peserta {
        display: flex;
        align-items: center;
        gap: 14px;

        margin-bottom: 18px;
        padding: 12px 14px;

        border-radius: 10px;

        background: #f4f8fc;

        color: #34495e;
        font-size: 13px;
        line-height: 1.6;
    }

    .info-peserta img,
    .foto-sekarang {
        width: 64px;
        height: 64px;

        border-radius: 12px;

        object-fit: cover;
    }

    .bintang {
        color: #f5a524;
    }

    .check {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        color: #34495e;
        font-size: 13px;
        font-weight: 600;
    }

    .actions {
        display: flex;
        justify-content: space-between;
        gap: 10px;

        max-width: 900px;
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 16px 30px;
        }

        .fields {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages />

    <form method="POST" action="{{ $baru ? route('superadmin.alumni.store') : route('superadmin.alumni.update', $alumni) }}" enctype="multipart/form-data">
        @csrf
        @unless ($baru)
            @method('PUT')
        @endunless

        @if ($baru && $dariPeserta)
            <input type="hidden" name="peserta_magang_id" value="{{ $alumni->peserta_magang_id }}">
        @endif

        <section class="panel">
            <h3>Data Alumni</h3>

            @if ($dariPeserta)
                <div class="info-peserta">
                    @if ($alumni->urlFoto())
                        <img src="{{ $alumni->urlFoto() }}" alt="">
                    @elseif ($alumni->pesertaMagang?->user)
                        <x-avatar :user="$alumni->pesertaMagang->user" :nama="$alumni->nama" :ukuran="64" style="border-radius: 12px;" />
                    @endif
                    <div>
                        Dari peserta <strong>{{ $alumni->pesertaMagang?->nama }}</strong> ({{ $alumni->pesertaMagang?->nomor_peserta }}).
                        Foto memakai foto profil peserta dan hanya disalin ke website selama alumni ditampilkan.<br>
                        Rating dari feedback:
                        @if ($rating)
                            <span class="bintang">{{ str_repeat('★', $rating) }}{{ str_repeat('☆', 5 - $rating) }}</span> ({{ $rating }}/5)
                        @else
                            belum ada
                        @endif
                        @unless ($alumni->disetujuiPeserta())
                            <br><strong style="color: #b54708;">Peserta menarik persetujuan tampil sebagai alumni; alumni tidak akan tampil di website.</strong>
                        @endunless
                    </div>
                </div>
            @endif

            <div class="fields">
                <x-form.input name="nama" label="Nama" :value="$alumni->nama" maxlength="255" required />
                <x-form.input name="universitas" label="Asal Universitas" :value="$alumni->universitas" maxlength="255" required />
                <x-form.input name="program_studi" label="Program Studi" :value="$alumni->program_studi" maxlength="255" />
                <x-form.input name="divisi" label="Divisi Penempatan" :value="$alumni->divisi" maxlength="255" />
                <x-form.input name="periode" label="Periode Magang" :value="$alumni->periode" maxlength="100" placeholder="mis. Juni – November 2026" />

                @unless ($dariPeserta)
                    <div class="x-form-group">
                        <label for="rating" class="x-form-label">Rating (bintang)</label>
                        <select id="rating" name="rating" class="x-form-control">
                            <option value="">Tanpa rating</option>
                            @foreach ([5, 4, 3, 2, 1] as $nilai)
                                <option value="{{ $nilai }}" @selected((int) old('rating', $alumni->rating) === $nilai)>{{ str_repeat('★', $nilai) }} ({{ $nilai }})</option>
                            @endforeach
                        </select>
                        @error('rating') <div class="x-form-error">{{ $message }}</div> @enderror
                    </div>
                @endunless
            </div>
        </section>

        <section class="panel">
            <h3>Project &amp; Testimoni</h3>

            <div class="fields">
                <div class="penuh">
                    <x-form.input name="judul_project" label="Judul Project Singkat" :value="$alumni->judul_project" maxlength="150" required hint="Tampil di kartu alumni." />
                </div>
                <div class="penuh">
                    <x-form.input name="deskripsi_project" type="textarea" label="Deskripsi Project" :value="$alumni->deskripsi_project" rows="5" maxlength="3000" required hint="Tampil di popup saat kartu diklik." />
                </div>
                <div class="penuh">
                    <x-form.input name="testimoni" type="textarea" label="Testimoni" :value="$alumni->testimoni" rows="3" maxlength="1500" :hint="$dariPeserta ? 'Terisi dari jawaban feedback “hal yang paling disukai”; boleh dirapikan.' : null" />
                </div>
            </div>
        </section>

        @unless ($dariPeserta)
            <section class="panel">
                <h3>Foto</h3>

                <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                    @if ($alumni->urlFoto())
                        <img src="{{ $alumni->urlFoto() }}" alt="" class="foto-sekarang">
                    @endif
                    <div>
                        <x-form.input name="foto" type="file" label="Unggah Foto" accept="image/jpeg,image/png" hint="JPG/PNG maksimal 2 MB, otomatis dipotong persegi." />
                        @if ($alumni->urlFoto())
                            <label class="check"><input type="checkbox" name="hapus_foto" value="1"> Hapus foto</label>
                        @endif
                    </div>
                </div>
            </section>
        @endunless

        <section class="panel">
            <label class="check">
                <input type="checkbox" name="tampil" value="1" @checked(old('tampil', $alumni->tampil))>
                Tampilkan di website
            </label>
        </section>

        <div class="actions">
            <x-button variant="secondary" :href="route('superadmin.alumni.index')">Batal</x-button>
            <x-button>{{ $baru ? 'Simpan Alumni' : 'Simpan Perubahan' }}</x-button>
        </div>
    </form>

</div>

@endsection
