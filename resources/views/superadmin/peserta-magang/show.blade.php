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


    /* =====================================================
       TOOLBAR BULAN & STATUS BARIS (Fase 3)
    ====================================================== */

    .absensi-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;

        margin-bottom: 14px;
    }

    .month-form {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .month-form label {
        color: #4b5d70;
        font-size: 12px;
        font-weight: 800;
    }

    .month-form select {
        min-width: 190px;
        height: 38px;

        padding: 0 12px;

        border: 1px solid #d7e0eb;
        border-radius: 8px;

        background: white;
        color: #17313d;

        font-family: inherit;
        font-size: 13px;
        font-weight: 700;

        cursor: pointer;
    }

    .toolbar-button {
        height: 38px;

        padding: 0 14px;

        border: 1px solid #d7e0eb;
        border-radius: 8px;

        background: white;
        color: #0878a8;

        font-family: inherit;
        font-size: 12px;
        font-weight: 800;

        cursor: pointer;

        transition: .2s ease;
    }

    .toolbar-button:hover {
        border-color: #0878a8;
        background: #f1f8fc;
    }

    .attendance-summary {
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
    }

    .summary-izin .summary-value {
        color: #b4630c;
    }

    .summary-sakit .summary-value {
        color: #c62828;
    }

    .summary-belum_diisi .summary-value {
        color: #8a99a3;
    }

    .action-meta {
        display: block;

        margin-top: 2px;

        color: #8a99a3;
        font-size: 10px;
        font-weight: 600;
    }

    .allowance-box {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));

        margin-bottom: 20px;

        border: 1px solid #e2eaf0;
        border-radius: 12px;

        background: #f8fbfd;

        overflow: hidden;
    }

    .allowance-item {
        display: flex;
        flex-direction: column;
        gap: 3px;

        padding: 14px 16px;
    }

    .allowance-item + .allowance-item {
        border-left: 1px solid #e2eaf0;
    }

    .allowance-item span {
        color: #7b8b96;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .5px;
        text-transform: uppercase;
    }

    .allowance-item strong {
        color: #17313d;
        font-size: 17px;
    }

    .allowance-item small {
        color: #8a99a3;
        font-size: 10px;
    }

    .allowance-total {
        background: #fff6ee;
    }

    .allowance-total strong {
        color: #d96512;
    }

    @media (max-width: 1100px) {
        .allowance-box {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .allowance-item:nth-child(3) {
            border-left: 0;
        }

        .allowance-item:nth-child(n + 3) {
            border-top: 1px solid #e2eaf0;
        }
    }

    .row-status {
        display: inline-block;

        margin-top: 4px;
        padding: 2px 8px;

        border-radius: 999px;

        font-size: 10px;
        font-weight: 800;
    }

    .row-status-izin {
        background: #fff6e8;
        color: #b4630c;
    }

    .row-status-sakit {
        background: #fff0f0;
        color: #c62828;
    }

    @media (max-width: 1100px) {
        .attendance-summary {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 700px) {
        .attendance-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    /* =====================================================
       AKUN LOGIN PESERTA
    ====================================================== */

    .akun-card {
        margin-bottom: 20px;

        scroll-margin-top: 20px;
    }

    .akun-password {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px 14px;

        margin-bottom: 16px;
        padding: 14px 16px;

        border: 1px dashed #f4a261;
        border-radius: 10px;

        background: #fff8f1;

        color: #7a4a17;
        font-size: 13px;
        line-height: 1.6;
    }

    .akun-password code {
        padding: 4px 10px;

        border-radius: 6px;

        background: #ffffff;

        color: #17324d;
        font-size: 16px;
        font-weight: 800;
        letter-spacing: 1px;
    }

    .akun-info {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;

        margin-bottom: 18px;
    }

    .akun-form {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 12px;
    }

    .akun-form .x-form-group {
        flex: 1 1 280px;

        margin-bottom: 0;
    }

    .akun-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .akun-actions form {
        margin: 0;
    }

    .akun-hint {
        margin-top: 12px;

        color: #7b8794;
        font-size: 12px;
        line-height: 1.6;
    }

    @media (max-width: 800px) {
        .akun-info {
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
             AKUN LOGIN PESERTA
        ================================================== --}}

        @php
            $akun = $pesertaMagang->user;
        @endphp

        <section class="card akun-card" id="akun">

            <div class="card-header">

                <div class="card-title">
                    Akun Login Peserta
                </div>

                <div class="card-description">
                    Akun untuk peserta mengisi biodata dan logbook di website.
                </div>

            </div>

            <div class="card-body">

                <x-alert type="success" :message="session('akun_status')" />
                <x-alert type="error" :message="session('akun_error')" />

                @if(session('password_sementara'))
                    <div class="akun-password" role="status">
                        <span>Password sementara:</span>
                        <code>{{ session('password_sementara') }}</code>
                        <span>
                            Catat dan berikan kepada peserta sekarang. Password ini <strong>hanya ditampilkan sekali</strong>
                            dan wajib diganti peserta saat login pertama.
                        </span>
                    </div>
                @endif

                @if($akun)

                    <div class="akun-info">

                        <div class="info-item">
                            <div class="info-label">Email Login</div>
                            <div class="info-value">{{ $akun->email }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Status Akun</div>
                            <div class="info-value">
                                <span class="status-badge {{ $akun->aktif ? 'status-aktif' : 'status-selesai' }}">
                                    {{ $akun->aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Password</div>
                            <div class="info-value">
                                {{ $akun->wajib_ganti_password ? 'Masih sementara (belum diganti)' : 'Sudah diganti peserta' }}
                            </div>
                        </div>

                    </div>

                    <div class="akun-actions">

                        <form
                            method="POST"
                            action="{{ route('superadmin.peserta-magang.akun.reset', $pesertaMagang) }}"
                            onsubmit="return confirm('Buat password sementara baru? Password lama tidak bisa dipakai lagi.')"
                        >
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-secondary">↻ Reset Password</button>
                        </form>

                        <form method="POST" action="{{ route('superadmin.peserta-magang.akun.status', $pesertaMagang) }}">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-secondary">
                                {{ $akun->aktif ? '⏸ Nonaktifkan Akun' : '▶ Aktifkan Akun' }}
                            </button>
                        </form>

                        <form
                            method="POST"
                            action="{{ route('superadmin.peserta-magang.akun.destroy', $pesertaMagang) }}"
                            onsubmit="return confirm('Hapus akun login peserta ini? Data peserta tetap tersimpan.')"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus Akun</button>
                        </form>

                    </div>

                @else

                    <form
                        method="POST"
                        action="{{ route('superadmin.peserta-magang.akun.store', $pesertaMagang) }}"
                        class="akun-form"
                    >
                        @csrf

                        <x-form.input
                            name="email"
                            type="email"
                            label="Email Login"
                            :value="$pesertaMagang->pengajuanMagang?->mahasiswa?->email"
                            maxlength="255"
                            required
                        />

                        <button type="submit" class="btn btn-orange">+ Buat Akun Login</button>

                    </form>

                    <p class="akun-hint">
                        Sistem membuat password sementara yang ditampilkan sekali setelah akun dibuat.
                        Email terisi otomatis dari data pendaftaran bila ada.
                    </p>

                @endif

            </div>

        </section>

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

                    {{-- LOGBOOK --}}

                    <a
                        href="{{ route('superadmin.peserta-magang.logbook', ['pesertaMagang' => $pesertaMagang, 'bulan' => $bulanDipilih->format('Y-m')]) }}"
                        class="action-item"
                    >
                        <span class="action-icon">📒</span>
                        <span>
                            Logbook Kegiatan
                            @if($ringkasanLogbook['total'] > 0)
                                <small class="action-meta">
                                    {{ $ringkasanLogbook['Diterima'] }}/{{ $ringkasanLogbook['total'] }} hari diterima
                                    @if($ringkasanLogbook['belum'] > 0)
                                        · {{ $ringkasanLogbook['belum'] }} belum diperiksa
                                    @endif
                                </small>
                            @endif
                        </span>
                    </a>

                    {{-- PENILAIAN --}}

                    <a
                        href="{{ route('superadmin.peserta-magang.penilaian', $pesertaMagang) }}"
                        class="action-item"
                    >
                        <span class="action-icon">★</span>
                        <span>
                            Penilaian Akhir
                            <small class="action-meta">
                                @if($pesertaMagang->penilaian)
                                    Nilai {{ number_format($pesertaMagang->penilaian->nilai_akhir, 2, ',', '.') }}
                                    (predikat {{ $pesertaMagang->penilaian->predikat }})
                                @else
                                    Belum dinilai
                                @endif
                            </small>
                        </span>
                    </a>

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

                {{-- PILIH BULAN & MUAT ULANG --}}

                <div class="absensi-toolbar">

                    <form method="GET" action="{{ route('superadmin.peserta-magang.show', $pesertaMagang) }}" class="month-form">
                        <label for="bulan">Bulan</label>
                        <select id="bulan" name="bulan" onchange="this.form.submit()">
                            @foreach($pilihanBulan as $nilai => $label)
                                <option value="{{ $nilai }}" @selected($nilai === $bulanDipilih->format('Y-m'))>{{ $label }}</option>
                            @endforeach
                        </select>
                        <noscript><button type="submit" class="toolbar-button">Tampilkan</button></noscript>
                    </form>

                    @if($pesertaMagang->google_sheet_url)
                        <form method="POST" action="{{ route('superadmin.peserta-magang.absensi.refresh', $pesertaMagang) }}">
                            @csrf
                            <input type="hidden" name="bulan" value="{{ $bulanDipilih->format('Y-m') }}">
                            <button type="submit" class="toolbar-button">⟳ Muat ulang data</button>
                        </form>
                    @endif

                </div>

                <div class="sheet-note">

                    @if($tabAktif)
                        Tab <strong>{{ $tabAktif }}</strong>
                        @if($diambilPada)
                            · data diambil {{ $diambilPada->diffForHumans() }}
                            (disimpan sementara {{ intdiv(config('absensi.cache_seconds'), 60) }} menit).
                        @endif
                        <br>
                    @endif

                    Kolom
                    <strong>Activity, Output, Lokasi, dan Action</strong>
                    tetap diisi oleh mahasiswa melalui Google Sheets.
                    Admin dapat mengubah empat waktu absensi melalui MAGENTA.

                </div>

                @if(count($sheetRows) > 0)

                    {{-- =================================================
                         REKAP ABSENSI
                    ================================================== --}}


                    <div class="attendance-summary">

                        @foreach([
                            'tercatat' => ['Hari Tercatat', 'Baris yang terisi di Google Sheets.'],
                            'hadir' => ['Hadir', 'Memiliki data waktu absensi.'],
                            'izin' => ['Izin', 'Activity berisi "izin".'],
                            'sakit' => ['Sakit', 'Activity berisi "sakit".'],
                            'belum_diisi' => ['Belum Diisi', 'Tanpa waktu & keterangan.'],
                        ] as $kunci => [$label, $keterangan])
                            <div class="summary-card summary-{{ $kunci }}">
                                <div class="summary-label">{{ $label }}</div>
                                <div class="summary-value">{{ $rekap[$kunci] }}</div>
                                <div class="summary-description">{{ $keterangan }}</div>
                            </div>
                        @endforeach

                    </div>

                    {{-- REKAP KEHADIRAN & UANG SAKU (mengikuti footer template sheet) --}}

                    <div class="allowance-box">

                        <div class="allowance-item">
                            <span>Masuk penuh</span>
                            <strong>{{ $rekap['hari_penuh'] }} hari</strong>
                            <small>× Rp {{ number_format(config('absensi.uang_saku.hari_penuh'), 0, ',', '.') }}</small>
                        </div>

                        <div class="allowance-item">
                            <span>Masuk setengah hari</span>
                            <strong>{{ $rekap['setengah_hari'] }} hari</strong>
                            <small>× Rp {{ number_format(config('absensi.uang_saku.setengah_hari'), 0, ',', '.') }}</small>
                        </div>

                        <div class="allowance-item">
                            <span>Absen / izin / sakit</span>
                            <strong>{{ $rekap['izin'] + $rekap['sakit'] }} hari</strong>
                            @if($rekap['tidak_lengkap'])
                                <small>{{ $rekap['tidak_lengkap'] }} hari jam belum lengkap</small>
                            @endif
                        </div>

                        <div class="allowance-item allowance-total">
                            <span>Total uang saku</span>
                            <strong>Rp {{ number_format($uangSaku, 0, ',', '.') }}</strong>
                            <small>{{ $bulanDipilih->translatedFormat('F Y') }}</small>
                        </div>

                    </div>


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

                                            @php($statusBaris = \App\Services\RekapAbsensi::statusBaris($row))
                                            @if(in_array($statusBaris, ['izin', 'sakit'], true))
                                                <span class="row-status row-status-{{ $statusBaris }}">{{ ucfirst($statusBaris) }}</span>
                                            @endif
                                        </td>

                                        {{-- PAGI MASUK --}}

                                        <td>

                                            <input
                                                type="time"
                                                name="pagi_masuk"
                                                form="{{ $formId }}"
                                                class="attendance-input"
                                                value="{{ \App\Services\RekapAbsensi::normalisasiJam($row['pagi_masuk']) }}"
                                            >

                                        </td>

                                        {{-- PAGI KELUAR --}}

                                        <td>

                                            <input
                                                type="time"
                                                name="pagi_keluar"
                                                form="{{ $formId }}"
                                                class="attendance-input"
                                                value="{{ \App\Services\RekapAbsensi::normalisasiJam($row['pagi_keluar']) }}"
                                            >

                                        </td>

                                        {{-- SIANG MASUK --}}

                                        <td>

                                            <input
                                                type="time"
                                                name="siang_masuk"
                                                form="{{ $formId }}"
                                                class="attendance-input"
                                                value="{{ \App\Services\RekapAbsensi::normalisasiJam($row['siang_masuk']) }}"
                                            >

                                        </td>

                                        {{-- SIANG KELUAR --}}

                                        <td>

                                            <input
                                                type="time"
                                                name="siang_keluar"
                                                form="{{ $formId }}"
                                                class="attendance-input"
                                                value="{{ \App\Services\RekapAbsensi::normalisasiJam($row['siang_keluar']) }}"
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
                                                    name="bulan"
                                                    value="{{ $bulanDipilih->format('Y-m') }}"
                                                >

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
                            {{ $bulanDipilih->translatedFormat('F Y') }}
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
