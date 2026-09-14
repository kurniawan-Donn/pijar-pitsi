<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Kelola Konten - MAGENTA
    </title>


    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        body {
            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f4f7fb;

            color: #173f70;
        }


        a {
            text-decoration: none;
        }


        /* =====================================================
           LAYOUT
        ===================================================== */

        .layout {
            display: flex;

            min-height: 100vh;
        }


        /* =====================================================
           SIDEBAR
        ===================================================== */

        .sidebar {
            width: 275px;

            min-height: 100vh;

            background: #12345b;

            color: #ffffff;

            padding: 24px 18px 20px;

            display: flex;

            flex-direction: column;

            position: fixed;

            left: 0;

            top: 0;

            bottom: 0;

            overflow-y: auto;
        }


        /* =====================================================
           BRAND
        ===================================================== */

        .brand {
            display: flex;

            align-items: center;

            gap: 13px;

            padding:
                0 8px 22px;

            border-bottom:
                1px solid
                rgba(255, 255, 255, .18);
        }


        .brand-logo {
            width: 46px;

            height: 46px;

            min-width: 46px;

            border-radius: 10px;

            background: #ffffff;

            color: #1779c9;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 21px;

            font-weight: 800;
        }


        .brand-title {
            font-size: 22px;

            line-height: 1.1;

            font-weight: 800;

            color: #ffffff;
        }


        .brand-subtitle {
            margin-top: 5px;

            font-size: 10px;

            line-height: 1.4;

            letter-spacing: .5px;

            color:
                rgba(255, 255, 255, .78);
        }


        /* =====================================================
           MENU
        ===================================================== */

        .menu-area {
            margin-top: 24px;
        }


        .menu-title {
            font-size: 11px;

            font-weight: 700;

            letter-spacing: 1px;

            color:
                rgba(255, 255, 255, .58);

            margin:
                0 10px 10px;
        }


        .menu-list {
            display: flex;

            flex-direction: column;

            gap: 4px;
        }


        .menu-item {
            min-height: 44px;

            padding:
                0 13px;

            border-radius: 9px;

            display: flex;

            align-items: center;

            gap: 12px;

            color: #ffffff;

            font-size: 14px;

            transition:
                background .2s ease,
                transform .2s ease;
        }


        .menu-item:hover {
            background:
                rgba(255, 255, 255, .08);
        }


        .menu-item.active {
            background: #1779c9;

            color: #ffffff;

            font-weight: 700;
        }


        .menu-icon {
            width: 20px;

            min-width: 20px;

            text-align: center;

            font-size: 16px;
        }


        /* =====================================================
           SIDEBAR DIVIDER
        ===================================================== */

        .sidebar-divider {
            height: 1px;

            background:
                rgba(255, 255, 255, .18);

            margin:
                auto 0 18px;
        }


        /* =====================================================
           LOGOUT
        ===================================================== */

        .logout-btn {
            width: 100%;

            min-height: 44px;

            border: 0;

            border-radius: 8px;

            background:
                rgba(255, 255, 255, .10);

            color: #ffffff;

            font-size: 13px;

            font-weight: 600;

            cursor: pointer;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 10px;

            transition:
                background .2s ease;
        }


        .logout-btn:hover {
            background:
                rgba(255, 255, 255, .16);
        }


        /* =====================================================
           MAIN
        ===================================================== */

        .main {
            width:
                calc(100% - 275px);

            margin-left: 275px;

            min-height: 100vh;
        }


        /* =====================================================
           TOPBAR
        ===================================================== */

        .topbar {
            height: 64px;

            background: #ffffff;

            border-bottom:
                1px solid #e5eaf1;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding:
                0 32px;
        }


        .page-title {
            font-size: 20px;

            font-weight: 800;

            color: #173f70;
        }


        .page-subtitle {
            margin-top: 3px;

            font-size: 12px;

            color: #8193a9;
        }


        /* =====================================================
           ADMIN PROFILE
        ===================================================== */

        .admin-profile {
            display: flex;

            align-items: center;

            gap: 10px;
        }


        .admin-info {
            text-align: right;
        }


        .admin-name {
            font-size: 13px;

            font-weight: 700;

            color: #173f70;
        }


        .admin-role {
            margin-top: 2px;

            font-size: 11px;

            color: #8b9caf;
        }


        .admin-avatar {
            width: 36px;

            height: 36px;

            border-radius: 50%;

            background: #eaf3fc;

            color: #1779c9;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 14px;

            font-weight: 800;
        }


        /* =====================================================
           CONTENT
        ===================================================== */

        .content {
            padding:
                28px 32px 50px;
        }


        /* =====================================================
           INTRO
        ===================================================== */

        .intro-card {
            background: #ffffff;

            border-left:
                4px solid #1779c9;

            border-radius: 12px;

            padding:
                22px 24px;

            margin-bottom: 20px;

            box-shadow:
                0 3px 14px
                rgba(24, 61, 105, .05);
        }


        .intro-line {
            width: 40px;

            height: 3px;

            background: #f47b20;

            margin-bottom: 12px;
        }


        .intro-title {
            font-size: 22px;

            font-weight: 800;

            color: #245b9d;

            margin-bottom: 7px;
        }


        .intro-description {
            font-size: 13px;

            line-height: 1.7;

            color: #71849c;
        }


        /* =====================================================
           ALERT
        ===================================================== */

        .alert-success {
            background: #ecfdf3;

            border:
                1px solid #b8efcc;

            color: #18753d;

            border-radius: 9px;

            padding:
                12px 16px;

            margin-bottom: 18px;

            font-size: 13px;
        }


        .alert-error {
            background: #fff1f1;

            border:
                1px solid #f2c3c3;

            color: #b42318;

            border-radius: 9px;

            padding:
                12px 16px;

            margin-bottom: 18px;

            font-size: 13px;
        }


        .alert-error ul {
            margin-top: 7px;

            margin-left: 18px;
        }


        .alert-error li {
            margin-bottom: 3px;
        }


        /* =====================================================
           SECTION CARD
        ===================================================== */

        .section-card {
            background: #ffffff;

            border-radius: 12px;

            margin-bottom: 20px;

            box-shadow:
                0 3px 15px
                rgba(24, 61, 105, .055);

            overflow: hidden;
        }


        /* =====================================================
           SECTION HEADER
        ===================================================== */

        .section-header {
            padding:
                18px 22px;

            border-bottom:
                1px solid #e8edf4;

            display: flex;

            align-items: center;

            gap: 13px;
        }


        .section-number {
            width: 40px;

            height: 40px;

            min-width: 40px;

            border-radius: 9px;

            background: #edf5ff;

            color: #2470b8;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 16px;

            font-weight: 800;
        }


        .section-title {
            font-size: 17px;

            font-weight: 800;

            color: #173f70;
        }


        .section-description {
            margin-top: 4px;

            font-size: 12px;

            color: #8092a9;
        }


        /* =====================================================
           SECTION BODY
        ===================================================== */

        .section-body {
            padding:
                22px;
        }


        /* =====================================================
           FIELD
        ===================================================== */

        .field {
            margin-bottom: 17px;
        }


        .field:last-child {
            margin-bottom: 0;
        }


        .field label {
            display: block;

            margin-bottom: 7px;

            font-size: 13px;

            font-weight: 700;

            color: #173f70;
        }


        .required {
            color: #ef4f42;
        }


        input,
        textarea {
            width: 100%;

            border:
                1px solid #d6dfeb;

            border-radius: 8px;

            background: #ffffff;

            color: #284765;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            font-size: 13px;

            outline: none;

            transition:
                border-color .2s ease,
                box-shadow .2s ease;
        }


        input {
            height: 43px;

            padding:
                0 13px;
        }


        textarea {
            min-height: 105px;

            padding:
                11px 13px;

            resize: vertical;

            line-height: 1.65;
        }


        input::placeholder,
        textarea::placeholder {
            color: #a1afbf;
        }


        input:focus,
        textarea:focus {
            border-color: #3579c6;

            box-shadow:
                0 0 0 3px
                rgba(53, 121, 198, .09);
        }


        .field-help {
            margin-top: 5px;

            font-size: 11px;

            color: #8498b0;
        }


        /* =====================================================
           SUB CARD
        ===================================================== */

        .sub-card {
            background: #f8fafc;

            border:
                1px solid #e4eaf2;

            border-radius: 10px;

            padding:
                17px;

            margin-top: 16px;
        }


        .sub-card:first-child {
            margin-top: 0;
        }


        .sub-card-title {
            display: flex;

            align-items: center;

            font-size: 13px;

            font-weight: 800;

            color: #23568f;

            margin-bottom: 14px;
        }


        /* =====================================================
           GRID
        ===================================================== */

        .grid-2 {
            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 18px;
        }


        /* =====================================================
           STEP NUMBER
        ===================================================== */

        .step-number {
            display: inline-flex;

            width: 28px;

            height: 28px;

            min-width: 28px;

            border-radius: 50%;

            background: #177eb7;

            color: #ffffff;

            align-items: center;

            justify-content: center;

            margin-right: 8px;

            font-size: 12px;

            font-weight: 800;
        }


        /* =====================================================
           SAVE AREA
        ===================================================== */

        .save-area {
            display: flex;

            justify-content: flex-end;

            padding-top: 2px;
        }


        .save-btn {
            min-width: 210px;

            height: 46px;

            border: 0;

            border-radius: 8px;

            background: #f47b20;

            color: #ffffff;

            font-size: 13px;

            font-weight: 700;

            cursor: pointer;

            transition:
                background .2s ease,
                transform .2s ease;
        }


        .save-btn:hover {
            background: #e96c12;

            transform: translateY(-1px);
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 1050px) {

            .sidebar {
                width: 245px;
            }


            .main {
                width:
                    calc(100% - 245px);

                margin-left: 245px;
            }


            .topbar {
                padding:
                    0 24px;
            }


            .content {
                padding:
                    24px;
            }

        }


        @media (max-width: 850px) {

            .grid-2 {
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

                min-height: auto;

                padding:
                    20px 16px;
            }


            .main {
                width: 100%;

                margin-left: 0;
            }


            .topbar {
                height: auto;

                min-height: 64px;

                padding:
                    17px 20px;

                align-items: flex-start;
            }


            .admin-profile {
                display: none;
            }


            .content {
                padding:
                    20px 16px 40px;
            }


            .intro-card {
                padding:
                    20px;
            }


            .intro-title {
                font-size: 20px;
            }


            .section-header {
                padding:
                    17px;
            }


            .section-body {
                padding:
                    17px;
            }


            .sub-card {
                padding:
                    15px;
            }


            .save-area {
                justify-content: stretch;
            }


            .save-btn {
                width: 100%;
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


            <div>

                <div class="brand-title">
                    MAGENTA
                </div>

                <div class="brand-subtitle">
                    SISTEM INFORMASI MAGANG
                </div>

            </div>

        </div>


        {{-- =================================================
             UTAMA
        ================================================== --}}

        <div class="menu-area">

            <div class="menu-title">
                UTAMA
            </div>


            <div class="menu-list">

                {{-- DASHBOARD MENYESUAIKAN ROLE --}}

                <a
                    href="{{ auth()->user()->role === 'super_admin'
                        ? route('superadmin.dashboard')
                        : route('admin.dashboard') }}"
                    class="menu-item"
                >

                    <span class="menu-icon">
                        ▦
                    </span>

                    <span>
                        Dashboard
                    </span>

                </a>

            </div>

        </div>


        {{-- =================================================
             SISTEM
        ================================================== --}}

        <div class="menu-area">

            <div class="menu-title">
                SISTEM
            </div>


            <div class="menu-list">


                {{-- PROFIL PERUSAHAAN --}}

                <a
                    href="{{ route('superadmin.perusahaan.index') }}"
                    class="menu-item"
                >

                    <span class="menu-icon">
                        ▰
                    </span>

                    <span>
                        Profil Perusahaan
                    </span>

                </a>


                {{-- KELOLA BANNER --}}

                <a
                    href="{{ route('superadmin.banners.index') }}"
                    class="menu-item"
                >

                    <span class="menu-icon">
                        ▧
                    </span>

                    <span>
                        Kelola Banner
                    </span>

                </a>


                {{-- KELOLA KONTEN --}}

                <a
                    href="{{ route('superadmin.pengaturan.index') }}"
                    class="menu-item active"
                >

                    <span class="menu-icon">
                        ⚙
                    </span>

                    <span>
                        Kelola Konten
                    </span>

                </a>


                {{-- KELOLA PENGGUNA
                     HANYA SUPER ADMIN --}}

                @if(auth()->user()->role === 'super_admin')

                    <a
                        href="{{ route('superadmin.pengguna.index') }}"
                        class="menu-item"
                    >

                        <span class="menu-icon">
                            ◉
                        </span>

                        <span>
                            Kelola Pengguna
                        </span>

                    </a>

                @endif


            </div>

        </div>


        <div class="sidebar-divider"></div>


        {{-- =================================================
             LOGOUT
        ================================================== --}}

        <form
            action="{{ route('logout') }}"
            method="POST"
        >

            @csrf


            <button
                type="submit"
                class="logout-btn"
            >

                <span>
                    ↪
                </span>

                <span>
                    Logout
                </span>

            </button>

        </form>


    </aside>



    {{-- =====================================================
         MAIN
    ====================================================== --}}

    <main class="main">


        {{-- =================================================
             TOPBAR
        ================================================== --}}

        <header class="topbar">


            <div>

                <div class="page-title">
                    Kelola Konten
                </div>

                <div class="page-subtitle">
                    Edit seluruh konten yang tampil pada halaman utama MAGENTA
                </div>

            </div>


            <div class="admin-profile">

                <div class="admin-info">

                    <div class="admin-name">
                        {{ auth()->user()->name }}
                    </div>


                    <div class="admin-role">

                        {{ auth()->user()->role === 'super_admin'
                            ? 'Super Admin'
                            : 'Admin' }}

                    </div>

                </div>


                <div class="admin-avatar">

                    {{ strtoupper(
                        substr(
                            auth()->user()->name,
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

        <section class="content">


            {{-- =================================================
                 INTRO
            ================================================== --}}

            <div class="intro-card">

                <div class="intro-line"></div>


                <div class="intro-title">
                    Edit Konten Website
                </div>


                <div class="intro-description">

                    Semua teks pada halaman utama dapat diedit
                    dari halaman ini. Ubah isi yang diperlukan,
                    kemudian klik <strong>Simpan Semua Perubahan</strong>.

                </div>

            </div>



            {{-- =================================================
                 SUCCESS
            ================================================== --}}

            @if(session('success'))

                <div class="alert-success">

                    {{ session('success') }}

                </div>

            @endif



            {{-- =================================================
                 ERROR
            ================================================== --}}

            @if($errors->any())

                <div class="alert-error">

                    <strong>
                        Ada data yang belum benar.
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
                 FORM UTAMA
            ================================================== --}}

            <form
                action="{{ route('superadmin.pengaturan.update') }}"
                method="POST"
            >

                @csrf

                @method('PUT')



                {{-- =================================================
                     1. INFORMASI TERBARU
                ================================================== --}}

                <div class="section-card">


                    <div class="section-header">

                        <div class="section-number">
                            1
                        </div>


                        <div>

                            <div class="section-title">
                                Informasi Terbaru
                            </div>

                            <div class="section-description">
                                Edit bagian informasi terbaru pada halaman utama.
                            </div>

                        </div>

                    </div>


                    <div class="section-body">


                        <div class="field">

                            <label for="informasi_terbaru_judul">

                                Judul

                                <span class="required">
                                    *
                                </span>

                            </label>


                            <input
                                type="text"
                                id="informasi_terbaru_judul"
                                name="informasi_terbaru_judul"
                                value="{{ old(
                                    'informasi_terbaru_judul',
                                    $pengaturan?->informasi_terbaru_judul
                                    ?? 'Informasi Terbaru'
                                ) }}"
                                required
                            >

                        </div>


                        <div class="field">

                            <label for="informasi_terbaru_deskripsi">
                                Deskripsi
                            </label>


                            <textarea
                                id="informasi_terbaru_deskripsi"
                                name="informasi_terbaru_deskripsi"
                                placeholder="Masukkan informasi terbaru..."
                            >{{ old(
                                'informasi_terbaru_deskripsi',
                                $pengaturan?->informasi_terbaru_deskripsi
                                ?? ''
                            ) }}</textarea>

                        </div>


                    </div>

                </div>



                {{-- =================================================
                     2. PENGUMUMAN
                ================================================== --}}

                <div class="section-card">


                    <div class="section-header">

                        <div class="section-number">
                            2
                        </div>


                        <div>

                            <div class="section-title">
                                Pengumuman
                            </div>

                            <div class="section-description">
                                Edit pengumuman dan tiga kartu informasi.
                            </div>

                        </div>

                    </div>


                    <div class="section-body">


                        <div class="field">

                            <label for="pengumuman_judul">

                                Judul Pengumuman

                                <span class="required">
                                    *
                                </span>

                            </label>


                            <input
                                type="text"
                                id="pengumuman_judul"
                                name="pengumuman_judul"
                                value="{{ old(
                                    'pengumuman_judul',
                                    $pengaturan?->pengumuman_judul
                                    ?? 'Pengumuman'
                                ) }}"
                                required
                            >

                        </div>


                        <div class="field">

                            <label for="pengumuman_deskripsi">
                                Isi Pengumuman
                            </label>


                            <textarea
                                id="pengumuman_deskripsi"
                                name="pengumuman_deskripsi"
                                placeholder="Masukkan isi pengumuman..."
                            >{{ old(
                                'pengumuman_deskripsi',
                                $pengaturan?->pengumuman_deskripsi
                                ?? ''
                            ) }}</textarea>

                        </div>



                        {{-- CARD 1 --}}

                        <div class="sub-card">

                            <div class="sub-card-title">
                                Kartu Informasi 1
                            </div>


                            <div class="grid-2">


                                <div class="field">

                                    <label for="pengumuman_card1_judul">
                                        Judul
                                    </label>


                                    <input
                                        type="text"
                                        id="pengumuman_card1_judul"
                                        name="pengumuman_card1_judul"
                                        value="{{ old(
                                            'pengumuman_card1_judul',
                                            $pengaturan?->pengumuman_card1_judul
                                            ?? 'Persiapkan Dokumen'
                                        ) }}"
                                        required
                                    >

                                </div>


                                <div class="field">

                                    <label for="pengumuman_card1_deskripsi">
                                        Isi
                                    </label>


                                    <textarea
                                        id="pengumuman_card1_deskripsi"
                                        name="pengumuman_card1_deskripsi"
                                        placeholder="Isi kartu informasi..."
                                    >{{ old(
                                        'pengumuman_card1_deskripsi',
                                        $pengaturan?->pengumuman_card1_deskripsi
                                        ?? ''
                                    ) }}</textarea>

                                </div>


                            </div>

                        </div>



                        {{-- CARD 2 --}}

                        <div class="sub-card">

                            <div class="sub-card-title">
                                Kartu Informasi 2
                            </div>


                            <div class="grid-2">


                                <div class="field">

                                    <label for="pengumuman_card2_judul">
                                        Judul
                                    </label>


                                    <input
                                        type="text"
                                        id="pengumuman_card2_judul"
                                        name="pengumuman_card2_judul"
                                        value="{{ old(
                                            'pengumuman_card2_judul',
                                            $pengaturan?->pengumuman_card2_judul
                                            ?? 'Pendaftaran Online'
                                        ) }}"
                                        required
                                    >

                                </div>


                                <div class="field">

                                    <label for="pengumuman_card2_deskripsi">
                                        Isi
                                    </label>


                                    <textarea
                                        id="pengumuman_card2_deskripsi"
                                        name="pengumuman_card2_deskripsi"
                                        placeholder="Isi kartu informasi..."
                                    >{{ old(
                                        'pengumuman_card2_deskripsi',
                                        $pengaturan?->pengumuman_card2_deskripsi
                                        ?? ''
                                    ) }}</textarea>

                                </div>


                            </div>

                        </div>



                        {{-- CARD 3 --}}

                        <div class="sub-card">

                            <div class="sub-card-title">
                                Kartu Informasi 3
                            </div>


                            <div class="grid-2">


                                <div class="field">

                                    <label for="pengumuman_card3_judul">
                                        Judul
                                    </label>


                                    <input
                                        type="text"
                                        id="pengumuman_card3_judul"
                                        name="pengumuman_card3_judul"
                                        value="{{ old(
                                            'pengumuman_card3_judul',
                                            $pengaturan?->pengumuman_card3_judul
                                            ?? 'Informasi Perusahaan'
                                        ) }}"
                                        required
                                    >

                                </div>


                                <div class="field">

                                    <label for="pengumuman_card3_deskripsi">
                                        Isi
                                    </label>


                                    <textarea
                                        id="pengumuman_card3_deskripsi"
                                        name="pengumuman_card3_deskripsi"
                                        placeholder="Isi kartu informasi..."
                                    >{{ old(
                                        'pengumuman_card3_deskripsi',
                                        $pengaturan?->pengumuman_card3_deskripsi
                                        ?? ''
                                    ) }}</textarea>

                                </div>


                            </div>

                        </div>


                    </div>

                </div>



                {{-- =================================================
                     3. PERSYARATAN MAGANG
                ================================================== --}}

                <div class="section-card">


                    <div class="section-header">

                        <div class="section-number">
                            3
                        </div>


                        <div>

                            <div class="section-title">
                                Persyaratan Magang
                            </div>

                            <div class="section-description">
                                Edit judul, deskripsi, dan tiga persyaratan.
                            </div>

                        </div>

                    </div>


                    <div class="section-body">


                        <div class="field">

                            <label for="persyaratan_judul">

                                Judul

                                <span class="required">
                                    *
                                </span>

                            </label>


                            <input
                                type="text"
                                id="persyaratan_judul"
                                name="persyaratan_judul"
                                value="{{ old(
                                    'persyaratan_judul',
                                    $pengaturan?->persyaratan_judul
                                    ?? 'Persyaratan Magang'
                                ) }}"
                                required
                            >

                        </div>


                        <div class="field">

                            <label for="persyaratan_deskripsi">
                                Deskripsi
                            </label>


                            <textarea
                                id="persyaratan_deskripsi"
                                name="persyaratan_deskripsi"
                                placeholder="Masukkan deskripsi persyaratan..."
                            >{{ old(
                                'persyaratan_deskripsi',
                                $pengaturan?->persyaratan_deskripsi
                                ?? ''
                            ) }}</textarea>

                        </div>



                        {{-- PERSYARATAN 1 --}}

                        <div class="sub-card">

                            <div class="sub-card-title">
                                Persyaratan 1
                            </div>


                            <div class="grid-2">


                                <div class="field">

                                    <label for="persyaratan1_judul">
                                        Judul
                                    </label>


                                    <input
                                        type="text"
                                        id="persyaratan1_judul"
                                        name="persyaratan1_judul"
                                        value="{{ old(
                                            'persyaratan1_judul',
                                            $pengaturan?->persyaratan1_judul
                                            ?? 'Surat Pengantar'
                                        ) }}"
                                        required
                                    >

                                </div>


                                <div class="field">

                                    <label for="persyaratan1_deskripsi">
                                        Isi
                                    </label>


                                    <textarea
                                        id="persyaratan1_deskripsi"
                                        name="persyaratan1_deskripsi"
                                        placeholder="Isi persyaratan..."
                                    >{{ old(
                                        'persyaratan1_deskripsi',
                                        $pengaturan?->persyaratan1_deskripsi
                                        ?? ''
                                    ) }}</textarea>

                                </div>


                            </div>

                        </div>



                        {{-- PERSYARATAN 2 --}}

                        <div class="sub-card">

                            <div class="sub-card-title">
                                Persyaratan 2
                            </div>


                            <div class="grid-2">


                                <div class="field">

                                    <label for="persyaratan2_judul">
                                        Judul
                                    </label>


                                    <input
                                        type="text"
                                        id="persyaratan2_judul"
                                        name="persyaratan2_judul"
                                        value="{{ old(
                                            'persyaratan2_judul',
                                            $pengaturan?->persyaratan2_judul
                                            ?? 'Data Mahasiswa'
                                        ) }}"
                                        required
                                    >

                                </div>


                                <div class="field">

                                    <label for="persyaratan2_deskripsi">
                                        Isi
                                    </label>


                                    <textarea
                                        id="persyaratan2_deskripsi"
                                        name="persyaratan2_deskripsi"
                                        placeholder="Isi persyaratan..."
                                    >{{ old(
                                        'persyaratan2_deskripsi',
                                        $pengaturan?->persyaratan2_deskripsi
                                        ?? ''
                                    ) }}</textarea>

                                </div>


                            </div>

                        </div>



                        {{-- PERSYARATAN 3 --}}

                        <div class="sub-card">

                            <div class="sub-card-title">
                                Persyaratan 3
                            </div>


                            <div class="grid-2">


                                <div class="field">

                                    <label for="persyaratan3_judul">
                                        Judul
                                    </label>


                                    <input
                                        type="text"
                                        id="persyaratan3_judul"
                                        name="persyaratan3_judul"
                                        value="{{ old(
                                            'persyaratan3_judul',
                                            $pengaturan?->persyaratan3_judul
                                            ?? 'Dokumen Pendukung'
                                        ) }}"
                                        required
                                    >

                                </div>


                                <div class="field">

                                    <label for="persyaratan3_deskripsi">
                                        Isi
                                    </label>


                                    <textarea
                                        id="persyaratan3_deskripsi"
                                        name="persyaratan3_deskripsi"
                                        placeholder="Isi persyaratan..."
                                    >{{ old(
                                        'persyaratan3_deskripsi',
                                        $pengaturan?->persyaratan3_deskripsi
                                        ?? ''
                                    ) }}</textarea>

                                </div>


                            </div>

                        </div>


                    </div>

                </div>



                {{-- =================================================
                     4. ALUR MAGANG
                ================================================== --}}

                <div class="section-card">


                    <div class="section-header">

                        <div class="section-number">
                            4
                        </div>


                        <div>

                            <div class="section-title">
                                Alur Magang
                            </div>

                            <div class="section-description">
                                Edit judul, deskripsi, dan empat tahapan alur magang.
                            </div>

                        </div>

                    </div>


                    <div class="section-body">


                        <div class="field">

                            <label for="alur_judul">

                                Judul

                                <span class="required">
                                    *
                                </span>

                            </label>


                            <input
                                type="text"
                                id="alur_judul"
                                name="alur_judul"
                                value="{{ old(
                                    'alur_judul',
                                    $pengaturan?->alur_judul
                                    ?? 'Alur Magang'
                                ) }}"
                                required
                            >

                        </div>


                        <div class="field">

                            <label for="alur_deskripsi">
                                Deskripsi
                            </label>


                            <textarea
                                id="alur_deskripsi"
                                name="alur_deskripsi"
                                placeholder="Masukkan deskripsi alur magang..."
                            >{{ old(
                                'alur_deskripsi',
                                $pengaturan?->alur_deskripsi
                                ?? ''
                            ) }}</textarea>

                        </div>



                        {{-- LANGKAH 1 --}}

                        <div class="sub-card">

                            <div class="sub-card-title">

                                <span class="step-number">
                                    1
                                </span>

                                Langkah Pertama

                            </div>


                            <div class="grid-2">


                                <div class="field">

                                    <label for="alur1_judul">
                                        Judul
                                    </label>


                                    <input
                                        type="text"
                                        id="alur1_judul"
                                        name="alur1_judul"
                                        value="{{ old(
                                            'alur1_judul',
                                            $pengaturan?->alur1_judul
                                            ?? 'Persiapkan'
                                        ) }}"
                                        required
                                    >

                                </div>


                                <div class="field">

                                    <label for="alur1_deskripsi">
                                        Isi
                                    </label>


                                    <textarea
                                        id="alur1_deskripsi"
                                        name="alur1_deskripsi"
                                        placeholder="Isi langkah pertama..."
                                    >{{ old(
                                        'alur1_deskripsi',
                                        $pengaturan?->alur1_deskripsi
                                        ?? ''
                                    ) }}</textarea>

                                </div>


                            </div>

                        </div>



                        {{-- LANGKAH 2 --}}

                        <div class="sub-card">

                            <div class="sub-card-title">

                                <span class="step-number">
                                    2
                                </span>

                                Langkah Kedua

                            </div>


                            <div class="grid-2">


                                <div class="field">

                                    <label for="alur2_judul">
                                        Judul
                                    </label>


                                    <input
                                        type="text"
                                        id="alur2_judul"
                                        name="alur2_judul"
                                        value="{{ old(
                                            'alur2_judul',
                                            $pengaturan?->alur2_judul
                                            ?? 'Daftar'
                                        ) }}"
                                        required
                                    >

                                </div>


                                <div class="field">

                                    <label for="alur2_deskripsi">
                                        Isi
                                    </label>


                                    <textarea
                                        id="alur2_deskripsi"
                                        name="alur2_deskripsi"
                                        placeholder="Isi langkah kedua..."
                                    >{{ old(
                                        'alur2_deskripsi',
                                        $pengaturan?->alur2_deskripsi
                                        ?? ''
                                    ) }}</textarea>

                                </div>


                            </div>

                        </div>



                        {{-- LANGKAH 3 --}}

                        <div class="sub-card">

                            <div class="sub-card-title">

                                <span class="step-number">
                                    3
                                </span>

                                Langkah Ketiga

                            </div>


                            <div class="grid-2">


                                <div class="field">

                                    <label for="alur3_judul">
                                        Judul
                                    </label>


                                    <input
                                        type="text"
                                        id="alur3_judul"
                                        name="alur3_judul"
                                        value="{{ old(
                                            'alur3_judul',
                                            $pengaturan?->alur3_judul
                                            ?? 'Verifikasi'
                                        ) }}"
                                        required
                                    >

                                </div>


                                <div class="field">

                                    <label for="alur3_deskripsi">
                                        Isi
                                    </label>


                                    <textarea
                                        id="alur3_deskripsi"
                                        name="alur3_deskripsi"
                                        placeholder="Isi langkah ketiga..."
                                    >{{ old(
                                        'alur3_deskripsi',
                                        $pengaturan?->alur3_deskripsi
                                        ?? ''
                                    ) }}</textarea>

                                </div>


                            </div>

                        </div>



                        {{-- LANGKAH 4 --}}

                        <div class="sub-card">

                            <div class="sub-card-title">

                                <span class="step-number">
                                    4
                                </span>

                                Langkah Keempat

                            </div>


                            <div class="grid-2">


                                <div class="field">

                                    <label for="alur4_judul">
                                        Judul
                                    </label>


                                    <input
                                        type="text"
                                        id="alur4_judul"
                                        name="alur4_judul"
                                        value="{{ old(
                                            'alur4_judul',
                                            $pengaturan?->alur4_judul
                                            ?? 'Pelaksanaan'
                                        ) }}"
                                        required
                                    >

                                </div>


                                <div class="field">

                                    <label for="alur4_deskripsi">
                                        Isi
                                    </label>


                                    <textarea
                                        id="alur4_deskripsi"
                                        name="alur4_deskripsi"
                                        placeholder="Isi langkah keempat..."
                                    >{{ old(
                                        'alur4_deskripsi',
                                        $pengaturan?->alur4_deskripsi
                                        ?? ''
                                    ) }}</textarea>

                                </div>


                            </div>

                        </div>


                    </div>

                </div>



                {{-- =================================================
                     SAVE
                ================================================== --}}

                <div class="save-area">

                    <button
                        type="submit"
                        class="save-btn"
                    >
                        Simpan Semua Perubahan
                    </button>

                </div>


            </form>


        </section>


    </main>


</div>


</body>

</html>