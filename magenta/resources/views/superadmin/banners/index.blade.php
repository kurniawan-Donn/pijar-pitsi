<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Kelola Banner - MAGENTA</title>

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


        /* =====================================================
           BRAND
        ====================================================== */

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
            min-width: 36px;

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


        .topbar-left h1 {
            color: #17365d;

            font-size: 16px;

            line-height: 1.2;
        }


        .topbar-left p {
            margin-top: 3px;

            color: #7c8da3;

            font-size: 9px;
        }


        .user-box {
            display: flex;

            align-items: center;

            gap: 9px;
        }


        .user-info {
            text-align: right;
        }


        .user-name {
            color: #243b5a;

            font-size: 10px;

            font-weight: 700;

            line-height: 1.2;
        }


        .user-role {
            margin-top: 2px;

            color: #8a96a6;

            font-size: 8px;
        }


        .user-avatar {
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

            gap: 15px;

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

            min-width: 42px;

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

        .alert {
            margin-bottom: 16px;

            padding: 12px 15px;

            border-radius: 8px;

            font-size: 11px;
        }


        .alert-success {
            background: #ecfdf3;

            border:
                1px solid #b7ebcc;

            color: #167342;

            font-weight: 600;
        }


        .alert-danger {
            background: #fff1f2;

            border:
                1px solid #fecdd3;

            color: #b42318;
        }


        .alert-danger strong {
            display: block;

            margin-bottom: 7px;
        }


        .alert-danger ul {
            margin: 0;

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
           BANNER LAYOUT
        ====================================================== */

        .banner-layout {
            display: grid;

            grid-template-columns:
                minmax(0, 1.25fr)
                minmax(340px, .75fr);

            gap: 20px;

            align-items: start;
        }


        /* =====================================================
           CARD
        ====================================================== */

        .card {
            background: #ffffff;

            border:
                1px solid #e2e9f1;

            border-radius: 12px;

            box-shadow:
                0 4px 16px
                rgba(18, 52, 91, .05);

            overflow: hidden;
        }


        .card-header {
            padding: 17px 20px;

            border-bottom:
                1px solid #edf1f5;
        }


        .card-header h3 {
            color: #17365d;

            font-size: 12px;

            line-height: 1.2;
        }


        .card-header p {
            margin-top: 4px;

            color: #8996a7;

            font-size: 9px;

            line-height: 1.5;
        }


        /* =====================================================
           PREVIEW
        ====================================================== */

        .preview-area {
            padding: 20px;
        }


        .current-label {
            display: inline-flex;

            align-items: center;

            gap: 6px;

            margin-bottom: 12px;

            padding: 6px 10px;

            border-radius: 20px;

            background: #ecfdf3;

            color: #168044;

            font-size: 9px;

            font-weight: 700;
        }


        .current-banner {
            overflow: hidden;

            border:
                1px solid #e2e9f1;

            border-radius: 10px;

            background: #f8fafc;
        }


        .current-banner-image {
            display: block;

            width: 100%;

            height: 290px;

            object-fit: cover;

            background: #e9eef4;
        }


        .no-image {
            width: 100%;

            height: 290px;

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: center;

            background: #f1f5f9;

            color: #94a3b8;

            font-size: 10px;
        }


        .no-image-icon {
            margin-bottom: 9px;

            font-size: 34px;
        }


        .preview-info {
            padding: 16px;

            background: #ffffff;
        }


        .preview-info h4 {
            color: #17365d;

            font-size: 15px;

            line-height: 1.35;
        }


        .preview-description {
            margin-top: 7px;

            color: #64748b;

            font-size: 10px;

            line-height: 1.65;

            white-space: pre-line;
        }


        .preview-button {
            display: inline-block;

            margin-top: 11px;

            padding: 7px 11px;

            border-radius: 6px;

            background: #f58220;

            color: #ffffff;

            font-size: 9px;

            font-weight: 700;
        }


        .preview-link {
            margin-top: 7px;

            color: #7c8da3;

            font-size: 8px;

            line-height: 1.5;

            word-break: break-all;
        }


        /* =====================================================
           FORM
        ====================================================== */

        .form-body {
            padding: 20px;
        }


        .form-group {
            margin-bottom: 16px;
        }


        .form-group label {
            display: block;

            margin-bottom: 6px;

            color: #334155;

            font-size: 10px;

            font-weight: 700;
        }


        .form-group label span {
            color: #dc3545;
        }


        .form-control {
            width: 100%;

            min-height: 37px;

            padding: 9px 10px;

            border:
                1px solid #d5dce5;

            border-radius: 7px;

            background: #ffffff;

            color: #1f2937;

            font-size: 10px;

            outline: none;

            transition: .2s ease;
        }


        .form-control:hover {
            border-color: #b9c5d2;
        }


        .form-control:focus {
            border-color: #1779c9;

            box-shadow:
                0 0 0 3px
                rgba(23, 121, 201, .09);
        }


        textarea.form-control {
            min-height: 90px;

            resize: vertical;

            line-height: 1.5;
        }


        .file-input {
            padding: 8px;

            background: #f8fafc;

            cursor: pointer;
        }


        .form-help {
            margin-top: 5px;

            color: #8a96a5;

            font-size: 8px;

            line-height: 1.5;
        }


        .old-image {
            margin-top: 9px;

            padding: 9px;

            border:
                1px solid #e2e9f1;

            border-radius: 7px;

            background: #f8fafc;
        }


        .old-image-title {
            margin-bottom: 7px;

            color: #64748b;

            font-size: 8px;

            font-weight: 700;
        }


        .old-image img {
            width: 100%;

            height: 80px;

            display: block;

            object-fit: cover;

            border-radius: 5px;
        }


        /* =====================================================
           BUTTON
        ====================================================== */

        .btn {
            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 6px;

            min-height: 36px;

            padding: 0 13px;

            border: none;

            border-radius: 7px;

            cursor: pointer;

            font-size: 9px;

            font-weight: 700;

            transition: .2s ease;
        }


        .btn-primary {
            background: #1779c9;

            color: #ffffff;

            box-shadow:
                0 4px 10px
                rgba(23, 121, 201, .13);
        }


        .btn-primary:hover {
            background: #1265aa;

            transform: translateY(-1px);
        }


        .btn-danger {
            background: #dc3545;

            color: #ffffff;
        }


        .btn-danger:hover {
            background: #b91c2c;
        }


        .btn-secondary {
            background: #eef1f5;

            color: #526171;
        }


        .btn-secondary:hover {
            background: #dfe5eb;
        }


        /* =====================================================
           FORM ACTION
        ====================================================== */

        .form-actions {
            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 10px;

            margin-top: 19px;

            padding-top: 16px;

            border-top:
                1px solid #edf1f5;
        }


        .form-actions-note {
            color: #98a2b0;

            font-size: 8px;
        }


        .form-actions-right {
            display: flex;

            align-items: center;

            gap: 7px;
        }


        /* =====================================================
           DELETE
        ====================================================== */

        .delete-form {
            margin-top: 9px;
        }


        /* =====================================================
           EMPTY STATE
        ====================================================== */

        .empty-state {
            padding: 50px 25px;

            text-align: center;
        }


        .empty-icon {
            width: 58px;

            height: 58px;

            margin: 0 auto 14px;

            border-radius: 50%;

            background: #eef6ff;

            color: #1779c9;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 25px;
        }


        .empty-state h3 {
            color: #17365d;

            font-size: 15px;
        }


        .empty-state p {
            max-width: 460px;

            margin: 7px auto 0;

            color: #7c8da3;

            font-size: 9px;

            line-height: 1.7;
        }


        .add-banner-card {
            margin-top: 18px;
        }


        /* =====================================================
           FOOT NOTE
        ====================================================== */

        .foot-note {
            margin-top: 15px;

            color: #98a2b0;

            font-size: 8px;

            text-align: center;
        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 1100px) {

            .banner-layout {
                grid-template-columns: 1fr;
            }

        }


        @media (max-width: 800px) {

            .sidebar {
                position: relative;

                width: 100%;

                min-width: 0;

                height: auto;

                padding: 20px 16px;
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

                padding: 14px 18px;
            }


            .content {
                padding: 22px 18px 35px;
            }

        }


        @media (max-width: 550px) {

            .user-box {
                display: none;
            }


            .page-header {
                align-items: flex-start;
            }


            .preview-area,
            .form-body {
                padding: 15px;
            }


            .current-banner-image,
            .no-image {
                height: 210px;
            }


            .form-actions {
                align-items: stretch;

                flex-direction: column;
            }


            .form-actions-right {
                width: 100%;
            }


            .form-actions-right .btn {
                flex: 1;
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

            {{-- DASHBOARD MENGIKUTI ROLE --}}

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
            >

                <span class="menu-icon">
                    ▰
                </span>

                Profil Perusahaan

            </a>


            {{-- KELOLA BANNER --}}

            <a
                href="{{ route('superadmin.banners.index') }}"
                class="active"
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
                    ⇥ &nbsp; Logout
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


            <div class="topbar-left">

                <h1>
                    Kelola Banner
                </h1>


                <p>
                    Kelola banner utama website MAGENTA
                </p>

            </div>


            {{-- USER PROFILE --}}

            <div class="user-box">

                <div class="user-info">

                    <div class="user-name">
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </div>


                    <div class="user-role">

                        {{ auth()->user()->role === 'super_admin'
                            ? 'Super Admin'
                            : 'Admin' }}

                    </div>

                </div>


                <div class="user-avatar">

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
                        ▧
                    </div>


                    <div>

                        <h2>
                            Banner Utama Website
                        </h2>


                        <p>
                            Website MAGENTA menggunakan satu banner utama
                            yang dapat dikelola dari halaman ini.
                        </p>

                    </div>


                </div>


            </div>


            {{-- =================================================
                 SUCCESS
            ================================================== --}}

            @if(session('success'))

                <div class="alert alert-success">

                    ✓ &nbsp;

                    {{ session('success') }}

                </div>

            @endif


            {{-- =================================================
                 ERROR
            ================================================== --}}

            @if($errors->any())

                <div class="alert alert-danger">

                    <strong>
                        Terjadi kesalahan:
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
                        Banner utama MAGENTA
                    </strong>


                    <p>
                        Banner digunakan sebagai tampilan utama pada
                        halaman depan website. Karena website MAGENTA
                        menggunakan satu banner utama, banner yang
                        tersimpan akan menjadi banner aktif.
                    </p>

                </div>

            </div>


            {{-- =================================================
                 JIKA SUDAH ADA BANNER
            ================================================== --}}

            @if($banner)

                <div class="banner-layout">


                    {{-- =================================================
                         PREVIEW
                    ================================================== --}}

                    <div class="card">


                        <div class="card-header">

                            <h3>
                                Banner Saat Ini
                            </h3>


                            <p>
                                Tampilan banner yang sedang digunakan
                                pada halaman utama website.
                            </p>

                        </div>


                        <div class="preview-area">


                            <div class="current-label">

                                ✓

                                Banner Aktif

                            </div>


                            <div class="current-banner">


                                @if($banner->gambar)

                                    <img
                                        src="{{ asset('storage/' . $banner->gambar) }}"
                                        alt="{{ $banner->judul }}"
                                        class="current-banner-image"
                                    >

                                @else

                                    <div class="no-image">

                                        <div class="no-image-icon">
                                            ▧
                                        </div>

                                        Belum ada gambar banner

                                    </div>

                                @endif


                                <div class="preview-info">

                                    <h4>
                                        {{ $banner->judul }}
                                    </h4>


                                    @if($banner->deskripsi)

                                        <div class="preview-description">
                                            {{ $banner->deskripsi }}
                                        </div>

                                    @endif


                                    @if($banner->tombol_teks)

                                        <div>

                                            <span class="preview-button">
                                                {{ $banner->tombol_teks }}
                                            </span>

                                        </div>

                                    @endif


                                    @if($banner->tombol_link)

                                        <div class="preview-link">

                                            Link:

                                            {{ $banner->tombol_link }}

                                        </div>

                                    @endif


                                </div>

                            </div>


                        </div>

                    </div>


                    {{-- =================================================
                         FORM UBAH BANNER
                    ================================================== --}}

                    <div class="card">


                        <div class="card-header">

                            <h3>
                                Ubah Banner
                            </h3>


                            <p>
                                Perbarui informasi banner utama MAGENTA.
                            </p>

                        </div>


                        <div class="form-body">


                            <form
                                action="{{ route('superadmin.banners.store') }}"
                                method="POST"
                                enctype="multipart/form-data"
                            >

                                @csrf


                                {{-- JUDUL --}}

                                <div class="form-group">

                                    <label for="judul">

                                        Judul Banner

                                        <span>
                                            *
                                        </span>

                                    </label>


                                    <input
                                        type="text"
                                        id="judul"
                                        name="judul"
                                        class="form-control"
                                        value="{{ old('judul', $banner->judul) }}"
                                        placeholder="Contoh: Selamat Datang di MAGENTA"
                                        required
                                    >

                                </div>


                                {{-- DESKRIPSI --}}

                                <div class="form-group">

                                    <label for="deskripsi">
                                        Deskripsi
                                    </label>


                                    <textarea
                                        id="deskripsi"
                                        name="deskripsi"
                                        class="form-control"
                                        placeholder="Masukkan deskripsi singkat banner..."
                                    >{{ old('deskripsi', $banner->deskripsi) }}</textarea>

                                </div>


                                {{-- GAMBAR --}}

                                <div class="form-group">

                                    <label for="gambar">
                                        Gambar Banner
                                    </label>


                                    <input
                                        type="file"
                                        id="gambar"
                                        name="gambar"
                                        class="form-control file-input"
                                        accept=".jpg,.jpeg,.png,.webp"
                                    >


                                    <div class="form-help">

                                        Pilih gambar baru jika ingin
                                        mengganti gambar banner.

                                        Format:
                                        JPG, JPEG, PNG, WEBP.

                                        Maksimal 2 MB.

                                    </div>


                                    @if($banner->gambar)

                                        <div class="old-image">

                                            <div class="old-image-title">

                                                Gambar yang sedang digunakan

                                            </div>


                                            <img
                                                src="{{ asset('storage/' . $banner->gambar) }}"
                                                alt="Banner saat ini"
                                            >

                                        </div>

                                    @endif

                                </div>


                                {{-- TEKS TOMBOL --}}

                                <div class="form-group">

                                    <label for="tombol_teks">
                                        Teks Tombol
                                    </label>


                                    <input
                                        type="text"
                                        id="tombol_teks"
                                        name="tombol_teks"
                                        class="form-control"
                                        value="{{ old('tombol_teks', $banner->tombol_teks) }}"
                                        placeholder="Contoh: Daftar Magang"
                                    >


                                    <div class="form-help">

                                        Contoh:
                                        Daftar Magang

                                    </div>

                                </div>


                                {{-- LINK --}}

                                <div class="form-group">

                                    <label for="tombol_link">
                                        Link Tombol
                                    </label>


                                    <input
                                        type="text"
                                        id="tombol_link"
                                        name="tombol_link"
                                        class="form-control"
                                        value="{{ old('tombol_link', $banner->tombol_link) }}"
                                        placeholder="Masukkan link Google Form"
                                    >


                                    <div class="form-help">

                                        Untuk pendaftaran,
                                        masukkan link Google Form.

                                    </div>

                                </div>


                                {{-- ACTION --}}

                                <div class="form-actions">


                                    <div class="form-actions-note">

                                        Banner utama hanya satu.

                                    </div>


                                    <div class="form-actions-right">

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


                            {{-- DELETE --}}

                            <form
                                action="{{ route('superadmin.banners.destroy', $banner) }}"
                                method="POST"
                                class="delete-form"
                                onsubmit="return confirm('Yakin ingin menghapus banner utama?')"
                            >

                                @csrf

                                @method('DELETE')


                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                    style="width:100%;"
                                >

                                    ×

                                    Hapus Banner

                                </button>

                            </form>


                        </div>

                    </div>


                </div>


            @else


                {{-- =================================================
                     BELUM ADA BANNER
                ================================================== --}}

                <div class="card">


                    <div class="empty-state">

                        <div class="empty-icon">
                            ▧
                        </div>


                        <h3>
                            Belum Ada Banner
                        </h3>


                        <p>
                            Website MAGENTA belum memiliki banner utama.
                            Silakan tambahkan banner melalui form di bawah.
                        </p>

                    </div>


                </div>


                {{-- =================================================
                     FORM TAMBAH
                ================================================== --}}

                <div class="card add-banner-card">


                    <div class="card-header">

                        <h3>
                            Tambah Banner Utama
                        </h3>


                        <p>
                            Banner ini akan ditampilkan pada halaman
                            utama website MAGENTA.
                        </p>

                    </div>


                    <div class="form-body">


                        <form
                            action="{{ route('superadmin.banners.store') }}"
                            method="POST"
                            enctype="multipart/form-data"
                        >

                            @csrf


                            {{-- JUDUL --}}

                            <div class="form-group">

                                <label for="judul">

                                    Judul Banner

                                    <span>
                                        *
                                    </span>

                                </label>


                                <input
                                    type="text"
                                    id="judul"
                                    name="judul"
                                    class="form-control"
                                    value="{{ old('judul') }}"
                                    placeholder="Contoh: Selamat Datang di MAGENTA"
                                    required
                                >

                            </div>


                            {{-- DESKRIPSI --}}

                            <div class="form-group">

                                <label for="deskripsi">
                                    Deskripsi
                                </label>


                                <textarea
                                    id="deskripsi"
                                    name="deskripsi"
                                    class="form-control"
                                    placeholder="Masukkan deskripsi banner..."
                                >{{ old('deskripsi') }}</textarea>

                            </div>


                            {{-- GAMBAR --}}

                            <div class="form-group">

                                <label for="gambar">
                                    Gambar Banner
                                </label>


                                <input
                                    type="file"
                                    id="gambar"
                                    name="gambar"
                                    class="form-control file-input"
                                    accept=".jpg,.jpeg,.png,.webp"
                                >


                                <div class="form-help">

                                    Format JPG, JPEG, PNG atau WEBP.

                                    Maksimal 2 MB.

                                </div>

                            </div>


                            {{-- TOMBOL --}}

                            <div class="form-group">

                                <label for="tombol_teks">
                                    Teks Tombol
                                </label>


                                <input
                                    type="text"
                                    id="tombol_teks"
                                    name="tombol_teks"
                                    class="form-control"
                                    value="{{ old('tombol_teks') }}"
                                    placeholder="Contoh: Daftar Magang"
                                >

                            </div>


                            {{-- LINK --}}

                            <div class="form-group">

                                <label for="tombol_link">
                                    Link Tombol
                                </label>


                                <input
                                    type="text"
                                    id="tombol_link"
                                    name="tombol_link"
                                    class="form-control"
                                    value="{{ old('tombol_link') }}"
                                    placeholder="Masukkan link Google Form"
                                >


                                <div class="form-help">

                                    Untuk tombol pendaftaran,
                                    masukkan link Google Form.

                                </div>

                            </div>


                            {{-- ACTION --}}

                            <div class="form-actions">


                                <div class="form-actions-note">

                                    <span style="color:#dc3545;">
                                        *
                                    </span>

                                    Judul wajib diisi.

                                </div>


                                <div class="form-actions-right">

                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >

                                        +

                                        Simpan Banner

                                    </button>

                                </div>


                            </div>


                        </form>


                    </div>

                </div>


            @endif


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