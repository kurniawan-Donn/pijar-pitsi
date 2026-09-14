<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Super Admin - MAGENTA</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #12345b;
            min-height: 100vh;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        button {
            font-family: inherit;
        }

        /* =========================================================
           LAYOUT
        ========================================================= */

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* =========================================================
           SIDEBAR
        ========================================================= */

        .sidebar {
            width: 275px;
            min-width: 275px;
            min-height: 100vh;
            background: #12345b;
            color: #ffffff;
            padding: 28px 18px 24px;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            overflow-y: auto;
        }

        .brand {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 4px 6px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.16);
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
            font-size: 18px;
            font-weight: 800;
        }

        .brand-text {
            padding-top: 1px;
        }

        .brand-text h2 {
            font-size: 20px;
            line-height: 1.1;
            font-weight: 800;
            letter-spacing: 0.2px;
        }

        .brand-text span {
            display: block;
            margin-top: 7px;
            font-size: 8px;
            line-height: 1.4;
            font-weight: 700;
            letter-spacing: 1.15px;
            color: rgba(255, 255, 255, 0.86);
        }

        /* =========================================================
           MENU
        ========================================================= */

        .menu-title {
            padding: 0 9px;
            margin-top: 22px;
            margin-bottom: 10px;
            color: rgba(255, 255, 255, 0.65);
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .menu a {
            min-height: 40px;
            padding: 0 13px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            gap: 11px;
            color: rgba(255, 255, 255, 0.94);
            font-size: 12px;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .menu a:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .menu a.active {
            background: #1779c9;
            color: #ffffff;
            font-weight: 700;
        }

        .menu-icon {
            width: 16px;
            min-width: 16px;
            text-align: center;
            font-size: 12px;
            font-weight: 700;
        }

        /* =========================================================
           LOGOUT
        ========================================================= */

        .logout-area {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.14);
        }

        .logout-area form {
            margin: 0;
        }

        .logout-btn {
            width: 100%;
            min-height: 38px;
            border: none;
            border-radius: 7px;
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 12px;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.14);
        }

        /* =========================================================
           MAIN
        ========================================================= */

        .main {
            margin-left: 275px;
            width: calc(100% - 275px);
            min-height: 100vh;
        }

        /* =========================================================
           TOPBAR
        ========================================================= */

        .topbar {
            height: 64px;
            background: #ffffff;
            border-bottom: 1px solid #e5ebf2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
        }

        .page-title h1 {
            color: #12345b;
            font-size: 19px;
            line-height: 1.2;
            font-weight: 700;
        }

        .page-title p {
            color: #73849a;
            font-size: 11px;
            margin-top: 4px;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-text {
            text-align: right;
        }

        .admin-text strong {
            display: block;
            color: #12345b;
            font-size: 11px;
            font-weight: 700;
        }

        .admin-text span {
            display: block;
            color: #73849a;
            font-size: 9px;
            margin-top: 3px;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #eaf3fc;
            color: #1269aa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
        }

        /* =========================================================
           CONTENT
        ========================================================= */

        .content {
            padding: 28px 30px 40px;
        }

        .content-container {
            max-width: 1280px;
            margin: 0 auto;
        }

        /* =========================================================
           WELCOME
        ========================================================= */

        .welcome {
            background: linear-gradient(
                135deg,
                #2177b8,
                #124e86
            );
            color: #ffffff;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 22px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 5px 16px rgba(18, 52, 91, 0.08);
        }

        .welcome::after {
            content: "";
            position: absolute;
            width: 170px;
            height: 170px;
            border: 18px solid rgba(255, 255, 255, 0.06);
            border-radius: 50%;
            right: -40px;
            top: -60px;
        }

        .welcome h2 {
            font-size: 20px;
            line-height: 1.3;
            margin-bottom: 8px;
        }

        .welcome p {
            max-width: 700px;
            font-size: 11px;
            line-height: 1.7;
            color: #eaf4ff;
        }

        /* =========================================================
           SECTION TITLE
        ========================================================= */

        .section-title {
            margin-bottom: 12px;
        }

        .section-title h2 {
            color: #12345b;
            font-size: 15px;
            font-weight: 700;
        }

        .section-title p {
            color: #7c8da1;
            font-size: 10px;
            margin-top: 4px;
        }

        /* =========================================================
           QUICK ACTION
        ========================================================= */

        .action-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 22px;
        }

        .action {
            background: #ffffff;
            border: 1px solid #e2e9f1;
            border-radius: 10px;
            padding: 17px;
            min-height: 120px;
            position: relative;
            transition: 0.2s ease;
            box-shadow: 0 3px 12px rgba(18, 52, 91, 0.03);
        }

        .action:hover {
            border-color: #b9d4ef;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(18, 52, 91, 0.07);
        }

        .action-icon {
            width: 34px;
            height: 34px;
            background: #edf6fd;
            border: 1px solid #dceefa;
            color: #1675b8;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 11px;
            font-size: 14px;
            font-weight: 700;
        }

        .action-arrow {
            position: absolute;
            right: 15px;
            top: 15px;
            color: #a7b5c5;
            font-size: 14px;
        }

        .action h3 {
            color: #173c65;
            font-size: 12px;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .action p {
            color: #7b899a;
            font-size: 10px;
            line-height: 1.55;
            max-width: 290px;
        }

        /* =========================================================
           BOTTOM GRID
        ========================================================= */

        .bottom-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 14px;
        }

        .info-card {
            background: #ffffff;
            border: 1px solid #e2e9f1;
            border-radius: 10px;
            padding: 18px;
            box-shadow: 0 3px 12px rgba(18, 52, 91, 0.03);
        }

        .info-card h2 {
            color: #12345b;
            font-size: 13px;
            margin-bottom: 13px;
            font-weight: 700;
        }

        /* =========================================================
           FLOW
        ========================================================= */

        .flow-item {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            padding: 10px 0;
            border-bottom: 1px solid #edf1f5;
        }

        .flow-item:first-child {
            padding-top: 2px;
        }

        .flow-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .flow-number {
            width: 27px;
            height: 27px;
            min-width: 27px;
            background: #edf6fd;
            color: #1768b0;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
        }

        .flow-text strong {
            display: block;
            color: #243b5a;
            font-size: 10px;
            margin-bottom: 3px;
        }

        .flow-text span {
            display: block;
            color: #8996a7;
            font-size: 9px;
            line-height: 1.55;
        }

        /* =========================================================
           SYSTEM INFO
        ========================================================= */

        .system-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 0;
            border-bottom: 1px solid #edf1f5;
        }

        .system-item:first-child {
            padding-top: 2px;
        }

        .system-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .system-icon {
            width: 27px;
            height: 27px;
            min-width: 27px;
            background: #edf6fd;
            color: #1768b0;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
        }

        .system-text strong {
            display: block;
            color: #243b5a;
            font-size: 10px;
            margin-bottom: 2px;
        }

        .system-text span {
            display: block;
            color: #8996a7;
            font-size: 9px;
            line-height: 1.45;
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 1050px) {

            .sidebar {
                width: 240px;
                min-width: 240px;
            }

            .main {
                margin-left: 240px;
                width: calc(100% - 240px);
            }

            .action-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .bottom-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {

            .layout {
                display: block;
            }

            .sidebar {
                position: relative;
                width: 100%;
                min-width: 0;
                min-height: auto;
                padding: 20px 16px;
            }

            .main {
                margin-left: 0;
                width: 100%;
            }

            .logout-area {
                margin-top: 18px;
            }

            .topbar {
                height: auto;
                min-height: 70px;
                padding: 14px 18px;
            }

            .admin-profile {
                display: none;
            }

            .content {
                padding: 20px 15px 30px;
            }

            .action-grid {
                grid-template-columns: 1fr;
            }

            .bottom-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="sidebar">

        <!-- BRAND -->

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


        <!-- UTAMA -->

        <div class="menu-title">
            UTAMA
        </div>

        <div class="menu">

            <a
                href="{{ route('superadmin.dashboard') }}"
                class="active"
            >

                <span class="menu-icon">
                    ⌂
                </span>

                <span>
                    Dashboard
                </span>

            </a>

        </div>


        <!-- SISTEM -->

        <div class="menu-title">
            SISTEM
        </div>

        <div class="menu">

            <!-- PROFIL PERUSAHAAN -->

            <a
                href="{{ route('superadmin.perusahaan.index') }}"
            >

                <span class="menu-icon">
                    ▰
                </span>

                <span>
                    Profil Perusahaan
                </span>

            </a>


            <!-- KELOLA BANNER -->

            <a
                href="{{ route('superadmin.banners.index') }}"
            >

                <span class="menu-icon">
                    ▧
                </span>

                <span>
                    Kelola Banner
                </span>

            </a>


            <!-- KELOLA KONTEN -->

            <a
                href="{{ route('superadmin.pengaturan.index') }}"
            >

                <span class="menu-icon">
                    ⚙
                </span>

                <span>
                    Kelola Konten
                </span>

            </a>


            <!-- KELOLA PENGGUNA -->

            <a
                href="{{ route('superadmin.pengguna.index') }}"
            >

                <span class="menu-icon">
                    ♟
                </span>

                <span>
                    Kelola Pengguna
                </span>

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

                    <span class="menu-icon">
                        ↪
                    </span>

                    <span>
                        Logout
                    </span>

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
                    Kelola informasi dan website MAGENTA
                </p>

            </div>


            <div class="admin-profile">

                <div class="admin-text">

                    <strong>
                        {{ auth()->user()->name ?? 'Super Admin' }}
                    </strong>

                    <span>
                        Super Admin
                    </span>

                </div>


                <div class="avatar">

                    {{ strtoupper(
                        substr(
                            auth()->user()->name ?? 'S',
                            0,
                            1
                        )
                    ) }}

                </div>

            </div>

        </header>


        <!-- =================================================
             CONTENT
        ================================================== -->

        <div class="content">

            <div class="content-container">


                <!-- WELCOME -->

                <div class="welcome">

                    <h2>
                        Selamat datang di MAGENTA 👋
                    </h2>

                    <p>
                        Kelola informasi perusahaan, banner, dan konten
                        website MAGENTA melalui dashboard Super Admin.
                        Pendaftaran magang dilakukan melalui Google Form
                        yang tersedia pada website.
                    </p>

                </div>


                <!-- =================================================
                     AKSES CEPAT
                ================================================== -->

                <div class="section-title">

                    <h2>
                        Akses Cepat
                    </h2>

                    <p>
                        Menu pengelolaan website MAGENTA
                    </p>

                </div>


                <div class="action-grid">


                    <!-- PROFIL PERUSAHAAN -->

                    <a
                        href="{{ route('superadmin.perusahaan.index') }}"
                        class="action"
                    >

                        <div class="action-icon">
                            ▰
                        </div>

                        <div class="action-arrow">
                            →
                        </div>

                        <h3>
                            Profil Perusahaan
                        </h3>

                        <p>
                            Kelola nama, alamat, kontak, website,
                            dan informasi perusahaan.
                        </p>

                    </a>


                    <!-- BANNER -->

                    <a
                        href="{{ route('superadmin.banners.index') }}"
                        class="action"
                    >

                        <div class="action-icon">
                            ▧
                        </div>

                        <div class="action-arrow">
                            →
                        </div>

                        <h3>
                            Kelola Banner
                        </h3>

                        <p>
                            Kelola banner utama yang ditampilkan
                            pada halaman depan website.
                        </p>

                    </a>


                    <!-- KELOLA KONTEN -->

                    <a
                        href="{{ route('superadmin.pengaturan.index') }}"
                        class="action"
                    >

                        <div class="action-icon">
                            ⚙
                        </div>

                        <div class="action-arrow">
                            →
                        </div>

                        <h3>
                            Kelola Konten
                        </h3>

                        <p>
                            Kelola judul dan isi konten yang
                            ditampilkan pada website MAGENTA.
                        </p>

                    </a>

                </div>


                <!-- =================================================
                     BOTTOM CONTENT
                ================================================== -->

                <div class="bottom-grid">


                    <!-- ALUR -->

                    <div class="info-card">

                        <h2>
                            Alur Pendaftaran Magang
                        </h2>


                        <div class="flow-item">

                            <div class="flow-number">
                                1
                            </div>

                            <div class="flow-text">

                                <strong>
                                    Mahasiswa melihat informasi
                                </strong>

                                <span>
                                    Mahasiswa membaca persyaratan
                                    dan alur pendaftaran magang
                                    pada website MAGENTA.
                                </span>

                            </div>

                        </div>


                        <div class="flow-item">

                            <div class="flow-number">
                                2
                            </div>

                            <div class="flow-text">

                                <strong>
                                    Mahasiswa mengisi Google Form
                                </strong>

                                <span>
                                    Pendaftaran dilakukan melalui
                                    Google Form yang disediakan
                                    oleh perusahaan.
                                </span>

                            </div>

                        </div>


                        <div class="flow-item">

                            <div class="flow-number">
                                3
                            </div>

                            <div class="flow-text">

                                <strong>
                                    Data pendaftaran diterima
                                </strong>

                                <span>
                                    Data yang dikirim melalui
                                    Google Form diterima sesuai
                                    mekanisme perusahaan.
                                </span>

                            </div>

                        </div>


                        <div class="flow-item">

                            <div class="flow-number">
                                4
                            </div>

                            <div class="flow-text">

                                <strong>
                                    Proses selanjutnya
                                </strong>

                                <span>
                                    Pihak perusahaan melakukan
                                    proses selanjutnya sesuai
                                    kebutuhan pendaftaran magang.
                                </span>

                            </div>

                        </div>

                    </div>


                    <!-- INFORMASI SISTEM -->

                    <div class="info-card">

                        <h2>
                            Informasi Sistem
                        </h2>


                        <div class="system-item">

                            <div class="system-icon">
                                M
                            </div>

                            <div class="system-text">

                                <strong>
                                    MAGENTA
                                </strong>

                                <span>
                                    Sistem Informasi Magang
                                </span>

                            </div>

                        </div>


                        <div class="system-item">

                            <div class="system-icon">
                                ●
                            </div>

                            <div class="system-text">

                                <strong>
                                    Status Sistem
                                </strong>

                                <span>
                                    Sistem berjalan normal
                                </span>

                            </div>

                        </div>


                        <div class="system-item">

                            <div class="system-icon">
                                →
                            </div>

                            <div class="system-text">

                                <strong>
                                    Metode Pendaftaran
                                </strong>

                                <span>
                                    Google Form
                                </span>

                            </div>

                        </div>


                        <div class="system-item">

                            <div class="system-icon">
                                ✓
                            </div>

                            <div class="system-text">

                                <strong>
                                    Fokus Sistem
                                </strong>

                                <span>
                                    Informasi magang dan pengelolaan
                                    website perusahaan
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

</body>
</html>