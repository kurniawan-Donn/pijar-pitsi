{{-- resources/views/superadmin/peserta-magang/show.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Detail Peserta Magang')
@section('page_title', 'Detail Peserta Magang')
@section('page_subtitle', 'Informasi lengkap peserta dan koneksi Google Sheets absensi.')

@push('styles')
<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        background: #f4f7fb;
        color: #263238;
        min-height: 100vh;
    }

    button,
    input {
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

    .btn-secondary {
        background: white;

        color: #52646e;

        border:
            1px solid #dce5ea;
    }

    .btn-secondary:hover {
        background: #f7fafc;
    }

    .btn-orange {
        background: #f47b20;
        color: white;
    }

    .btn-orange:hover {
        background: #d96512;

        transform: translateY(-1px);
    }

    .btn-danger {
        background: #fff0f0;

        color: #c62828;

        border:
            1px solid #ffd1d1;
    }

    .btn-danger:hover {
        background: #ffe1e1;
    }

    /* =====================================================
       GRID
    ====================================================== */

    .dashboard-grid {
        display: grid;

        grid-template-columns:
            1.25fr 0.75fr;

        gap: 20px;

        margin-bottom: 20px;
    }

    /* =====================================================
       CARD
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
            21px 23px;

        border-bottom:
            1px solid #edf1f4;
    }

    .card-title {
        font-size: 15px;

        font-weight: 800;

        color: #263238;
    }

    .card-description {
        margin-top: 4px;

        color: #8a99a3;

        font-size: 11px;
    }

    .card-body {
        padding:
            23px;
    }

    /* =====================================================
       PROFILE CARD
    ====================================================== */

    .profile-top {
        display: flex;

        align-items: center;

        gap: 15px;

        margin-bottom: 24px;
    }

    .profile-avatar {
        width: 62px;
        height: 62px;

        border-radius: 15px;

        background:
            linear-gradient(
                135deg,
                #0878a8,
                #0c91be
            );

        color: white;

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 23px;

        font-weight: 800;

        flex-shrink: 0;
    }

    .profile-name {
        font-size: 21px;

        font-weight: 800;

        color: #17313d;
    }

    .profile-nim {
        margin-top: 4px;

        font-size: 12px;

        color: #7b8b96;
    }

    /* =====================================================
       INFORMATION
    ====================================================== */

    .info-grid {
        display: grid;

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap:
            17px 25px;
    }

    .info-item {
        min-width: 0;
    }

    .info-label {
        color: #8a99a3;

        font-size: 10px;

        font-weight: 800;

        text-transform: uppercase;

        letter-spacing: 0.7px;

        margin-bottom: 5px;
    }

    .info-value {
        color: #34454f;

        font-size: 13px;

        font-weight: 700;

        line-height: 1.5;

        word-break: break-word;
    }

    .info-value.normal {
        font-weight: 500;
    }

    /* =====================================================
       STATUS
    ====================================================== */

    .status-badge {
        display: inline-flex;

        align-items: center;

        justify-content: center;

        padding:
            7px 12px;

        border-radius: 20px;

        font-size: 10px;

        font-weight: 800;

        text-transform: uppercase;
    }

    .status-aktif {
        background: #eaf8f0;

        color: #16834b;
    }

    .status-selesai {
        background: #eef1f4;

        color: #667781;
    }

    /* =====================================================
       GOOGLE SHEETS
    ====================================================== */

    .sheets-card {
        background:
            linear-gradient(
                135deg,
                #f8fcfe,
                #eef8fc
            );

        border:
            1px solid #d8eaf1;

        border-radius: 12px;

        padding: 20px;
    }

    .sheets-icon {
        width: 48px;
        height: 48px;

        border-radius: 12px;

        background: #ffffff;

        color: #16834b;

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 20px;

        margin-bottom: 13px;
    }

    .sheets-title {
        font-size: 15px;

        font-weight: 800;

        color: #263238;

        margin-bottom: 7px;
    }

    .sheets-description {
        color: #71818b;

        font-size: 11px;

        line-height: 1.7;

        margin-bottom: 16px;
    }

    .sheets-url {
        display: block;

        background: white;

        border:
            1px solid #dfe9ee;

        border-radius: 8px;

        padding:
            11px 12px;

        color: #0878a8;

        font-size: 11px;

        line-height: 1.5;

        word-break: break-all;

        margin-bottom: 12px;
    }

    .sheets-url:hover {
        border-color: #b9d8e4;
    }

    .sheets-missing {
        padding:
            13px;

        border-radius: 8px;

        background: #fff8ed;

        border:
            1px solid #ffe1b7;

        color: #9a5b12;

        font-size: 11px;

        line-height: 1.6;
    }

    /* =====================================================
       QUICK ACTION
    ====================================================== */

    .action-list {
        display: grid;

        gap: 10px;
    }

    .action-item {
        display: flex;

        align-items: center;

        gap: 12px;

        padding:
            12px;

        border:
            1px solid #e5edf1;

        border-radius: 9px;

        color: #465762;

        font-size: 12px;

        font-weight: 700;

        transition: 0.2s ease;
    }

    .action-item:hover {
        background: #f8fbfd;

        border-color: #cfe0e8;
    }

    .action-icon {
        width: 34px;
        height: 34px;

        border-radius: 8px;

        background: #eef8fc;

        color: #0878a8;

        display: flex;

        align-items: center;

        justify-content: center;

        flex-shrink: 0;
    }

    /* =====================================================
       REKAP ABSENSI
    ====================================================== */

    .attendance-summary {
        display: grid;

        grid-template-columns:
            repeat(3, minmax(0, 1fr));

        gap: 14px;

        margin-bottom: 20px;
    }

    .summary-card {
        background: white;

        border:
            1px solid #e2eaf0;

        border-radius: 12px;

        padding: 18px;

        box-shadow:
            0 5px 18px rgba(31, 77, 102, 0.04);
    }

    .summary-label {
        color: #7b8b96;

        font-size: 10px;

        font-weight: 800;

        text-transform: uppercase;

        letter-spacing: 0.6px;
    }

    .summary-value {
        margin-top: 6px;

        color: #17313d;

        font-size: 28px;

        font-weight: 800;

        line-height: 1;
    }

    .summary-description {
        margin-top: 7px;

        color: #8a99a3;

        font-size: 10px;

        line-height: 1.5;
    }

    /* =====================================================
       ABSENSI TABLE
    ====================================================== */

    .absensi-table-wrapper {
        overflow-x: auto;
    }

    .absensi-table {
        width: 100%;

        border-collapse: collapse;

        min-width: 1280px;
    }

    .absensi-table th {
        text-align: left;

        background: #f8fafc;

        color: #697a85;

        padding:
            12px 14px;

        font-size: 10px;

        text-transform: uppercase;

        letter-spacing: 0.4px;

        border-bottom:
            1px solid #e7edf1;

        white-space: nowrap;
    }

    .absensi-table td {
        padding:
            13px 14px;

        font-size: 11px;

        color: #52636d;

        border-bottom:
            1px solid #edf2f5;

        vertical-align: top;
    }

    .absensi-table tr:last-child td {
        border-bottom: none;
    }

    .attendance-input {
        width: 92px;

        min-height: 34px;

        padding:
            0 9px;

        border:
            1px solid #d7e2e8;

        border-radius: 7px;

        background: white;

        color: #34454f;

        font-size: 11px;

        outline: none;
    }

    .attendance-input:focus {
        border-color: #0878a8;

        box-shadow:
            0 0 0 3px rgba(8,120,168,0.08);
    }

    .attendance-save {
        min-height: 34px;

        padding:
            0 11px;

        border: none;

        border-radius: 7px;

        background: #f47b20;

        color: white;

        font-size: 10px;

        font-weight: 800;

        cursor: pointer;

        white-space: nowrap;
    }

    .attendance-save:hover {
        background: #d96512;
    }

    .attendance-edit-form {
        margin: 0;
    }

    .sheet-note {
        padding:
            14px 16px;

        margin-bottom: 16px;

        border-radius: 9px;

        background: #f7fafc;

        border:
            1px solid #e3ebf0;

        color: #667781;

        font-size: 11px;

        line-height: 1.6;
    }

    .sheet-alert-error {
        padding:
            12px 15px;

        margin-bottom: 15px;

        border-radius: 8px;

        background: #fff0f0;

        border:
            1px solid #ffd1d1;

        color: #c62828;

        font-size: 11px;

        font-weight: 700;
    }

    .empty-absensi {
        text-align: center;

        padding:
            40px 20px;

        color: #8a99a3;

        font-size: 12px;

        line-height: 1.7;
    }

    /* =====================================================
       FOOTER ACTION
    ====================================================== */

    .bottom-actions {
        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 15px;

        flex-wrap: wrap;

        margin-top: 20px;
    }

    .bottom-left {
        display: flex;

        align-items: center;

        gap: 9px;

        flex-wrap: wrap;
    }

    /* =====================================================
       RESPONSIVE
    ====================================================== */

    @media (max-width: 1100px) {

        .content {
            padding: 22px;
        }

        .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .attendance-summary {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {

        .content {
            padding: 18px;
        }

        .content-header {
            flex-direction: column;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .card-body {
            padding: 20px;
        }

        .card-header {
            padding:
                20px;
        }

        .bottom-actions {
            align-items: flex-start;

            flex-direction: column;
        }

        .attendance-summary {
            grid-template-columns: 1fr;
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
                    {{ $pesertaMagang->nama }}
                </div>

                <div class="heading-description">
                    Detail data peserta magang yang tersimpan pada sistem MAGENTA.
                </div>

            </div>

            <a
                href="{{ route('superadmin.peserta-magang.index') }}"
                class="btn btn-secondary"
            >
                ← Kembali
            </a>

        </div>

        {{-- =================================================
             INFORMASI PESERTA + GOOGLE SHEETS
        ================================================== --}}

        <div class="dashboard-grid">

            {{-- INFORMASI PESERTA --}}

            <section class="card">

                <div class="card-header">

                    <div class="card-title">
                        Informasi Peserta
                    </div>

                    <div class="card-description">
                        Data identitas dan periode kegiatan magang.
                    </div>

                </div>

                <div class="card-body">

                    <div class="profile-top">

                        <div class="profile-avatar">
                            {{ $pesertaInitial }}
                        </div>

                        <div>

                            <div class="profile-name">
                                {{ $pesertaMagang->nama }}
                            </div>

                            <div class="profile-nim">
                                NIM:
                                {{ $pesertaMagang->nim ?: '-' }}
                            </div>

                        </div>

                    </div>

                    <div class="info-grid">

                        {{-- NAMA --}}

                        <div class="info-item">

                            <div class="info-label">
                                Nama Lengkap
                            </div>

                            <div class="info-value">
                                {{ $pesertaMagang->nama }}
                            </div>

                        </div>

                        {{-- NIM --}}

                        <div class="info-item">

                            <div class="info-label">
                                NIM
                            </div>

                            <div class="info-value">
                                {{ $pesertaMagang->nim ?: '-' }}
                            </div>

                        </div>

                        {{-- KAMPUS --}}

                        <div class="info-item">

                            <div class="info-label">
                                Asal Kampus
                            </div>

                            <div class="info-value">
                                {{ $pesertaMagang->asal_kampus ?: '-' }}
                            </div>

                        </div>

                        {{-- PRODI --}}

                        <div class="info-item">

                            <div class="info-label">
                                Program Studi
                            </div>

                            <div class="info-value">
                                {{ $pesertaMagang->program_studi ?: '-' }}
                            </div>

                        </div>

                        {{-- PERIODE MULAI --}}

                        <div class="info-item">

                            <div class="info-label">
                                Periode Mulai
                            </div>

                            <div class="info-value normal">

                                @if($pesertaMagang->periode_mulai)

                                    {{ $pesertaMagang->periode_mulai->format('d F Y') }}

                                @else

                                    -

                                @endif

                            </div>

                        </div>

                        {{-- PERIODE SELESAI --}}

                        <div class="info-item">

                            <div class="info-label">
                                Periode Selesai
                            </div>

                            <div class="info-value normal">

                                @if($pesertaMagang->periode_selesai)

                                    {{ $pesertaMagang->periode_selesai->format('d F Y') }}

                                @else

                                    -

                                @endif

                            </div>

                        </div>

                        {{-- PEMBIMBING --}}

                        <div class="info-item">

                            <div class="info-label">
                                Pembimbing / Mentor
                            </div>

                            <div class="info-value">
                                {{ $pesertaMagang->pembimbing ?: '-' }}
                            </div>

                        </div>

                        {{-- STATUS --}}

                        <div class="info-item">

                            <div class="info-label">
                                Status
                            </div>

                            <div class="info-value">

                                @if($pesertaMagang->status === 'aktif')

                                    <span class="status-badge status-aktif">
                                        Aktif
                                    </span>

                                @else

                                    <span class="status-badge status-selesai">
                                        Selesai
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            </section>

            {{-- GOOGLE SHEETS --}}

            <section class="card">

                <div class="card-header">

                    <div class="card-title">
                        Absensi
                    </div>

                    <div class="card-description">
                        Google Sheets yang digunakan untuk absensi peserta.
                    </div>

                </div>

                <div class="card-body">

                    <div class="sheets-card">

                        <div class="sheets-icon">
                            ▣
                        </div>

                        <div class="sheets-title">
                            Google Sheets Absensi
                        </div>

                        <div class="sheets-description">
                            Spreadsheet ini menjadi tempat pencatatan absensi dan Daily Activity peserta.
                        </div>

                        @if($pesertaMagang->google_sheet_url)

                            <a
                                href="{{ $pesertaMagang->google_sheet_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="sheets-url"
                            >
                                {{ $pesertaMagang->google_sheet_url }}
                            </a>

                            <a
                                href="{{ $pesertaMagang->google_sheet_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="btn btn-orange"
                                style="width:100%;"
                            >
                                Buka Google Sheets Absensi ↗
                            </a>

                        @else

                            <div class="sheets-missing">
                                Link Google Sheets absensi belum tersedia untuk peserta ini.
                            </div>

                        @endif

                    </div>

                </div>

            </section>

        </div>

        {{-- =================================================
             AKSI PESERTA
        ================================================== --}}

        <section class="card">

            <div class="card-header">

                <div class="card-title">
                    Aksi Peserta
                </div>

                <div class="card-description">
                    Kelola data peserta atau buka spreadsheet absensi.
                </div>

            </div>

            <div class="card-body">

                <div class="action-list">

                    {{-- EDIT DATA PESERTA --}}

                    <a
                        href="{{ route(
                            'superadmin.peserta-magang.edit',
                            $pesertaMagang
                        ) }}"
                        class="action-item"
                    >

                        <span class="action-icon">
                            ✎
                        </span>

                        <span>
                            Edit Data Peserta
                        </span>

                    </a>

                    {{-- GOOGLE SHEETS --}}

                    @if($pesertaMagang->google_sheet_url)

                        <a
                            href="{{ $pesertaMagang->google_sheet_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="action-item"
                        >

                            <span class="action-icon">
                                ▣
                            </span>

                            <span>
                                Buka Google Sheets Absensi
                            </span>

                        </a>

                    @endif

                </div>

            </div>

        </section>

        {{-- =================================================
             RIWAYAT ABSENSI & DAILY ACTIVITY
        ================================================== --}}

        <section
            class="card"
            style="margin-top:20px;"
        >

            <div class="card-header">

                <div class="card-title">
                    Riwayat Absensi & Daily Activity
                </div>

                <div class="card-description">
                    Data dibaca langsung dari Google Sheets peserta melalui koneksi MAGENTA.
                </div>

            </div>

            <div class="card-body">

                {{-- SUCCESS --}}

                <x-alert type="success" :message="session('success')" />

                {{-- ERROR --}}

                <x-alert type="error" :message="session('error')" />

                {{-- GOOGLE SHEETS ERROR --}}

                @if($sheetError)

                    <div class="sheet-alert-error">
                        {{ $sheetError }}
                    </div>

                @endif

                {{-- NOTE --}}

                <div class="sheet-note">

                    Periode aktif:

                    <strong>
                        {{ $currentMonthTitle }}
                    </strong>.

                    Kolom

                    <strong>
                        Activity, Output, Lokasi, dan Action
                    </strong>

                    tetap diisi oleh mahasiswa melalui Google Sheets.

                    Admin dapat mengubah empat waktu absensi melalui MAGENTA.

                </div>

                @if(count($sheetRows) > 0)

                    {{-- =================================================
                         REKAP ABSENSI
                    ================================================== --}}

                    @php

                        $totalTercatat =
                            count($sheetRows);

                        $totalHadir = 0;

                        $totalIzinSakit = 0;

                        foreach ($sheetRows as $rekapRow) {

                            $activity =
                                strtolower(
                                    trim(
                                        (string)
                                        ($rekapRow['activity'] ?? '')
                                    )
                                );

                            $pagiMasuk =
                                trim(
                                    (string)
                                    ($rekapRow['pagi_masuk'] ?? '')
                                );

                            $pagiKeluar =
                                trim(
                                    (string)
                                    ($rekapRow['pagi_keluar'] ?? '')
                                );

                            $siangMasuk =
                                trim(
                                    (string)
                                    ($rekapRow['siang_masuk'] ?? '')
                                );

                            $siangKeluar =
                                trim(
                                    (string)
                                    ($rekapRow['siang_keluar'] ?? '')
                                );

                            $adaAbsensi =
                                $pagiMasuk !== '' ||
                                $pagiKeluar !== '' ||
                                $siangMasuk !== '' ||
                                $siangKeluar !== '';

                            $isIzinSakit =
                                str_contains(
                                    $activity,
                                    'izin'
                                ) ||
                                str_contains(
                                    $activity,
                                    'ijin'
                                ) ||
                                str_contains(
                                    $activity,
                                    'sakit'
                                );

                            if ($isIzinSakit) {

                                $totalIzinSakit++;

                            } elseif ($adaAbsensi) {

                                $totalHadir++;

                            }

                        }

                    @endphp

                    <div class="attendance-summary">

                        {{-- HARI TERCATAT --}}

                        <div class="summary-card">

                            <div class="summary-label">
                                Hari Tercatat
                            </div>

                            <div class="summary-value">
                                {{ $totalTercatat }}
                            </div>

                            <div class="summary-description">
                                Data yang tersedia pada Google Sheets.
                            </div>

                        </div>

                        {{-- HADIR --}}

                        <div class="summary-card">

                            <div class="summary-label">
                                Hadir
                            </div>

                            <div class="summary-value">
                                {{ $totalHadir }}
                            </div>

                            <div class="summary-description">
                                Memiliki data waktu absensi.
                            </div>

                        </div>

                        {{-- IZIN / SAKIT --}}

                        <div class="summary-card">

                            <div class="summary-label">
                                Izin / Sakit
                            </div>

                            <div class="summary-value">
                                {{ $totalIzinSakit }}
                            </div>

                            <div class="summary-description">
                                Berdasarkan keterangan Activity.
                            </div>

                        </div>

                    </div>

                    {{-- =================================================
                         NORMALISASI JAM
                    ================================================== --}}

                    @php

                        $normalizeTime = function ($value) {

                            $value =
                                trim(
                                    (string) $value
                                );

                            if ($value === '') {
                                return '';
                            }

                            $value =
                                str_replace(
                                    '.',
                                    ':',
                                    $value
                                );

                            if (
                                preg_match(
                                    '/^(\d{1,2}):(\d{2})$/',
                                    $value,
                                    $matches
                                )
                            ) {

                                $hour =
                                    (int) $matches[1];

                                $minute =
                                    (int) $matches[2];

                                if (
                                    $hour >= 0 &&
                                    $hour <= 23 &&
                                    $minute >= 0 &&
                                    $minute <= 59
                                ) {

                                    return sprintf(
                                        '%02d:%02d',
                                        $hour,
                                        $minute
                                    );

                                }

                            }

                            return '';

                        };

                    @endphp

                    {{-- =================================================
                         TABEL ABSENSI
                    ================================================== --}}

                    <div class="absensi-table-wrapper">

                        <table class="absensi-table">

                            <thead>

                                <tr>

                                    <th>
                                        No
                                    </th>

                                    <th>
                                        Tanggal
                                    </th>

                                    <th>
                                        Pagi Masuk
                                    </th>

                                    <th>
                                        Pagi Keluar
                                    </th>

                                    <th>
                                        Siang Masuk
                                    </th>

                                    <th>
                                        Siang Keluar
                                    </th>

                                    <th>
                                        Activity
                                    </th>

                                    <th>
                                        Output
                                    </th>

                                    <th>
                                        Lokasi
                                    </th>

                                    <th>
                                        Action
                                    </th>

                                    <th>
                                        Simpan
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($sheetRows as $row)

                                    @php

                                        $formId =
                                            'attendance-form-'
                                            . $row['sheet_row'];

                                    @endphp

                                    <tr>

                                        {{-- NO --}}

                                        <td>
                                            {{ $row['no'] ?: '-' }}
                                        </td>

                                        {{-- TANGGAL --}}

                                        <td>
                                            {{ $row['tanggal'] ?: '-' }}
                                        </td>

                                        {{-- PAGI MASUK --}}

                                        <td>

                                            <input
                                                type="time"
                                                name="pagi_masuk"
                                                form="{{ $formId }}"
                                                class="attendance-input"
                                                value="{{ $normalizeTime($row['pagi_masuk']) }}"
                                            >

                                        </td>

                                        {{-- PAGI KELUAR --}}

                                        <td>

                                            <input
                                                type="time"
                                                name="pagi_keluar"
                                                form="{{ $formId }}"
                                                class="attendance-input"
                                                value="{{ $normalizeTime($row['pagi_keluar']) }}"
                                            >

                                        </td>

                                        {{-- SIANG MASUK --}}

                                        <td>

                                            <input
                                                type="time"
                                                name="siang_masuk"
                                                form="{{ $formId }}"
                                                class="attendance-input"
                                                value="{{ $normalizeTime($row['siang_masuk']) }}"
                                            >

                                        </td>

                                        {{-- SIANG KELUAR --}}

                                        <td>

                                            <input
                                                type="time"
                                                name="siang_keluar"
                                                form="{{ $formId }}"
                                                class="attendance-input"
                                                value="{{ $normalizeTime($row['siang_keluar']) }}"
                                            >

                                        </td>

                                        {{-- ACTIVITY --}}

                                        <td>
                                            {{ $row['activity'] ?: '-' }}
                                        </td>

                                        {{-- OUTPUT --}}

                                        <td>
                                            {{ $row['output'] ?: '-' }}
                                        </td>

                                        {{-- LOKASI --}}

                                        <td>
                                            {{ $row['lokasi'] ?: '-' }}
                                        </td>

                                        {{-- ACTION --}}

                                        <td>
                                            {{ $row['action'] ?: '-' }}
                                        </td>

                                        {{-- SIMPAN --}}

                                        <td>

                                            <form
                                                id="{{ $formId }}"
                                                action="{{ route(
                                                    'superadmin.peserta-magang.absensi.update',
                                                    $pesertaMagang
                                                ) }}"
                                                method="POST"
                                                class="attendance-edit-form"
                                            >

                                                @csrf

                                                @method('PUT')

                                                <input
                                                    type="hidden"
                                                    name="sheet_row"
                                                    value="{{ $row['sheet_row'] }}"
                                                >

                                                <button
                                                    type="submit"
                                                    class="attendance-save"
                                                >
                                                    Simpan
                                                </button>

                                            </form>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="empty-absensi">

                        Belum ada data absensi dari Google Sheets untuk periode

                        <strong>
                            {{ $currentMonthTitle }}
                        </strong>.

                        <br>

                        Pastikan tab bulan tersebut tersedia di Google Sheets peserta.

                    </div>

                @endif

            </div>

        </section>

        {{-- =================================================
             BOTTOM ACTIONS
        ================================================== --}}

        <div class="bottom-actions">

            <div class="bottom-left">

                {{-- EDIT PESERTA --}}

                <a
                    href="{{ route(
                        'superadmin.peserta-magang.edit',
                        $pesertaMagang
                    ) }}"
                    class="btn btn-secondary"
                >
                    ✎ Edit Peserta
                </a>

                {{-- GOOGLE SHEETS --}}

                @if($pesertaMagang->google_sheet_url)

                    <a
                        href="{{ $pesertaMagang->google_sheet_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn btn-orange"
                    >
                        ▣ Buka Absensi
                    </a>

                @endif

            </div>

            {{-- HAPUS PESERTA --}}

            <form
                action="{{ route(
                    'superadmin.peserta-magang.destroy',
                    $pesertaMagang
                ) }}"
                method="POST"
                onsubmit="return confirm(
                    'Yakin ingin menghapus peserta ini? Data absensi lokal peserta juga akan ikut terhapus.'
                );"
            >

                @csrf

                @method('DELETE')

                <button
                    type="submit"
                    class="btn btn-danger"
                >
                    Hapus Peserta
                </button>

            </form>

        </div>

    </div>

@endsection
