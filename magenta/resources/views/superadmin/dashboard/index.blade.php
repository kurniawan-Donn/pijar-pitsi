<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Dashboard Super Admin - MAGENTA
    </title>


    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }


        /* =====================================================
           SIDEBAR
        ====================================================== */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;

            width: 260px;
            height: 100vh;

            background: #111827;
            color: white;

            padding: 25px 18px;

            overflow-y: auto;
        }


        .logo {
            text-align: center;

            font-size: 25px;
            font-weight: bold;

            color: #a78bfa;

            margin-bottom: 8px;
        }


        .role {
            text-align: center;

            color: #d1d5db;

            font-size: 13px;

            margin-bottom: 30px;
        }


        .menu-title {
            font-size: 11px;

            color: #9ca3af;

            text-transform: uppercase;

            letter-spacing: 1px;

            margin:
                20px 12px
                10px;
        }


        .menu a {
            display: block;

            color: #e5e7eb;

            text-decoration: none;

            padding: 13px 15px;

            border-radius: 8px;

            margin-bottom: 6px;

            transition: 0.2s;
        }


        .menu a:hover {
            background: #374151;
            color: white;
        }


        .menu a.active {
            background: #7c3aed;
            color: white;
        }


        /* =====================================================
           LOGOUT
        ====================================================== */

        .logout-btn {
            width: 100%;

            margin-top: 25px;

            padding: 12px;

            border: none;

            border-radius: 8px;

            background: #ef4444;

            color: white;

            cursor: pointer;

            font-weight: bold;
        }


        .logout-btn:hover {
            background: #dc2626;
        }


        /* =====================================================
           MAIN
        ====================================================== */

        .main {
            margin-left: 260px;

            min-height: 100vh;
        }


        /* =====================================================
           TOPBAR
        ====================================================== */

        .topbar {
            background: white;

            padding:
                20px 30px;

            display: flex;

            justify-content: space-between;

            align-items: center;

            box-shadow:
                0 2px 10px
                rgba(0,0,0,0.05);
        }


        .topbar h1 {
            font-size: 24px;
        }


        .user-info {
            color: #666;
        }


        .user-info strong {
            color: #7c3aed;
        }


        /* =====================================================
           CONTENT
        ====================================================== */

        .content {
            padding: 30px;
        }


        /* =====================================================
           WELCOME
        ====================================================== */

        .welcome {
            background: white;

            padding: 25px;

            border-radius: 15px;

            margin-bottom: 25px;

            box-shadow:
                0 5px 20px
                rgba(0,0,0,0.05);
        }


        .welcome h2 {
            margin-bottom: 10px;

            color: #7c3aed;
        }


        .welcome p {
            color: #6b7280;

            line-height: 1.6;
        }


        /* =====================================================
           STATISTICS
        ====================================================== */

        .cards {
            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 20px;

            margin-bottom: 30px;
        }


        .card {
            background: white;

            padding: 25px;

            border-radius: 15px;

            box-shadow:
                0 5px 20px
                rgba(0,0,0,0.05);
        }


        .card h3 {
            font-size: 15px;

            color: #666;

            margin-bottom: 10px;
        }


        .number {
            font-size: 32px;

            font-weight: bold;

            color: #7c3aed;
        }


        /* =====================================================
           QUICK ACTIONS
        ====================================================== */

        .quick-actions {
            background: white;

            padding: 25px;

            border-radius: 15px;

            box-shadow:
                0 5px 20px
                rgba(0,0,0,0.05);
        }


        .quick-actions h2 {
            margin-bottom: 20px;
        }


        .action-grid {
            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 15px;
        }


        .action {
            display: block;

            padding: 20px;

            border:
                1px solid #e5e7eb;

            border-radius: 12px;

            text-decoration: none;

            color: #374151;

            transition: 0.2s;
        }


        .action:hover {
            border-color: #7c3aed;

            background: #faf5ff;
        }


        .action h3 {
            margin-bottom: 8px;

            color: #7c3aed;
        }


        .action p {
            font-size: 14px;

            color: #6b7280;

            line-height: 1.5;
        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 1100px) {

            .cards {
                grid-template-columns:
                    repeat(2, 1fr);
            }


            .action-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }

        }


        @media (max-width: 700px) {

            .sidebar {
                position: relative;

                width: 100%;

                height: auto;
            }


            .main {
                margin-left: 0;
            }


            .cards,
            .action-grid {
                grid-template-columns:
                    1fr;
            }


            .topbar {
                flex-direction: column;

                align-items: flex-start;

                gap: 10px;
            }

        }

    </style>

</head>


<body>


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="sidebar">


        <div class="logo">
            MAGENTA
        </div>


        <div class="role">
            SUPER ADMIN
        </div>


        <div class="menu">


            <!-- =================================================
                 UTAMA
            ================================================== -->

            <div class="menu-title">
                Utama
            </div>


            <a
                href="{{ route('superadmin.dashboard') }}"
                class="active"
            >
                Dashboard
            </a>


            <!-- =================================================
                 MANAJEMEN MAGANG
            ================================================== -->

            <div class="menu-title">
                Manajemen Magang
            </div>


            <!-- DATA MAHASISWA -->

            <a href="#">
                Data Mahasiswa
            </a>


            <!-- PROFIL PERUSAHAAN -->

            <a
                href="{{ route('superadmin.perusahaan.index') }}"
            >
                Profil Perusahaan
            </a>


            <!-- PENGAJUAN -->

            <a
                href="{{ route('admin.pengajuan.index') }}"
            >
                Pengajuan Magang
            </a>


            <!-- DATA MAGANG -->

            <a href="#">
                Data Magang
            </a>


            <!-- ABSENSI -->

            <a href="#">
                Absensi
            </a>


            <!-- LOGBOOK -->

            <a href="#">
                Logbook
            </a>


            <!-- LAPORAN -->

            <a href="#">
                Laporan Magang
            </a>


            <!-- PENILAIAN -->

            <a href="#">
                Penilaian
            </a>



            <!-- =================================================
                 SISTEM
            ================================================== -->

            <div class="menu-title">
                Sistem
            </div>


            <!-- KELOLA ADMIN -->

            <a href="#">
                Kelola Admin
            </a>


            <!-- KELOLA BANNER -->

            <a
                href="{{ route('superadmin.banners.create') }}"
            >
                Kelola Banner
            </a>


            <!-- PENGATURAN SISTEM -->

            <a
                href="{{ route('superadmin.pengaturan.index') }}"
            >
                Pengaturan Sistem
            </a>


            <!-- RIWAYAT -->

            <a href="#">
                Riwayat Aktivitas
            </a>


        </div>



        <!-- =================================================
             LOGOUT
        ================================================== -->

        <form
            action="{{ route('logout') }}"
            method="POST"
        >

            @csrf

            <button
                type="submit"
                class="logout-btn"
            >
                Logout
            </button>

        </form>


    </aside>



    <!-- =====================================================
         MAIN
    ====================================================== -->

    <main class="main">


        <!-- =================================================
             TOPBAR
        ================================================== -->

        <header class="topbar">


            <h1>
                Dashboard Super Admin
            </h1>


            <div class="user-info">

                Login sebagai:

                <strong>
                    {{ auth()->user()->name }}
                </strong>

            </div>


        </header>



        <!-- =================================================
             CONTENT
        ================================================== -->

        <div class="content">


            <!-- =================================================
                 WELCOME
            ================================================== -->

            <div class="welcome">


                <h2>
                    Selamat Datang di MAGENTA 👋
                </h2>


                <p>
                    Kamu sedang masuk sebagai Super Admin.
                    Kelola seluruh sistem informasi magang
                    dari dashboard ini.
                </p>


            </div>



            <!-- =================================================
                 STATISTIK
            ================================================== -->

            <div class="cards">


                <!-- TOTAL MAHASISWA -->

                <div class="card">

                    <h3>
                        Total Mahasiswa
                    </h3>


                    <div class="number">
                        0
                    </div>

                </div>


                <!-- PENGAJUAN MENUNGGU -->

                <div class="card">

                    <h3>
                        Pengajuan Menunggu
                    </h3>


                    <div class="number">
                        0
                    </div>

                </div>


                <!-- MAGANG AKTIF -->

                <div class="card">

                    <h3>
                        Magang Aktif
                    </h3>


                    <div class="number">
                        0
                    </div>

                </div>


            </div>



            <!-- =================================================
                 AKSES CEPAT
            ================================================== -->

            <div class="quick-actions">


                <h2>
                    Akses Cepat
                </h2>


                <div class="action-grid">


                    <!-- KELOLA ADMIN -->

                    <a
                        href="#"
                        class="action"
                    >

                        <h3>
                            Kelola Admin
                        </h3>


                        <p>
                            Tambahkan, ubah, atau
                            nonaktifkan akun Admin.
                        </p>

                    </a>



                    <!-- KELOLA BANNER -->

                    <a
                        href="{{ route('superadmin.banners.create') }}"
                        class="action"
                    >

                        <h3>
                            Kelola Banner
                        </h3>


                        <p>
                            Ganti gambar, judul,
                            deskripsi, dan tombol
                            banner website utama.
                        </p>

                    </a>



                    <!-- PENGAJUAN -->

                    <a
                        href="{{ route('admin.pengajuan.index') }}"
                        class="action"
                    >

                        <h3>
                            Pengajuan Magang
                        </h3>


                        <p>
                            Periksa dan proses
                            pengajuan magang mahasiswa.
                        </p>

                    </a>



                    <!-- DATA MAHASISWA -->

                    <a
                        href="#"
                        class="action"
                    >

                        <h3>
                            Data Mahasiswa
                        </h3>


                        <p>
                            Lihat dan kelola seluruh
                            data mahasiswa.
                        </p>

                    </a>



                    <!-- PROFIL PERUSAHAAN -->

                    <a
                        href="{{ route('superadmin.perusahaan.index') }}"
                        class="action"
                    >

                        <h3>
                            Profil Perusahaan
                        </h3>


                        <p>
                            Kelola profil perusahaan
                            tempat pelaksanaan magang.
                        </p>

                    </a>



                    <!-- PENGATURAN -->

                    <a
                        href="{{ route('superadmin.pengaturan.index') }}"
                        class="action"
                    >

                        <h3>
                            Pengaturan
                        </h3>


                        <p>
                            Atur konten website
                            MAGENTA.
                        </p>

                    </a>



                    <!-- RIWAYAT -->

                    <a
                        href="#"
                        class="action"
                    >

                        <h3>
                            Riwayat Aktivitas
                        </h3>


                        <p>
                            Pantau aktivitas pengguna
                            dalam sistem.
                        </p>

                    </a>


                </div>


            </div>


        </div>


    </main>


</body>

</html>