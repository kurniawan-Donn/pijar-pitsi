{{-- resources/views/superadmin/perusahaan/index.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Profil Perusahaan')
@section('page_title', 'Profil Perusahaan')
@section('page_subtitle', 'Kelola informasi perusahaan PILARA')

@push('styles')
<style>
    body {
        font-family: var(--font);
        background: var(--latar);
        color: var(--utama-gelap);
    }

    button,
    input,
    textarea {
        font-family: var(--font);
    }

    /* =====================================================
       CONTENT
    ====================================================== */

    .content {
        padding: 28px 30px 40px;
    }

    /* =====================================================
       PAGE HEADER
    ====================================================== */

    .page-header {
        display: flex;

        align-items: center;

        justify-content: space-between;

        margin-bottom: 18px;
    }

    .page-header-left {
        display: flex;

        align-items: center;

        gap: 13px;
    }

    .page-header-icon {
        width: 42px;

        height: 42px;

        border-radius: 9px;

        background: var(--utama-muda);

        border:
            1px solid var(--utama-muda);

        color: var(--utama);

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 17px;
    }

    .page-header h2 {
        color: var(--utama-gelap);

        font-size: 17px;

        line-height: 1.2;
    }

    .page-header p {
        margin-top: 4px;

        color: var(--teks-sekunder);

        font-size: 10px;

        line-height: 1.5;
    }

    /* =====================================================
       NOTICE
    ====================================================== */

    .notice {
        display: flex;

        align-items: flex-start;

        gap: 11px;

        margin-bottom: 18px;

        padding: 14px 16px;

        border:
            1px solid #f3dfb0;

        border-left:
            4px solid var(--peringatan);

        border-radius: 10px;

        background: linear-gradient(
            135deg,
            var(--peringatan-muda),
            var(--peringatan-muda)
        );
    }

    .notice-icon {
        width: 27px;

        height: 27px;

        min-width: 27px;

        border-radius: 7px;

        background: #fff0c7;

        color: var(--peringatan);

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 12px;

        font-weight: 700;
    }

    .notice strong {
        display: block;

        margin-bottom: 3px;

        color: var(--peringatan);

        font-size: 11px;
    }

    .notice p {
        color: var(--peringatan);

        font-size: 9px;

        line-height: 1.6;
    }

    /* =====================================================
       FORM CARD
    ====================================================== */

    .form-card {
        overflow: hidden;

        background: #ffffff;

        border:
            1px solid var(--garis);

        border-radius: 12px;

        box-shadow:
            0 4px 16px
            rgba(18, 52, 91, .05);
    }

    .form-card-header {
        display: flex;

        align-items: center;

        gap: 11px;

        padding: 17px 20px;

        border-bottom:
            1px solid var(--garis);
    }

    .form-card-icon {
        width: 34px;

        height: 34px;

        min-width: 34px;

        border-radius: 8px;

        background: var(--utama-muda);

        color: var(--utama);

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 14px;
    }

    .form-card-header h3 {
        color: var(--utama-gelap);

        font-size: 12px;

        line-height: 1.2;
    }

    .form-card-header p {
        margin-top: 3px;

        color: var(--teks-sekunder);

        font-size: 9px;
    }

    .form-body {
        padding: 20px;
    }

    /* =====================================================
       FORM GRID
    ====================================================== */

    .grid {
        display: grid;

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 16px;
    }

    .full {
        grid-column: 1 / -1;
    }

    .form-group {
        min-width: 0;
    }

    .form-group label {
        display: block;

        margin-bottom: 6px;

        color: var(--utama-gelap);

        font-size: 10px;

        font-weight: 700;
    }

    .required {
        color: var(--bahaya);
    }

    .form-group input,
    .form-group textarea {
        width: 100%;

        padding: 10px 11px;

        border:
            1px solid var(--garis-kuat);

        border-radius: 7px;

        background: #ffffff;

        color: var(--teks);

        font-size: 11px;

        transition: .2s ease;
    }

    .form-group input {
        height: 38px;
    }

    .form-group input:hover,
    .form-group textarea:hover {
        border-color: var(--garis-kuat);
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;

        border-color: var(--utama);

        box-shadow:
            0 0 0 3px
            rgba(23, 121, 201, .09);
    }

    .form-group textarea {
        min-height: 90px;

        resize: vertical;

        line-height: 1.5;
    }

    .help {
        margin-top: 5px;

        color: var(--teks-sekunder);

        font-size: 9px;

        line-height: 1.5;
    }

    /* =====================================================
       DIVIDER
    ====================================================== */

    .form-divider {
        height: 1px;

        margin:
            21px 0 18px;

        border: none;

        background: var(--garis);
    }

    /* =====================================================
       PETA LOKASI
    ===================================================== */

    .peta-pengaturan {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 18px;
        align-items: start;
    }

    .peta-centang {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        margin-bottom: 12px;

        color: var(--teks);
        font-size: 13px;
        font-weight: 600;
    }

    .peta-pratinjau iframe,
    .peta-kosong {
        width: 100%;
        height: 190px;

        border: 1px solid var(--garis);
        border-radius: 10px;
    }

    .peta-pratinjau > span {
        display: block;

        margin-top: 5px;

        color: var(--teks-sekunder);
        font-size: 11.5px;
        text-align: center;
    }

    .peta-kosong {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;

        padding: 14px;

        background: var(--permukaan-redup);
        color: var(--teks-sekunder);

        font-size: 12px;
        text-align: center;
    }

    @media (max-width: 800px) {
        .peta-pengaturan {
            grid-template-columns: 1fr;
        }
    }

    /* =====================================================
       ACTIONS
    ====================================================== */

    .actions {
        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 12px;

        margin-top: 20px;

        padding-top: 18px;

        border-top:
            1px solid var(--garis);
    }

    .action-left {
        color: var(--teks-sekunder);

        font-size: 9px;
    }

    .action-left b {
        color: var(--bahaya);
    }

    .action-buttons {
        display: flex;

        align-items: center;

        gap: 8px;
    }

    .btn {
        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 6px;

        min-height: 36px;

        padding: 0 14px;

        border: none;

        border-radius: 7px;

        cursor: pointer;

        font-size: 10px;

        font-weight: 700;

        transition: .2s ease;
    }

    .btn-secondary {
        background: var(--garis);

        color: var(--teks-sekunder);
    }

    .btn-secondary:hover {
        background: var(--garis-kuat);
    }

    .btn-primary {
        background: var(--utama);

        color: #ffffff;

        box-shadow:
            0 4px 10px
            rgba(23, 121, 201, .16);
    }

    .btn-primary:hover {
        background: var(--utama-gelap);

        transform: translateY(-1px);
    }

    /* =====================================================
       FOOT NOTE
    ====================================================== */

    .foot-note {
        margin-top: 15px;

        color: var(--teks-redup);

        font-size: 9px;

        text-align: center;
    }

    /* =====================================================
       RESPONSIVE
    ====================================================== */

    @media (max-width: 1050px) {

        .grid {
            grid-template-columns: 1fr;
        }

        .full {
            grid-column: auto;
        }

    }

    @media (max-width: 800px) {

        .content {
            padding:
                22px 18px 35px;
        }

        .page-header {
            align-items: flex-start;
        }

        .form-body {
            padding: 18px;
        }

        .actions {
            align-items: flex-start;

            flex-direction: column;
        }

        .action-buttons {
            width: 100%;
        }

        .action-buttons .btn {
            flex: 1;
        }

    }

    @media (max-width: 500px) {

        .page-header-left {
            align-items: flex-start;
        }

        .page-header-icon {
            width: 38px;

            height: 38px;

            min-width: 38px;
        }

        .page-header h2 {
            font-size: 15px;
        }

        .form-card-header {
            padding: 15px;
        }

        .form-body {
            padding: 15px;
        }

        .notice {
            padding: 12px;
        }

    }

</style>
@endpush

@section('content')

{{-- =================================================
     CONTENT
================================================== --}}

<div class="content">

    {{-- =================================================
         PAGE HEADER
    ================================================== --}}

    <div class="page-header">

        <div class="page-header-left">

            <div class="page-header-icon">
                <x-icon name="building" />
            </div>

            <div>

                <h2>
                    Informasi Perusahaan
                </h2>

                <p>
                    Informasi berikut akan digunakan
                    pada halaman website PILARA.
                </p>

            </div>

        </div>

    </div>

    {{-- =================================================
         SUCCESS
    ================================================== --}}

    <x-alert type="success" :message="session('success')" />

    {{-- =================================================
         ERROR
    ================================================== --}}

    <x-alert
        type="error"
        title="Periksa data berikut:"
        :messages="$errors->all()"
    />

    {{-- =================================================
         NOTICE
    ================================================== --}}

    <div class="notice">

        <div class="notice-icon">
            !
        </div>

        <div>

            <strong>
                Informasi pengelolaan website
            </strong>

            <p>
                Data perusahaan dapat diubah langsung
                dari halaman ini. Perubahan yang disimpan
                akan digunakan untuk menampilkan informasi
                perusahaan pada website PILARA.
            </p>

        </div>

    </div>

    {{-- =================================================
         FORM CARD
    ================================================== --}}

    <div class="form-card">

        {{-- CARD HEADER --}}

        <div class="form-card-header">

            <div class="form-card-icon">
                <x-icon name="pencil" />
            </div>

            <div>

                <h3>
                    Edit Profil Perusahaan
                </h3>

                <p>
                    Lengkapi informasi perusahaan
                    dengan data terbaru.
                </p>

            </div>

        </div>

        {{-- FORM BODY --}}

        <div class="form-body">

            <form
                action="{{ route('superadmin.perusahaan.update') }}"
                method="POST"
            >

                @csrf

                @method('PUT')

                {{-- =================================================
                     IDENTITAS
                ================================================== --}}

                <div class="grid">

                    {{-- NAMA PERUSAHAAN --}}

                    <div class="form-group full">

                        <label for="nama">

                            Nama Perusahaan

                            <span class="required">
                                *
                            </span>

                        </label>

                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            value="{{ old(
                                'nama',
                                $perusahaan?->nama ?? ''
                            ) }}"
                            placeholder="Masukkan nama perusahaan"
                            required
                        >

                    </div>

                    {{-- ALAMAT --}}

                    <div class="form-group full">

                        <label for="alamat">

                            Alamat

                            <span class="required">
                                *
                            </span>

                        </label>

                        <textarea
                            id="alamat"
                            name="alamat"
                            placeholder="Masukkan alamat lengkap perusahaan"
                            required
                        >{{ old(
                            'alamat',
                            $perusahaan?->alamat ?? ''
                        ) }}</textarea>

                        <div class="help">
                            Masukkan alamat lengkap perusahaan.
                        </div>

                    </div>

                    {{-- KOTA --}}

                    <div class="form-group">

                        <label for="kota">
                            Kota
                        </label>

                        <input
                            type="text"
                            id="kota"
                            name="kota"
                            value="{{ old(
                                'kota',
                                $perusahaan?->kota ?? ''
                            ) }}"
                            placeholder="Contoh: Jakarta"
                        >

                    </div>

                    {{-- TELEPON --}}

                    <div class="form-group">

                        <label for="telepon">
                            Nomor Telepon
                        </label>

                        <input
                            type="text"
                            id="telepon"
                            name="telepon"
                            value="{{ old(
                                'telepon',
                                $perusahaan?->telepon ?? ''
                            ) }}"
                            placeholder="Contoh: 08123456789"
                        >

                    </div>

                </div>

                {{-- DIVIDER --}}

                <hr class="form-divider">

                {{-- =================================================
                     KONTAK
                ================================================== --}}

                <div class="grid">

                    {{-- EMAIL --}}

                    <div class="form-group">

                        <label for="email">
                            Email Perusahaan
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old(
                                'email',
                                $perusahaan?->email ?? ''
                            ) }}"
                            placeholder="Contoh: info@perusahaan.com"
                        >

                    </div>

                    {{-- WEBSITE --}}

                    <div class="form-group">

                        <label for="website">
                            Website
                        </label>

                        <input
                            type="text"
                            id="website"
                            name="website"
                            value="{{ old(
                                'website',
                                $perusahaan?->website ?? ''
                            ) }}"
                            placeholder="Contoh: https://www.perusahaan.com"
                        >

                        <div class="help">
                            Kosongkan jika perusahaan tidak memiliki website.
                        </div>

                    </div>

                    {{-- DESKRIPSI --}}

                    <div class="form-group full">

                        <label for="deskripsi">
                            Deskripsi Perusahaan
                        </label>

                        <textarea
                            id="deskripsi"
                            name="deskripsi"
                            placeholder="Masukkan deskripsi singkat perusahaan"
                        >{{ old(
                            'deskripsi',
                            $perusahaan?->deskripsi ?? ''
                        ) }}</textarea>

                        <div class="help">
                            Deskripsi singkat mengenai perusahaan
                            yang dapat ditampilkan pada website PILARA.
                        </div>

                    </div>

                </div>

                {{-- DIVIDER --}}

                <hr class="form-divider">

                {{-- =================================================
                     PETA LOKASI (bagian Kontak di beranda)
                ================================================== --}}

                <div class="peta-pengaturan">

                    <div>

                        <label class="peta-centang">
                            <input type="checkbox" name="tampilkan_peta" value="1" @checked(old('tampilkan_peta', $perusahaan?->tampilkan_peta ?? true))>
                            Tampilkan peta lokasi di bagian Kontak beranda
                        </label>

                        <div class="form-group">

                            <label for="peta_embed">
                                Kode Sematan Google Maps (opsional)
                            </label>

                            <textarea
                                id="peta_embed"
                                name="peta_embed"
                                rows="3"
                                placeholder='&lt;iframe src="https://www.google.com/maps/embed?pb=..."&gt;&lt;/iframe&gt;'
                            >{{ old('peta_embed', $perusahaan?->peta_embed_url ?? '') }}</textarea>

                            <div class="help">
                                Kosongkan agar peta dibuat otomatis dari <b>alamat</b> di atas.
                                Agar titiknya tepat: buka Google Maps → cari kantor → <b>Bagikan</b> →
                                <b>Sematkan peta</b> → <b>Salin HTML</b>, lalu tempel di sini.
                            </div>

                        </div>

                    </div>

                    <div class="peta-pratinjau">
                        @if ($perusahaan?->urlPeta())
                            <iframe
                                src="{{ $perusahaan->urlPeta() }}"
                                title="Pratinjau peta lokasi"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                allowfullscreen
                            ></iframe>
                            <span>Pratinjau peta yang tampil sekarang</span>
                        @else
                            <div class="peta-kosong">
                                <x-icon name="map-pin" :size="22" />
                                {{ $perusahaan?->tampilkan_peta === false ? 'Peta sedang disembunyikan.' : 'Isi alamat lalu simpan untuk melihat pratinjau peta.' }}
                            </div>
                        @endif
                    </div>

                </div>

                {{-- =================================================
                     ACTION
                ================================================== --}}

                <div class="actions">

                    <div class="action-left">

                        <span>

                            <b>*</b>
                            Wajib diisi

                        </span>

                    </div>

                    <div class="action-buttons">

                        {{-- KEMBALI BERDASARKAN ROLE --}}

                        <a
                            href="{{ auth()->user()->role === 'super_admin'
                                ? route('superadmin.dashboard')
                                : route('admin.dashboard') }}"
                            class="btn btn-secondary"
                        >

                            ←
                            Kembali

                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >

                            <x-icon name="check" />
                            Simpan Perubahan

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    {{-- FOOT NOTE --}}

    <div class="foot-note">

        PILARA
        &nbsp;•&nbsp;
        Sistem Informasi Magang

    </div>

</div>

@endsection
