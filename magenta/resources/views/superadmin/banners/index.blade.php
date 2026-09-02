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
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f4f7fb;

            color: #1e293b;
        }


        body {
            min-height: 100vh;
        }


        a {
            text-decoration: none;
        }


        button,
        input,
        textarea {
            font-family: inherit;
        }


        /* =====================================================
           SIDEBAR
        ===================================================== */

        .sidebar {

            position: fixed;

            left: 0;
            top: 0;
            bottom: 0;

            width: 250px;

            background: #062b63;

            color: white;

            padding: 25px 18px;

            overflow-y: auto;

            z-index: 1000;
        }


        .brand {

            text-align: center;

            padding-bottom: 24px;

            margin-bottom: 22px;

            border-bottom:
                1px solid
                rgba(255,255,255,.15);
        }


        .brand h2 {

            margin: 0;

            font-size: 25px;

            font-weight: 800;

            letter-spacing: 1px;
        }


        .brand p {

            margin: 6px 0 0;

            font-size: 10px;

            color: #cbd5e1;

            letter-spacing: .5px;
        }


        .menu-title {

            margin:
                22px 10px 8px;

            font-size: 10px;

            font-weight: 700;

            color: #94a3b8;

            letter-spacing: 1px;
        }


        .menu-link {

            display: flex;

            align-items: center;

            gap: 11px;

            padding: 11px 13px;

            margin-bottom: 5px;

            border-radius: 8px;

            color: #dbeafe;

            font-size: 13px;

            transition: .2s;
        }


        .menu-link:hover {

            background:
                rgba(255,255,255,.10);

            color: white;
        }


        .menu-link.active {

            background: #f58220;

            color: white;

            font-weight: 700;
        }


        .menu-icon {

            width: 20px;

            text-align: center;

            font-size: 15px;
        }


        .logout-area {

            margin-top: 28px;

            padding-top: 18px;

            border-top:
                1px solid
                rgba(255,255,255,.15);
        }


        .logout-button {

            width: 100%;

            border: none;

            padding: 11px 13px;

            border-radius: 8px;

            background:
                rgba(255,255,255,.08);

            color: white;

            cursor: pointer;

            font-size: 13px;

            text-align: left;
        }


        .logout-button:hover {

            background: #dc3545;
        }


        /* =====================================================
           MAIN
        ===================================================== */

        .main {

            margin-left: 250px;

            min-height: 100vh;
        }


        /* =====================================================
           TOPBAR
        ===================================================== */

        .topbar {

            height: 75px;

            background: white;

            border-bottom:
                1px solid
                #e2e8f0;

            padding: 0 35px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            position: sticky;

            top: 0;

            z-index: 900;
        }


        .topbar-left h1 {

            margin: 0;

            color: #062b63;

            font-size: 21px;

            font-weight: 800;
        }


        .topbar-left p {

            margin: 5px 0 0;

            color: #64748b;

            font-size: 12px;
        }


        .user-box {

            display: flex;

            align-items: center;

            gap: 10px;
        }


        .user-avatar {

            width: 40px;
            height: 40px;

            border-radius: 50%;

            background: #eaf2ff;

            color: #062b63;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 14px;

            font-weight: 800;
        }


        .user-name {

            font-size: 13px;

            font-weight: 700;

            color: #334155;
        }


        .user-role {

            margin-top: 2px;

            font-size: 10px;

            color: #64748b;
        }


        /* =====================================================
           CONTENT
        ===================================================== */

        .content {

            padding: 30px 35px;
        }


        /* =====================================================
           ALERT
        ===================================================== */

        .alert {

            padding: 13px 16px;

            border-radius: 9px;

            margin-bottom: 22px;

            font-size: 13px;
        }


        .alert-success {

            background: #ecfdf5;

            color: #047857;

            border:
                1px solid
                #a7f3d0;
        }


        .alert-danger {

            background: #fef2f2;

            color: #b91c1c;

            border:
                1px solid
                #fecaca;
        }


        .alert-danger ul {

            margin: 7px 0 0 18px;

            padding: 0;
        }


        /* =====================================================
           PAGE HEADER
        ===================================================== */

        .page-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 25px;
        }


        .page-header h2 {

            margin: 0;

            color: #062b63;

            font-size: 25px;

            font-weight: 800;
        }


        .page-header p {

            margin: 6px 0 0;

            color: #64748b;

            font-size: 13px;
        }


        /* =====================================================
           BUTTON
        ===================================================== */

        .btn {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 7px;

            padding: 10px 16px;

            border: none;

            border-radius: 8px;

            font-size: 12px;

            font-weight: 700;

            cursor: pointer;

            transition: .2s;
        }


        .btn-primary {

            background: #0b63ce;

            color: white;
        }


        .btn-primary:hover {

            background: #084eaa;
        }


        .btn-danger {

            background: #dc3545;

            color: white;
        }


        .btn-danger:hover {

            background: #b91c2c;
        }


        /* =====================================================
           MAIN GRID
        ===================================================== */

        .banner-layout {

            display: grid;

            grid-template-columns:
                minmax(0, 1.25fr)
                minmax(360px, .75fr);

            gap: 25px;

            align-items: start;
        }


        /* =====================================================
           CARD
        ===================================================== */

        .card {

            background: white;

            border:
                1px solid
                #e2e8f0;

            border-radius: 13px;

            box-shadow:
                0 2px 8px
                rgba(15,23,42,.04);

            overflow: hidden;
        }


        .card-header {

            padding: 20px 22px;

            border-bottom:
                1px solid
                #edf2f7;
        }


        .card-header h3 {

            margin: 0;

            color: #062b63;

            font-size: 17px;

            font-weight: 800;
        }


        .card-header p {

            margin: 5px 0 0;

            color: #64748b;

            font-size: 11px;

            line-height: 1.5;
        }


        /* =====================================================
           CURRENT BANNER
        ===================================================== */

        .preview-area {

            padding: 22px;
        }


        .current-banner {

            border:
                1px solid
                #e2e8f0;

            border-radius: 10px;

            overflow: hidden;

            background: #f8fafc;
        }


        .current-banner-image {

            width: 100%;

            height: 300px;

            display: block;

            object-fit: cover;

            background: #e2e8f0;
        }


        .no-image {

            width: 100%;

            height: 300px;

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: center;

            background: #f1f5f9;

            color: #94a3b8;

            font-size: 13px;
        }


        .no-image-icon {

            font-size: 45px;

            margin-bottom: 10px;
        }


        .preview-info {

            padding: 18px;

            background: white;
        }


        .preview-info h4 {

            margin: 0;

            color: #062b63;

            font-size: 20px;

            font-weight: 800;
        }


        .preview-description {

            margin: 9px 0 0;

            color: #64748b;

            font-size: 13px;

            line-height: 1.7;

            white-space: pre-line;
        }


        .preview-button {

            display: inline-block;

            margin-top: 15px;

            padding: 9px 14px;

            border-radius: 7px;

            background: #f58220;

            color: white;

            font-size: 11px;

            font-weight: 700;
        }


        .preview-link {

            display: block;

            margin-top: 7px;

            color: #64748b;

            font-size: 10px;

            word-break: break-all;
        }


        .current-label {

            display: inline-flex;

            align-items: center;

            gap: 6px;

            margin-bottom: 12px;

            padding: 6px 10px;

            border-radius: 20px;

            background: #dcfce7;

            color: #15803d;

            font-size: 10px;

            font-weight: 800;
        }


        /* =====================================================
           FORM
        ===================================================== */

        .form-body {

            padding: 22px;
        }


        .form-group {

            margin-bottom: 17px;
        }


        .form-group label {

            display: block;

            margin-bottom: 7px;

            color: #334155;

            font-size: 12px;

            font-weight: 700;
        }


        .form-group label span {

            color: #dc3545;
        }


        .form-control {

            width: 100%;

            padding: 11px 12px;

            border:
                1px solid
                #cbd5e1;

            border-radius: 8px;

            background: white;

            color: #334155;

            font-size: 12px;

            outline: none;

            transition: .2s;
        }


        .form-control:focus {

            border-color: #0b63ce;

            box-shadow:
                0 0 0 3px
                rgba(11,99,206,.10);
        }


        textarea.form-control {

            min-height: 115px;

            resize: vertical;

            line-height: 1.6;
        }


        .file-input {

            padding: 9px;

            background: #f8fafc;

            cursor: pointer;
        }


        .form-help {

            margin-top: 6px;

            color: #94a3b8;

            font-size: 10px;

            line-height: 1.5;
        }


        .old-image {

            margin-top: 10px;

            padding: 10px;

            background: #f8fafc;

            border-radius: 8px;

            border:
                1px solid
                #e2e8f0;
        }


        .old-image-title {

            margin-bottom: 8px;

            color: #64748b;

            font-size: 10px;

            font-weight: 700;
        }


        .old-image img {

            width: 100%;

            height: 90px;

            object-fit: cover;

            border-radius: 6px;
        }


        .form-actions {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 10px;

            padding-top: 5px;
        }


        .form-actions-right {

            display: flex;

            gap: 8px;
        }


        /* =====================================================
           EMPTY STATE
        ===================================================== */

        .empty-state {

            padding: 60px 25px;

            text-align: center;
        }


        .empty-icon {

            width: 70px;
            height: 70px;

            margin: 0 auto 18px;

            border-radius: 50%;

            background: #eaf2ff;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 30px;
        }


        .empty-state h3 {

            margin: 0 0 8px;

            color: #062b63;

            font-size: 20px;
        }


        .empty-state p {

            max-width: 480px;

            margin: 0 auto;

            color: #64748b;

            font-size: 12px;

            line-height: 1.7;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 1100px) {

            .banner-layout {

                grid-template-columns: 1fr;
            }

        }


        @media (max-width: 850px) {

            .sidebar {

                position: relative;

                width: 100%;

                height: auto;
            }


            .main {

                margin-left: 0;
            }


            .topbar {

                position: relative;

                padding: 15px 20px;

                height: auto;
            }


            .content {

                padding: 20px;
            }

        }


        @media (max-width: 600px) {

            .user-box {

                display: none;
            }


            .page-header {

                flex-direction: column;

                align-items: flex-start;
            }


            .content {

                padding: 15px;
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

                flex-direction: column;

                align-items: stretch;
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


<div class="page-wrapper">


    {{-- =====================================================
         SIDEBAR
    ====================================================== --}}

    <aside class="sidebar">


        <div class="brand">

            <h2>
                MAGENTA
            </h2>

            <p>
                SISTEM INFORMASI MAGANG
            </p>

        </div>


        <div class="menu-title">
            UTAMA
        </div>


        <a
            href="{{ route('superadmin.dashboard') }}"
            class="menu-link"
        >

            <span class="menu-icon">
                ▦
            </span>

            <span>
                Dashboard
            </span>

        </a>


        <div class="menu-title">
            PENDAFTARAN
        </div>


        <a
            href="#"
            class="menu-link"
        >

            <span class="menu-icon">
                👨‍🎓
            </span>

            <span>
                Data Mahasiswa
            </span>

        </a>


        <a
            href="#"
            class="menu-link"
        >

            <span class="menu-icon">
                📄
            </span>

            <span>
                Pengajuan Magang
            </span>

        </a>


        <a
            href="#"
            class="menu-link"
        >

            <span class="menu-icon">
                🔎
            </span>

            <span>
                Cek Status Pengajuan
            </span>

        </a>


        <div class="menu-title">
            SISTEM
        </div>


        <a
            href="{{ route('superadmin.perusahaan.index') }}"
            class="menu-link"
        >

            <span class="menu-icon">
                🏢
            </span>

            <span>
                Profil Perusahaan
            </span>

        </a>


        <a
            href="{{ route('superadmin.banners.index') }}"
            class="menu-link active"
        >

            <span class="menu-icon">
                🖼️
            </span>

            <span>
                Kelola Banner
            </span>

        </a>


        <a
            href="{{ route('superadmin.pengaturan.index') }}"
            class="menu-link"
        >

            <span class="menu-icon">
                ⚙
            </span>

            <span>
                Pengaturan Website
            </span>

        </a>


        <a
            href="#"
            class="menu-link"
        >

            <span class="menu-icon">
                🕘
            </span>

            <span>
                Riwayat Aktivitas
            </span>

        </a>


        <div class="logout-area">


            <form
                action="{{ route('logout') }}"
                method="POST"
            >

                @csrf

                <button
                    type="submit"
                    class="logout-button"
                >

                    ↪ &nbsp; Logout

                </button>

            </form>


        </div>


    </aside>


    {{-- =====================================================
         MAIN
    ====================================================== --}}

    <main class="main">


        {{-- TOPBAR --}}

        <header class="topbar">


            <div class="topbar-left">

                <h1>
                    Kelola Banner
                </h1>

                <p>
                    Kelola satu banner utama untuk halaman depan MAGENTA
                </p>

            </div>


            <div class="user-box">


                <div class="user-avatar">

                    {{ strtoupper(
                        substr(
                            auth()->user()->name ?? 'A',
                            0,
                            1
                        )
                    ) }}

                </div>


                <div>

                    <div class="user-name">

                        {{ auth()->user()->name ?? 'Administrator' }}

                    </div>


                    <div class="user-role">

                        Super Administrator

                    </div>

                </div>


            </div>


        </header>


        {{-- =====================================================
             CONTENT
        ====================================================== --}}

        <div class="content">


            {{-- SUCCESS --}}

            @if(session('success'))

                <div class="alert alert-success">

                    ✓

                    {{ session('success') }}

                </div>

            @endif


            {{-- ERROR --}}

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


            {{-- PAGE HEADER --}}

            <div class="page-header">


                <div>

                    <h2>
                        Banner Utama Website
                    </h2>

                    <p>
                        Website MAGENTA menggunakan satu banner utama.
                        Banner dapat diganti kapan saja dari halaman ini.
                    </p>

                </div>


            </div>


            {{-- =================================================
                 BANNER ADA
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
                                Banner yang sedang digunakan pada halaman utama.
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
                                            🖼️
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
                         FORM EDIT
                    ================================================== --}}

                    <div class="card">


                        <div class="card-header">

                            <h3>
                                Ubah Banner
                            </h3>

                            <p>
                                Isi data di bawah untuk mengganti banner utama.
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

                                    <label>

                                        Judul Banner

                                        <span>*</span>

                                    </label>


                                    <input
                                        type="text"
                                        name="judul"
                                        class="form-control"
                                        value="{{ old('judul', $banner->judul) }}"
                                        placeholder="Contoh: Selamat Datang di MAGENTA"
                                        required
                                    >

                                </div>


                                {{-- DESKRIPSI --}}

                                <div class="form-group">

                                    <label>
                                        Deskripsi
                                    </label>


                                    <textarea
                                        name="deskripsi"
                                        class="form-control"
                                        placeholder="Masukkan deskripsi singkat banner..."
                                    >{{ old('deskripsi', $banner->deskripsi) }}</textarea>

                                </div>


                                {{-- GAMBAR --}}

                                <div class="form-group">

                                    <label>
                                        Gambar Banner
                                    </label>


                                    <input
                                        type="file"
                                        name="gambar"
                                        class="form-control file-input"
                                        accept=".jpg,.jpeg,.png,.webp"
                                    >


                                    <div class="form-help">

                                        Pilih gambar baru hanya jika ingin
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


                                {{-- TOMBOL --}}

                                <div class="form-group">

                                    <label>
                                        Teks Tombol
                                    </label>


                                    <input
                                        type="text"
                                        name="tombol_teks"
                                        class="form-control"
                                        value="{{ old('tombol_teks', $banner->tombol_teks) }}"
                                        placeholder="Contoh: Ajukan Magang"
                                    >

                                </div>


                                {{-- LINK --}}

                                <div class="form-group">

                                    <label>
                                        Link Tombol
                                    </label>


                                    <input
                                        type="text"
                                        name="tombol_link"
                                        class="form-control"
                                        value="{{ old('tombol_link', $banner->tombol_link) }}"
                                        placeholder="Contoh: #pengajuan"
                                    >

                                </div>


                                {{-- ACTION --}}

                                <div class="form-actions">


                                    <div>

                                        <small
                                            style="
                                                color:#94a3b8;
                                                font-size:10px;
                                            "
                                        >

                                            Banner hanya satu.

                                        </small>

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
                                style="margin-top:10px;"
                                onsubmit="return confirm('Yakin ingin menghapus banner utama?')"
                            >

                                @csrf

                                @method('DELETE')


                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                    style="width:100%;"
                                >

                                    🗑

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
                            🖼️
                        </div>


                        <h3>
                            Belum Ada Banner
                        </h3>


                        <p>

                            Website MAGENTA belum memiliki banner utama.
                            Silakan isi form di bawah untuk menambahkan
                            banner pertama.

                        </p>


                    </div>


                </div>


                <br>


                <div class="card">


                    <div class="card-header">

                        <h3>
                            Tambah Banner Utama
                        </h3>

                        <p>
                            Banner ini akan ditampilkan pada halaman utama MAGENTA.
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

                                <label>

                                    Judul Banner

                                    <span>*</span>

                                </label>


                                <input
                                    type="text"
                                    name="judul"
                                    class="form-control"
                                    value="{{ old('judul') }}"
                                    placeholder="Contoh: Selamat Datang di MAGENTA"
                                    required
                                >

                            </div>


                            {{-- DESKRIPSI --}}

                            <div class="form-group">

                                <label>
                                    Deskripsi
                                </label>


                                <textarea
                                    name="deskripsi"
                                    class="form-control"
                                    placeholder="Masukkan deskripsi banner..."
                                >{{ old('deskripsi') }}</textarea>

                            </div>


                            {{-- GAMBAR --}}

                            <div class="form-group">

                                <label>
                                    Gambar Banner
                                </label>


                                <input
                                    type="file"
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

                                <label>
                                    Teks Tombol
                                </label>


                                <input
                                    type="text"
                                    name="tombol_teks"
                                    class="form-control"
                                    value="{{ old('tombol_teks') }}"
                                    placeholder="Contoh: Ajukan Magang"
                                >

                            </div>


                            {{-- LINK --}}

                            <div class="form-group">

                                <label>
                                    Link Tombol
                                </label>


                                <input
                                    type="text"
                                    name="tombol_link"
                                    class="form-control"
                                    value="{{ old('tombol_link') }}"
                                    placeholder="Contoh: #pengajuan"
                                >

                            </div>


                            <button
                                type="submit"
                                class="btn btn-primary"
                            >

                                + Simpan Banner

                            </button>


                        </form>


                    </div>


                </div>


            @endif


        </div>


    </main>


</div>


</body>

</html>