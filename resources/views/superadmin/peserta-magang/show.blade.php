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
    }

    /* =====================================================
       KETERANGAN ITEM AKSI
    ====================================================== */

    .action-meta {
        display: block;

        margin-top: 2px;

        color: #8a99a3;
        font-size: 10px;
        font-weight: 600;
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
                        Google Sheets (Riwayat)
                    </div>

                    <div class="card-description">
                        Buku kerja lama peserta. Kehadiran & logbook kini diisi di website.
                    </div>

                </div>

                <div class="card-body">

                    <div class="sheets-card">

                        <div class="sheets-icon">
                            ▣
                        </div>

                        <div class="sheets-title">
                            Buku Kerja Lama
                        </div>

                        <div class="sheets-description">
                            Aplikasi tidak lagi membaca sheet ini. Isinya bisa diimpor sekali ke Logbook Harian.
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
                                Buka Google Sheets ↗
                            </a>

                            <a
                                href="{{ route('superadmin.peserta-magang.logbook-harian', $pesertaMagang) }}#impor"
                                class="btn btn-secondary"
                                style="width:100%; margin-top:8px;"
                            >
                                Impor ke Logbook Harian
                            </a>

                        @else

                            <div class="sheets-missing">
                                Peserta ini tidak punya link Google Sheets lama.
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

        @include('superadmin.peserta-magang.partials.penempatan')

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
                                Buka Google Sheets (riwayat)
                            </span>

                        </a>

                    @endif

                    {{-- BIODATA --}}

                    <a
                        href="{{ route('superadmin.peserta-magang.biodata', $pesertaMagang) }}"
                        class="action-item"
                    >
                        <span class="action-icon">☷</span>
                        <span>
                            Biodata Peserta
                            <small class="action-meta">
                                @if($pesertaMagang->biodata)
                                    {{ $pesertaMagang->biodata->labelStatus() }} · {{ $pesertaMagang->biodata->progres()['persen'] }}% data wajib terisi
                                @else
                                    Belum diisi
                                @endif
                            </small>
                        </span>
                    </a>

                    {{-- SURAT KETERANGAN --}}

                    <a
                        href="{{ route('superadmin.peserta-magang.surat', $pesertaMagang) }}"
                        class="action-item"
                    >
                        <span class="action-icon">✉</span>
                        <span>
                            Surat Keterangan Magang
                            <small class="action-meta">
                                @if($pesertaMagang->suratKeterangan?->sudahTerbit())
                                    Terbit · {{ $pesertaMagang->suratKeterangan->nomor_surat }}
                                @elseif($pesertaMagang->suratKeterangan)
                                    Draft · belum diterbitkan
                                @else
                                    Dibuat saat magang selesai
                                @endif
                            </small>
                        </span>
                    </a>

                    {{-- FEEDBACK --}}

                    <a
                        href="{{ route('superadmin.peserta-magang.feedback', $pesertaMagang) }}"
                        class="action-item"
                    >
                        <span class="action-icon">✎</span>
                        <span>
                            Feedback Magang
                            <small class="action-meta">
                                {{ $pesertaMagang->feedback ? 'Sudah dikirim · rata-rata '.number_format($pesertaMagang->feedback->rataRata(), 2, ',', '.') : 'Belum dikirim' }}
                            </small>
                        </span>
                    </a>

                    {{-- LOGBOOK HARIAN (DATABASE) --}}

                    <a
                        href="{{ route('superadmin.peserta-magang.logbook-harian', $pesertaMagang) }}"
                        class="action-item"
                    >
                        <span class="action-icon">✓</span>
                        <span>
                            Logbook Harian & Izin
                            <small class="action-meta">Verifikasi, rekap uang saku, dan cetak per periode</small>
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
                        ▣ Google Sheets
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
                    'Yakin ingin menghapus peserta ini? Akun, biodata, logbook, penilaian, feedback, dan surat peserta juga akan ikut terhapus.'
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
