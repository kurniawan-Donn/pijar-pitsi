{{-- resources/views/superadmin/peserta-magang/edit.blade.php --}}

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Edit Peserta Magang - MAGENTA
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


        button,
        input,
        select {
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


        /* =====================================================
           ERROR
        ====================================================== */

        .error-alert {

            background: #fff0f0;

            border:
                1px solid #ffd0d0;

            color: #b42318;

            border-radius: 9px;

            padding:
                13px 16px;

            margin-bottom: 20px;

            font-size: 13px;
        }


        .error-alert strong {

            display: block;

            margin-bottom: 7px;
        }


        .error-alert ul {

            margin-left: 18px;
        }


        .error-alert li {

            margin-bottom: 3px;
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
           GOOGLE SHEETS
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
           CURRENT SHEET LINK
        ====================================================== */

        .current-sheet {

            margin-top: 10px;

            background: white;

            border:
                1px solid #e0e8ed;

            border-radius: 8px;

            padding:
                11px 12px;

            font-size: 11px;

            line-height: 1.6;

            word-break: break-all;

            color: #0878a8;
        }


        .current-sheet-label {

            display: block;

            color: #87959e;

            font-size: 10px;

            font-weight: 800;

            text-transform: uppercase;

            letter-spacing: 0.5px;

            margin-bottom: 4px;
        }


        .current-sheet a {

            color: #0878a8;
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


            .form-grid {

                grid-template-columns: 1fr;
            }


            .form-group.full {

                grid-column: auto;
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
                    Edit Peserta Magang
                </div>

                <div class="page-subtitle">
                    Perbarui informasi peserta dan tautan Google Sheets absensi.
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
                        Edit Peserta
                    </div>

                    <div class="heading-description">
                        Perbarui data peserta magang yang tersimpan di MAGENTA.
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


            {{-- ERROR --}}

            @if($errors->any())

                <div class="error-alert">

                    <strong>
                        Data belum dapat diperbarui.
                    </strong>

                    <ul>

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


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
                                            ▣
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


        </main>


    </div>


</body>

</html>