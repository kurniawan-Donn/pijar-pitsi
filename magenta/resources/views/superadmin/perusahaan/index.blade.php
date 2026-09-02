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

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f4f7fb;

            color: #1f2937;
        }


        a {
            text-decoration: none;
        }



        /* =====================================================
           SIDEBAR
        ====================================================== */

        .sidebar {

            position: fixed;

            left: 0;
            top: 0;

            width: 255px;
            height: 100vh;

            background: #0f2f5f;

            color: white;

            padding: 28px 18px;

            overflow-y: auto;

            z-index: 1000;
        }


        .brand {

            display: flex;

            align-items: center;

            gap: 12px;

            padding: 0 10px;

            margin-bottom: 8px;
        }


        .brand-logo {

            width: 42px;
            height: 42px;

            border-radius: 10px;

            background: white;

            color: #1769aa;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 18px;

            font-weight: bold;
        }


        .brand-text h2 {

            font-size: 21px;

            color: white;

            margin-bottom: 3px;
        }


        .brand-text span {

            font-size: 10px;

            color: #bcd0e8;

            letter-spacing: .5px;
        }


        .sidebar-line {

            height: 1px;

            background:
                rgba(255,255,255,.12);

            margin: 25px 5px;
        }



        /* =====================================================
           MENU
        ====================================================== */

        .menu-title {

            font-size: 10px;

            text-transform: uppercase;

            letter-spacing: 1.2px;

            color: #9fb9d8;

            margin:
                22px 12px 9px;
        }


        .menu a {

            display: flex;

            align-items: center;

            gap: 12px;

            padding: 12px 13px;

            margin-bottom: 5px;

            border-radius: 9px;

            color: #dbe8f6;

            font-size: 14px;

            transition: .2s;
        }


        .menu a:hover {

            background:
                rgba(255,255,255,.10);

            color: white;
        }


        .menu a.active {

            background: #1769aa;

            color: white;

            box-shadow:
                0 5px 15px
                rgba(0,0,0,.12);
        }


        .menu-icon {

            width: 25px;

            text-align: center;

            font-size: 15px;
        }



        /* =====================================================
           LOGOUT
        ====================================================== */

        .logout-area {

            margin-top: 30px;

            padding-top: 20px;

            border-top:
                1px solid
                rgba(255,255,255,.12);
        }


        .logout-btn {

            width: 100%;

            border: none;

            background:
                rgba(255,255,255,.08);

            color: #dbe8f6;

            padding: 12px;

            border-radius: 9px;

            cursor: pointer;

            font-weight: bold;

            transition: .2s;
        }


        .logout-btn:hover {

            background: #dc3545;

            color: white;
        }



        /* =====================================================
           MAIN
        ====================================================== */

        .main {

            margin-left: 255px;

            min-height: 100vh;
        }



        /* =====================================================
           TOPBAR
        ====================================================== */

        .topbar {

            height: 75px;

            background: white;

            display: flex;

            align-items: center;

            justify-content:
                space-between;

            padding:
                0 35px;

            border-bottom:
                1px solid #e6ebf2;
        }


        .page-title h1 {

            font-size: 22px;

            color: #173b68;

            margin-bottom: 4px;
        }


        .page-title p {

            font-size: 13px;

            color: #7b8794;
        }


        .admin-profile {

            display: flex;

            align-items: center;

            gap: 11px;
        }


        .admin-text {

            text-align: right;
        }


        .admin-text strong {

            display: block;

            color: #173b68;

            font-size: 14px;
        }


        .admin-text span {

            font-size: 11px;

            color: #8995a5;
        }


        .avatar {

            width: 38px;
            height: 38px;

            border-radius: 50%;

            background: #e7f2fc;

            color: #1769aa;

            display: flex;

            align-items: center;

            justify-content: center;

            font-weight: bold;
        }



        /* =====================================================
           CONTENT
        ====================================================== */

        .content {

            padding:
                30px 35px 50px;
        }



        /* =====================================================
           PAGE HEADER
        ====================================================== */

        .page-header {

            display: flex;

            align-items: center;

            justify-content:
                space-between;

            margin-bottom: 25px;
        }


        .page-header-left {

            display: flex;

            align-items: center;

            gap: 15px;
        }


        .page-header-icon {

            width: 52px;
            height: 52px;

            border-radius: 13px;

            background: #e8f3fc;

            color: #1769aa;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 23px;

            border:
                1px solid #d6eafa;
        }


        .page-header h2 {

            font-size: 21px;

            color: #173b68;

            margin-bottom: 5px;
        }


        .page-header p {

            font-size: 13px;

            color: #7b8794;
        }



        /* =====================================================
           INFO NOTICE
        ====================================================== */

        .notice {

            background:
                linear-gradient(
                    135deg,
                    #fff8e8,
                    #fffdf7
                );

            border:
                1px solid #f6dfab;

            border-left:
                4px solid #f59e0b;

            border-radius: 12px;

            padding: 17px 19px;

            margin-bottom: 22px;

            display: flex;

            align-items: flex-start;

            gap: 13px;
        }


        .notice-icon {

            width: 30px;
            height: 30px;

            border-radius: 8px;

            background: #fff0c7;

            color: #c27a00;

            display: flex;

            align-items: center;

            justify-content: center;

            font-weight: bold;

            flex-shrink: 0;
        }


        .notice strong {

            display: block;

            color: #7a5200;

            font-size: 13px;

            margin-bottom: 4px;
        }


        .notice p {

            color: #8a6a2b;

            font-size: 12px;

            line-height: 1.6;
        }



        /* =====================================================
           ALERT
        ====================================================== */

        .alert-success {

            background: #ecfdf3;

            border:
                1px solid #b7ebcc;

            color: #167342;

            padding: 14px 17px;

            border-radius: 10px;

            margin-bottom: 20px;

            font-size: 13px;

            font-weight: 600;
        }


        .alert-error {

            background: #fff1f2;

            border:
                1px solid #fecdd3;

            color: #b42318;

            padding: 15px 17px;

            border-radius: 10px;

            margin-bottom: 20px;

            font-size: 13px;
        }


        .alert-error strong {

            display: block;

            margin-bottom: 8px;
        }


        .alert-error ul {

            padding-left: 20px;

            line-height: 1.7;
        }



        /* =====================================================
           FORM CARD
        ====================================================== */

        .form-card {

            background: white;

            border:
                1px solid #e4eaf1;

            border-radius: 16px;

            box-shadow:
                0 5px 20px
                rgba(23,59,104,.05);

            overflow: hidden;
        }


        .form-card-header {

            padding: 21px 25px;

            border-bottom:
                1px solid #edf1f5;

            display: flex;

            align-items: center;

            gap: 12px;
        }


        .form-card-icon {

            width: 38px;
            height: 38px;

            border-radius: 9px;

            background: #edf6fd;

            color: #1769aa;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 17px;
        }


        .form-card-header h3 {

            color: #173b68;

            font-size: 15px;

            margin-bottom: 3px;
        }


        .form-card-header p {

            color: #8995a5;

            font-size: 11px;
        }


        .form-body {

            padding: 25px;
        }



        /* =====================================================
           FORM GRID
        ====================================================== */

        .grid {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap:
                20px;
        }


        .full {

            grid-column:
                1 / -1;
        }


        .form-group {

            margin-bottom: 2px;
        }


        .form-group label {

            display: block;

            font-size: 12px;

            font-weight: bold;

            color: #334155;

            margin-bottom: 8px;
        }


        .required {

            color: #e05252;
        }


        .form-group input,
        .form-group textarea {

            width: 100%;

            padding:
                12px 13px;

            border:
                1px solid #d5dce5;

            border-radius: 9px;

            font-size: 13px;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            color: #1f2937;

            background: white;

            transition: .2s;
        }


        .form-group input:hover,
        .form-group textarea:hover {

            border-color: #b9c5d2;
        }


        .form-group input:focus,
        .form-group textarea:focus {

            outline: none;

            border-color: #1769aa;

            box-shadow:
                0 0 0 3px
                rgba(23,105,170,.10);
        }


        .form-group textarea {

            min-height: 120px;

            resize: vertical;

            line-height: 1.6;
        }


        .help {

            margin-top: 6px;

            color: #8a96a5;

            font-size: 11px;

            line-height: 1.5;
        }



        /* =====================================================
           FORM DIVIDER
        ====================================================== */

        .form-divider {

            border: none;

            border-top:
                1px solid #edf1f5;

            margin:
                27px 0 22px;
        }



        /* =====================================================
           ACTIONS
        ====================================================== */

        .actions {

            display: flex;

            align-items: center;

            justify-content:
                space-between;

            gap: 10px;

            padding-top: 23px;

            margin-top: 25px;

            border-top:
                1px solid #edf1f5;
        }


        .action-left {

            color: #8995a5;

            font-size: 11px;
        }


        .action-buttons {

            display: flex;

            gap: 10px;
        }


        .btn {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 7px;

            padding:
                11px 17px;

            border: none;

            border-radius: 9px;

            cursor: pointer;

            font-size: 12px;

            font-weight: bold;

            transition: .2s;
        }


        .btn-secondary {

            background: #eef1f5;

            color: #526171;
        }


        .btn-secondary:hover {

            background: #dfe5eb;
        }


        .btn-primary {

            background: #1769aa;

            color: white;

            box-shadow:
                0 4px 10px
                rgba(23,105,170,.18);
        }


        .btn-primary:hover {

            background: #0f4c81;

            transform:
                translateY(-1px);
        }



        /* =====================================================
           FOOT NOTE
        ====================================================== */

        .foot-note {

            margin-top: 18px;

            color: #98a2b0;

            font-size: 11px;

            text-align: center;
        }



        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 1000px) {

            .grid {

                grid-template-columns:
                    1fr;
            }


            .full {

                grid-column: auto;
            }

        }


        @media (max-width: 800px) {

            .sidebar {

                position: relative;

                width: 100%;

                height: auto;
            }


            .main {

                margin-left: 0;
            }


            .topbar {

                height: auto;

                padding:
                    18px 20px;

                flex-direction: column;

                align-items: flex-start;

                gap: 15px;
            }


            .admin-profile {

                width: 100%;

                justify-content:
                    flex-start;
            }


            .admin-text {

                text-align: left;
            }


            .content {

                padding:
                    22px 18px 35px;
            }


            .page-header {

                align-items: flex-start;

                flex-direction: column;
            }


            .form-body {

                padding: 20px;
            }


            .actions {

                align-items:
                    flex-start;

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

            .sidebar {

                padding:
                    20px 14px;
            }


            .brand-text h2 {

                font-size: 18px;
            }


            .page-header-icon {

                width: 45px;
                height: 45px;
            }


            .page-header h2 {

                font-size: 19px;
            }


            .form-card-header {

                padding: 18px;
            }


            .form-body {

                padding: 18px;
            }

        }

    </style>

</head>


<body>


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="sidebar">


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


        <div class="sidebar-line"></div>


        <div class="menu">


            <!-- UTAMA -->

            <div class="menu-title">
                Utama
            </div>


            <a href="{{ route('superadmin.dashboard') }}">

                <span class="menu-icon">
                    ▣
                </span>

                Dashboard

            </a>


            <!-- PENDAFTARAN -->

            <div class="menu-title">
                Pendaftaran
            </div>


            <a href="#">

                <span class="menu-icon">
                    ♙
                </span>

                Data Mahasiswa

            </a>


            <a href="{{ route('admin.pengajuan.index') }}">

                <span class="menu-icon">
                    ▤
                </span>

                Pengajuan Magang

            </a>


            <a href="#">

                <span class="menu-icon">
                    ✓
                </span>

                Cek Status Pengajuan

            </a>


            <!-- SISTEM -->

            <div class="menu-title">
                Sistem
            </div>


            <a
                href="{{ route('superadmin.perusahaan.index') }}"
                class="active"
            >

                <span class="menu-icon">
                    ▰
                </span>

                Profil Perusahaan

            </a>


            <a
                href="{{ route('superadmin.banners.create') }}"
            >

                <span class="menu-icon">
                    ▧
                </span>

                Kelola Banner

            </a>


            <a
                href="{{ route('superadmin.pengaturan.index') }}"
            >

                <span class="menu-icon">
                    ⚙
                </span>

                Pengaturan Website

            </a>


            <a href="#">

                <span class="menu-icon">
                    ◷
                </span>

                Riwayat Aktivitas

            </a>


        </div>


        <!-- LOGOUT -->

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



    <!-- =====================================================
         MAIN
    ====================================================== -->

    <main class="main">


        <!-- TOPBAR -->

        <header class="topbar">


            <div class="page-title">

                <h1>
                    Profil Perusahaan
                </h1>

                <p>
                    Kelola informasi perusahaan MAGENTA
                </p>

            </div>


            <div class="admin-profile">

                <div class="admin-text">

                    <strong>
                        {{ auth()->user()->name }}
                    </strong>

                    <span>
                        Super Admin
                    </span>

                </div>


                <div class="avatar">

                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                </div>

            </div>


        </header>



        <!-- CONTENT -->

        <div class="content">


            <!-- PAGE HEADER -->

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



            <!-- SUCCESS -->

            @if(session('success'))

                <div class="alert-success">

                    ✓ &nbsp;

                    {{ session('success') }}

                </div>

            @endif



            <!-- ERROR -->

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



            <!-- NOTICE -->

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



            <!-- FORM CARD -->

            <div class="form-card">


                <!-- CARD HEADER -->

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



                <!-- FORM -->

                <div class="form-body">


                    <form
                        action="{{ route('superadmin.perusahaan.update') }}"
                        method="POST"
                    >

                        @csrf

                        @method('PUT')



                        <!-- IDENTITAS -->

                        <div class="grid">


                            <!-- NAMA -->

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
                                    value="{{ old('nama', $perusahaan?->nama ?? '') }}"
                                    placeholder="Masukkan nama perusahaan"
                                    required
                                >

                            </div>



                            <!-- ALAMAT -->

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
                                >{{ old('alamat', $perusahaan?->alamat ?? '') }}</textarea>


                                <div class="help">

                                    Masukkan alamat lengkap
                                    perusahaan.

                                </div>

                            </div>



                            <!-- KOTA -->

                            <div class="form-group">

                                <label for="kota">
                                    Kota
                                </label>


                                <input
                                    type="text"
                                    id="kota"
                                    name="kota"
                                    value="{{ old('kota', $perusahaan?->kota ?? '') }}"
                                    placeholder="Contoh: Jakarta"
                                >

                            </div>



                            <!-- TELEPON -->

                            <div class="form-group">

                                <label for="telepon">
                                    Nomor Telepon
                                </label>


                                <input
                                    type="text"
                                    id="telepon"
                                    name="telepon"
                                    value="{{ old('telepon', $perusahaan?->telepon ?? '') }}"
                                    placeholder="Contoh: 08123456789"
                                >

                            </div>


                        </div>



                        <!-- DIVIDER -->

                        <hr class="form-divider">



                        <!-- KONTAK -->

                        <div class="grid">


                            <!-- EMAIL -->

                            <div class="form-group">

                                <label for="email">
                                    Email Perusahaan
                                </label>


                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email', $perusahaan?->email ?? '') }}"
                                    placeholder="Contoh: info@perusahaan.com"
                                >

                            </div>



                            <!-- WEBSITE -->

                            <div class="form-group">

                                <label for="website">
                                    Website
                                </label>


                                <input
                                    type="text"
                                    id="website"
                                    name="website"
                                    value="{{ old('website', $perusahaan?->website ?? '') }}"
                                    placeholder="Contoh: https://www.perusahaan.com"
                                >


                                <div class="help">

                                    Kosongkan jika perusahaan
                                    tidak memiliki website.

                                </div>

                            </div>



                            <!-- DESKRIPSI -->

                            <div class="form-group full">

                                <label for="deskripsi">
                                    Deskripsi Perusahaan
                                </label>


                                <textarea
                                    id="deskripsi"
                                    name="deskripsi"
                                    placeholder="Masukkan deskripsi singkat perusahaan"
                                >{{ old('deskripsi', $perusahaan?->deskripsi ?? '') }}</textarea>


                                <div class="help">

                                    Deskripsi singkat mengenai
                                    perusahaan yang dapat ditampilkan
                                    pada website MAGENTA.

                                </div>

                            </div>


                        </div>



                        <!-- ACTION -->

                        <div class="actions">


                            <div class="action-left">

                                <span>
                                    <b>*</b>
                                    Wajib diisi
                                </span>

                            </div>


                            <div class="action-buttons">


                                <a
                                    href="{{ route('superadmin.dashboard') }}"
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



            <div class="foot-note">

                MAGENTA &nbsp;•&nbsp;
                Sistem Informasi Magang

            </div>


        </div>


    </main>


</body>

</html>