<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Pengguna - MAGENTA</title>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #17365d;
        }

        body {
            min-height: 100vh;
        }

        a {
            text-decoration: none;
        }

        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 275px;
            height: 100vh;
            background: #12345b;
            color: #ffffff;
            padding: 20px 16px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 8px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.16);
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #ffffff;
            color: #1779c9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 800;
        }

        .brand-text {
            line-height: 1.15;
        }

        .brand-title {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.3px;
        }

        .brand-subtitle {
            margin-top: 4px;
            font-size: 9px;
            color: #c8d9ec;
            letter-spacing: 0.4px;
        }

        .menu-section {
            margin-top: 22px;
        }

        .menu-title {
            padding: 0 8px;
            margin-bottom: 8px;
            font-size: 10px;
            font-weight: 700;
            color: #9fb8d4;
            letter-spacing: 0.8px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 11px;
            width: 100%;
            min-height: 42px;
            padding: 0 12px;
            margin-bottom: 4px;
            border-radius: 8px;
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .menu-item.active {
            background: #1779c9;
        }

        .menu-icon {
            width: 18px;
            text-align: center;
            font-size: 14px;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.16);
        }

        .logout-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 38px;
            border: 0;
            border-radius: 7px;
            background: rgba(255, 255, 255, 0.10);
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .logout-button:hover {
            background: rgba(255, 255, 255, 0.17);
        }

        /* =========================
           MAIN
        ========================= */

        .main {
            margin-left: 275px;
            min-height: 100vh;
        }

        /* =========================
           TOPBAR
        ========================= */

        .topbar {
            height: 64px;
            background: #ffffff;
            border-bottom: 1px solid #e4eaf2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
        }

        .topbar-title {
            font-size: 19px;
            font-weight: 800;
            color: #174a7e;
        }

        .topbar-subtitle {
            margin-top: 3px;
            font-size: 11px;
            color: #7b91aa;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-info {
            text-align: right;
        }

        .admin-name {
            font-size: 12px;
            font-weight: 700;
            color: #174a7e;
        }

        .admin-role {
            margin-top: 2px;
            font-size: 10px;
            color: #8095ad;
        }

        .admin-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #eaf3fb;
            color: #1779c9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
        }

        /* =========================
           CONTENT
        ========================= */

        .content {
            padding: 28px;
        }

        .page-intro {
            background: #ffffff;
            border-radius: 10px;
            border-left: 4px solid #1779c9;
            padding: 20px 22px;
            margin-bottom: 20px;
            box-shadow: 0 3px 12px rgba(19, 52, 91, 0.05);
        }

        .intro-accent {
            width: 34px;
            height: 3px;
            background: #f28c28;
            margin-bottom: 10px;
        }

        .page-intro h1 {
            margin: 0;
            font-size: 21px;
            color: #174a7e;
        }

        .page-intro p {
            margin: 7px 0 0;
            font-size: 12px;
            line-height: 1.6;
            color: #71869f;
        }

        /* =========================
           ALERT
        ========================= */

        .alert {
            padding: 13px 15px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 12px;
            line-height: 1.5;
        }

        .alert-danger {
            background: #fff1f1;
            border: 1px solid #f2caca;
            color: #a33a3a;
        }

        .alert-danger ul {
            margin: 7px 0 0 18px;
            padding: 0;
        }

        /* =========================
           FORM CARD
        ========================= */

        .card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(19, 52, 91, 0.05);
            overflow: hidden;
        }

        .card-header {
            padding: 18px 20px;
            border-bottom: 1px solid #e8edf3;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-number {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #edf5fc;
            color: #1779c9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 800;
        }

        .card-header h2 {
            margin: 0;
            font-size: 15px;
            color: #174a7e;
        }

        .card-header p {
            margin: 4px 0 0;
            font-size: 11px;
            color: #7b91aa;
        }

        .card-body {
            padding: 22px 20px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            margin-bottom: 7px;
            font-size: 12px;
            font-weight: 700;
            color: #174a7e;
        }

        .required {
            color: #e05252;
        }

        .form-control {
            width: 100%;
            height: 42px;
            padding: 0 12px;
            border: 1px solid #d7e0eb;
            border-radius: 7px;
            background: #ffffff;
            color: #243f5c;
            font-size: 12px;
            outline: none;
            transition: 0.2s ease;
        }

        .form-control:focus {
            border-color: #1779c9;
            box-shadow: 0 0 0 3px rgba(23, 121, 201, 0.08);
        }

        .form-control::placeholder {
            color: #a7b7c9;
        }

        select.form-control {
            cursor: pointer;
        }

        .form-help {
            margin-top: 6px;
            font-size: 10px;
            color: #879bb1;
            line-height: 1.5;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        /* =========================
           ACTION
        ========================= */

        .form-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            padding: 18px 20px;
            border-top: 1px solid #e8edf3;
            background: #fbfcfe;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 120px;
            height: 40px;
            padding: 0 16px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s ease;
            border: 1px solid transparent;
        }

        .button-secondary {
            background: #ffffff;
            color: #60758d;
            border-color: #d7e0eb;
        }

        .button-secondary:hover {
            background: #f4f7fb;
        }

        .button-primary {
            background: #1779c9;
            color: #ffffff;
            border-color: #1779c9;
        }

        .button-primary:hover {
            background: #1269b1;
            border-color: #1269b1;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 900px) {
            .sidebar {
                width: 230px;
            }

            .main {
                margin-left: 230px;
            }

            .form-grid {
                grid-template-columns: 1fr;
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

            .topbar {
                padding: 0 18px;
            }

            .content {
                padding: 18px;
            }

            .admin-info {
                display: none;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column-reverse;
                align-items: stretch;
            }

            .button {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div>
            <div class="brand">
                <div class="brand-logo">M</div>

                <div class="brand-text">
                    <div class="brand-title">MAGENTA</div>
                    <div class="brand-subtitle">
                        SISTEM INFORMASI MAGANG
                    </div>
                </div>
            </div>

            <div class="menu-section">
                <div class="menu-title">
                    UTAMA
                </div>

                <a
                    href="{{ route('superadmin.dashboard') }}"
                    class="menu-item"
                >
                    <span class="menu-icon">▦</span>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-title">
                    SISTEM
                </div>

                <a
                    href="{{ route('superadmin.perusahaan.index') }}"
                    class="menu-item"
                >
                    <span class="menu-icon">▰</span>
                    <span>Profil Perusahaan</span>
                </a>

                <a
                    href="{{ route('superadmin.banners.index') }}"
                    class="menu-item"
                >
                    <span class="menu-icon">▧</span>
                    <span>Kelola Banner</span>
                </a>

                <a
                    href="{{ route('superadmin.pengaturan.index') }}"
                    class="menu-item"
                >
                    <span class="menu-icon">⚙</span>
                    <span>Kelola Konten</span>
                </a>

                @if(auth()->user()->role === 'super_admin')
                    <a
                        href="{{ route('superadmin.pengguna.index') }}"
                        class="menu-item active"
                    >
                        <span class="menu-icon">◉</span>
                        <span>Kelola Pengguna</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="sidebar-bottom">
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


    <!-- MAIN -->
    <main class="main">

        <!-- TOPBAR -->
        <header class="topbar">

            <div>
                <div class="topbar-title">
                    Tambah Pengguna
                </div>

                <div class="topbar-subtitle">
                    Tambahkan akun Admin atau Super Admin ke sistem MAGENTA
                </div>
            </div>

            <div class="admin-profile">

                <div class="admin-info">
                    <div class="admin-name">
                        {{ auth()->user()->name }}
                    </div>

                    <div class="admin-role">
                        Super Admin
                    </div>
                </div>

                <div class="admin-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

            </div>

        </header>


        <!-- CONTENT -->
        <section class="content">

            <!-- INTRO -->
            <div class="page-intro">

                <div class="intro-accent"></div>

                <h1>
                    Tambah Pengguna
                </h1>

                <p>
                    Buat akun pengguna baru untuk mengakses sistem administrasi MAGENTA.
                    Pilih role sesuai dengan hak akses yang akan diberikan.
                </p>

            </div>


            <!-- VALIDATION ERROR -->
            @if ($errors->any())

                <div class="alert alert-danger">

                    <strong>
                        Data belum dapat disimpan.
                    </strong>

                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>

                </div>

            @endif


            <!-- FORM -->
            <div class="card">

                <div class="card-header">

                    <div class="card-number">
                        1
                    </div>

                    <div>
                        <h2>
                            Informasi Pengguna
                        </h2>

                        <p>
                            Isi data akun pengguna yang akan ditambahkan.
                        </p>
                    </div>

                </div>


                <form
                    action="{{ route('superadmin.pengguna.store') }}"
                    method="POST"
                >

                    @csrf

                    <div class="card-body">

                        <!-- NAMA -->
                        <div class="form-group">

                            <label
                                for="name"
                                class="form-label"
                            >
                                Nama Lengkap
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control"
                                value="{{ old('name') }}"
                                placeholder="Masukkan nama lengkap"
                                maxlength="255"
                                required
                            >

                        </div>


                        <!-- EMAIL -->
                        <div class="form-group">

                            <label
                                for="email"
                                class="form-label"
                            >
                                Email
                                <span class="required">*</span>
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email') }}"
                                placeholder="contoh@email.com"
                                maxlength="255"
                                required
                            >

                            <div class="form-help">
                                Email digunakan untuk login ke sistem MAGENTA.
                            </div>

                        </div>


                        <!-- PASSWORD -->
                        <div class="form-grid">

                            <div class="form-group">

                                <label
                                    for="password"
                                    class="form-label"
                                >
                                    Password
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Minimal 8 karakter"
                                    minlength="8"
                                    required
                                >

                            </div>


                            <!-- KONFIRMASI PASSWORD -->
                            <div class="form-group">

                                <label
                                    for="password_confirmation"
                                    class="form-label"
                                >
                                    Konfirmasi Password
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    class="form-control"
                                    placeholder="Ulangi password"
                                    minlength="8"
                                    required
                                >

                            </div>

                        </div>


                        <!-- ROLE -->
                        <div class="form-group">

                            <label
                                for="role"
                                class="form-label"
                            >
                                Role Pengguna
                                <span class="required">*</span>
                            </label>

                            <select
                                id="role"
                                name="role"
                                class="form-control"
                                required
                            >

                                <option value="">
                                    -- Pilih Role --
                                </option>

                                <option
                                    value="admin"
                                    {{ old('role') === 'admin' ? 'selected' : '' }}
                                >
                                    Admin
                                </option>

                                <option
                                    value="super_admin"
                                    {{ old('role') === 'super_admin' ? 'selected' : '' }}
                                >
                                    Super Admin
                                </option>

                            </select>

                            <div class="form-help">
                                Admin memiliki akses administrasi sesuai konfigurasi sistem.
                                Super Admin dapat mengelola pengguna.
                            </div>

                        </div>

                    </div>


                    <!-- ACTION -->
                    <div class="form-actions">

                        <a
                            href="{{ route('superadmin.pengguna.index') }}"
                            class="button button-secondary"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="button button-primary"
                        >
                            Simpan Pengguna
                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</body>
</html>