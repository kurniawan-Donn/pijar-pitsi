{{-- resources/views/superadmin/peserta-magang/edit.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Edit Peserta Magang')
@section('page_title', 'Edit Peserta Magang')
@section('page_subtitle', 'Perbarui informasi peserta dan tautan Google Sheets absensi.')

@push('styles')
<style>
    body {
        font-family: var(--font);
        background: var(--latar);
        color: var(--teks);
        min-height: 100vh;
    }

    button,
    input,
    select {
        font-family: inherit;
    }

    /* =====================================================
       CONTENT
    ====================================================== */

    .content {

        padding: 32px;
    }

    .content-header {

        display: flex;

        align-items: flex-start;

        justify-content: space-between;

        gap: 20px;

        margin-bottom: 25px;
    }

    .heading {

        font-size: 17px;

        color: var(--teks);

        font-weight: 800;
    }

    .heading-description {

        color: var(--teks-sekunder);

        font-size: 13px;

        margin-top: 7px;
    }

    /* =====================================================
       BUTTON
    ====================================================== */

    .btn {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 8px;

        min-height: 42px;

        padding:
            0 17px;

        border-radius: 8px;

        border: none;

        font-size: 13px;

        font-weight: 700;

        cursor: pointer;

        transition: 0.2s ease;
    }

    .btn-primary {

        background: var(--utama);

        color: white;
    }

    .btn-primary:hover {

        background: var(--utama-gelap);

        transform: translateY(-1px);
    }

    .btn-secondary {

        background: white;

        color: var(--teks-sekunder);

        border:
            1px solid var(--garis);
    }

    .btn-secondary:hover {

        background: var(--permukaan-redup);
    }

    /* =====================================================
       CARD
    ====================================================== */

    .card {

        background: white;

        border:
            1px solid var(--garis);

        border-radius: 14px;

        box-shadow:
            0 8px 28px rgba(31, 77, 102, 0.06);

        overflow: hidden;
    }

    .card-header {

        padding:
            22px 25px;

        border-bottom:
            1px solid var(--garis);
    }

    .card-title {

        font-size: 16px;

        font-weight: 800;

        color: var(--teks);
    }

    .card-description {

        margin-top: 5px;

        color: var(--teks-sekunder);

        font-size: 12px;

        line-height: 1.6;
    }

    .card-body {

        padding:
            25px;
    }

    /* =====================================================
       FORM GRID
    ====================================================== */

    .form-grid {

        display: grid;

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap:
            20px;
    }

    .form-group {

        display: flex;

        flex-direction: column;
    }

    .form-group.full {

        grid-column:
            1 / -1;
    }

    .form-label {

        margin-bottom: 7px;

        color: var(--teks);

        font-size: 12px;

        font-weight: 800;
    }

    .required {

        color: var(--bahaya);
    }

    .form-input,
    .form-select {

        width: 100%;

        min-height: 44px;

        padding:
            0 13px;

        border:
            1px solid var(--garis);

        border-radius: 8px;

        background: white;

        color: var(--teks);

        font-size: 13px;

        outline: none;

        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .form-input:focus,
    .form-select:focus {

        border-color: var(--utama);

        box-shadow:
            0 0 0 3px
            rgba(8, 120, 168, 0.10);
    }

    .form-help {

        margin-top: 6px;

        color: var(--teks-sekunder);

        font-size: 11px;

        line-height: 1.6;
    }

    .field-error {

        margin-top: 6px;

        color: var(--bahaya);

        font-size: 11px;
    }

    /* =====================================================
       GOOGLE SHEETS
    ====================================================== */

    .sheets-box {

        background: var(--permukaan-redup);

        border:
            1px solid var(--garis);

        border-radius: 10px;

        padding:
            18px;
    }

    .sheets-title {

        display: flex;

        align-items: center;

        gap: 9px;

        color: var(--utama);

        font-size: 13px;

        font-weight: 800;

        margin-bottom: 7px;
    }

    .sheets-description {

        color: var(--teks-sekunder);

        font-size: 11px;

        line-height: 1.7;

        margin-bottom: 14px;
    }

    /* =====================================================
       CURRENT SHEET LINK
    ====================================================== */

    .current-sheet {

        margin-top: 10px;

        background: white;

        border:
            1px solid var(--garis);

        border-radius: 8px;

        padding:
            11px 12px;

        font-size: 11px;

        line-height: 1.6;

        word-break: break-all;

        color: var(--utama);
    }

    .current-sheet-label {

        display: block;

        color: var(--teks-sekunder);

        font-size: 10px;

        font-weight: 800;

        text-transform: uppercase;

        letter-spacing: 0.5px;

        margin-bottom: 4px;
    }

    .current-sheet a {

        color: var(--utama);
    }

    .current-sheet a:hover {

        text-decoration: underline;
    }

    /* =====================================================
       FOOTER
    ====================================================== */

    .card-footer {

        padding:
            20px 25px;

        background: var(--permukaan-redup);

        border-top:
            1px solid var(--garis);

        display: flex;

        align-items: center;

        justify-content: flex-end;

        gap: 10px;
    }

    /* =====================================================
       RESPONSIVE
    ====================================================== */

    @media (max-width: 900px) {

        .content {

            padding: 22px;
        }

        .form-grid {

            grid-template-columns: 1fr;
        }

        .form-group.full {

            grid-column: auto;
        }

    }

    @media (max-width: 700px) {

        .content {

            padding: 18px;
        }

        .content-header {

            flex-direction: column;
        }

        .card-body {

            padding: 20px;
        }

        .card-header {

            padding:
                20px;
        }

        .card-footer {

            padding:
                18px 20px;

            flex-direction: column-reverse;

            align-items: stretch;
        }

        .card-footer .btn {

            width: 100%;
        }

    }

</style>
@endpush

@section('content')

@php
    $pesertaInitial = strtoupper(substr($pesertaMagang->nama, 0, 1));
@endphp

    {{-- CONTENT --}}

    <div class="content">

        {{-- HEADER --}}

        <div class="content-header">

            <div>

                <div class="heading">
                    Edit Peserta
                </div>

                <div class="heading-description">
                    Perbarui data peserta magang yang tersimpan di PILARA.
                </div>

            </div>

            <a
                href="{{ route(
                    'superadmin.peserta-magang.show',
                    $pesertaMagang
                ) }}"
                class="btn btn-secondary"
            >
                ← Kembali
            </a>

        </div>

        {{-- PESAN --}}

        <x-alert type="success" :message="session('success')" />

        {{-- ERROR --}}

        <x-alert
            type="error"
            title="Data belum dapat diperbarui."
            :messages="$errors->all()"
        />

        {{-- FORM --}}

        <form
            action="{{ route(
                'superadmin.peserta-magang.update',
                $pesertaMagang
            ) }}"
            method="POST"
        >

            @csrf

            @method('PUT')

            <section class="card">

                {{-- HEADER --}}

                <div class="card-header">

                    <div class="card-title">
                        Informasi Peserta Magang
                    </div>

                    <div class="card-description">
                        Periksa dan perbarui data peserta sebelum menyimpan perubahan.
                    </div>

                </div>

                {{-- BODY --}}

                <div class="card-body">

                    <div class="form-grid">

                        {{-- NAMA --}}

                        <div class="form-group">

                            <label
                                for="nama"
                                class="form-label"
                            >

                                Nama Lengkap
                                <span class="required">
                                    *
                                </span>

                            </label>

                            <input
                                type="text"
                                id="nama"
                                name="nama"
                                class="form-input"
                                value="{{ old(
                                    'nama',
                                    $pesertaMagang->nama
                                ) }}"
                                placeholder="Masukkan nama lengkap mahasiswa"
                                maxlength="255"
                                required
                            >

                            @error('nama')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                        {{-- NIM --}}

                        <div class="form-group">

                            <label
                                for="nim"
                                class="form-label"
                            >
                                NIM
                            </label>

                            <input
                                type="text"
                                id="nim"
                                name="nim"
                                class="form-input"
                                value="{{ old(
                                    'nim',
                                    $pesertaMagang->nim
                                ) }}"
                                placeholder="Masukkan NIM"
                                maxlength="50"
                            >

                            @error('nim')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                        {{-- KAMPUS --}}

                        <div class="form-group">

                            <label
                                for="asal_kampus"
                                class="form-label"
                            >
                                Asal Kampus
                            </label>

                            <input
                                type="text"
                                id="asal_kampus"
                                name="asal_kampus"
                                class="form-input"
                                value="{{ old(
                                    'asal_kampus',
                                    $pesertaMagang->asal_kampus
                                ) }}"
                                placeholder="Contoh: Politeknik Madiun"
                                maxlength="255"
                            >

                            @error('asal_kampus')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                        {{-- PROGRAM STUDI --}}

                        <div class="form-group">

                            <label
                                for="program_studi"
                                class="form-label"
                            >
                                Program Studi
                            </label>

                            <input
                                type="text"
                                id="program_studi"
                                name="program_studi"
                                class="form-input"
                                value="{{ old(
                                    'program_studi',
                                    $pesertaMagang->program_studi
                                ) }}"
                                placeholder="Contoh: Teknik Informatika"
                                maxlength="255"
                            >

                            @error('program_studi')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                        {{-- PERIODE MULAI --}}

                        <div class="form-group">

                            <label
                                for="periode_mulai"
                                class="form-label"
                            >
                                Periode Mulai
                            </label>

                            <input
                                type="date"
                                id="periode_mulai"
                                name="periode_mulai"
                                class="form-input"
                                value="{{ old(
                                    'periode_mulai',
                                    $pesertaMagang->periode_mulai
                                        ? $pesertaMagang->periode_mulai->format('Y-m-d')
                                        : ''
                                ) }}"
                            >

                            @error('periode_mulai')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                        {{-- PERIODE SELESAI --}}

                        <div class="form-group">

                            <label
                                for="periode_selesai"
                                class="form-label"
                            >
                                Periode Selesai
                            </label>

                            <input
                                type="date"
                                id="periode_selesai"
                                name="periode_selesai"
                                class="form-input"
                                value="{{ old(
                                    'periode_selesai',
                                    $pesertaMagang->periode_selesai
                                        ? $pesertaMagang->periode_selesai->format('Y-m-d')
                                        : ''
                                ) }}"
                            >

                            @error('periode_selesai')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                        {{-- PEMBIMBING --}}

                        <div class="form-group">

                            <label
                                for="pembimbing"
                                class="form-label"
                            >
                                Pembimbing / Mentor
                            </label>

                            <input
                                type="text"
                                id="pembimbing"
                                name="pembimbing"
                                class="form-input"
                                value="{{ old(
                                    'pembimbing',
                                    $pesertaMagang->pembimbing
                                ) }}"
                                placeholder="Nama pembimbing / mentor"
                                maxlength="255"
                            >

                            @error('pembimbing')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                        {{-- STATUS --}}

                        <div class="form-group">

                            <label
                                for="status"
                                class="form-label"
                            >

                                Status
                                <span class="required">
                                    *
                                </span>

                            </label>

                            <select
                                id="status"
                                name="status"
                                class="form-select"
                                required
                            >

                                <option
                                    value="aktif"
                                    {{ old(
                                        'status',
                                        $pesertaMagang->status
                                    ) === 'aktif'
                                        ? 'selected'
                                        : ''
                                    }}
                                >
                                    Aktif
                                </option>

                                <option
                                    value="selesai"
                                    {{ old(
                                        'status',
                                        $pesertaMagang->status
                                    ) === 'selesai'
                                        ? 'selected'
                                        : ''
                                    }}
                                >
                                    Selesai
                                </option>

                            </select>

                            @error('status')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                        {{-- GOOGLE SHEETS --}}

                        <div class="form-group full">

                            <div class="sheets-box">

                                <div class="sheets-title">

                                    <span>
                                        <x-icon name="file-spreadsheet" />
                                    </span>

                                    Google Sheets Absensi

                                </div>

                                <div class="sheets-description">
                                    Perbarui link Google Sheets yang digunakan untuk menyimpan absensi dan Daily Activity peserta ini.
                                </div>

                                <label
                                    for="google_sheet_url"
                                    class="form-label"
                                >

                                    Link Google Sheets

                                </label>

                                <input
                                    type="url"
                                    id="google_sheet_url"
                                    name="google_sheet_url"
                                    class="form-input"
                                    value="{{ old(
                                        'google_sheet_url',
                                        $pesertaMagang->google_sheet_url
                                    ) }}"
                                    placeholder="https://docs.google.com/spreadsheets/d/..."
                                    maxlength="2048"
                                >

                                <div class="form-help">
                                    Masukkan URL Google Sheets yang sesuai dengan peserta ini.
                                </div>

                                @error('google_sheet_url')

                                    <div class="field-error">
                                        {{ $message }}
                                    </div>

                                @enderror

                                @if($pesertaMagang->google_sheet_url)

                                    <div class="current-sheet">

                                        <span class="current-sheet-label">
                                            Link Saat Ini
                                        </span>

                                        <a
                                            href="{{ $pesertaMagang->google_sheet_url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            {{ $pesertaMagang->google_sheet_url }}
                                        </a>

                                    </div>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

                {{-- FOOTER --}}

                <div class="card-footer">

                    <a
                        href="{{ route(
                            'superadmin.peserta-magang.show',
                            $pesertaMagang
                        ) }}"
                        class="btn btn-secondary"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Simpan Perubahan
                    </button>

                </div>

            </section>

        </form>

    </div>

@endsection
