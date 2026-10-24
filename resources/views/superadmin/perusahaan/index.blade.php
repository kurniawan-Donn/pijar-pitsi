<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Profil Perusahaan - MAGENTA
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
            color: #243b5a;
        }

        a {
            text-decoration: none;
        }

        button,
        input,
        textarea {
            font-family: Arial, Helvetica, sans-serif;
        }

        /* =====================================================
           LAYOUT
        ====================================================== */

        .layout {
            min-height: 100vh;
        }

        /* =====================================================
           SIDEBAR
        ====================================================== */

        .sidebar {
            width: 275px;
            min-width: 275px;

            background: #12345b;
            color: white;

            position: fixed;

            top: 0;
            left: 0;
            bottom: 0;

            padding: 28px 18px 24px;

            z-index: 1000;

            overflow-y: auto;
        }

        .brand {
            display: flex;
            align-items: flex-start;

            gap: 12px;

            padding: 4px 6px 25px;

            border-bottom:
                1px solid rgba(255, 255, 255, .12);
        }

        .brand-logo {
            width: 36px;
            height: 36px;

            flex-shrink: 0;

            border-radius: 8px;

            background: #ffffff;
            color: #12345b;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 17px;
            font-weight: 700;
        }

        .brand-text h2 {
            color: #ffffff;

            font-size: 17px;

            line-height: 1.1;

            letter-spacing: .2px;
        }

        .brand-text span {
            display: block;

            margin-top: 4px;

            color:
                rgba(255, 255, 255, .65);

            font-size: 9px;

            line-height: 1.35;

            letter-spacing: .6px;
        }

        /* =====================================================
           MENU
        ====================================================== */

        .menu-title {
            margin: 22px 8px 8px;

            color:
                rgba(255, 255, 255, .62);

            font-size: 9px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: 1.2px;
        }

        .menu {
            display: flex;

            flex-direction: column;

            gap: 4px;
        }

        .menu a {
            display: flex;

            align-items: center;

            gap: 10px;

            min-height: 40px;

            padding: 0 13px;

            margin-bottom: 4px;

            border-radius: 7px;

            color: #e4edf7;

            font-size: 12px;

            font-weight: 600;

            transition: .2s ease;
        }

        .menu a:hover {
            background:
                rgba(255, 255, 255, .08);

            color: #ffffff;
        }

        .menu a.active {
            background: #1779c9;

            color: #ffffff;
        }

        .menu-icon {
            width: 18px;

            min-width: 18px;

            display: flex;

            align-items: center;

            justify-content: center;

            text-align: center;

            font-size: 13px;

            line-height: 1;
        }

        /* =====================================================
           LOGOUT
        ====================================================== */

        .logout-area {
            position: absolute;

            left: 18px;
            right: 18px;
            bottom: 24px;

            padding-top: 15px;

            border-top:
                1px solid rgba(255, 255, 255, .12);
        }

        .logout-area form {
            margin: 0;
        }

        .logout-btn {
            width: 100%;

            min-height: 40px;

            border: none;

            border-radius: 7px;

            background:
                rgba(255, 255, 255, .08);

            color: #e4edf7;

            cursor: pointer;

            font-size: 12px;

            font-weight: 600;

            transition: .2s ease;
        }

        .logout-btn:hover {
            background: #dc3545;

            color: #ffffff;
        }

        /* =====================================================
           MAIN
        ====================================================== */

        .main {
            margin-left: 275px;

            width:
                calc(100% - 275px);

            min-height: 100vh;
        }

        /* =====================================================
           TOPBAR
        ====================================================== */

        .topbar {
            height: 64px;

            background: #ffffff;

            border-bottom:
                1px solid #e5ebf2;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 28px;
        }

        .page-title h1 {
            color: #17365d;

            font-size: 16px;

            line-height: 1.2;
        }

        .page-title p {
            margin-top: 3px;

            color: #7c8da3;

            font-size: 9px;
        }

        .admin-profile {
            display: flex;

            align-items: center;

            gap: 9px;
        }

        .admin-text {
            text-align: right;
        }

        .admin-text strong {
            display: block;

            color: #243b5a;

            font-size: 10px;

            line-height: 1.2;
        }

        .admin-text span {
            display: block;

            margin-top: 2px;

            color: #8a96a6;

            font-size: 8px;
        }

        .avatar {
            width: 30px;

            height: 30px;

            border-radius: 50%;

            background: #eef5ff;

            color: #1768b0;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 11px;

            font-weight: 700;
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

            background: #eaf4ff;

            border:
                1px solid #d8eafb;

            color: #1779c9;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 17px;
        }

        .page-header h2 {
            color: #17365d;

            font-size: 17px;

            line-height: 1.2;
        }

        .page-header p {
            margin-top: 4px;

            color: #7c8da3;

            font-size: 10px;

            line-height: 1.5;
        }

        /* =====================================================
           ALERT
        ====================================================== */

        .alert-success {
            margin-bottom: 16px;

            padding: 12px 15px;

            border:
                1px solid #b7ebcc;

            border-radius: 8px;

            background: #ecfdf3;

            color: #167342;

            font-size: 11px;

            font-weight: 600;
        }

        .alert-error {
            margin-bottom: 16px;

            padding: 13px 15px;

            border:
                1px solid #fecdd3;

            border-radius: 8px;

            background: #fff1f2;

            color: #b42318;

            font-size: 11px;
        }

        .alert-error strong {
            display: block;

            margin-bottom: 7px;

            font-size: 11px;
        }

        .alert-error ul {
            padding-left: 18px;

            line-height: 1.6;
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
                4px solid #f59e0b;

            border-radius: 10px;

            background: linear-gradient(
                135deg,
                #fff8e8,
                #fffdf7
            );
        }

        .notice-icon {
            width: 27px;

            height: 27px;

            min-width: 27px;

            border-radius: 7px;

            background: #fff0c7;

            color: #c27a00;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 12px;

            font-weight: 700;
        }

        .notice strong {
            display: block;

            margin-bottom: 3px;

            color: #7a5200;

            font-size: 11px;
        }

        .notice p {
            color: #8a6a2b;

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
                1px solid #e2e9f1;

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
                1px solid #edf1f5;
        }

        .form-card-icon {
            width: 34px;

            height: 34px;

            min-width: 34px;

            border-radius: 8px;

            background: #eef6ff;

            color: #1779c9;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 14px;
        }

        .form-card-header h3 {
            color: #17365d;

            font-size: 12px;

            line-height: 1.2;
        }

        .form-card-header p {
            margin-top: 3px;

            color: #8996a7;

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

            color: #334155;

            font-size: 10px;

            font-weight: 700;
        }

        .required {
            color: #e05252;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;

            padding: 10px 11px;

            border:
                1px solid #d5dce5;

            border-radius: 7px;

            background: #ffffff;

            color: #1f2937;

            font-size: 11px;

            transition: .2s ease;
        }

        .form-group input {
            height: 38px;
        }

        .form-group input:hover,
        .form-group textarea:hover {
            border-color: #b9c5d2;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;

            border-color: #1779c9;

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

            color: #8a96a5;

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

            background: #edf1f5;
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
                1px solid #edf1f5;
        }

        .action-left {
            color: #8995a5;

            font-size: 9px;
        }

        .action-left b {
            color: #e05252;
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
            background: #eef1f5;

            color: #526171;
        }

        .btn-secondary:hover {
            background: #dfe5eb;
        }

        .btn-primary {
            background: #1779c9;

            color: #ffffff;

            box-shadow:
                0 4px 10px
                rgba(23, 121, 201, .16);
        }

        .btn-primary:hover {
            background: #1265aa;

            transform: translateY(-1px);
        }

        /* =====================================================
           FOOT NOTE
        ====================================================== */

        .foot-note {
            margin-top: 15px;

            color: #98a2b0;

            font-size: 9px;

            text-align: center;
        }

        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 1050px) {

            .sidebar {
                width: 240px;

                min-width: 240px;
            }

            .main {
                margin-left: 240px;

                width:
                    calc(100% - 240px);
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .full {
                grid-column: auto;
            }

        }


        @media (max-width: 800px) {

            .sidebar {
                position: relative;

                width: 100%;

                min-width: 0;

                height: auto;

                padding:
                    20px 16px;
            }

            .main {
                margin-left: 0;

                width: 100%;
            }

            .logout-area {
                position: static;

                margin-top: 18px;
            }

            .topbar {
                height: auto;

                min-height: 64px;

                padding:
                    14px 18px;

                gap: 12px;
            }

            .admin-profile {
                display: none;
            }

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

            .brand-text h2 {
                font-size: 16px;
            }

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

</head>


<body>

<div class="layout">


    {{-- =====================================================
         SIDEBAR
    ====================================================== --}}

    <aside class="sidebar">


        {{-- BRAND --}}

        <div class="brand">

            <div class="brand-logo">
                M
            </div>


            <div class="brand-text">

                <h2>
                    MAGENTA
                </h2>


                <span>
                    SISTEM INFORMASI MAGANG
                </span>

            </div>

        </div>


        {{-- =================================================
             UTAMA
        ================================================== --}}

        <div class="menu-title">
            UTAMA
        </div>


        <div class="menu">

            {{-- DASHBOARD BERDASARKAN ROLE --}}

            <a
                href="{{ auth()->user()->role === 'super_admin'
                    ? route('superadmin.dashboard')
                    : route('admin.dashboard') }}"
            >

                <span class="menu-icon">
                    ▣
                </span>

                Dashboard

            </a>

        </div>


        {{-- =================================================
             SISTEM
        ================================================== --}}

        <div class="menu-title">
            SISTEM
        </div>


        <div class="menu">


            {{-- PROFIL PERUSAHAAN --}}

            <a
                href="{{ route('superadmin.perusahaan.index') }}"
                class="active"
            >

                <span class="menu-icon">
                    ▰
                </span>

                Profil Perusahaan

            </a>


            {{-- KELOLA BANNER --}}

            <a
                href="{{ route('superadmin.banners.index') }}"
            >

                <span class="menu-icon">
                    ▧
                </span>

                Kelola Banner

            </a>


            {{-- KELOLA KONTEN --}}

            <a
                href="{{ route('superadmin.pengaturan.index') }}"
            >

                <span class="menu-icon">
                    ⚙
                </span>

                Kelola Konten

            </a>


            {{-- KELOLA PENGGUNA
                 HANYA SUPER ADMIN --}}

            @if(auth()->user()->role === 'super_admin')

                <a
                    href="{{ route('superadmin.pengguna.index') }}"
                >

                    <span class="menu-icon">
                        ◉
                    </span>

                    Kelola Pengguna

                </a>

            @endif


        </div>


        {{-- =================================================
             LOGOUT
        ================================================== --}}

        <div class="logout-area">


            <form
                action="{{ route('logout') }}"
                method="POST"
            >

                @csrf


                <button
                    type="submit"
                    class="logout-btn"
                >

                    ⇥ &nbsp;

                    Logout

                </button>


            </form>


        </div>


    </aside>



    {{-- =====================================================
         MAIN
    ====================================================== --}}

    <main class="main">


        {{-- =================================================
             TOPBAR
        ================================================== --}}

        <header class="topbar">


            <div class="page-title">

                <h1>
                    Profil Perusahaan
                </h1>


                <p>
                    Kelola informasi perusahaan MAGENTA
                </p>

            </div>


            {{-- USER PROFILE --}}

            <div class="admin-profile">


                <div class="admin-text">

                    <strong>
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </strong>


                    <span>

                        {{ auth()->user()->role === 'super_admin'
                            ? 'Super Admin'
                            : 'Admin' }}

                    </span>

                </div>


                <div class="avatar">

                    {{ strtoupper(
                        substr(
                            auth()->user()->name ?? 'A',
                            0,
                            1
                        )
                    ) }}

                </div>


            </div>


        </header>



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
                        ▰
                    </div>


                    <div>

                        <h2>
                            Informasi Perusahaan
                        </h2>


                        <p>
                            Informasi berikut akan digunakan
                            pada halaman website MAGENTA.
                        </p>

                    </div>


                </div>


            </div>


            {{-- =================================================
                 SUCCESS
            ================================================== --}}

            @if(session('success'))

                <div class="alert-success">

                    ✓ &nbsp;

                    {{ session('success') }}

                </div>

            @endif


            {{-- =================================================
                 ERROR
            ================================================== --}}

            @if($errors->any())

                <div class="alert-error">


                    <strong>
                        Periksa data berikut:
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
                        perusahaan pada website MAGENTA.
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
                        ✎
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
                                    yang dapat ditampilkan pada website MAGENTA.
                                </div>


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

                                    ✓
                                    Simpan Perubahan

                                </button>


                            </div>


                        </div>


                    </form>


                </div>


            </div>


            {{-- FOOT NOTE --}}

            <div class="foot-note">

                MAGENTA
                &nbsp;•&nbsp;
                Sistem Informasi Magang

            </div>


        </div>


    </main>


</div>


</body>

</html>