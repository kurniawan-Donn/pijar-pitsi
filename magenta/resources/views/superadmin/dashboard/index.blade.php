<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Super Admin - MAGENTA</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
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

            font-size: 11px;

            color: #bcd0e8;

            letter-spacing: .5px;
        }


        .sidebar-line {

            height: 1px;

            background: rgba(255,255,255,.12);

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

            margin: 22px 12px 9px;
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

            background: rgba(255,255,255,.10);

            color: white;
        }


        .menu a.active {

            background: #1976c9;

            color: white;

            box-shadow:
                0 5px 15px rgba(0,0,0,.12);
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
                1px solid rgba(255,255,255,.12);
        }


        .logout-btn {

            width: 100%;

            border: none;

            background: rgba(255,255,255,.08);

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

            justify-content: space-between;

            padding: 0 35px;

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


        /* =====================================================
           CONTENT
        ====================================================== */

        .content {

            padding: 30px 35px 45px;
        }


        /* =====================================================
           WELCOME
        ====================================================== */

        .welcome {

            background:
                linear-gradient(
                    135deg,
                    #1769aa,
                    #0f4c81
                );

            color: white;

            border-radius: 16px;

            padding: 28px 30px;

            margin-bottom: 25px;

            position: relative;

            overflow: hidden;
        }


        .welcome::after {

            content: "";

            position: absolute;

            width: 180px;
            height: 180px;

            border-radius: 50%;

            border:
                25px solid rgba(255,255,255,.06);

            right: -60px;
            top: -70px;
        }


        .welcome h2 {

            font-size: 23px;

            margin-bottom: 9px;
        }


        .welcome p {

            color: #dcecff;

            font-size: 14px;

            line-height: 1.6;

            max-width: 700px;
        }


        /* =====================================================
           STATISTICS
        ====================================================== */

        .statistics {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 18px;

            margin-bottom: 28px;
        }


        .stat-card {

            background: white;

            border:
                1px solid #e8edf3;

            border-radius: 14px;

            padding: 22px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            box-shadow:
                0 4px 15px rgba(23,59,104,.04);
        }


        .stat-info h3 {

            font-size: 12px;

            color: #7c8795;

            font-weight: normal;

            margin-bottom: 8px;
        }


        .stat-number {

            font-size: 29px;

            font-weight: bold;

            color: #173b68;
        }


        .stat-icon {

            width: 50px;
            height: 50px;

            border-radius: 12px;

            background: #eaf4fc;

            color: #1769aa;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 21px;
        }


        /* =====================================================
           SECTION HEADER
        ====================================================== */

        .section-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 16px;
        }


        .section-header h2 {

            font-size: 18px;

            color: #173b68;
        }


        .section-header p {

            color: #8995a5;

            font-size: 12px;
        }


        /* =====================================================
           QUICK ACTIONS
        ====================================================== */

        .action-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 17px;

            margin-bottom: 28px;
        }


        .action {

            background: white;

            border:
                1px solid #e7edf4;

            border-radius: 14px;

            padding: 21px;

            transition: .2s;

            position: relative;
        }


        .action:hover {

            border-color: #1976c9;

            transform: translateY(-2px);

            box-shadow:
                0 8px 22px rgba(23,105,170,.09);
        }


        .action-icon {

            width: 43px;
            height: 43px;

            border-radius: 10px;

            background: #edf6fd;

            color: #1769aa;

            display: flex;

            align-items: center;

            justify-content: center;

            margin-bottom: 15px;

            font-size: 18px;
        }


        .action h3 {

            color: #173b68;

            font-size: 15px;

            margin-bottom: 7px;
        }


        .action p {

            color: #7b8794;

            font-size: 12px;

            line-height: 1.6;
        }


        .arrow {

            position: absolute;

            right: 18px;
            top: 18px;

            color: #a8b5c4;

            font-size: 17px;
        }


        /* =====================================================
           INFORMATION
        ====================================================== */

        .info-grid {

            display: grid;

            grid-template-columns:
                1.4fr 1fr;

            gap: 18px;
        }


        .info-card {

            background: white;

            border:
                1px solid #e7edf4;

            border-radius: 14px;

            padding: 23px;

            box-shadow:
                0 4px 15px rgba(23,59,104,.04);
        }


        .info-card h2 {

            font-size: 16px;

            color: #173b68;

            margin-bottom: 17px;
        }


        .info-item {

            display: flex;

            align-items: center;

            gap: 12px;

            padding: 12px 0;

            border-bottom:
                1px solid #eef2f6;
        }


        .info-item:last-child {

            border-bottom: none;
        }


        .info-item-icon {

            width: 35px;
            height: 35px;

            border-radius: 8px;

            background: #edf6fd;

            color: #1769aa;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 14px;
        }


        .info-item strong {

            display: block;

            font-size: 13px;

            color: #334155;

            margin-bottom: 3px;
        }


        .info-item span {

            font-size: 11px;

            color: #8995a5;
        }


        /* =====================================================
           BUTTON
        ====================================================== */

        .btn {

            display: inline-block;

            background: #1769aa;

            color: white;

            padding: 9px 15px;

            border-radius: 8px;

            font-size: 12px;

            font-weight: bold;
        }


        .btn:hover {

            background: #0f4c81;
        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 1100px) {

            .statistics {

                grid-template-columns:
                    repeat(2, 1fr);
            }


            .action-grid {

                grid-template-columns:
                    repeat(2, 1fr);
            }


            .info-grid {

                grid-template-columns: 1fr;
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

                padding: 18px 20px;

                gap: 15px;

                flex-direction: column;

                align-items: flex-start;
            }


            .admin-profile {

                width: 100%;

                justify-content: flex-start;
            }


            .admin-text {

                text-align: left;
            }


            .content {

                padding: 22px 18px;
            }


            .statistics,
            .action-grid {

                grid-template-columns: 1fr;
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

                <h2>MAGENTA</h2>

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


            <a
                href="{{ route('superadmin.dashboard') }}"
                class="active"
            >

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
                    Dashboard
                </h1>

                <p>
                    Kelola pendaftaran magang perusahaan
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


            <!-- WELCOME -->

            <div class="welcome">

                <h2>
                    Selamat datang di MAGENTA 👋
                </h2>

                <p>
                    Kelola proses pendaftaran magang,
                    data mahasiswa, pengajuan, dan
                    informasi website perusahaan
                    melalui dashboard ini.
                </p>

            </div>



            <!-- STATISTICS -->

            <div class="statistics">


                <div class="stat-card">

                    <div class="stat-info">

                        <h3>
                            Total Mahasiswa
                        </h3>

                        <div class="stat-number">
                            0
                        </div>

                    </div>

                    <div class="stat-icon">
                        ♙
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-info">

                        <h3>
                            Pengajuan Menunggu
                        </h3>

                        <div class="stat-number">
                            0
                        </div>

                    </div>

                    <div class="stat-icon">
                        ▤
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-info">

                        <h3>
                            Pengajuan Disetujui
                        </h3>

                        <div class="stat-number">
                            0
                        </div>

                    </div>

                    <div class="stat-icon">
                        ✓
                    </div>

                </div>


            </div>



            <!-- AKSES CEPAT -->

            <div class="section-header">

                <div>

                    <h2>
                        Akses Cepat
                    </h2>

                    <p>
                        Menu yang sering digunakan
                    </p>

                </div>

            </div>


            <div class="action-grid">


                <!-- PENGAJUAN -->

                <a
                    href="{{ route('admin.pengajuan.index') }}"
                    class="action"
                >

                    <span class="arrow">
                        →
                    </span>

                    <div class="action-icon">
                        ▤
                    </div>

                    <h3>
                        Pengajuan Magang
                    </h3>

                    <p>
                        Lihat, periksa, dan proses
                        pengajuan mahasiswa.
                    </p>

                </a>


                <!-- MAHASISWA -->

                <a
                    href="#"
                    class="action"
                >

                    <span class="arrow">
                        →
                    </span>

                    <div class="action-icon">
                        ♙
                    </div>

                    <h3>
                        Data Mahasiswa
                    </h3>

                    <p>
                        Lihat dan kelola data
                        mahasiswa pendaftar.
                    </p>

                </a>


                <!-- CEK STATUS -->

                <a
                    href="#"
                    class="action"
                >

                    <span class="arrow">
                        →
                    </span>

                    <div class="action-icon">
                        ✓
                    </div>

                    <h3>
                        Cek Status
                    </h3>

                    <p>
                        Periksa status pengajuan
                        mahasiswa.
                    </p>

                </a>


                <!-- PROFIL -->

                <a
                    href="{{ route('superadmin.perusahaan.index') }}"
                    class="action"
                >

                    <span class="arrow">
                        →
                    </span>

                    <div class="action-icon">
                        ▰
                    </div>

                    <h3>
                        Profil Perusahaan
                    </h3>

                    <p>
                        Kelola informasi perusahaan
                        yang ditampilkan di website.
                    </p>

                </a>


                <!-- BANNER -->

                <a
                    href="{{ route('superadmin.banners.create') }}"
                    class="action"
                >

                    <span class="arrow">
                        →
                    </span>

                    <div class="action-icon">
                        ▧
                    </div>

                    <h3>
                        Kelola Banner
                    </h3>

                    <p>
                        Kelola banner dan informasi
                        utama website.
                    </p>

                </a>


                <!-- PENGATURAN -->

                <a
                    href="{{ route('superadmin.pengaturan.index') }}"
                    class="action"
                >

                    <span class="arrow">
                        →
                    </span>

                    <div class="action-icon">
                        ⚙
                    </div>

                    <h3>
                        Pengaturan Website
                    </h3>

                    <p>
                        Atur konten informasi website
                        MAGENTA.
                    </p>

                </a>


            </div>



            <!-- INFORMASI -->

            <div class="info-grid">


                <!-- ALUR PENGELOLAAN -->

                <div class="info-card">

                    <h2>
                        Alur Pendaftaran Magang
                    </h2>


                    <div class="info-item">

                        <div class="info-item-icon">
                            1
                        </div>

                        <div>

                            <strong>
                                Mahasiswa mengajukan
                            </strong>

                            <span>
                                Mahasiswa mengisi formulir pendaftaran.
                            </span>

                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-item-icon">
                            2
                        </div>

                        <div>

                            <strong>
                                Admin melakukan verifikasi
                            </strong>

                            <span>
                                Data dan dokumen diperiksa.
                            </span>

                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-item-icon">
                            3
                        </div>

                        <div>

                            <strong>
                                Status diperbarui
                            </strong>

                            <span>
                                Pengajuan diterima atau ditolak.
                            </span>

                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-item-icon">
                            4
                        </div>

                        <div>

                            <strong>
                                Mahasiswa melihat status
                            </strong>

                            <span>
                                Status dapat dicek melalui website.
                            </span>

                        </div>

                    </div>


                </div>



                <!-- INFORMASI SISTEM -->

                <div class="info-card">

                    <h2>
                        Informasi Sistem
                    </h2>


                    <div class="info-item">

                        <div class="info-item-icon">
                            M
                        </div>

                        <div>

                            <strong>
                                MAGENTA
                            </strong>

                            <span>
                                Sistem Informasi Magang
                            </span>

                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-item-icon">
                            ●
                        </div>

                        <div>

                            <strong>
                                Status Sistem
                            </strong>

                            <span>
                                Sistem berjalan normal
                            </span>

                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-item-icon">
                            →
                        </div>

                        <div>

                            <strong>
                                Fokus Sistem
                            </strong>

                            <span>
                                Pendaftaran & pengelolaan pengajuan
                            </span>

                        </div>

                    </div>


                </div>


            </div>


        </div>


    </main>


</body>

</html>