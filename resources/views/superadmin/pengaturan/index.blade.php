{{-- resources/views/superadmin/pengaturan/index.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Kelola Konten')
@section('page_title', 'Kelola Konten')
@section('page_subtitle', 'Edit seluruh konten yang tampil pada halaman utama PILARA')

@push('styles')
<style>
    body {
        font-family: var(--font);

        background: var(--latar);

        color: var(--utama-gelap);
    }

    /* =====================================================
       CONTENT
    ===================================================== */

    .content {
        padding:
            28px 32px 50px;
    }

    /* =====================================================
       INTRO
    ===================================================== */

    .intro-card {
        background: #ffffff;

        border-left:
            4px solid var(--utama);

        border-radius: 12px;

        padding:
            22px 24px;

        margin-bottom: 20px;

        box-shadow:
            0 3px 14px
            rgba(24, 61, 105, .05);
    }

    .intro-line {
        width: 40px;

        height: 3px;

        background: var(--utama);

        margin-bottom: 12px;
    }

    .intro-title {
        font-size: 22px;

        font-weight: 800;

        color: var(--utama-gelap);

        margin-bottom: 7px;
    }

    .intro-description {
        font-size: 13px;

        line-height: 1.7;

        color: var(--teks-sekunder);
    }

    /* =====================================================
       SECTION CARD
    ===================================================== */

    .section-card {
        background: #ffffff;

        border-radius: 12px;

        margin-bottom: 20px;

        box-shadow:
            0 3px 15px
            rgba(24, 61, 105, .055);

        overflow: hidden;
    }

    /* =====================================================
       SECTION HEADER
    ===================================================== */

    .section-header {
        padding:
            18px 22px;

        border-bottom:
            1px solid var(--garis);

        display: flex;

        align-items: center;

        gap: 13px;
    }

    .section-number {
        width: 40px;

        height: 40px;

        min-width: 40px;

        border-radius: 9px;

        background: var(--utama-muda);

        color: var(--utama);

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 16px;

        font-weight: 800;
    }

    .section-title {
        font-size: 17px;

        font-weight: 800;

        color: var(--utama-gelap);
    }

    .section-description {
        margin-top: 4px;

        font-size: 12px;

        color: var(--teks-sekunder);
    }

    /* =====================================================
       SECTION BODY
    ===================================================== */

    .section-body {
        padding:
            22px;
    }

    /* =====================================================
       FIELD
    ===================================================== */

    .field {
        margin-bottom: 17px;
    }

    .field:last-child {
        margin-bottom: 0;
    }

    .field label {
        display: block;

        margin-bottom: 7px;

        font-size: 13px;

        font-weight: 700;

        color: var(--utama-gelap);
    }

    .required {
        color: var(--bahaya);
    }

    input,
    textarea {
        width: 100%;

        border:
            1px solid var(--garis-kuat);

        border-radius: 8px;

        background: #ffffff;

        color: var(--utama-gelap);

        font-family: var(--font);

        font-size: 13px;

        outline: none;

        transition:
            border-color .2s ease,
            box-shadow .2s ease;
    }

    input {
        height: 43px;

        padding:
            0 13px;
    }

    textarea {
        min-height: 105px;

        padding:
            11px 13px;

        resize: vertical;

        line-height: 1.65;
    }

    input::placeholder,
    textarea::placeholder {
        color: var(--teks-redup);
    }

    input:focus,
    textarea:focus {
        border-color: var(--utama);

        box-shadow:
            0 0 0 3px
            rgba(53, 121, 198, .09);
    }

    .field-help {
        margin-top: 5px;

        font-size: 11px;

        color: var(--teks-redup);
    }

    /* =====================================================
       SUB CARD
    ===================================================== */

    .sub-card {
        background: var(--permukaan-redup);

        border:
            1px solid var(--garis);

        border-radius: 10px;

        padding:
            17px;

        margin-top: 16px;
    }

    .sub-card:first-child {
        margin-top: 0;
    }

    .sub-card-title {
        display: flex;

        align-items: center;

        font-size: 13px;

        font-weight: 800;

        color: var(--utama-gelap);

        margin-bottom: 14px;
    }

    /* =====================================================
       GRID
    ===================================================== */

    .grid-2 {
        display: grid;

        grid-template-columns:
            1fr 1fr;

        gap: 18px;
    }

    /* =====================================================
       STEP NUMBER
    ===================================================== */

    .step-number {
        display: inline-flex;

        width: 28px;

        height: 28px;

        min-width: 28px;

        border-radius: 50%;

        background: var(--utama);

        color: #ffffff;

        align-items: center;

        justify-content: center;

        margin-right: 8px;

        font-size: 12px;

        font-weight: 800;
    }

    /* =====================================================
       SAVE AREA
    ===================================================== */

    .save-area {
        display: flex;

        justify-content: flex-end;

        padding-top: 2px;
    }

    .save-btn {
        min-width: 210px;

        height: 46px;

        border: 0;

        border-radius: 8px;

        background: var(--utama);

        color: #ffffff;

        font-size: 13px;

        font-weight: 700;

        cursor: pointer;

        transition:
            background .2s ease,
            transform .2s ease;
    }

    .save-btn:hover {
        background: var(--peringatan);

        transform: translateY(-1px);
    }

    /* =====================================================
       RESPONSIVE
    ===================================================== */

    @media (max-width: 1050px) {

        .content {
            padding:
                24px;
        }

    }

    @media (max-width: 850px) {

        .grid-2 {
            grid-template-columns: 1fr;
        }

    }

    @media (max-width: 760px) {

        .content {
            padding:
                20px 16px 40px;
        }

        .intro-card {
            padding:
                20px;
        }

        .intro-title {
            font-size: 20px;
        }

        .section-header {
            padding:
                17px;
        }

        .section-body {
            padding:
                17px;
        }

        .sub-card {
            padding:
                15px;
        }

        .save-area {
            justify-content: stretch;
        }

        .save-btn {
            width: 100%;
        }

    }

</style>
@endpush

@section('content')

{{-- =================================================
     CONTENT
================================================== --}}

<section class="content">

    {{-- =================================================
         INTRO
    ================================================== --}}

    <div class="intro-card">

        <div class="intro-line"></div>

        <div class="intro-title">
            Edit Konten Website
        </div>

        <div class="intro-description">

            Semua teks pada halaman utama dapat diedit
            dari halaman ini. Ubah isi yang diperlukan,
            kemudian klik <strong>Simpan Semua Perubahan</strong>.

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
        title="Ada data yang belum benar."
        :messages="$errors->all()"
    />

    {{-- =================================================
         FORM UTAMA
    ================================================== --}}

    <form
        action="{{ route('superadmin.pengaturan.update') }}"
        method="POST"
    >

        @csrf

        @method('PUT')

        {{-- =================================================
             1. INFORMASI TERBARU
        ================================================== --}}

        <div class="section-card">

            <div class="section-header">

                <div class="section-number">
                    1
                </div>

                <div>

                    <div class="section-title">
                        Informasi Terbaru
                    </div>

                    <div class="section-description">
                        Edit bagian informasi terbaru pada halaman utama.
                    </div>

                </div>

            </div>

            <div class="section-body">

                <div class="field">

                    <label for="informasi_terbaru_judul">

                        Judul

                        <span class="required">
                            *
                        </span>

                    </label>

                    <input
                        type="text"
                        id="informasi_terbaru_judul"
                        name="informasi_terbaru_judul"
                        value="{{ old(
                            'informasi_terbaru_judul',
                            $pengaturan?->informasi_terbaru_judul
                            ?? 'Informasi Terbaru'
                        ) }}"
                        required
                    >

                </div>

                <div class="field">

                    <label for="informasi_terbaru_deskripsi">
                        Deskripsi
                    </label>

                    <textarea
                        id="informasi_terbaru_deskripsi"
                        name="informasi_terbaru_deskripsi"
                        placeholder="Masukkan informasi terbaru..."
                    >{{ old(
                        'informasi_terbaru_deskripsi',
                        $pengaturan?->informasi_terbaru_deskripsi
                        ?? ''
                    ) }}</textarea>

                </div>

            </div>

        </div>

        {{-- =================================================
             2. PENGUMUMAN
        ================================================== --}}

        <div class="section-card">

            <div class="section-header">

                <div class="section-number">
                    2
                </div>

                <div>

                    <div class="section-title">
                        Pengumuman
                    </div>

                    <div class="section-description">
                        Edit pengumuman dan tiga kartu informasi.
                    </div>

                </div>

            </div>

            <div class="section-body">

                <div class="field">

                    <label for="pengumuman_judul">

                        Judul Pengumuman

                        <span class="required">
                            *
                        </span>

                    </label>

                    <input
                        type="text"
                        id="pengumuman_judul"
                        name="pengumuman_judul"
                        value="{{ old(
                            'pengumuman_judul',
                            $pengaturan?->pengumuman_judul
                            ?? 'Pengumuman'
                        ) }}"
                        required
                    >

                </div>

                <div class="field">

                    <label for="pengumuman_deskripsi">
                        Isi Pengumuman
                    </label>

                    <textarea
                        id="pengumuman_deskripsi"
                        name="pengumuman_deskripsi"
                        placeholder="Masukkan isi pengumuman..."
                    >{{ old(
                        'pengumuman_deskripsi',
                        $pengaturan?->pengumuman_deskripsi
                        ?? ''
                    ) }}</textarea>

                </div>

                {{-- CARD 1 --}}

                <div class="sub-card">

                    <div class="sub-card-title">
                        Kartu Informasi 1
                    </div>

                    <div class="grid-2">

                        <div class="field">

                            <label for="pengumuman_card1_judul">
                                Judul
                            </label>

                            <input
                                type="text"
                                id="pengumuman_card1_judul"
                                name="pengumuman_card1_judul"
                                value="{{ old(
                                    'pengumuman_card1_judul',
                                    $pengaturan?->pengumuman_card1_judul
                                    ?? 'Persiapkan Dokumen'
                                ) }}"
                                required
                            >

                        </div>

                        <div class="field">

                            <label for="pengumuman_card1_deskripsi">
                                Isi
                            </label>

                            <textarea
                                id="pengumuman_card1_deskripsi"
                                name="pengumuman_card1_deskripsi"
                                placeholder="Isi kartu informasi..."
                            >{{ old(
                                'pengumuman_card1_deskripsi',
                                $pengaturan?->pengumuman_card1_deskripsi
                                ?? ''
                            ) }}</textarea>

                        </div>

                    </div>

                </div>

                {{-- CARD 2 --}}

                <div class="sub-card">

                    <div class="sub-card-title">
                        Kartu Informasi 2
                    </div>

                    <div class="grid-2">

                        <div class="field">

                            <label for="pengumuman_card2_judul">
                                Judul
                            </label>

                            <input
                                type="text"
                                id="pengumuman_card2_judul"
                                name="pengumuman_card2_judul"
                                value="{{ old(
                                    'pengumuman_card2_judul',
                                    $pengaturan?->pengumuman_card2_judul
                                    ?? 'Pendaftaran Online'
                                ) }}"
                                required
                            >

                        </div>

                        <div class="field">

                            <label for="pengumuman_card2_deskripsi">
                                Isi
                            </label>

                            <textarea
                                id="pengumuman_card2_deskripsi"
                                name="pengumuman_card2_deskripsi"
                                placeholder="Isi kartu informasi..."
                            >{{ old(
                                'pengumuman_card2_deskripsi',
                                $pengaturan?->pengumuman_card2_deskripsi
                                ?? ''
                            ) }}</textarea>

                        </div>

                    </div>

                </div>

                {{-- CARD 3 --}}

                <div class="sub-card">

                    <div class="sub-card-title">
                        Kartu Informasi 3
                    </div>

                    <div class="grid-2">

                        <div class="field">

                            <label for="pengumuman_card3_judul">
                                Judul
                            </label>

                            <input
                                type="text"
                                id="pengumuman_card3_judul"
                                name="pengumuman_card3_judul"
                                value="{{ old(
                                    'pengumuman_card3_judul',
                                    $pengaturan?->pengumuman_card3_judul
                                    ?? 'Informasi Perusahaan'
                                ) }}"
                                required
                            >

                        </div>

                        <div class="field">

                            <label for="pengumuman_card3_deskripsi">
                                Isi
                            </label>

                            <textarea
                                id="pengumuman_card3_deskripsi"
                                name="pengumuman_card3_deskripsi"
                                placeholder="Isi kartu informasi..."
                            >{{ old(
                                'pengumuman_card3_deskripsi',
                                $pengaturan?->pengumuman_card3_deskripsi
                                ?? ''
                            ) }}</textarea>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- =================================================
             3. PERSYARATAN MAGANG
        ================================================== --}}

        <div class="section-card">

            <div class="section-header">

                <div class="section-number">
                    3
                </div>

                <div>

                    <div class="section-title">
                        Persyaratan Magang
                    </div>

                    <div class="section-description">
                        Edit judul, deskripsi, dan tiga persyaratan.
                    </div>

                </div>

            </div>

            <div class="section-body">

                <div class="field">

                    <label for="persyaratan_judul">

                        Judul

                        <span class="required">
                            *
                        </span>

                    </label>

                    <input
                        type="text"
                        id="persyaratan_judul"
                        name="persyaratan_judul"
                        value="{{ old(
                            'persyaratan_judul',
                            $pengaturan?->persyaratan_judul
                            ?? 'Persyaratan Magang'
                        ) }}"
                        required
                    >

                </div>

                <div class="field">

                    <label for="persyaratan_deskripsi">
                        Deskripsi
                    </label>

                    <textarea
                        id="persyaratan_deskripsi"
                        name="persyaratan_deskripsi"
                        placeholder="Masukkan deskripsi persyaratan..."
                    >{{ old(
                        'persyaratan_deskripsi',
                        $pengaturan?->persyaratan_deskripsi
                        ?? ''
                    ) }}</textarea>

                </div>

                {{-- PERSYARATAN 1 --}}

                <div class="sub-card">

                    <div class="sub-card-title">
                        Persyaratan 1
                    </div>

                    <div class="grid-2">

                        <div class="field">

                            <label for="persyaratan1_judul">
                                Judul
                            </label>

                            <input
                                type="text"
                                id="persyaratan1_judul"
                                name="persyaratan1_judul"
                                value="{{ old(
                                    'persyaratan1_judul',
                                    $pengaturan?->persyaratan1_judul
                                    ?? 'Surat Pengantar'
                                ) }}"
                                required
                            >

                        </div>

                        <div class="field">

                            <label for="persyaratan1_deskripsi">
                                Isi
                            </label>

                            <textarea
                                id="persyaratan1_deskripsi"
                                name="persyaratan1_deskripsi"
                                placeholder="Isi persyaratan..."
                            >{{ old(
                                'persyaratan1_deskripsi',
                                $pengaturan?->persyaratan1_deskripsi
                                ?? ''
                            ) }}</textarea>

                        </div>

                    </div>

                </div>

                {{-- PERSYARATAN 2 --}}

                <div class="sub-card">

                    <div class="sub-card-title">
                        Persyaratan 2
                    </div>

                    <div class="grid-2">

                        <div class="field">

                            <label for="persyaratan2_judul">
                                Judul
                            </label>

                            <input
                                type="text"
                                id="persyaratan2_judul"
                                name="persyaratan2_judul"
                                value="{{ old(
                                    'persyaratan2_judul',
                                    $pengaturan?->persyaratan2_judul
                                    ?? 'Data Mahasiswa'
                                ) }}"
                                required
                            >

                        </div>

                        <div class="field">

                            <label for="persyaratan2_deskripsi">
                                Isi
                            </label>

                            <textarea
                                id="persyaratan2_deskripsi"
                                name="persyaratan2_deskripsi"
                                placeholder="Isi persyaratan..."
                            >{{ old(
                                'persyaratan2_deskripsi',
                                $pengaturan?->persyaratan2_deskripsi
                                ?? ''
                            ) }}</textarea>

                        </div>

                    </div>

                </div>

                {{-- PERSYARATAN 3 --}}

                <div class="sub-card">

                    <div class="sub-card-title">
                        Persyaratan 3
                    </div>

                    <div class="grid-2">

                        <div class="field">

                            <label for="persyaratan3_judul">
                                Judul
                            </label>

                            <input
                                type="text"
                                id="persyaratan3_judul"
                                name="persyaratan3_judul"
                                value="{{ old(
                                    'persyaratan3_judul',
                                    $pengaturan?->persyaratan3_judul
                                    ?? 'Dokumen Pendukung'
                                ) }}"
                                required
                            >

                        </div>

                        <div class="field">

                            <label for="persyaratan3_deskripsi">
                                Isi
                            </label>

                            <textarea
                                id="persyaratan3_deskripsi"
                                name="persyaratan3_deskripsi"
                                placeholder="Isi persyaratan..."
                            >{{ old(
                                'persyaratan3_deskripsi',
                                $pengaturan?->persyaratan3_deskripsi
                                ?? ''
                            ) }}</textarea>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- =================================================
             4. ALUR MAGANG
        ================================================== --}}

        <div class="section-card">

            <div class="section-header">

                <div class="section-number">
                    4
                </div>

                <div>

                    <div class="section-title">
                        Alur Magang
                    </div>

                    <div class="section-description">
                        Edit judul, deskripsi, dan empat tahapan alur magang.
                    </div>

                </div>

            </div>

            <div class="section-body">

                <div class="field">

                    <label for="alur_judul">

                        Judul

                        <span class="required">
                            *
                        </span>

                    </label>

                    <input
                        type="text"
                        id="alur_judul"
                        name="alur_judul"
                        value="{{ old(
                            'alur_judul',
                            $pengaturan?->alur_judul
                            ?? 'Alur Magang'
                        ) }}"
                        required
                    >

                </div>

                <div class="field">

                    <label for="alur_deskripsi">
                        Deskripsi
                    </label>

                    <textarea
                        id="alur_deskripsi"
                        name="alur_deskripsi"
                        placeholder="Masukkan deskripsi alur magang..."
                    >{{ old(
                        'alur_deskripsi',
                        $pengaturan?->alur_deskripsi
                        ?? ''
                    ) }}</textarea>

                </div>

                {{-- LANGKAH 1 --}}

                <div class="sub-card">

                    <div class="sub-card-title">

                        <span class="step-number">
                            1
                        </span>

                        Langkah Pertama

                    </div>

                    <div class="grid-2">

                        <div class="field">

                            <label for="alur1_judul">
                                Judul
                            </label>

                            <input
                                type="text"
                                id="alur1_judul"
                                name="alur1_judul"
                                value="{{ old(
                                    'alur1_judul',
                                    $pengaturan?->alur1_judul
                                    ?? 'Persiapkan'
                                ) }}"
                                required
                            >

                        </div>

                        <div class="field">

                            <label for="alur1_deskripsi">
                                Isi
                            </label>

                            <textarea
                                id="alur1_deskripsi"
                                name="alur1_deskripsi"
                                placeholder="Isi langkah pertama..."
                            >{{ old(
                                'alur1_deskripsi',
                                $pengaturan?->alur1_deskripsi
                                ?? ''
                            ) }}</textarea>

                        </div>

                    </div>

                </div>

                {{-- LANGKAH 2 --}}

                <div class="sub-card">

                    <div class="sub-card-title">

                        <span class="step-number">
                            2
                        </span>

                        Langkah Kedua

                    </div>

                    <div class="grid-2">

                        <div class="field">

                            <label for="alur2_judul">
                                Judul
                            </label>

                            <input
                                type="text"
                                id="alur2_judul"
                                name="alur2_judul"
                                value="{{ old(
                                    'alur2_judul',
                                    $pengaturan?->alur2_judul
                                    ?? 'Daftar'
                                ) }}"
                                required
                            >

                        </div>

                        <div class="field">

                            <label for="alur2_deskripsi">
                                Isi
                            </label>

                            <textarea
                                id="alur2_deskripsi"
                                name="alur2_deskripsi"
                                placeholder="Isi langkah kedua..."
                            >{{ old(
                                'alur2_deskripsi',
                                $pengaturan?->alur2_deskripsi
                                ?? ''
                            ) }}</textarea>

                        </div>

                    </div>

                </div>

                {{-- LANGKAH 3 --}}

                <div class="sub-card">

                    <div class="sub-card-title">

                        <span class="step-number">
                            3
                        </span>

                        Langkah Ketiga

                    </div>

                    <div class="grid-2">

                        <div class="field">

                            <label for="alur3_judul">
                                Judul
                            </label>

                            <input
                                type="text"
                                id="alur3_judul"
                                name="alur3_judul"
                                value="{{ old(
                                    'alur3_judul',
                                    $pengaturan?->alur3_judul
                                    ?? 'Verifikasi'
                                ) }}"
                                required
                            >

                        </div>

                        <div class="field">

                            <label for="alur3_deskripsi">
                                Isi
                            </label>

                            <textarea
                                id="alur3_deskripsi"
                                name="alur3_deskripsi"
                                placeholder="Isi langkah ketiga..."
                            >{{ old(
                                'alur3_deskripsi',
                                $pengaturan?->alur3_deskripsi
                                ?? ''
                            ) }}</textarea>

                        </div>

                    </div>

                </div>

                {{-- LANGKAH 4 --}}

                <div class="sub-card">

                    <div class="sub-card-title">

                        <span class="step-number">
                            4
                        </span>

                        Langkah Keempat

                    </div>

                    <div class="grid-2">

                        <div class="field">

                            <label for="alur4_judul">
                                Judul
                            </label>

                            <input
                                type="text"
                                id="alur4_judul"
                                name="alur4_judul"
                                value="{{ old(
                                    'alur4_judul',
                                    $pengaturan?->alur4_judul
                                    ?? 'Pelaksanaan'
                                ) }}"
                                required
                            >

                        </div>

                        <div class="field">

                            <label for="alur4_deskripsi">
                                Isi
                            </label>

                            <textarea
                                id="alur4_deskripsi"
                                name="alur4_deskripsi"
                                placeholder="Isi langkah keempat..."
                            >{{ old(
                                'alur4_deskripsi',
                                $pengaturan?->alur4_deskripsi
                                ?? ''
                            ) }}</textarea>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- =================================================
             5. PENDAFTARAN GOOGLE FORM
        ================================================== --}}

        <div class="section-card">

            <div class="section-header">

                <div class="section-number">
                    5
                </div>

                <div>

                    <div class="section-title">
                        Pendaftaran Google Form
                    </div>

                    <div class="section-description">
                        Atur link Google Form yang digunakan mahasiswa
                        untuk melakukan pendaftaran magang.
                    </div>

                </div>

            </div>

            <div class="section-body">

                <div class="field">

                    <label for="google_form_url">
                        Link Google Form Pendaftaran
                    </label>

                    <input
                        type="url"
                        id="google_form_url"
                        name="google_form_url"
                        value="{{ old(
                            'google_form_url',
                            $pengaturan?->google_form_url ?? ''
                        ) }}"
                        placeholder="https://docs.google.com/forms/..."
                    >

                    @error('google_form_url')
                        <div class="field-help" style="color:var(--bahaya);">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="field-help">
                        Masukkan URL Google Form resmi yang digunakan untuk
                        pendaftaran mahasiswa magang.
                    </div>

                </div>

            </div>

        </div>

                        {{-- =================================================
             SAVE
        ================================================== --}}

        <div class="save-area">

            <button
                type="submit"
                class="save-btn"
            >
                Simpan Semua Perubahan
            </button>

        </div>

    </form>

</section>

@endsection
