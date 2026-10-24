{{-- resources/views/website/index.blade.php --}}

@php

    /*
    |--------------------------------------------------------------------------
    | LINK GOOGLE FORM
    |--------------------------------------------------------------------------
    |
    | Mengambil link Google Form dari database melalui
    | menu Kelola Konten.
    |
    | Jika database belum memiliki link, maka menggunakan
    | placeholder sementara.
    |
    */

    $googleFormUrl =
        $pengaturan?->google_form_url
        ?: 'ISI_LINK_GOOGLE_FORM_DI_SINI';


    /*
    |--------------------------------------------------------------------------
    | DATA BANNER
    |--------------------------------------------------------------------------
    */

    $banner = isset($banners) && $banners->isNotEmpty()
        ? $banners->first()
        : null;


    /*
    |--------------------------------------------------------------------------
    | DATA PERUSAHAAN
    |--------------------------------------------------------------------------
    */

    $namaPerusahaan = $perusahaan?->nama
        ?: $perusahaan?->nama_perusahaan
        ?: 'MAGENTA';


    /*
    |--------------------------------------------------------------------------
    | DATA PENGATURAN WEBSITE
    |--------------------------------------------------------------------------
    */

    $informasiTerbaruJudul =
        $pengaturan?->informasi_terbaru_judul
        ?? 'Informasi Terbaru';

    $informasiTerbaruDeskripsi =
        $pengaturan?->informasi_terbaru_deskripsi
        ?? 'Informasi terbaru mengenai kegiatan dan penerimaan mahasiswa magang.';


    /*
    |--------------------------------------------------------------------------
    | PENGUMUMAN
    |--------------------------------------------------------------------------
    */

    $pengumumanJudul =
        $pengaturan?->pengumuman_judul
        ?? 'Pengumuman';

    $pengumumanDeskripsi =
        $pengaturan?->pengumuman_deskripsi
        ?? 'Informasi terbaru mengenai kegiatan dan penerimaan mahasiswa magang.';


    $pengumumanCard1Judul =
        $pengaturan?->pengumuman_card1_judul
        ?? 'Persiapkan Dokumen';

    $pengumumanCard1Deskripsi =
        $pengaturan?->pengumuman_card1_deskripsi
        ?? 'Pastikan dokumen persyaratan telah disiapkan sebelum melakukan pendaftaran.';


    $pengumumanCard2Judul =
        $pengaturan?->pengumuman_card2_judul
        ?? 'Pendaftaran Online';

    $pengumumanCard2Deskripsi =
        $pengaturan?->pengumuman_card2_deskripsi
        ?? 'Pendaftaran dilakukan melalui Google Form yang telah disediakan.';


    $pengumumanCard3Judul =
        $pengaturan?->pengumuman_card3_judul
        ?? 'Informasi Perusahaan';

    $pengumumanCard3Deskripsi =
        $pengaturan?->pengumuman_card3_deskripsi
        ?? 'Informasi perusahaan dan kontak tersedia pada bagian Kontak.';


    /*
    |--------------------------------------------------------------------------
    | PERSYARATAN
    |--------------------------------------------------------------------------
    */

    $persyaratanJudul =
        $pengaturan?->persyaratan_judul
        ?? 'Persyaratan Magang';

    $persyaratanDeskripsi =
        $pengaturan?->persyaratan_deskripsi
        ?? 'Pastikan seluruh kebutuhan administrasi telah disiapkan sebelum mengisi Google Form.';


    $persyaratan1Judul =
        $pengaturan?->persyaratan1_judul
        ?? 'Surat Pengantar';

    $persyaratan1Deskripsi =
        $pengaturan?->persyaratan1_deskripsi
        ?? 'Siapkan surat pengantar atau surat permohonan magang dari kampus sesuai ketentuan yang berlaku.';


    $persyaratan2Judul =
        $pengaturan?->persyaratan2_judul
        ?? 'Data Mahasiswa';

    $persyaratan2Deskripsi =
        $pengaturan?->persyaratan2_deskripsi
        ?? 'Siapkan data diri dan informasi akademik yang diperlukan pada saat mengisi formulir pendaftaran.';


    $persyaratan3Judul =
        $pengaturan?->persyaratan3_judul
        ?? 'Dokumen Pendukung';

    $persyaratan3Deskripsi =
        $pengaturan?->persyaratan3_deskripsi
        ?? 'Siapkan dokumen pendukung lain sesuai dengan persyaratan yang ditentukan oleh perusahaan.';


    /*
    |--------------------------------------------------------------------------
    | ALUR MAGANG
    |--------------------------------------------------------------------------
    */

    $alurJudul =
        $pengaturan?->alur_judul
        ?? 'Alur Magang';

    $alurDeskripsi =
        $pengaturan?->alur_deskripsi
        ?? 'Ikuti tahapan berikut agar proses pendaftaran magang berjalan dengan baik.';


    $alur1Judul =
        $pengaturan?->alur1_judul
        ?? 'Persiapkan';

    $alur1Deskripsi =
        $pengaturan?->alur1_deskripsi
        ?? 'Siapkan data dan dokumen yang diperlukan.';


    $alur2Judul =
        $pengaturan?->alur2_judul
        ?? 'Daftar';

    $alur2Deskripsi =
        $pengaturan?->alur2_deskripsi
        ?? 'Isi Google Form pendaftaran magang secara lengkap.';


    $alur3Judul =
        $pengaturan?->alur3_judul
        ?? 'Verifikasi';

    $alur3Deskripsi =
        $pengaturan?->alur3_deskripsi
        ?? 'Data pendaftaran akan diproses dan diverifikasi oleh pihak terkait.';


    $alur4Judul =
        $pengaturan?->alur4_judul
        ?? 'Pelaksanaan';

    $alur4Deskripsi =
        $pengaturan?->alur4_deskripsi
        ?? 'Mahasiswa melaksanakan kegiatan magang sesuai ketentuan perusahaan.';


    /*
    |--------------------------------------------------------------------------
    | URL WEBSITE PERUSAHAAN
    |--------------------------------------------------------------------------
    */

    $websiteUrl = null;

    if ($perusahaan?->website) {

        $websiteUrl = $perusahaan->website;

        if (
            !str_starts_with($websiteUrl, 'http://') &&
            !str_starts_with($websiteUrl, 'https://')
        ) {
            $websiteUrl = 'https://' . $websiteUrl;
        }
    }

@endphp


<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="Sistem Informasi Magang {{ $namaPerusahaan }}"
    >

    <title>
        {{ $namaPerusahaan }} - Sistem Informasi Magang
    </title>


    <style>

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
            color: inherit;
        }


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


        .logo {

            display: flex;
            align-items: center;

            gap: 10px;

            color: var(--primary);

            font-size: 27px;
            font-weight: 800;

            letter-spacing: -0.5px;

            flex-shrink: 0;

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

            transition: 0.2s ease;

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

            transition: 0.2s ease;

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


        /* =====================================================
           HERO IMAGE
        ====================================================== */

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

            letter-spacing: 1px;

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
            max-width: 1050px;

            margin:
                -30px auto 0;

            position: relative;

            z-index: 10;

        }


        .quick-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

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

            transition: 0.2s ease;

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

            line-height: 1.7;

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
                80px 0;

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

            line-height: 1.7;

        }


        /* =====================================================
           PERSYARATAN
        ====================================================== */

        .requirements-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 20px;

        }


        .requirement-card {

            background: white;

            border:
                1px solid var(--border);

            border-radius: 14px;

            padding: 28px;

            box-shadow: var(--shadow);

        }


        .requirement-number {

            width: 42px;
            height: 42px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: #eef8fc;

            color: var(--primary);

            font-weight: 800;

            margin-bottom: 16px;

        }


        .requirement-card h3 {

            color: #20343d;

            font-size: 18px;

            margin-bottom: 8px;

        }


        .requirement-card p {

            color: var(--muted);

            font-size: 14px;

            line-height: 1.7;

        }


        /* =====================================================
           ALUR MAGANG
        ====================================================== */

        .flow-grid {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 20px;

            position: relative;

        }


        .flow-card {

            background: white;

            border:
                1px solid var(--border);

            border-radius: 14px;

            padding: 28px 22px;

            text-align: center;

            box-shadow: var(--shadow);

            position: relative;

        }


        .flow-number {

            width: 48px;
            height: 48px;

            margin:
                0 auto 15px;

            border-radius: 50%;

            background: var(--primary);

            color: white;

            display: flex;

            align-items: center;
            justify-content: center;

            font-weight: 800;

            font-size: 18px;

        }


        .flow-card h3 {

            color: #20343d;

            font-size: 17px;

            margin-bottom: 8px;

        }


        .flow-card p {

            color: var(--muted);

            font-size: 13px;

            line-height: 1.7;

        }


        /* =====================================================
           CTA PENDAFTARAN
        ====================================================== */

        .registration-box {

            background:
                linear-gradient(
                    135deg,
                    #0878a8,
                    #075d83
                );

            border-radius: 20px;

            padding: 45px;

            color: white;

            text-align: center;

            box-shadow:
                0 20px 45px
                rgba(8, 120, 168, 0.16);

        }


        .registration-box h2 {

            font-size: 32px;

            margin-bottom: 12px;

        }


        .registration-box p {

            max-width: 650px;

            margin:
                0 auto 25px;

            color:
                rgba(255,255,255,0.85);

            font-size: 15px;

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

            word-break: break-word;

        }


        .company-contact-item a {
            color: inherit;
        }


        .company-contact-item a:hover {
            color: #ffffff;
            text-decoration: underline;
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
                    repeat(3, 1fr);

            }


            .announcement-grid {
                grid-template-columns: 1fr;
            }


            .company-box {
                grid-template-columns: 1fr;
            }


            .requirements-grid {

                grid-template-columns:
                    repeat(2, 1fr);

            }


            .flow-grid {

                grid-template-columns:
                    repeat(2, 1fr);

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


            .requirements-grid {
                grid-template-columns: 1fr;
            }


            .flow-grid {
                grid-template-columns: 1fr;
            }


            .section-heading h2 {
                font-size: 29px;
            }


            .company-box {

                padding:
                    30px 25px;

            }


            .company-box h2 {
                font-size: 27px;
            }


            .registration-box {

                padding:
                    35px 25px;

            }


            .registration-box h2 {
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

                    <a href="#pengumuman">
                        Pengumuman
                    </a>

                </li>


                <li>

                    <a href="#persyaratan">
                        Persyaratan
                    </a>

                </li>


                <li>

                    <a href="#alur">
                        Alur
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
    ====================================================== --}}

    <section class="hero">

        <div class="hero-container">


            <div class="hero-content">

                <div class="hero-label">
                    SISTEM INFORMASI MAGANG
                </div>


                <h1>

                    @if($banner?->judul)

                        {{ $banner->judul }}

                    @else

                        Selamat Datang di

                        <span>
                            MAGENTA
                        </span>

                    @endif

                </h1>


                <p>

                    @if($banner?->deskripsi)

                        {{ $banner->deskripsi }}

                    @elseif($perusahaan?->nama)

                        Sistem informasi magang
                        {{ $perusahaan->nama }}
                        untuk membantu mahasiswa
                        memperoleh informasi dan
                        melakukan pendaftaran magang.

                    @else

                        Sistem informasi magang
                        untuk membantu mahasiswa
                        memperoleh informasi dan
                        melakukan pendaftaran magang.

                    @endif

                </p>


                <div class="hero-buttons">


                    <a
                        href="{{ $googleFormUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn btn-orange"
                    >
                        Daftar Magang
                    </a>


                    <a
                        href="#persyaratan"
                        class="btn btn-primary"
                    >
                        Lihat Persyaratan
                    </a>


                </div>

            </div>



            <div class="hero-image-box">

                @if($banner?->gambar)

                    <img
                        src="{{ asset('storage/' . $banner->gambar) }}"
                        alt="{{ $banner->judul ?: 'Banner MAGENTA' }}"
                        class="hero-image"
                    >

                @else

                    <div class="hero-placeholder">
                        MAGENTA
                    </div>

                @endif

            </div>


        </div>

    </section>



    {{-- =====================================================
         QUICK MENU
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
                        {{ $persyaratanJudul }}
                    </h3>

                    <p>
                        {{ $persyaratanDeskripsi }}
                    </p>

                    <a
                        href="#persyaratan"
                        class="quick-link"
                    >
                        Lihat Persyaratan →
                    </a>

                </div>



                {{-- PENDAFTARAN --}}

                <div class="quick-card">

                    <div class="quick-icon">
                        📝
                    </div>

                    <h3>
                        Pendaftaran Magang
                    </h3>

                    <p>
                        Pendaftaran magang dilakukan
                        melalui Google Form resmi.
                    </p>

                    <a
                        href="{{ $googleFormUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="quick-link"
                    >
                        Daftar Sekarang →
                    </a>

                </div>



                {{-- ALUR --}}

                <div class="quick-card">

                    <div class="quick-icon">
                        🔄
                    </div>

                    <h3>
                        {{ $alurJudul }}
                    </h3>

                    <p>
                        {{ $alurDeskripsi }}
                    </p>

                    <a
                        href="#alur"
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
                    {{ $informasiTerbaruJudul }}
                </div>

                <h2>
                    {{ $pengumumanJudul }}
                </h2>

                <p>
                    {{ $informasiTerbaruDeskripsi }}
                </p>

            </div>


            <div class="announcement-grid">


                {{-- PENGUMUMAN UTAMA --}}

                <div class="announcement-main">

                    <span class="announcement-badge">
                        PENGUMUMAN
                    </span>


                    <h3>
                        {{ $pengumumanJudul }}
                    </h3>


                    <p>
                        {{ $pengumumanDeskripsi }}
                    </p>

                </div>



                {{-- INFORMASI TAMBAHAN --}}

                <div class="announcement-side">


                    <div class="announcement-item">

                        <h4>
                            {{ $pengumumanCard1Judul }}
                        </h4>

                        <p>
                            {{ $pengumumanCard1Deskripsi }}
                        </p>

                    </div>


                    <div class="announcement-item">

                        <h4>
                            {{ $pengumumanCard2Judul }}
                        </h4>

                        <p>
                            {{ $pengumumanCard2Deskripsi }}
                        </p>

                    </div>


                    <div class="announcement-item">

                        <h4>
                            {{ $pengumumanCard3Judul }}
                        </h4>

                        <p>
                            {{ $pengumumanCard3Deskripsi }}
                        </p>

                    </div>


                </div>

            </div>

        </div>

    </section>



    {{-- =====================================================
         PERSYARATAN MAGANG
    ====================================================== --}}

    <section
        id="persyaratan"
        class="section"
    >

        <div class="container">


            <div class="section-heading">

                <div class="label">
                    Sebelum Mendaftar
                </div>

                <h2>
                    {{ $persyaratanJudul }}
                </h2>

                <p>
                    {{ $persyaratanDeskripsi }}
                </p>

            </div>


            <div class="requirements-grid">


                <div class="requirement-card">

                    <div class="requirement-number">
                        01
                    </div>

                    <h3>
                        {{ $persyaratan1Judul }}
                    </h3>

                    <p>
                        {{ $persyaratan1Deskripsi }}
                    </p>

                </div>



                <div class="requirement-card">

                    <div class="requirement-number">
                        02
                    </div>

                    <h3>
                        {{ $persyaratan2Judul }}
                    </h3>

                    <p>
                        {{ $persyaratan2Deskripsi }}
                    </p>

                </div>



                <div class="requirement-card">

                    <div class="requirement-number">
                        03
                    </div>

                    <h3>
                        {{ $persyaratan3Judul }}
                    </h3>

                    <p>
                        {{ $persyaratan3Deskripsi }}
                    </p>

                </div>


            </div>

        </div>

    </section>



    {{-- =====================================================
         ALUR MAGANG
    ====================================================== --}}

    <section
        id="alur"
        class="section section-light"
    >

        <div class="container">


            <div class="section-heading">

                <div class="label">
                    Proses Magang
                </div>

                <h2>
                    {{ $alurJudul }}
                </h2>

                <p>
                    {{ $alurDeskripsi }}
                </p>

            </div>


            <div class="flow-grid">


                <div class="flow-card">

                    <div class="flow-number">
                        1
                    </div>

                    <h3>
                        {{ $alur1Judul }}
                    </h3>

                    <p>
                        {{ $alur1Deskripsi }}
                    </p>

                </div>



                <div class="flow-card">

                    <div class="flow-number">
                        2
                    </div>

                    <h3>
                        {{ $alur2Judul }}
                    </h3>

                    <p>
                        {{ $alur2Deskripsi }}
                    </p>

                </div>



                <div class="flow-card">

                    <div class="flow-number">
                        3
                    </div>

                    <h3>
                        {{ $alur3Judul }}
                    </h3>

                    <p>
                        {{ $alur3Deskripsi }}
                    </p>

                </div>



                <div class="flow-card">

                    <div class="flow-number">
                        4
                    </div>

                    <h3>
                        {{ $alur4Judul }}
                    </h3>

                    <p>
                        {{ $alur4Deskripsi }}
                    </p>

                </div>


            </div>

        </div>

    </section>



    {{-- =====================================================
         CTA PENDAFTARAN
    ====================================================== --}}

    <section class="section section-light">

        <div class="container">

            <div class="registration-box">

                <h2>
                    Siap Mendaftar Magang?
                </h2>


                <p>
                    Pastikan seluruh persyaratan telah
                    disiapkan, kemudian lakukan pendaftaran
                    melalui Google Form resmi.
                </p>


                <a
                    href="{{ $googleFormUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn btn-orange"
                >
                    Buka Form Pendaftaran
                </a>

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


            @if($perusahaan)

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
                            {{ $perusahaan->nama ?: $perusahaan->nama_perusahaan ?: 'Perusahaan' }}
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
                                pada halaman administrasi.
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

                                <a
                                    href="{{ $websiteUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    {{ $perusahaan->website }}
                                </a>

                            </div>

                        @endif


                    </div>

                </div>


            @else


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
                            Informasi Perusahaan
                        </div>

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
                    Sistem Informasi Magang
                </div>

            </div>


            <div class="footer-text">

                © {{ date('Y') }}

                {{ $namaPerusahaan }}

                . All rights reserved.

            </div>


        </div>

    </footer>


</body>

</html>