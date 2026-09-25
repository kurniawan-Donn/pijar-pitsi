{{-- resources/views/superadmin/peserta-magang/show.blade.php --}}

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Detail Peserta Magang - MAGENTA
    </title>


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #263238;
            min-height: 100vh;
        }


        a {
            text-decoration: none;
        }


        button {
            font-family: inherit;
        }


        /* =====================================================
           LAYOUT
        ====================================================== */

        .sidebar {

            position: fixed;

            top: 0;
            left: 0;
            bottom: 0;

            width: 275px;

            background: #12345b;

            color: white;

            padding: 24px 18px;

            overflow-y: auto;

            z-index: 1000;

        }


        .main-wrapper {

            margin-left: 275px;

            min-height: 100vh;

        }


        /* =====================================================
           BRAND
        ====================================================== */

        .brand {

            display: flex;

            align-items: center;

            gap: 12px;

            padding:
                4px 8px 28px;

            border-bottom:
                1px solid rgba(255,255,255,0.12);

            margin-bottom: 25px;

        }


        .brand-mark {

            width: 44px;
            height: 44px;

            background: white;

            color: #12345b;

            border-radius: 10px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 20px;

            font-weight: 800;

            flex-shrink: 0;

        }


        .brand-name {

            font-size: 18px;

            font-weight: 800;

            line-height: 1.2;

        }


        .brand-subtitle {

            margin-top: 3px;

            font-size: 11px;

            color:
                rgba(255,255,255,0.65);

        }


        /* =====================================================
           SIDEBAR MENU
        ====================================================== */

        .menu-section {

            margin-bottom: 25px;

        }


        .menu-title {

            color:
                rgba(255,255,255,0.45);

            font-size: 10px;

            font-weight: 800;

            letter-spacing: 1.2px;

            margin:
                0 10px 9px;

            text-transform: uppercase;

        }


        .menu {

            display: grid;

            gap: 5px;

        }


        .menu-link {

            display: flex;

            align-items: center;

            gap: 11px;

            color:
                rgba(255,255,255,0.82);

            padding:
                11px 12px;

            border-radius: 8px;

            font-size: 13px;

            font-weight: 600;

            transition: 0.2s ease;

        }


        .menu-link:hover {

            background:
                rgba(255,255,255,0.08);

            color: white;

        }


        .menu-link.active {

            background:
                rgba(255,255,255,0.14);

            color: white;

            box-shadow:
                inset 3px 0 0 #f47b20;

        }


        .menu-icon {

            width: 20px;

            text-align: center;

            font-size: 15px;

            flex-shrink: 0;

        }


        /* =====================================================
           SIDEBAR BOTTOM
        ====================================================== */

        .sidebar-bottom {

            margin-top: 35px;

            padding-top: 20px;

            border-top:
                1px solid rgba(255,255,255,0.12);

        }


        .logout-button {

            width: 100%;

            display: flex;

            align-items: center;

            gap: 11px;

            border: none;

            background: transparent;

            color:
                rgba(255,255,255,0.78);

            padding:
                11px 12px;

            border-radius: 8px;

            cursor: pointer;

            font-size: 13px;

            font-weight: 600;

            text-align: left;

        }


        .logout-button:hover {

            background:
                rgba(255,255,255,0.08);

            color: white;

        }


        /* =====================================================
           TOPBAR
        ====================================================== */

        .topbar {

            min-height: 78px;

            background: white;

            border-bottom:
                1px solid #e3eaf1;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding:
                16px 32px;

        }


        .page-title {

            font-size: 20px;

            font-weight: 800;

            color: #17313d;

        }


        .page-subtitle {

            margin-top: 4px;

            color: #7b8b96;

            font-size: 12px;

        }


        .user-info {

            display: flex;

            align-items: center;

            gap: 12px;

        }


        .user-avatar {

            width: 40px;
            height: 40px;

            border-radius: 50%;

            background: #eaf3f8;

            color: #0878a8;

            display: flex;

            align-items: center;

            justify-content: center;

            font-weight: 800;

            font-size: 14px;

        }


        .user-name {

            font-size: 13px;

            font-weight: 700;

            color: #263238;

        }


        .user-role {

            margin-top: 3px;

            font-size: 11px;

            color: #7b8b96;

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
           SHEETS BOX
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
           ABSENSI PREVIEW
        ====================================================== */

        .absensi-table-wrapper {

            overflow-x: auto;

        }


        .absensi-table {

            width: 100%;

            border-collapse: collapse;

            min-width: 650px;

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

            white-space: nowrap;

        }


        .absensi-table tr:last-child td {

            border-bottom: none;

        }


        .absensi-status {

            display: inline-flex;

            padding:
                5px 8px;

            border-radius: 15px;

            font-size: 9px;

            font-weight: 800;

            text-transform: uppercase;

        }


        .absensi-hadir {

            background: #eaf8f0;

            color: #16834b;

        }


        .absensi-izin {

            background: #fff5e9;

            color: #b86618;

        }


        .absensi-sakit {

            background: #eef4ff;

            color: #3867ad;

        }


        .absensi-alpa {

            background: #fff0f0;

            color: #c62828;

        }


        .empty-absensi {

            text-align: center;

            padding:
                40px 20px;

            color: #8a99a3;

            font-size: 12px;

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

        @media (max-width: 1000px) {

            .sidebar {

                width: 230px;

            }


            .main-wrapper {

                margin-left: 230px;

            }


            .content {

                padding: 22px;

            }


            .topbar {

                padding:
                    16px 22px;

            }


            .dashboard-grid {

                grid-template-columns: 1fr;

            }

        }


        @media (max-width: 700px) {

            .sidebar {

                position: static;

                width: 100%;

                min-height: auto;

            }


            .main-wrapper {

                margin-left: 0;

            }


            .topbar {

                min-height: 78px;

                padding:
                    15px 20px;

            }


            .content {

                padding: 18px;

            }


            .content-header {

                flex-direction: column;

            }


            .user-info {

                display: none;

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

    </style>

</head>


<body>


    @php

        $role = auth()->user()->role;

        $isSuperAdmin = $role === 'super_admin';

        $dashboardRoute = $isSuperAdmin
            ? route('superadmin.dashboard')
            : route('admin.dashboard');

        $roleLabel = $isSuperAdmin
            ? 'Super Admin'
            : 'Admin';

        $userName = auth()->user()->name;

        $userInitial = strtoupper(
            substr($userName, 0, 1)
        );

        $pesertaInitial = strtoupper(
            substr($pesertaMagang->nama, 0, 1)
        );

    @endphp


    {{-- =====================================================
         SIDEBAR
    ====================================================== --}}

    <aside class="sidebar">


        <div class="brand">

            <div class="brand-mark">
                M
            </div>

            <div>

                <div class="brand-name">
                    MAGENTA
                </div>

                <div class="brand-subtitle">
                    Sistem Informasi Magang
                </div>

            </div>

        </div>


        {{-- UTAMA --}}

        <div class="menu-section">

            <div class="menu-title">
                Utama
            </div>

            <nav class="menu">

                <a
                    href="{{ $dashboardRoute }}"
                    class="menu-link"
                >

                    <span class="menu-icon">
                        ▦
                    </span>

                    Dashboard

                </a>

            </nav>

        </div>


        {{-- SISTEM --}}

        <div class="menu-section">

            <div class="menu-title">
                Sistem
            </div>

            <nav class="menu">


                <a
                    href="{{ route('superadmin.perusahaan.index') }}"
                    class="menu-link"
                >

                    <span class="menu-icon">
                        ◉
                    </span>

                    Profil Perusahaan

                </a>


                <a
                    href="{{ route('superadmin.banners.index') }}"
                    class="menu-link"
                >

                    <span class="menu-icon">
                        ▣
                    </span>

                    Kelola Banner

                </a>


                <a
                    href="{{ route('superadmin.pengaturan.index') }}"
                    class="menu-link"
                >

                    <span class="menu-icon">
                        ✎
                    </span>

                    Kelola Konten

                </a>


                <a
                    href="{{ route('superadmin.peserta-magang.index') }}"
                    class="menu-link active"
                >

                    <span class="menu-icon">
                        ♙
                    </span>

                    Data Peserta

                </a>


                @if($isSuperAdmin)

                    <a
                        href="{{ route('superadmin.pengguna.index') }}"
                        class="menu-link"
                    >

                        <span class="menu-icon">
                            ♟
                        </span>

                        Kelola Pengguna

                    </a>

                @endif


            </nav>

        </div>


        {{-- LOGOUT --}}

        <div class="sidebar-bottom">

            <form
                action="{{ route('logout') }}"
                method="POST"
            >

                @csrf

                <button
                    type="submit"
                    class="logout-button"
                >

                    <span class="menu-icon">
                        ⇥
                    </span>

                    Keluar

                </button>

            </form>

        </div>


    </aside>


    {{-- =====================================================
         MAIN
    ====================================================== --}}

    <div class="main-wrapper">


        {{-- TOPBAR --}}

        <header class="topbar">


            <div>

                <div class="page-title">
                    Detail Peserta Magang
                </div>

                <div class="page-subtitle">
                    Informasi lengkap peserta dan koneksi Google Sheets absensi.
                </div>

            </div>


            <div class="user-info">

                <div class="user-avatar">
                    {{ $userInitial }}
                </div>

                <div>

                    <div class="user-name">
                        {{ $userName }}
                    </div>

                    <div class="user-role">
                        {{ $roleLabel }}
                    </div>

                </div>

            </div>


        </header>


        {{-- CONTENT --}}

        <main class="content">


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


            {{-- MAIN GRID --}}

            <div class="dashboard-grid">


                {{-- =================================================
                     INFORMASI PESERTA
                ================================================== --}}

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


                {{-- =================================================
                     GOOGLE SHEETS
                ================================================== --}}

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
                 AKSI
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


                        {{-- EDIT --}}

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
                 RIWAYAT ABSENSI
            ================================================== --}}

            <section
                class="card"
                style="margin-top:20px;"
            >


                <div class="card-header">

                    <div class="card-title">
                        Riwayat Absensi
                    </div>

                    <div class="card-description">
                        Data absensi yang tersimpan pada database MAGENTA.
                    </div>

                </div>


                @if($pesertaMagang->absensis->count() > 0)


                    <div class="absensi-table-wrapper">

                        <table class="absensi-table">

                            <thead>

                                <tr>

                                    <th>
                                        Tanggal
                                    </th>

                                    <th>
                                        Jam Masuk
                                    </th>

                                    <th>
                                        Jam Pulang
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th>
                                        Keterangan
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach($pesertaMagang->absensis as $absensi)

                                    <tr>

                                        <td>

                                            {{ $absensi->tanggal
                                                ? $absensi->tanggal->format('d F Y')
                                                : '-'
                                            }}

                                        </td>


                                        <td>

                                            {{ $absensi->jam_masuk ?: '-' }}

                                        </td>


                                        <td>

                                            {{ $absensi->jam_pulang ?: '-' }}

                                        </td>


                                        <td>

                                            @if($absensi->status === 'hadir')

                                                <span class="absensi-status absensi-hadir">
                                                    Hadir
                                                </span>

                                            @elseif($absensi->status === 'izin')

                                                <span class="absensi-status absensi-izin">
                                                    Izin
                                                </span>

                                            @elseif($absensi->status === 'sakit')

                                                <span class="absensi-status absensi-sakit">
                                                    Sakit
                                                </span>

                                            @else

                                                <span class="absensi-status absensi-alpa">
                                                    Alpa
                                                </span>

                                            @endif

                                        </td>


                                        <td>

                                            {{ $absensi->keterangan ?: '-' }}

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>


                @else


                    <div class="empty-absensi">

                        Belum ada data absensi yang tersimpan di database MAGENTA.

                        <br>

                        Data absensi peserta saat ini dapat dibuka melalui Google Sheets.

                    </div>


                @endif


            </section>


            {{-- =================================================
                 BOTTOM ACTIONS
            ================================================== --}}

            <div class="bottom-actions">


                <div class="bottom-left">

                    <a
                        href="{{ route(
                            'superadmin.peserta-magang.edit',
                            $pesertaMagang
                        ) }}"
                        class="btn btn-secondary"
                    >
                        ✎ Edit Peserta
                    </a>


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


                <form
                    action="{{ route(
                        'superadmin.peserta-magang.destroy',
                        $pesertaMagang
                    ) }}"
                    method="POST"
                    onsubmit="return confirm(
                        'Yakin ingin menghapus peserta ini? Data absensinya juga akan ikut terhapus.'
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


        </main>


    </div>


</body>

</html>