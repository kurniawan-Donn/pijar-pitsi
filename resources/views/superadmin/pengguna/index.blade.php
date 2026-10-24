<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Kelola Pengguna - MAGENTA</title>

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

        button,
        input {
            font-family: inherit;
        }

        /* =========================================================
           LAYOUT
        ========================================================= */

        .app {
            min-height: 100vh;
            display: flex;
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
            z-index: 1000;
        }

        .brand {
            display: flex;
            align-items: flex-start;
            gap: 12px;

            padding: 4px 6px 25px;

            border-bottom:
                1px solid rgba(255, 255, 255, 0.16);
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

        .brand-name {
            font-size: 20px;
            line-height: 1.1;
            font-weight: 800;
            letter-spacing: 0.2px;
        }

        .brand-subtitle {
            margin-top: 7px;

            font-size: 8px;
            line-height: 1.4;

            font-weight: 700;
            letter-spacing: 1.15px;

            color:
                rgba(255, 255, 255, 0.86);
        }

        .menu-section {
            margin-top: 22px;
        }

        .menu-title {
            padding: 0 9px;

            margin-bottom: 10px;

            color:
                rgba(255, 255, 255, 0.65);

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

        .menu-item {
            min-height: 40px;

            padding: 0 13px;

            border-radius: 7px;

            display: flex;
            align-items: center;

            gap: 11px;

            color:
                rgba(255, 255, 255, 0.94);

            font-size: 12px;

            font-weight: 600;

            transition: .2s ease;
        }

        .menu-item:hover {
            background:
                rgba(255, 255, 255, 0.08);
        }

        .menu-item.active {
            background: #1779c9;
            color: #ffffff;
        }

        .menu-icon {
            width: 16px;
            min-width: 16px;

            text-align: center;

            font-size: 12px;

            font-weight: 700;
        }

        .sidebar-divider {
            height: 1px;

            background:
                rgba(255, 255, 255, 0.14);

            margin: 20px 4px 16px;
        }

        .logout-form {
            margin: 0;
        }

        .logout-button {
            width: 100%;

            min-height: 38px;

            border: none;

            border-radius: 7px;

            background:
                rgba(255, 255, 255, 0.08);

            color: #ffffff;

            cursor: pointer;

            display: flex;
            align-items: center;
            justify-content: center;

            gap: 10px;

            font-size: 12px;

            font-weight: 600;

            transition: .2s ease;
        }

        .logout-button:hover {
            background:
                rgba(255, 255, 255, 0.14);
        }

        /* =========================================================
           MAIN
        ========================================================= */

        .main {
            margin-left: 275px;

            width:
                calc(100% - 275px);

            min-height: 100vh;
        }

        /* =========================================================
           TOPBAR
        ========================================================= */

        .topbar {
            height: 64px;

            background: #ffffff;

            border-bottom:
                1px solid #e5ebf2;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 28px;
        }

        .page-heading {
            min-width: 0;
        }

        .page-title {
            font-size: 19px;

            line-height: 1.2;

            font-weight: 700;

            color: #12345b;
        }

        .page-subtitle {
            margin-top: 4px;

            font-size: 11px;

            color: #73849a;
        }

        .profile-area {
            display: flex;

            align-items: center;

            gap: 10px;
        }

        .profile-text {
            text-align: right;
        }

        .profile-name {
            font-size: 11px;

            font-weight: 700;

            color: #12345b;
        }

        .profile-role {
            margin-top: 3px;

            font-size: 9px;

            color: #73849a;
        }

        .profile-avatar {
            width: 36px;

            height: 36px;

            border-radius: 50%;

            background: #eaf3fc;

            color: #1269aa;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 12px;

            font-weight: 700;
        }

        /* =========================================================
           CONTENT
        ========================================================= */

        .content {
            padding:
                28px 30px 40px;
        }

        .content-container {
            max-width: 1280px;

            margin: 0 auto;
        }

        /* =========================================================
           INFORMATION HEADER
        ========================================================= */

        .info-header {
            display: flex;

            align-items: center;

            gap: 14px;

            margin-bottom: 20px;
        }

        .info-icon {
            width: 46px;

            height: 46px;

            min-width: 46px;

            border-radius: 12px;

            background: #eaf5fd;

            border:
                1px solid #d5eafa;

            color: #1374b9;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 18px;

            font-weight: 700;
        }

        .info-title {
            font-size: 20px;

            color: #12345b;

            font-weight: 700;
        }

        .info-description {
            margin-top: 4px;

            font-size: 11px;

            color: #74859a;
        }

        /* =========================================================
           ALERT
        ========================================================= */

        .alert {
            border-radius: 9px;

            padding: 13px 16px;

            margin-bottom: 18px;

            display: flex;

            align-items: flex-start;

            gap: 12px;

            font-size: 11px;

            line-height: 1.5;
        }

        .alert-icon {
            width: 24px;

            height: 24px;

            min-width: 24px;

            border-radius: 7px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-weight: 700;

            font-size: 11px;
        }

        .alert-success {
            background: #f0faf4;

            border:
                1px solid #cdebd8;

            color: #247443;
        }

        .alert-success .alert-icon {
            background: #d9f2e2;
            color: #247443;
        }

        .alert-error {
            background: #fff5f5;

            border:
                1px solid #f1d0d0;

            color: #a43c3c;
        }

        .alert-error .alert-icon {
            background: #fde2e2;
            color: #a43c3c;
        }

        .alert-title {
            font-weight: 700;

            margin-bottom: 2px;
        }

        /* =========================================================
           MAIN CARD
        ========================================================= */

        .card {
            background: #ffffff;

            border:
                1px solid #e2e9f1;

            border-radius: 12px;

            box-shadow:
                0 4px 16px
                rgba(18, 52, 91, 0.04);

            overflow: hidden;
        }

        .card-header {
            min-height: 68px;

            padding:
                15px 20px;

            border-bottom:
                1px solid #e8edf3;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;
        }

        .card-heading {
            display: flex;

            align-items: center;

            gap: 12px;
        }

        .card-heading-icon {
            width: 34px;

            height: 34px;

            min-width: 34px;

            border-radius: 9px;

            background: #edf6fd;

            border:
                1px solid #dceefa;

            color: #1675b8;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 14px;

            font-weight: 700;
        }

        .card-title {
            font-size: 14px;

            font-weight: 700;

            color: #12345b;
        }

        .card-description {
            margin-top: 3px;

            font-size: 10px;

            color: #7a8b9f;
        }

        /* =========================================================
           BUTTON
        ========================================================= */

        .btn-primary {
            min-height: 38px;

            padding:
                0 17px;

            border: none;

            border-radius: 7px;

            background: #1472bb;

            color: #ffffff;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 8px;

            font-size: 11px;

            font-weight: 700;

            cursor: pointer;

            transition: .2s ease;
        }

        .btn-primary:hover {
            background: #105f9e;
        }

        .btn-icon {
            font-size: 13px;

            font-weight: 700;
        }

        /* =========================================================
           TABLE
        ========================================================= */

        .table-wrapper {
            width: 100%;

            overflow-x: auto;
        }

        table {
            width: 100%;

            border-collapse: collapse;
        }

        thead th {
            background: #f8fafc;

            color: #64758a;

            font-size: 9px;

            font-weight: 800;

            letter-spacing: .7px;

            text-transform: uppercase;

            padding:
                13px 18px;

            text-align: left;

            border-bottom:
                1px solid #e5ebf1;

            white-space: nowrap;
        }

        tbody td {
            padding:
                16px 18px;

            border-bottom:
                1px solid #edf1f5;

            font-size: 11px;

            color: #4e6075;

            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #fbfdff;
        }

        .number-column {
            width: 55px;

            color: #6c7d91;
        }

        /* =========================================================
           USER
        ========================================================= */

        .user-cell {
            display: flex;

            align-items: center;

            gap: 11px;
        }

        .user-avatar {
            width: 34px;

            height: 34px;

            min-width: 34px;

            border-radius: 50%;

            background: #edf5fc;

            color: #176da8;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 11px;

            font-weight: 800;

            text-transform: uppercase;
        }

        .user-name {
            font-size: 11px;

            font-weight: 700;

            color: #173c65;
        }

        .user-email {
            margin-top: 3px;

            font-size: 10px;

            color: #7b8b9d;
        }

        /* =========================================================
           ROLE
        ========================================================= */

        .role-badge {
            display: inline-flex;

            align-items: center;

            min-height: 24px;

            padding:
                0 10px;

            border-radius: 20px;

            font-size: 9px;

            font-weight: 700;
        }

        .role-superadmin {
            background: #fff1df;

            color: #d97816;
        }

        .role-admin {
            background: #e8f3ff;

            color: #166da9;
        }

        /* =========================================================
           DATE
        ========================================================= */

        .date-text {
            color: #65778c;

            font-size: 10px;
        }

        /* =========================================================
           ACTIONS
        ========================================================= */

        .actions {
            display: flex;

            align-items: center;

            gap: 7px;
        }

        .action-form {
            margin: 0;
        }

        .action-button {
            min-height: 30px;

            padding:
                0 10px;

            border-radius: 6px;

            border:
                1px solid #d8e5f2;

            background: #f5f9fd;

            color: #1268a7;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            font-size: 10px;

            font-weight: 700;

            cursor: pointer;

            transition: .2s ease;
        }

        .action-button:hover {
            background: #eaf4fc;
        }

        .delete-button {
            border-color: #f0d4d4;

            background: #fff8f8;

            color: #c34a4a;
        }

        .delete-button:hover {
            background: #fff0f0;
        }

        .disabled-action {
            min-height: 30px;

            padding:
                0 9px;

            border-radius: 6px;

            background: #f5f6f8;

            color: #a3adb9;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            font-size: 9px;

            font-weight: 600;
        }

        /* =========================================================
           EMPTY STATE
        ========================================================= */

        .empty-state {
            padding:
                55px 20px;

            text-align: center;
        }

        .empty-icon {
            width: 50px;

            height: 50px;

            margin:
                0 auto 13px;

            border-radius: 50%;

            background: #eef5fb;

            color: #4f86ad;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 18px;
        }

        .empty-title {
            font-size: 13px;

            font-weight: 700;

            color: #365575;
        }

        .empty-description {
            margin-top: 5px;

            font-size: 10px;

            color: #8493a4;
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 1000px) {

            .sidebar {
                width: 230px;

                min-width: 230px;
            }

            .main {
                margin-left: 230px;

                width:
                    calc(100% - 230px);
            }

            .content {
                padding:
                    24px 20px 35px;
            }

            .topbar {
                padding:
                    0 20px;
            }

        }


        @media (max-width: 760px) {

            .sidebar {
                position: relative;

                width: 100%;

                min-width: 0;

                min-height: auto;
            }

            .main {
                margin-left: 0;

                width: 100%;
            }

            .app {
                display: block;
            }

            .brand {
                max-width: 300px;
            }

            .topbar {
                height: auto;

                min-height: 70px;

                padding:
                    14px 18px;
            }

            .profile-area {
                display: none;
            }

            .content {
                padding:
                    20px 14px 30px;
            }

            .card-header {
                align-items: flex-start;

                flex-direction: column;
            }

            .btn-primary {
                width: 100%;
            }

            thead th,
            tbody td {
                padding-left: 13px;

                padding-right: 13px;
            }

        }

    </style>

</head>


<body>


<div class="app">


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

                <div class="brand-name">
                    MAGENTA
                </div>


                <div class="brand-subtitle">
                    SISTEM INFORMASI MAGANG
                </div>

            </div>

        </div>


        {{-- =====================================================
             UTAMA
        ====================================================== --}}

        <div class="menu-section">

            <div class="menu-title">
                UTAMA
            </div>


            <nav class="menu">


                <a
                    href="{{ route('superadmin.dashboard') }}"
                    class="menu-item"
                >

                    <span class="menu-icon">
                        ⌂
                    </span>

                    <span>
                        Dashboard
                    </span>

                </a>


            </nav>

        </div>


        {{-- =====================================================
             SISTEM
        ====================================================== --}}

        <div class="menu-section">

            <div class="menu-title">
                SISTEM
            </div>


            <nav class="menu">


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
                    class="menu-item"
                >

                    <span class="menu-icon">
                        ⚙
                    </span>

                    <span>
                        Kelola Konten
                    </span>

                </a>


                {{-- KELOLA PENGGUNA --}}

                <a
                    href="{{ route('superadmin.pengguna.index') }}"
                    class="menu-item active"
                >

                    <span class="menu-icon">
                        ♟
                    </span>

                    <span>
                        Kelola Pengguna
                    </span>

                </a>


            </nav>

        </div>


        <div class="sidebar-divider"></div>


        {{-- =====================================================
             LOGOUT
        ====================================================== --}}

        <form
            action="{{ route('logout') }}"
            method="POST"
            class="logout-form"
        >

            @csrf


            <button
                type="submit"
                class="logout-button"
            >

                <span class="menu-icon">
                    ↪
                </span>

                <span>
                    Logout
                </span>

            </button>


        </form>


    </aside>


    {{-- =========================================================
         MAIN
    ========================================================== --}}

    <main class="main">


        {{-- =====================================================
             TOPBAR
        ====================================================== --}}

        <header class="topbar">


            <div class="page-heading">

                <div class="page-title">
                    Kelola Pengguna
                </div>


                <div class="page-subtitle">
                    Kelola akun Admin dan Super Admin MAGENTA
                </div>

            </div>


            {{-- USER PROFILE --}}

            <div class="profile-area">


                <div class="profile-text">

                    <div class="profile-name">
                        {{ auth()->user()->name ?? 'Pengguna' }}
                    </div>


                    <div class="profile-role">

                        {{ auth()->user()->role === 'super_admin'
                            ? 'Super Admin'
                            : 'Admin' }}

                    </div>

                </div>


                <div class="profile-avatar">

                    {{ strtoupper(
                        substr(
                            auth()->user()->name ?? 'P',
                            0,
                            1
                        )
                    ) }}

                </div>


            </div>


        </header>


        {{-- =====================================================
             CONTENT
        ====================================================== --}}

        <section class="content">


            <div class="content-container">


                {{-- =================================================
                     INFORMATION HEADER
                ================================================== --}}

                <div class="info-header">


                    <div class="info-icon">
                        ♟
                    </div>


                    <div>

                        <div class="info-title">
                            Manajemen Pengguna
                        </div>


                        <div class="info-description">
                            Kelola akun pengguna yang memiliki akses
                            ke sistem MAGENTA.
                        </div>

                    </div>


                </div>


                {{-- =================================================
                     SUCCESS
                ================================================== --}}

                @if(session('success'))

                    <div class="alert alert-success">


                        <div class="alert-icon">
                            ✓
                        </div>


                        <div>

                            <div class="alert-title">
                                Berhasil
                            </div>


                            <div>
                                {{ session('success') }}
                            </div>

                        </div>


                    </div>

                @endif


                {{-- =================================================
                     ERROR
                ================================================== --}}

                @if(session('error'))

                    <div class="alert alert-error">


                        <div class="alert-icon">
                            !
                        </div>


                        <div>

                            <div class="alert-title">
                                Tidak dapat diproses
                            </div>


                            <div>
                                {{ session('error') }}
                            </div>

                        </div>


                    </div>

                @endif


                {{-- =================================================
                     VALIDATION ERROR
                ================================================== --}}

                @if($errors->any())

                    <div class="alert alert-error">


                        <div class="alert-icon">
                            !
                        </div>


                        <div>

                            <div class="alert-title">
                                Periksa kembali data
                            </div>


                            <div>

                                @foreach($errors->all() as $error)

                                    <div>
                                        {{ $error }}
                                    </div>

                                @endforeach

                            </div>


                        </div>


                    </div>

                @endif


                {{-- =================================================
                     USER CARD
                ================================================== --}}

                <div class="card">


                    <div class="card-header">


                        <div class="card-heading">


                            <div class="card-heading-icon">
                                ♟
                            </div>


                            <div>

                                <div class="card-title">
                                    Daftar Pengguna
                                </div>


                                <div class="card-description">
                                    Daftar akun yang terdaftar di sistem MAGENTA.
                                </div>

                            </div>


                        </div>


                        <a
                            href="{{ route('superadmin.pengguna.create') }}"
                            class="btn-primary"
                        >

                            <span class="btn-icon">
                                +
                            </span>


                            <span>
                                Tambah Pengguna
                            </span>

                        </a>


                    </div>


                    {{-- =================================================
                         USERS EXIST
                    ================================================== --}}

                    @if($users->count() > 0)


                        <div class="table-wrapper">


                            <table>


                                <thead>

                                    <tr>

                                        <th class="number-column">
                                            No
                                        </th>

                                        <th>
                                            Pengguna
                                        </th>

                                        <th>
                                            Role
                                        </th>

                                        <th>
                                            Dibuat
                                        </th>

                                        <th>
                                            Aksi
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>


                                    @foreach($users as $index => $user)


                                        <tr>


                                            {{-- NO --}}

                                            <td class="number-column">
                                                {{ $index + 1 }}
                                            </td>


                                            {{-- PENGGUNA --}}

                                            <td>


                                                <div class="user-cell">


                                                    <div class="user-avatar">

                                                        {{ strtoupper(
                                                            substr(
                                                                $user->name,
                                                                0,
                                                                1
                                                            )
                                                        ) }}

                                                    </div>


                                                    <div>

                                                        <div class="user-name">
                                                            {{ $user->name }}
                                                        </div>


                                                        <div class="user-email">
                                                            {{ $user->email }}
                                                        </div>

                                                    </div>


                                                </div>


                                            </td>


                                            {{-- ROLE --}}

                                            <td>


                                                @if($user->role === 'super_admin')


                                                    <span
                                                        class="role-badge role-superadmin"
                                                    >
                                                        Super Admin
                                                    </span>


                                                @else


                                                    <span
                                                        class="role-badge role-admin"
                                                    >
                                                        Admin
                                                    </span>


                                                @endif


                                            </td>


                                            {{-- TANGGAL --}}

                                            <td>

                                                <span class="date-text">

                                                    {{ $user->created_at
                                                        ? $user->created_at->format('d/m/Y')
                                                        : '-'
                                                    }}

                                                </span>

                                            </td>


                                            {{-- AKSI --}}

                                            <td>


                                                <div class="actions">


                                                    {{-- EDIT --}}

                                                    <a
                                                        href="{{ route(
                                                            'superadmin.pengguna.edit',
                                                            $user
                                                        ) }}"
                                                        class="action-button"
                                                    >
                                                        Edit
                                                    </a>


                                                    {{-- HAPUS --}}

                                                    @if($user->id !== auth()->id())


                                                        <form
                                                            action="{{ route(
                                                                'superadmin.pengguna.destroy',
                                                                $user
                                                            ) }}"
                                                            method="POST"
                                                            class="action-form"
                                                            onsubmit="return confirm('Apakah kamu yakin ingin menghapus pengguna ini?');"
                                                        >

                                                            @csrf

                                                            @method('DELETE')


                                                            <button
                                                                type="submit"
                                                                class="action-button delete-button"
                                                            >
                                                                Hapus
                                                            </button>


                                                        </form>


                                                    @else


                                                        <span class="disabled-action">
                                                            Akun aktif
                                                        </span>


                                                    @endif


                                                </div>


                                            </td>


                                        </tr>


                                    @endforeach


                                </tbody>


                            </table>


                        </div>


                    @else


                        {{-- =================================================
                             EMPTY
                        ================================================== --}}

                        <div class="empty-state">


                            <div class="empty-icon">
                                ♟
                            </div>


                            <div class="empty-title">
                                Belum ada pengguna
                            </div>


                            <div class="empty-description">
                                Belum terdapat akun pengguna lain
                                di sistem MAGENTA.
                            </div>


                        </div>


                    @endif


                </div>


            </div>


        </section>


    </main>


</div>


</body>

</html>