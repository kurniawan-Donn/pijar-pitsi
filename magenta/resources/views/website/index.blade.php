<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @if(isset($perusahaan) && $perusahaan && $perusahaan->nama)
            {{ $perusahaan->nama }} - Sistem Informasi Magang
        @else
            MAGENTA - Sistem Informasi Magang
        @endif
    </title>


    <style>

        /* =====================================================
           RESET
        ====================================================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        html {
            scroll-behavior: smooth;
        }


        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #263238;
            background: #ffffff;
            line-height: 1.6;
        }


        a {
            text-decoration: none;
        }


        /* =====================================================
           COLOR
        ====================================================== */

        :root {
            --primary: #0878a8;
            --primary-dark: #075d83;
            --secondary: #f47b20;
            --secondary-dark: #d96512;

            --text: #263238;
            --muted: #667781;

            --light: #f5f9fc;
            --white: #ffffff;

            --border: #e4edf2;

            --shadow:
                0 10px 35px rgba(0, 72, 100, 0.08);
        }


        /* =====================================================
           NAVBAR
        ====================================================== */

        .navbar {
            position: sticky;
            top: 0;
            z-index: 9999;

            background: rgba(255, 255, 255, 0.97);

            border-bottom:
                1px solid var(--border);

            box-shadow:
                0 2px 12px rgba(0, 0, 0, 0.04);
        }


        .nav-container {
            width: 90%;
            max-width: 1250px;

            margin: auto;

            min-height: 78px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 30px;
        }


        /* LOGO */

        .logo {
            display: flex;
            align-items: center;

            gap: 10px;

            color: var(--primary);

            font-size: 27px;
            font-weight: 800;

            letter-spacing: -0.5px;
        }


        .logo-mark {
            width: 40px;
            height: 40px;

            border-radius: 10px;

            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    #0d94c9
                );

            display: flex;
            align-items: center;
            justify-content: center;

            color: white;

            font-size: 18px;
            font-weight: bold;
        }


        /* MENU */

        .nav-menu {
            display: flex;

            align-items: center;

            gap: 28px;

            list-style: none;
        }


        .nav-menu a {
            color: #455a64;

            font-size: 14px;

            font-weight: 600;

            transition: 0.2s;
        }


        .nav-menu a:hover {
            color: var(--primary);
        }


        .nav-admin {
            background: var(--primary);

            color: white !important;

            padding:
                11px 18px;

            border-radius: 7px;
        }


        .nav-admin:hover {
            background: var(--primary-dark);

            color: white !important;
        }


        /* =====================================================
           HERO
        ====================================================== */

        .hero {
            min-height: 560px;

            background:
                linear-gradient(
                    135deg,
                    #f3f9fc 0%,
                    #ffffff 55%,
                    #fff7f1 100%
                );

            display: flex;
            align-items: center;
        }


        .hero-container {
            width: 90%;
            max-width: 1250px;

            margin: auto;

            padding:
                75px 0;

            display: grid;

            grid-template-columns:
                1.05fr 0.95fr;

            align-items: center;

            gap: 70px;
        }


        .hero-content {
            max-width: 650px;
        }


        .hero-label {
            display: inline-block;

            padding:
                8px 14px;

            background:
                rgba(8, 120, 168, 0.10);

            color: var(--primary);

            border-radius: 30px;

            font-size: 13px;

            font-weight: 700;

            margin-bottom: 20px;
        }


        .hero-content h1 {
            font-size: 52px;

            line-height: 1.12;

            color: #17313d;

            margin-bottom: 20px;

            letter-spacing: -1.5px;
        }


        .hero-content h1 span {
            color: var(--primary);
        }


        .hero-content p {
            color: var(--muted);

            font-size: 17px;

            max-width: 600px;

            margin-bottom: 30px;
        }


        .hero-buttons {
            display: flex;

            gap: 12px;

            flex-wrap: wrap;
        }


        .btn {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            padding:
                13px 22px;

            border-radius: 7px;

            font-weight: 700;

            font-size: 14px;

            transition: 0.2s;

            border: none;

            cursor: pointer;
        }


        .btn-primary {
            background: var(--primary);

            color: white;
        }


        .btn-primary:hover {
            background: var(--primary-dark);

            transform: translateY(-1px);
        }


        .btn-orange {
            background: var(--secondary);

            color: white;
        }


        .btn-orange:hover {
            background: var(--secondary-dark);

            transform: translateY(-1px);
        }


        .btn-outline {
            background: white;

            color: var(--primary);

            border:
                1px solid var(--primary);
        }


        .btn-outline:hover {
            background: var(--primary);

            color: white;
        }


        /* HERO IMAGE */

        .hero-image-box {
            position: relative;

            min-height: 350px;

            border-radius: 24px;

            overflow: hidden;

            background:
                linear-gradient(
                    135deg,
                    #0878a8,
                    #0b5e80
                );

            box-shadow:
                0 25px 60px
                rgba(8, 120, 168, 0.18);
        }


        .hero-image {
            width: 100%;
            height: 100%;

            min-height: 350px;

            object-fit: cover;

            display: block;
        }


        .hero-placeholder {
            min-height: 350px;

            display: flex;

            align-items: center;
            justify-content: center;

            color: white;

            font-size: 35px;

            font-weight: 800;
        }


        /* =====================================================
           QUICK MENU
        ====================================================== */

        .quick-section {
            background: white;

            padding:
                0 0 55px;
        }


        .quick-container {
            width: 90%;
            max-width: 1150px;

            margin:
                -30px auto 0;

            position: relative;

            z-index: 10;
        }


        .quick-grid {
            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 16px;
        }


        .quick-card {
            background: white;

            border:
                1px solid var(--border);

            border-radius: 12px;

            padding: 25px 20px;

            box-shadow:
                0 10px 30px
                rgba(0, 70, 100, 0.08);

            transition: 0.2s;
        }


        .quick-card:hover {
            transform: translateY(-5px);

            box-shadow:
                0 16px 35px
                rgba(0, 70, 100, 0.13);
        }


        .quick-icon {
            width: 45px;
            height: 45px;

            border-radius: 10px;

            display: flex;

            align-items: center;
            justify-content: center;

            background:
                #eef8fc;

            color: var(--primary);

            font-size: 21px;

            margin-bottom: 15px;
        }


        .quick-card h3 {
            color: #263238;

            font-size: 17px;

            margin-bottom: 7px;
        }


        .quick-card p {
            color: var(--muted);

            font-size: 13px;

            margin-bottom: 14px;
        }


        .quick-link {
            color: var(--primary);

            font-size: 13px;

            font-weight: 700;
        }


        .quick-link:hover {
            color: var(--secondary);
        }


        /* =====================================================
           GENERAL SECTION
        ====================================================== */

        .section {
            padding:
                85px 0;
        }


        .section-light {
            background: var(--light);
        }


        .container {
            width: 90%;
            max-width: 1150px;

            margin: auto;
        }


        .section-heading {
            text-align: center;

            max-width: 700px;

            margin:
                0 auto 45px;
        }


        .section-heading .label {
            color: var(--secondary);

            text-transform: uppercase;

            font-size: 12px;

            font-weight: 800;

            letter-spacing: 1px;

            margin-bottom: 8px;
        }


        .section-heading h2 {
            font-size: 34px;

            color: #20343d;

            margin-bottom: 12px;
        }


        .section-heading p {
            color: var(--muted);

            font-size: 15px;
        }


        /* =====================================================
           PENGUMUMAN
        ====================================================== */

        .announcement-grid {
            display: grid;

            grid-template-columns:
                1.4fr 0.8fr;

            gap: 25px;
        }


        .announcement-main {
            background: white;

            border-radius: 15px;

            padding: 32px;

            border:
                1px solid var(--border);

            box-shadow: var(--shadow);
        }


        .announcement-badge {
            display: inline-block;

            background:
                #fff1e7;

            color:
                var(--secondary);

            padding:
                6px 11px;

            border-radius: 20px;

            font-size: 11px;

            font-weight: 800;

            margin-bottom: 15px;
        }


        .announcement-main h3 {
            color: #20343d;

            font-size: 24px;

            margin-bottom: 12px;
        }


        .announcement-main p {
            color: var(--muted);

            font-size: 15px;

            line-height: 1.8;
        }


        .announcement-side {
            display: grid;

            gap: 15px;
        }


        .announcement-item {
            background: white;

            border:
                1px solid var(--border);

            border-radius: 12px;

            padding: 22px;
        }


        .announcement-item h4 {
            color: var(--primary);

            font-size: 16px;

            margin-bottom: 6px;
        }


        .announcement-item p {
            color: var(--muted);

            font-size: 13px;
        }


        /* =====================================================
           TENTANG
        ====================================================== */

        .about-grid {
            display: grid;

            grid-template-columns:
                0.8fr 1.2fr;

            gap: 60px;

            align-items: center;
        }


        .about-label {
            color: var(--secondary);

            font-size: 13px;

            font-weight: 800;

            text-transform: uppercase;

            margin-bottom: 10px;
        }


        .about-content h2 {
            font-size: 36px;

            color: #20343d;

            margin-bottom: 18px;
        }


        .about-content p {
            color: var(--muted);

            line-height: 1.8;

            font-size: 15px;
        }


        .about-cards {
            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 15px;
        }


        .about-card {
            padding: 24px;

            border-radius: 12px;

            background: white;

            border:
                1px solid var(--border);

            box-shadow: var(--shadow);
        }


        .about-card h3 {
            color: var(--primary);

            font-size: 17px;

            margin-bottom: 8px;
        }


        .about-card p {
            color: var(--muted);

            font-size: 13px;
        }


        /* =====================================================
           PERUSAHAAN
        ====================================================== */

        .company-box {
            background:
                linear-gradient(
                    135deg,
                    #0878a8,
                    #075d83
                );

            border-radius: 20px;

            padding: 45px;

            color: white;

            display: grid;

            grid-template-columns:
                1.1fr 0.9fr;

            gap: 40px;

            align-items: center;
        }


        .company-box h2 {
            font-size: 32px;

            margin-bottom: 15px;
        }


        .company-box p {
            color:
                rgba(255,255,255,0.85);

            line-height: 1.8;
        }


        .company-contact {
            display: grid;

            gap: 12px;
        }


        .company-contact-item {
            background:
                rgba(255,255,255,0.10);

            padding:
                15px 18px;

            border-radius: 9px;

            font-size: 14px;
        }


        /* =====================================================
           FOOTER
        ====================================================== */

        footer {
            background: #17313d;

            color: white;

            padding:
                35px 0;
        }


        .footer-container {
            width: 90%;
            max-width: 1150px;

            margin: auto;

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;
        }


        .footer-logo {
            font-size: 22px;

            font-weight: 800;

            color: white;
        }


        .footer-text {
            color:
                rgba(255,255,255,0.65);

            font-size: 13px;
        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 1000px) {

            .nav-menu {
                gap: 15px;
            }


            .nav-menu a {
                font-size: 13px;
            }


            .hero-container {
                grid-template-columns: 1fr;

                gap: 40px;
            }


            .hero-content {
                max-width: 800px;

                text-align: center;

                margin: auto;
            }


            .hero-buttons {
                justify-content: center;
            }


            .hero-image-box {
                max-width: 600px;

                width: 100%;

                margin: auto;
            }


            .quick-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }


            .announcement-grid {
                grid-template-columns: 1fr;
            }


            .about-grid {
                grid-template-columns: 1fr;
            }


            .company-box {
                grid-template-columns: 1fr;
            }

        }


        @media (max-width: 700px) {

            .nav-container {
                flex-direction: column;

                padding:
                    15px 0;
            }


            .nav-menu {
                flex-wrap: wrap;

                justify-content: center;
            }


            .hero-content h1 {
                font-size: 38px;
            }


            .hero {
                min-height: auto;
            }


            .quick-grid {
                grid-template-columns: 1fr;
            }


            .about-cards {
                grid-template-columns: 1fr;
            }


            .section-heading h2 {
                font-size: 29px;
            }


            .company-box {
                padding: 30px 25px;
            }


            .company-box h2 {
                font-size: 27px;
            }


            .footer-container {
                flex-direction: column;

                text-align: center;
            }

        }

    </style>

</head>


<body>


    {{-- =====================================================
         NAVBAR
    ====================================================== --}}

    <header class="navbar">

        <div class="nav-container">

            <a
                href="{{ route('home') }}"
                class="logo"
            >

                <div class="logo-mark">
                    M
                </div>

                MAGENTA

            </a>


            <ul class="nav-menu">

                <li>
                    <a href="{{ route('home') }}">
                        Beranda
                    </a>
                </li>

                <li>
                    <a href="#tentang">
                        Tentang
                    </a>
                </li>

                <li>
                    <a href="#pengumuman">
                        Pengumuman
                    </a>
                </li>

                <li>
                    <a href="#kontak">
                        Kontak
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route('login') }}"
                        class="nav-admin"
                    >
                        Login Admin
                    </a>
                </li>

            </ul>

        </div>

    </header>



    {{-- =====================================================
         HERO
         DATA DIAMBIL DARI BANNER DATABASE
    ====================================================== --}}

    <section class="hero">

        <div class="hero-container">


            {{-- =================================================
                 HERO CONTENT
            ================================================== --}}

            <div class="hero-content">

                <div class="hero-label">
                    SISTEM INFORMASI MAGANG
                </div>


                {{-- =================================================
                     JUDUL BANNER
                ================================================== --}}

                <h1>

                    @if(
                        isset($banners) &&
                        $banners->isNotEmpty() &&
                        $banners->first()->judul
                    )

                        {{ $banners->first()->judul }}

                    @else

                        Selamat Datang di

                        <span>
                            MAGENTA
                        </span>

                    @endif

                </h1>


                {{-- =================================================
                     DESKRIPSI BANNER
                ================================================== --}}

                <p>

                    @if(
                        isset($banners) &&
                        $banners->isNotEmpty() &&
                        $banners->first()->deskripsi
                    )

                        {{ $banners->first()->deskripsi }}

                    @elseif(
                        isset($perusahaan) &&
                        $perusahaan &&
                        $perusahaan->nama
                    )

                        Sistem informasi magang
                        {{ $perusahaan->nama }}
                        untuk membantu mahasiswa
                        dalam proses pendaftaran,
                        pengajuan, dan pemantauan
                        kegiatan magang.

                    @else

                        Sistem informasi magang terintegrasi
                        untuk membantu mahasiswa dalam proses
                        pendaftaran, pengajuan, dan pemantauan
                        kegiatan magang.

                    @endif

                </p>


                {{-- =================================================
                     TOMBOL HERO
                ================================================== --}}

                <div class="hero-buttons">


                    {{-- TOMBOL DARI BANNER --}}

                    @if(
                        isset($banners) &&
                        $banners->isNotEmpty() &&
                        $banners->first()->tombol_teks
                    )

                        <a
                            href="{{ $banners->first()->tombol_link ?: url('/pengajuan') }}"
                            class="btn btn-orange"
                        >

                            {{ $banners->first()->tombol_teks }}

                        </a>

                    @else

                        <a
                            href="{{ url('/pengajuan') }}"
                            class="btn btn-orange"
                        >

                            Daftar Magang

                        </a>

                    @endif


                    {{-- TOMBOL STATUS --}}

                    <a
                        href="{{ url('/status') }}"
                        class="btn btn-primary"
                    >

                        Cek Status Pengajuan

                    </a>


                </div>

            </div>



            {{-- =================================================
                 HERO IMAGE
            ================================================== --}}

            <div class="hero-image-box">


                @if(
                    isset($banners) &&
                    $banners->isNotEmpty()
                )

                    @php
                        $banner = $banners->first();
                    @endphp


                    @if($banner->gambar)

                        <img
                            src="{{ asset('storage/' . $banner->gambar) }}"
                            alt="{{ $banner->judul }}"
                            class="hero-image"
                        >

                    @else

                        <div class="hero-placeholder">
                            MAGENTA
                        </div>

                    @endif


                @else

                    <div class="hero-placeholder">
                        MAGENTA
                    </div>

                @endif


            </div>

        </div>

    </section>



    {{-- =====================================================
         MENU UTAMA
    ====================================================== --}}

    <section class="quick-section">

        <div class="quick-container">

            <div class="quick-grid">


                {{-- PERSYARATAN --}}

                <div class="quick-card">

                    <div class="quick-icon">
                        📄
                    </div>

                    <h3>
                        Persyaratan Magang
                    </h3>

                    <p>
                        Lihat dokumen dan persyaratan
                        yang harus disiapkan.
                    </p>

                    <a
                        href="{{ url('/persyaratan') }}"
                        class="quick-link"
                    >
                        Lihat Persyaratan →
                    </a>

                </div>



                {{-- DAFTAR --}}

                <div class="quick-card">

                    <div class="quick-icon">
                        📝
                    </div>

                    <h3>
                        Daftar Magang
                    </h3>

                    <p>
                        Ajukan kegiatan magang
                        melalui formulir pendaftaran.
                    </p>

                    <a
                        href="{{ url('/pengajuan') }}"
                        class="quick-link"
                    >
                        Daftar Sekarang →
                    </a>

                </div>



                {{-- STATUS --}}

                <div class="quick-card">

                    <div class="quick-icon">
                        🔎
                    </div>

                    <h3>
                        Cek Status
                    </h3>

                    <p>
                        Periksa apakah pengajuan
                        sudah diterima atau belum.
                    </p>

                    <a
                        href="{{ url('/status') }}"
                        class="quick-link"
                    >
                        Cek Pengajuan →
                    </a>

                </div>



                {{-- ALUR --}}

                <div class="quick-card">

                    <div class="quick-icon">
                        🔄
                    </div>

                    <h3>
                        Alur Magang
                    </h3>

                    <p>
                        Pelajari proses magang
                        dari pengajuan sampai selesai.
                    </p>

                    <a
                        href="{{ url('/alur') }}"
                        class="quick-link"
                    >
                        Lihat Alur →
                    </a>

                </div>


            </div>

        </div>

    </section>



    {{-- =====================================================
         PENGUMUMAN
    ====================================================== --}}

    <section
        id="pengumuman"
        class="section section-light"
    >

        <div class="container">

            <div class="section-heading">

                <div class="label">
                    Informasi Terbaru
                </div>

                <h2>
                    Pengumuman
                </h2>

                <p>
                    Informasi terbaru mengenai kegiatan
                    dan penerimaan mahasiswa magang.
                </p>

            </div>


            <div class="announcement-grid">


                <div class="announcement-main">

                    <span class="announcement-badge">
                        PENGUMUMAN
                    </span>


                    <h3>
                        Informasi Pendaftaran Magang
                    </h3>


                    <p>

                        Silakan pastikan seluruh persyaratan
                        telah disiapkan sebelum melakukan
                        pengajuan magang melalui sistem MAGENTA.

                        <br><br>

                        Informasi mengenai jadwal,
                        ketentuan, dan proses seleksi
                        dapat diperbarui oleh administrator.

                    </p>

                </div>



                <div class="announcement-side">


                    <div class="announcement-item">

                        <h4>
                            Pendaftaran Magang
                        </h4>

                        <p>
                            Mahasiswa dapat melakukan
                            pengajuan melalui menu
                            Daftar Magang.
                        </p>

                    </div>


                    <div class="announcement-item">

                        <h4>
                            Periksa Status
                        </h4>

                        <p>
                            Setelah mengajukan,
                            gunakan menu Cek Status
                            untuk melihat perkembangan.
                        </p>

                    </div>


                    <div class="announcement-item">

                        <h4>
                            Informasi Perusahaan
                        </h4>

                        <p>
                            Informasi perusahaan dan
                            kontak tersedia pada bagian
                            Tentang dan Kontak.
                        </p>

                    </div>


                </div>

            </div>

        </div>

    </section>



    {{-- =====================================================
         TENTANG
    ====================================================== --}}

    <section
        id="tentang"
        class="section"
    >

        <div class="container">

            <div class="about-grid">


                <div class="about-content">

                    <div class="about-label">
                        Tentang MAGENTA
                    </div>


                    <h2>

                        {{ $pengaturan?->tentang_judul
                            ?? 'Sistem Informasi Magang Terintegrasi' }}

                    </h2>


                    <p>

                        {{ $pengaturan?->tentang_deskripsi
                            ?? 'MAGENTA merupakan sistem informasi yang dirancang untuk membantu proses pengajuan, pengelolaan, dan pemantauan kegiatan magang secara lebih terstruktur.' }}

                    </p>

                </div>



                <div class="about-cards">


                    <div class="about-card">

                        <h3>
                            {{ $pengaturan?->tentang_card1_judul ?? 'Pengajuan' }}
                        </h3>

                        <p>
                            {{ $pengaturan?->tentang_card1_deskripsi
                                ?? 'Mahasiswa dapat mengajukan kegiatan magang melalui sistem dengan mudah.' }}
                        </p>

                    </div>


                    <div class="about-card">

                        <h3>
                            {{ $pengaturan?->tentang_card2_judul ?? 'Pengelolaan' }}
                        </h3>

                        <p>
                            {{ $pengaturan?->tentang_card2_deskripsi
                                ?? 'Admin dapat mengelola proses pengajuan dan data mahasiswa.' }}
                        </p>

                    </div>


                    <div class="about-card">

                        <h3>
                            {{ $pengaturan?->tentang_card3_judul ?? 'Pemantauan' }}
                        </h3>

                        <p>
                            {{ $pengaturan?->tentang_card3_deskripsi
                                ?? 'Mahasiswa dapat memantau status pengajuan melalui sistem.' }}
                        </p>

                    </div>


                    <div class="about-card">

                        <h3>
                            Informasi
                        </h3>

                        <p>
                            Informasi dan pengumuman
                            dapat ditampilkan pada
                            halaman utama MAGENTA.
                        </p>

                    </div>


                </div>

            </div>

        </div>

    </section>



    {{-- =====================================================
         PROFIL PERUSAHAAN
    ====================================================== --}}

    <section
        id="kontak"
        class="section section-light"
    >

        <div class="container">


            @if(isset($perusahaan) && $perusahaan)


                <div class="company-box">


                    <div>

                        <div
                            style="
                                color:#f47b20;
                                font-size:13px;
                                font-weight:800;
                                margin-bottom:10px;
                                text-transform:uppercase;
                            "
                        >
                            Tentang Perusahaan
                        </div>


                        <h2>
                            {{ $perusahaan->nama ?: 'Perusahaan' }}
                        </h2>


                        @if($perusahaan->deskripsi)

                            <p>
                                {{ $perusahaan->deskripsi }}
                            </p>

                        @else

                            <p>
                                Informasi profil perusahaan
                                dapat ditambahkan melalui
                                menu Profil Perusahaan
                                pada halaman Super Admin.
                            </p>

                        @endif

                    </div>



                    <div class="company-contact">


                        @if($perusahaan->alamat)

                            <div class="company-contact-item">

                                📍

                                {{ $perusahaan->alamat }}

                                @if($perusahaan->kota)
                                    , {{ $perusahaan->kota }}
                                @endif

                            </div>

                        @endif



                        @if($perusahaan->email)

                            <div class="company-contact-item">

                                ✉️

                                {{ $perusahaan->email }}

                            </div>

                        @endif



                        @if($perusahaan->telepon)

                            <div class="company-contact-item">

                                ☎️

                                {{ $perusahaan->telepon }}

                            </div>

                        @endif



                        @if($perusahaan->website)

                            <div class="company-contact-item">

                                🌐

                                {{ $perusahaan->website }}

                            </div>

                        @endif


                    </div>

                </div>


            @else


                <div class="company-box">

                    <div>

                        <h2>
                            Profil Perusahaan
                        </h2>

                        <p>
                            Informasi perusahaan belum
                            tersedia.
                        </p>

                    </div>

                </div>


            @endif

        </div>

    </section>



    {{-- =====================================================
         FOOTER
    ====================================================== --}}

    <footer>

        <div class="footer-container">


            <div>

                <div class="footer-logo">
                    MAGENTA
                </div>

                <div class="footer-text">

                    Sistem Informasi Magang Terintegrasi

                </div>

            </div>


            <div class="footer-text">

                © 2026

                @if(isset($perusahaan) && $perusahaan && $perusahaan->nama)

                    {{ $perusahaan->nama }}

                @else

                    MAGENTA

                @endif

                . All rights reserved.

            </div>


        </div>

    </footer>


</body>

</html>