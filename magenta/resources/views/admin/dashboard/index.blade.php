{{-- resources/views/admin/dashboard/index.blade.php --}}

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - MAGENTA</title>

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
            color: inherit;
        }

        button {
            font-family: inherit;
        }

        /* =========================================================
           LAYOUT
        ========================================================== */

        .admin-layout {
            min-height: 100vh;
        }

        /* =========================================================
           SIDEBAR
        ========================================================== */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;

            width: 275px;
            height: 100vh;

            background: #12345b;
            color: #ffffff;

            padding: 24px 18px;

            display: flex;
            flex-direction: column;

            z-index: 1000;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;

            padding: 4px 8px 28px;
        }

        .brand-logo {
            width: 42px;
            height: 42px;

            border-radius: 10px;

            background: #ffffff;
            color: #12345b;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 18px;
            font-weight: 800;
        }

        .brand-text {
            min-width: 0;
        }

        .brand-title {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: .5px;
            line-height: 1.2;
        }

        .brand-subtitle {
            margin-top: 4px;

            color: #b8cbe0;

            font-size: 11px;
            line-height: 1.4;
        }

        .sidebar-section {
            margin-top: 8px;
        }

        .sidebar-section + .sidebar-section {
            margin-top: 22px;
        }

        .sidebar-section-title {
            padding: 0 12px;
            margin-bottom: 8px;

            color: #8eaccb;

            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;

            min-height: 46px;

            padding: 0 13px;

            border-radius: 9px;

            color: #d9e6f4;

            font-size: 14px;
            font-weight: 600;

            transition: .2s ease;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, .08);
            color: #ffffff;
        }

        .sidebar-link.active {
            background: #1779c9;
            color: #ffffff;

            box-shadow: 0 5px 14px rgba(23, 121, 201, .22);
        }

        .sidebar-icon {
            width: 22px;
            text-align: center;

            font-size: 17px;

            flex-shrink: 0;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 18px;

            border-top: 1px solid rgba(255, 255, 255, .10);
        }

        .logout-button {
            width: 100%;

            display: flex;
            align-items: center;
            gap: 12px;

            min-height: 46px;

            padding: 0 13px;

            border: 0;
            border-radius: 9px;

            background: transparent;
            color: #d9e6f4;

            font-size: 14px;
            font-weight: 600;

            cursor: pointer;

            transition: .2s ease;
        }

        .logout-button:hover {
            background: rgba(255, 255, 255, .08);
            color: #ffffff;
        }

        /* =========================================================
           MAIN
        ========================================================== */

        .main-content {
            margin-left: 275px;
            min-height: 100vh;
        }

        /* =========================================================
           TOPBAR
        ========================================================== */

        .topbar {
            min-height: 76px;

            padding: 0 32px;

            background: #ffffff;

            border-bottom: 1px solid #e5eaf0;

            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar-left h1 {
            font-size: 21px;
            font-weight: 750;
            color: #17324d;
        }

        .topbar-left p {
            margin-top: 4px;

            font-size: 12px;
            color: #7b8794;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;

            border-radius: 50%;

            background: #eaf3fb;
            color: #1779c9;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 15px;
            font-weight: 800;
        }

        .user-text {
            text-align: right;
        }

        .user-name {
            font-size: 13px;
            font-weight: 700;
            color: #23384d;
        }

        .user-role {
            margin-top: 3px;

            font-size: 11px;
            color: #7b8794;
        }

        /* =========================================================
           CONTENT
        ========================================================== */

        .content {
            padding: 30px 32px 40px;
        }

        /* =========================================================
           WELCOME CARD
        ========================================================== */

        .welcome-card {
            position: relative;
            overflow: hidden;

            padding: 28px 30px;

            border-radius: 14px;

            background: linear-gradient(
                135deg,
                #12345b 0%,
                #174c7e 100%
            );

            color: #ffffff;

            box-shadow: 0 8px 24px rgba(18, 52, 91, .10);
        }

        .welcome-card::before {
            content: "";

            position: absolute;

            right: -35px;
            bottom: -90px;

            width: 220px;
            height: 220px;

            border-radius: 50%;

            border: 35px solid rgba(255, 255, 255, .04);
        }

        .welcome-card::after {
            content: "";

            position: absolute;

            right: 90px;
            top: -100px;

            width: 170px;
            height: 170px;

            border-radius: 50%;

            background: rgba(255, 255, 255, .035);
        }

        .welcome-content {
            position: relative;
            z-index: 2;
        }

        .welcome-label {
            display: inline-flex;
            align-items: center;

            padding: 6px 10px;

            border-radius: 999px;

            background: rgba(255, 255, 255, .10);
            color: #dbeaf8;

            font-size: 10px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .welcome-card h2 {
            margin-top: 12px;

            font-size: 25px;
            font-weight: 750;
        }

        .welcome-card p {
            max-width: 760px;

            margin-top: 9px;

            color: #d9e8f6;

            font-size: 14px;
            line-height: 1.7;
        }

        /* =========================================================
           SECTION HEADER
        ========================================================== */

        .section-header {
            margin-top: 30px;
            margin-bottom: 14px;

            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .section-header h3 {
            font-size: 17px;
            font-weight: 750;
            color: #17324d;
        }

        .section-header span {
            font-size: 12px;
            color: #7b8794;
        }

        /* =========================================================
           QUICK ACCESS
        ========================================================== */

        .quick-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        .quick-card {
            min-height: 175px;

            padding: 22px;

            background: #ffffff;

            border: 1px solid #e5eaf0;
            border-radius: 13px;

            box-shadow: 0 4px 15px rgba(15, 40, 65, .04);

            transition: .2s ease;
        }

        .quick-card:hover {
            transform: translateY(-2px);

            border-color: #c9dceb;

            box-shadow: 0 8px 22px rgba(15, 40, 65, .07);
        }

        .quick-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 15px;
        }

        .quick-card-icon {
            width: 43px;
            height: 43px;

            border-radius: 10px;

            background: #eaf3fb;
            color: #1779c9;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 19px;
        }

        .quick-card-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;

            padding: 5px 8px;

            border-radius: 999px;

            background: #f1f8f4;
            color: #2f7b50;

            font-size: 9px;
            font-weight: 800;
            letter-spacing: .3px;
            text-transform: uppercase;
        }

        .quick-card-status::before {
            content: "";

            width: 6px;
            height: 6px;

            border-radius: 50%;

            background: #49a56d;
        }

        .quick-card h4 {
            margin-top: 15px;

            font-size: 15px;
            font-weight: 750;
            color: #1e3a56;
        }

        .quick-card p {
            margin-top: 6px;

            color: #778594;

            font-size: 12px;
            line-height: 1.6;
        }

        .quick-card-arrow {
            display: inline-block;

            margin-top: 11px;

            color: #1779c9;

            font-size: 12px;
            font-weight: 700;
        }

        /* =========================================================
           LOWER GRID
        ========================================================== */

        .lower-grid {
            display: grid;
            grid-template-columns: 1.35fr .9fr;

            gap: 18px;

            margin-top: 18px;
        }

        .panel {
            padding: 22px;

            background: #ffffff;

            border: 1px solid #e5eaf0;
            border-radius: 13px;

            box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
        }

        .panel-title {
            display: flex;
            align-items: center;
            justify-content: space-between;

            padding-bottom: 15px;

            border-bottom: 1px solid #edf1f5;
        }

        .panel-title h3 {
            font-size: 15px;
            font-weight: 750;
            color: #1e3a56;
        }

        .panel-title span {
            font-size: 11px;
            color: #8a96a3;
        }

        /* =========================================================
           SYSTEM STATUS
        ========================================================== */

        .status-list {
            display: flex;
            flex-direction: column;

            margin-top: 5px;
        }

        .status-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;

            padding: 15px 0;
        }

        .status-item + .status-item {
            border-top: 1px solid #edf1f5;
        }

        .status-left {
            display: flex;
            align-items: center;
            gap: 12px;

            min-width: 0;
        }

        .status-icon {
            width: 37px;
            height: 37px;

            flex-shrink: 0;

            border-radius: 9px;

            background: #f4f8fb;
            color: #1779c9;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 16px;
        }

        .status-info {
            min-width: 0;
        }

        .status-info h4 {
            color: #25415b;

            font-size: 13px;
            font-weight: 750;
        }

        .status-info p {
            margin-top: 4px;

            color: #86919e;

            font-size: 11px;
            line-height: 1.5;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;

            flex-shrink: 0;

            padding: 5px 9px;

            border-radius: 999px;

            background: #f1f8f4;
            color: #2f7b50;

            font-size: 9px;
            font-weight: 800;
        }

        .status-badge::before {
            content: "";

            width: 6px;
            height: 6px;

            border-radius: 50%;

            background: #49a56d;
        }

        /* =========================================================
           ACCOUNT
        ========================================================== */

        .account-box {
            margin-top: 16px;

            padding: 17px;

            border-radius: 10px;

            background: #f7f9fc;
        }

        .account-row {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 15px;

            padding: 9px 0;
        }

        .account-row + .account-row {
            border-top: 1px solid #e7edf3;
        }

        .account-label {
            color: #7a8794;
            font-size: 12px;
        }

        .account-value {
            color: #253d55;
            font-size: 12px;
            font-weight: 700;

            text-align: right;
            word-break: break-word;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;

            padding: 5px 9px;

            border-radius: 999px;

            background: #eaf3fb;
            color: #1779c9;

            font-size: 10px;
            font-weight: 800;
        }

        /* =========================================================
           NOTICE
        ========================================================== */

        .notice {
            margin-top: 18px;

            padding: 15px 17px;

            border-left: 4px solid #f28c28;
            border-radius: 8px;

            background: #fff8f0;
        }

        .notice strong {
            display: block;

            color: #a85a12;

            font-size: 12px;
        }

        .notice p {
            margin-top: 5px;

            color: #80674f;

            font-size: 11px;
            line-height: 1.6;
        }

        /* =========================================================
           RESPONSIVE
        ========================================================== */

        @media (max-width: 1100px) {

            .quick-grid {
                grid-template-columns: 1fr 1fr;
            }

            .lower-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 800px) {

            .sidebar {
                position: static;

                width: 100%;
                height: auto;

                padding: 18px;
            }

            .sidebar-footer {
                margin-top: 20px;
            }

            .main-content {
                margin-left: 0;
            }

            .topbar {
                height: auto;

                padding: 18px 20px;

                gap: 15px;
            }

            .content {
                padding: 22px 20px 30px;
            }
        }

        @media (max-width: 600px) {

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .user-info {
                width: 100%;
                justify-content: flex-start;
            }

            .user-text {
                text-align: left;
            }

            .quick-grid {
                grid-template-columns: 1fr;
            }

            .welcome-card {
                padding: 23px;
            }

            .welcome-card h2 {
                font-size: 21px;
            }

            .status-item {
                align-items: flex-start;
            }

            .status-badge {
                margin-top: 2px;
            }
        }
    </style>
</head>

<body>

<div class="admin-layout">

    {{-- =========================================================
         SIDEBAR
    ========================================================== --}}
    <aside class="sidebar">

        {{-- BRAND --}}
        <div class="brand">

            <div class="brand-logo">
                M
            </div>

            <div class="brand-text">

                <div class="brand-title">
                    MAGENTA
                </div>

                <div class="brand-subtitle">
                    Sistem Informasi Magang
                </div>

            </div>

        </div>


        {{-- =====================================================
             UTAMA
        ====================================================== --}}
        <div class="sidebar-section">

            <div class="sidebar-section-title">
                Utama
            </div>

            <nav class="sidebar-menu">

                <a
                    href="{{ route('admin.dashboard') }}"
                    class="sidebar-link active"
                >
                    <span class="sidebar-icon">⌂</span>
                    <span>Dashboard</span>
                </a>

            </nav>

        </div>


        {{-- =====================================================
             SISTEM
             Admin + Super Admin
        ====================================================== --}}
        <div class="sidebar-section">

            <div class="sidebar-section-title">
                Sistem
            </div>

            <nav class="sidebar-menu">

                <a
                    href="{{ route('superadmin.perusahaan.index') }}"
                    class="sidebar-link"
                >
                    <span class="sidebar-icon">▤</span>
                    <span>Profil Perusahaan</span>
                </a>


                <a
                    href="{{ route('superadmin.banners.index') }}"
                    class="sidebar-link"
                >
                    <span class="sidebar-icon">▣</span>
                    <span>Kelola Banner</span>
                </a>


                <a
                    href="{{ route('superadmin.pengaturan.index') }}"
                    class="sidebar-link"
                >
                    <span class="sidebar-icon">⚙</span>
                    <span>Kelola Konten</span>
                </a>

            </nav>

        </div>


        {{-- =====================================================
             LOGOUT
        ====================================================== --}}
        <div class="sidebar-footer">

            <form
                action="{{ route('logout') }}"
                method="POST"
            >

                @csrf

                <button
                    type="submit"
                    class="logout-button"
                >
                    <span class="sidebar-icon">↪</span>
                    <span>Logout</span>
                </button>

            </form>

        </div>

    </aside>


    {{-- =========================================================
         MAIN CONTENT
    ========================================================== --}}
    <main class="main-content">


        {{-- =====================================================
             TOPBAR
        ====================================================== --}}
        <header class="topbar">

            <div class="topbar-left">

                <h1>
                    Dashboard
                </h1>

                <p>
                    Panel administrasi sistem informasi magang
                </p>

            </div>


            <div class="user-info">

                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>


                <div class="user-text">

                    <div class="user-name">
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </div>

                    <div class="user-role">
                        Admin
                    </div>

                </div>

            </div>

        </header>


        {{-- =====================================================
             CONTENT
        ====================================================== --}}
        <div class="content">


            {{-- =================================================
                 WELCOME
            ================================================== --}}
            <section class="welcome-card">

                <div class="welcome-content">

                    <span class="welcome-label">
                        Panel Admin
                    </span>

                    <h2>
                        Selamat Datang, {{ auth()->user()->name ?? 'Administrator' }}
                    </h2>

                    <p>
                        Kelola informasi website MAGENTA melalui panel administrasi.
                        Gunakan menu yang tersedia untuk memperbarui profil perusahaan,
                        banner, dan konten website.
                    </p>

                </div>

            </section>


            {{-- =================================================
                 AKSES CEPAT
            ================================================== --}}
            <div class="section-header">

                <h3>
                    Akses Cepat
                </h3>

                <span>
                    Fitur yang dapat dikelola
                </span>

            </div>


            <div class="quick-grid">


                {{-- PROFIL PERUSAHAAN --}}
                <a
                    href="{{ route('superadmin.perusahaan.index') }}"
                    class="quick-card"
                >

                    <div class="quick-card-top">

                        <div class="quick-card-icon">
                            ▤
                        </div>

                        <span class="quick-card-status">
                            Aktif
                        </span>

                    </div>

                    <h4>
                        Profil Perusahaan
                    </h4>

                    <p>
                        Kelola identitas, alamat, kontak, website,
                        dan informasi perusahaan yang ditampilkan
                        pada website MAGENTA.
                    </p>

                    <span class="quick-card-arrow">
                        Kelola Profil →
                    </span>

                </a>


                {{-- BANNER --}}
                <a
                    href="{{ route('superadmin.banners.index') }}"
                    class="quick-card"
                >

                    <div class="quick-card-top">

                        <div class="quick-card-icon">
                            ▣
                        </div>

                        <span class="quick-card-status">
                            Aktif
                        </span>

                    </div>

                    <h4>
                        Kelola Banner
                    </h4>

                    <p>
                        Kelola banner utama yang digunakan sebagai
                        tampilan informasi utama pada halaman website.
                    </p>

                    <span class="quick-card-arrow">
                        Kelola Banner →
                    </span>

                </a>


                {{-- KONTEN --}}
                <a
                    href="{{ route('superadmin.pengaturan.index') }}"
                    class="quick-card"
                >

                    <div class="quick-card-top">

                        <div class="quick-card-icon">
                            ⚙
                        </div>

                        <span class="quick-card-status">
                            Aktif
                        </span>

                    </div>

                    <h4>
                        Kelola Konten
                    </h4>

                    <p>
                        Perbarui pengumuman, persyaratan,
                        alur magang, dan informasi website MAGENTA.
                    </p>

                    <span class="quick-card-arrow">
                        Kelola Konten →
                    </span>

                </a>

            </div>


            {{-- =================================================
                 LOWER CONTENT
            ================================================== --}}
            <div class="lower-grid">


                {{-- =================================================
                     STATUS SISTEM
                ================================================== --}}
                <section class="panel">

                    <div class="panel-title">

                        <h3>
                            Status Sistem
                        </h3>

                        <span>
                            Kondisi fitur
                        </span>

                    </div>


                    <div class="status-list">


                        {{-- PROFIL --}}
                        <div class="status-item">

                            <div class="status-left">

                                <div class="status-icon">
                                    ▤
                                </div>

                                <div class="status-info">

                                    <h4>
                                        Profil Perusahaan
                                    </h4>

                                    <p>
                                        Informasi identitas dan kontak perusahaan
                                        tersedia untuk dikelola.
                                    </p>

                                </div>

                            </div>

                            <span class="status-badge">
                                Tersedia
                            </span>

                        </div>


                        {{-- BANNER --}}
                        <div class="status-item">

                            <div class="status-left">

                                <div class="status-icon">
                                    ▣
                                </div>

                                <div class="status-info">

                                    <h4>
                                        Banner Website
                                    </h4>

                                    <p>
                                        Banner utama website dapat diperbarui
                                        melalui panel administrasi.
                                    </p>

                                </div>

                            </div>

                            <span class="status-badge">
                                Tersedia
                            </span>

                        </div>


                        {{-- KONTEN --}}
                        <div class="status-item">

                            <div class="status-left">

                                <div class="status-icon">
                                    ⚙
                                </div>

                                <div class="status-info">

                                    <h4>
                                        Konten Website
                                    </h4>

                                    <p>
                                        Informasi website dapat diperbarui
                                        tanpa mengubah struktur halaman.
                                    </p>

                                </div>

                            </div>

                            <span class="status-badge">
                                Tersedia
                            </span>

                        </div>


                    </div>

                </section>


                {{-- =================================================
                     INFORMASI AKUN
                ================================================== --}}
                <section class="panel">

                    <div class="panel-title">

                        <h3>
                            Informasi Akun
                        </h3>

                        <span>
                            Akun aktif
                        </span>

                    </div>


                    <div class="account-box">

                        <div class="account-row">

                            <span class="account-label">
                                Nama
                            </span>

                            <span class="account-value">
                                {{ auth()->user()->name ?? '-' }}
                            </span>

                        </div>


                        <div class="account-row">

                            <span class="account-label">
                                Email
                            </span>

                            <span class="account-value">
                                {{ auth()->user()->email ?? '-' }}
                            </span>

                        </div>


                        <div class="account-row">

                            <span class="account-label">
                                Role
                            </span>

                            <span class="account-value">

                                <span class="role-badge">
                                    ADMIN
                                </span>

                            </span>

                        </div>

                    </div>


                    <div class="notice">

                        <strong>
                            Hak Akses Admin
                        </strong>

                        <p>
                            Akun Admin dapat mengelola Profil Perusahaan,
                            Banner, dan Konten Website.
                            Pengelolaan pengguna tetap menjadi hak
                            khusus Super Admin.
                        </p>

                    </div>

                </section>


            </div>

        </div>

    </main>

</div>

</body>

</html>