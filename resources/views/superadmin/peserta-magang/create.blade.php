{{-- resources/views/superadmin/peserta-magang/create.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Tambah Peserta Magang')
@section('page_title', 'Tambah Peserta Magang')
@section('page_subtitle', 'Tambahkan data mahasiswa yang akan mengikuti kegiatan magang.')

@push('styles')
<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        background: #f4f7fb;
        color: #263238;
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

        font-size: 26px;

        color: #17313d;

        font-weight: 800;

    }

    .heading-description {

        color: #7b8b96;

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

        background: #0878a8;

        color: white;

    }

    .btn-primary:hover {

        background: #075d83;

        transform: translateY(-1px);

    }

    .btn-secondary {

        background: white;

        color: #52646e;

        border:
            1px solid #dce5ea;

    }

    .btn-secondary:hover {

        background: #f7fafc;

    }

    /* =====================================================
       FORM CARD
    ====================================================== */

    .card {

        background: white;

        border:
            1px solid #e2eaf0;

        border-radius: 14px;

        box-shadow:
            0 8px 28px rgba(31, 77, 102, 0.06);

        overflow: hidden;

    }

    .card-header {

        padding:
            22px 25px;

        border-bottom:
            1px solid #edf1f4;

    }

    .card-title {

        font-size: 16px;

        font-weight: 800;

        color: #263238;

    }

    .card-description {

        margin-top: 5px;

        color: #8a99a3;

        font-size: 12px;

        line-height: 1.6;

    }

    .card-body {

        padding:
            25px;

    }

    /* =====================================================
       FORM
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

        color: #34454f;

        font-size: 12px;

        font-weight: 800;

    }

    .required {

        color: #d64545;

    }

    .form-input,
    .form-select {

        width: 100%;

        min-height: 44px;

        padding:
            0 13px;

        border:
            1px solid #dce5ea;

        border-radius: 8px;

        background: white;

        color: #263238;

        font-size: 13px;

        outline: none;

        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease;

    }

    .form-input:focus,
    .form-select:focus {

        border-color: #0878a8;

        box-shadow:
            0 0 0 3px
            rgba(8, 120, 168, 0.10);

    }

    textarea.form-input {

        min-height: 105px;

        padding:
            12px 13px;

        resize: vertical;

        line-height: 1.6;

    }

    .form-help {

        margin-top: 6px;

        color: #8a99a3;

        font-size: 11px;

        line-height: 1.6;

    }

    .field-error {

        margin-top: 6px;

        color: #b42318;

        font-size: 11px;

    }

    /* =====================================================
       GOOGLE SHEETS BOX
    ====================================================== */

    .sheets-box {

        background: #f7fafc;

        border:
            1px solid #e3ebf0;

        border-radius: 10px;

        padding:
            18px;

    }

    .sheets-title {

        display: flex;

        align-items: center;

        gap: 9px;

        color: #0878a8;

        font-size: 13px;

        font-weight: 800;

        margin-bottom: 7px;

    }

    .sheets-description {

        color: #74848e;

        font-size: 11px;

        line-height: 1.7;

        margin-bottom: 14px;

    }

    /* =====================================================
       FORM FOOTER
    ====================================================== */

    .card-footer {

        padding:
            20px 25px;

        background: #fbfcfd;

        border-top:
            1px solid #edf1f4;

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

            grid-column:
                auto;

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

        }

    }

</style>
@endpush

@section('content')

    {{-- CONTENT --}}

    <div class="content">

        {{-- HEADER --}}

        <div class="content-header">

            <div>

                <div class="heading">
                    Tambah Peserta
                </div>

                <div class="heading-description">
                    Isi data peserta magang dan tautkan Google Sheets absensinya.
                </div>

            </div>

            <a
                href="{{ route('superadmin.peserta-magang.index') }}"
                class="btn btn-secondary"
            >

                ← Kembali

            </a>

        </div>

        {{-- VALIDATION ERROR --}}

        <x-alert
            type="error"
            title="Data belum dapat disimpan."
            :messages="$errors->all()"
        />

        {{-- FORM --}}

        <form
            action="{{ route('superadmin.peserta-magang.store') }}"
            method="POST"
        >

            @csrf

            <section class="card">

                {{-- CARD HEADER --}}

                <div class="card-header">

                    <div class="card-title">
                        Informasi Peserta Magang
                    </div>

                    <div class="card-description">
                        Data ini digunakan untuk mengidentifikasi peserta dan menghubungkannya dengan data absensi.
                    </div>

                </div>

                {{-- CARD BODY --}}

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
                                value="{{ old('nama') }}"
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
                                value="{{ old('nim') }}"
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
                                value="{{ old('asal_kampus') }}"
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
                                value="{{ old('program_studi') }}"
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
                                value="{{ old('periode_mulai') }}"
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
                                value="{{ old('periode_selesai') }}"
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
                                value="{{ old('pembimbing') }}"
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
                                    {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}
                                >
                                    Aktif
                                </option>

                                <option
                                    value="selesai"
                                    {{ old('status') === 'selesai' ? 'selected' : '' }}
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
                                        ▣
                                    </span>

                                    Google Sheets Absensi

                                </div>

                                <div class="sheets-description">
                                    Masukkan link Google Sheets yang digunakan untuk menyimpan data absensi dan Daily Activity peserta ini.
                                </div>

                                <label
                                    for="google_sheet_url"
                                    class="form-label"
                                >

                                    Link Google Sheets
                                    <span class="required">
                                        *
                                    </span>

                                </label>

                                <input
                                    type="url"
                                    id="google_sheet_url"
                                    name="google_sheet_url"
                                    class="form-input"
                                    value="{{ old('google_sheet_url') }}"
                                    placeholder="https://docs.google.com/spreadsheets/d/..."
                                    maxlength="2048"
                                >

                                <div class="form-help">
                                    Contoh: https://docs.google.com/spreadsheets/d/...
                                </div>

                                @error('google_sheet_url')

                                    <div class="field-error">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>

                        </div>

                    </div>

                </div>

                {{-- CARD FOOTER --}}

                <div class="card-footer">

                    <a
                        href="{{ route('superadmin.peserta-magang.index') }}"
                        class="btn btn-secondary"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >

                        Simpan Peserta

                    </button>

                </div>

            </section>

        </form>

    </div>

@endsection
