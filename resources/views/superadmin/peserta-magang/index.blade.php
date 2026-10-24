{{-- resources/views/superadmin/peserta-magang/index.blade.php --}}

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Data Peserta Magang - MAGENTA
    </title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #263238;
            min-height: 100vh;
        }

        a {
            text-decoration: none;
        }

        button,
        input,
        select {
            font-family: inherit;
        }


        /* =====================================================
           LAYOUT
        ====================================================== */

        .sidebar {

            position: fixed;

            top: 0;
            left: 0;
            bottom: 0;

            width: 275px;

            background: #12345b;

            color: white;

            padding: 24px 18px;

            overflow-y: auto;

            z-index: 1000;
        }


        .main-wrapper {

            margin-left: 275px;

            min-height: 100vh;
        }


        /* =====================================================
           BRAND
        ====================================================== */

        .brand {

            display: flex;
            align-items: center;

            gap: 12px;

            padding:
                4px 8px 28px;

            border-bottom:
                1px solid rgba(255,255,255,0.12);

            margin-bottom: 25px;
        }


        .brand-mark {

            width: 44px;
            height: 44px;

            background: white;

            color: #12345b;

            border-radius: 10px;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 20px;
            font-weight: 800;

            flex-shrink: 0;
        }


        .brand-name {

            font-size: 18px;
            font-weight: 800;

            line-height: 1.2;
        }


        .brand-subtitle {

            margin-top: 3px;

            font-size: 11px;

            color:
                rgba(255,255,255,0.65);
        }


        /* =====================================================
           SIDEBAR MENU
        ====================================================== */

        .menu-section {

            margin-bottom: 25px;
        }


        .menu-title {

            color:
                rgba(255,255,255,0.45);

            font-size: 10px;

            font-weight: 800;

            letter-spacing: 1.2px;

            margin:
                0 10px 9px;

            text-transform: uppercase;
        }


        .menu {

            display: grid;

            gap: 5px;

        }


        .menu-link {

            display: flex;

            align-items: center;

            gap: 11px;

            color:
                rgba(255,255,255,0.82);

            padding:
                11px 12px;

            border-radius: 8px;

            font-size: 13px;

            font-weight: 600;

            transition: 0.2s ease;
        }


        .menu-link:hover {

            background:
                rgba(255,255,255,0.08);

            color: white;
        }


        .menu-link.active {

            background:
                rgba(255,255,255,0.14);

            color: white;

            box-shadow:
                inset 3px 0 0 #f47b20;
        }


        .menu-icon {

            width: 20px;

            text-align: center;

            font-size: 15px;

            flex-shrink: 0;
        }


        /* =====================================================
           SIDEBAR BOTTOM
        ====================================================== */

        .sidebar-bottom {

            margin-top: 35px;

            padding-top: 20px;

            border-top:
                1px solid rgba(255,255,255,0.12);
        }


        .logout-button {

            width: 100%;

            display: flex;

            align-items: center;

            gap: 11px;

            border: none;

            background: transparent;

            color:
                rgba(255,255,255,0.78);

            padding:
                11px 12px;

            border-radius: 8px;

            cursor: pointer;

            font-size: 13px;

            font-weight: 600;

            text-align: left;

        }


        .logout-button:hover {

            background:
                rgba(255,255,255,0.08);

            color: white;
        }


        /* =====================================================
           TOPBAR
        ====================================================== */

        .topbar {

            height: 78px;

            background: white;

            border-bottom:
                1px solid #e3eaf1;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding:
                0 32px;
        }


        .page-title {

            font-size: 20px;

            font-weight: 800;

            color: #17313d;
        }


        .page-subtitle {

            margin-top: 4px;

            color: #7b8b96;

            font-size: 12px;
        }


        .user-info {

            display: flex;

            align-items: center;

            gap: 12px;
        }


        .user-avatar {

            width: 40px;
            height: 40px;

            border-radius: 50%;

            background: #eaf3f8;

            color: #0878a8;

            display: flex;

            align-items: center;
            justify-content: center;

            font-weight: 800;

            font-size: 14px;
        }


        .user-name {

            font-size: 13px;

            font-weight: 700;

            color: #263238;
        }


        .user-role {

            margin-top: 3px;

            font-size: 11px;

            color: #7b8b96;
        }


        /* =====================================================
           CONTENT
        ====================================================== */

        .content {

            padding: 32px;
        }


        .content-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 25px;
        }


        .heading {

            font-size: 26px;

            color: #17313d;

            font-weight: 800;
        }


        .heading-description {

            color: #7b8b96;

            font-size: 13px;

            margin-top: 7px;
        }


        /* =====================================================
           BUTTON
        ====================================================== */

        .btn {

            display: inline-flex;

            align-items: center;
            justify-content: center;

            gap: 8px;

            min-height: 42px;

            padding:
                0 17px;

            border-radius: 8px;

            border: none;

            font-size: 13px;

            font-weight: 700;

            cursor: pointer;

            transition: 0.2s ease;
        }


        .btn-primary {

            background: #0878a8;

            color: white;
        }


        .btn-primary:hover {

            background: #075d83;

            transform: translateY(-1px);
        }


        .btn-warning {

            background: #fff3e8;

            color: #c65f12;

            border:
                1px solid #ffd9bb;
        }


        .btn-warning:hover {

            background: #ffe9d6;
        }


        .btn-danger {

            background: #fff0f0;

            color: #c62828;

            border:
                1px solid #ffd1d1;
        }


        .btn-danger:hover {

            background: #ffe1e1;
        }


        .btn-small {

            min-height: 34px;

            padding:
                0 11px;

            font-size: 11px;
        }


        /* =====================================================
           ALERT
        ====================================================== */

        .alert {

            border-radius: 9px;

            padding:
                13px 16px;

            margin-bottom: 20px;

            font-size: 13px;

            font-weight: 600;
        }


        .alert-success {

            background: #ecfdf3;

            color: #137333;

            border:
                1px solid #b7ebc6;
        }


        /* =====================================================
           MAIN CARD
        ====================================================== */

        .card {

            background: white;

            border:
                1px solid #e2eaf0;

            border-radius: 14px;

            box-shadow:
                0 8px 28px rgba(31, 77, 102, 0.06);

            overflow: hidden;
        }


        .card-top {

            padding:
                20px 22px;

            border-bottom:
                1px solid #edf1f4;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            flex-wrap: wrap;
        }


        .card-title {

            font-size: 15px;

            font-weight: 800;

            color: #263238;
        }


        .card-info {

            margin-top: 4px;

            font-size: 11px;

            color: #8a99a3;
        }


        /* =====================================================
           TABLE
        ====================================================== */

        .table-wrapper {

            overflow-x: auto;
        }


        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 950px;
        }


        thead {

            background: #f8fafc;
        }


        th {

            text-align: left;

            padding:
                14px 16px;

            font-size: 11px;

            color: #697a85;

            text-transform: uppercase;

            letter-spacing: 0.4px;

            font-weight: 800;

            border-bottom:
                1px solid #e8eef2;

            white-space: nowrap;
        }


        td {

            padding:
                15px 16px;

            font-size: 13px;

            color: #465762;

            border-bottom:
                1px solid #eef2f5;

            vertical-align: middle;
        }


        tbody tr:hover {

            background: #fbfdfe;
        }


        tbody tr:last-child td {

            border-bottom: none;
        }


        .name-cell {

            font-weight: 700;

            color: #263238;
        }


        .secondary-text {

            margin-top: 3px;

            color: #84939d;

            font-size: 11px;
        }


        /* =====================================================
           STATUS
        ====================================================== */

        .status-badge {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            min-width: 68px;

            padding:
                6px 9px;

            border-radius: 20px;

            font-size: 10px;

            font-weight: 800;

            text-transform: uppercase;
        }


        .status-aktif {

            background: #eaf8f0;

            color: #16834b;
        }


        .status-selesai {

            background: #eef1f4;

            color: #667781;
        }


        /* =====================================================
           ACTIONS
        ====================================================== */

        .actions {

            display: flex;

            align-items: center;

            gap: 6px;

            flex-wrap: wrap;
        }


        .action-link {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            min-height: 32px;

            padding:
                0 10px;

            border-radius: 7px;

            font-size: 10px;

            font-weight: 800;

            transition: 0.2s ease;
        }


        .action-detail {

            background: #eef8fc;

            color: #0878a8;

        }


        .action-detail:hover {

            background: #dff2fa;
        }


        .action-edit {

            background: #fff5e9;

            color: #c66a19;

        }


        .action-edit:hover {

            background: #ffe9cf;
        }


        .action-delete {

            border: none;

            background: #fff0f0;

            color: #c62828;

            cursor: pointer;

        }


        .action-delete:hover {

            background: #ffe0e0;
        }


        /* =====================================================
           EMPTY STATE
        ====================================================== */

        .empty-state {

            padding:
                65px 25px;

            text-align: center;

            color: #7b8b96;
        }


        .empty-icon {

            width: 58px;
            height: 58px;

            margin:
                0 auto 15px;

            border-radius: 15px;

            background: #eef8fc;

            color: #0878a8;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 25px;
        }


        .empty-title {

            font-size: 16px;

            font-weight: 800;

            color: #34454f;

            margin-bottom: 6px;
        }


        .empty-description {

            font-size: 12px;

            color: #8a99a3;

            margin-bottom: 18px;
        }


        /* =====================================================
           PAGINATION
        ====================================================== */

        .pagination-wrapper {

            padding:
                18px 22px;

            border-top:
                1px solid #edf1f4;

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 15px;

            flex-wrap: wrap;

        }


        .pagination-info {

            font-size: 11px;

            color: #8998a2;
        }


        .pagination {

            display: flex;

            align-items: center;

            gap: 5px;

            flex-wrap: wrap;
        }


        .pagination a,
        .pagination span {

            min-width: 32px;
            height: 32px;

            padding:
                0 9px;

            display: inline-flex;

            align-items: center;
            justify-content: center;

            border-radius: 7px;

            font-size: 11px;

            font-weight: 700;

            border:
                1px solid #e1e8ed;

            background: white;

            color: #586a75;
        }


        .pagination span[aria-current="page"] {

            background: #0878a8;

            border-color: #0878a8;

            color: white;
        }


        .pagination a:hover {

            background: #f1f7fa;
        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 900px) {

            .sidebar {

                width: 230px;
            }

            .main-wrapper {

                margin-left: 230px;
            }

            .content {

                padding: 22px;
            }

            .topbar {

                padding:
                    0 22px;
            }

        }


        @media (max-width: 700px) {

            .sidebar {

                position: static;

                width: 100%;

                min-height: auto;
            }

            .main-wrapper {

                margin-left: 0;
            }

            .topbar {

                height: auto;

                min-height: 78px;

                padding:
                    15px 20px;
            }

            .content {

                padding: 18px;
            }

            .content-header {

                align-items: flex-start;

                flex-direction: column;
            }

            .user-info {

                display: none;
            }

        }

    </style>

</head>


<body>


    @php

        $role = auth()->user()->role;

        $isSuperAdmin = $role === 'super_admin';

        $dashboardRoute = $isSuperAdmin
            ? route('superadmin.dashboard')
            : route('admin.dashboard');

        $roleLabel = $isSuperAdmin
            ? 'Super Admin'
            : 'Admin';

        $userName = auth()->user()->name;

        $userInitial = strtoupper(
            substr($userName, 0, 1)
        );

    @endphp


    {{-- =====================================================
         SIDEBAR
    ====================================================== --}}

    <aside class="sidebar">


        <div class="brand">

            <div class="brand-mark">
                M
            </div>

            <div>

                <div class="brand-name">
                    MAGENTA
                </div>

                <div class="brand-subtitle">
                    Sistem Informasi Magang
                </div>

            </div>

        </div>


        {{-- UTAMA --}}

        <div class="menu-section">

            <div class="menu-title">
                Utama
            </div>

            <nav class="menu">

                <a
                    href="{{ $dashboardRoute }}"
                    class="menu-link"
                >

                    <span class="menu-icon">
                        ▦
                    </span>

                    Dashboard

                </a>

            </nav>

        </div>


        {{-- SISTEM --}}

        <div class="menu-section">

            <div class="menu-title">
                Sistem
            </div>

            <nav class="menu">


                {{-- PROFIL PERUSAHAAN --}}

                <a
                    href="{{ route('superadmin.perusahaan.index') }}"
                    class="menu-link"
                >

                    <span class="menu-icon">
                        ◉
                    </span>

                    Profil Perusahaan

                </a>


                {{-- BANNER --}}

                <a
                    href="{{ route('superadmin.banners.index') }}"
                    class="menu-link"
                >

                    <span class="menu-icon">
                        ▣
                    </span>

                    Kelola Banner

                </a>


                {{-- KONTEN --}}

                <a
                    href="{{ route('superadmin.pengaturan.index') }}"
                    class="menu-link"
                >

                    <span class="menu-icon">
                        ✎
                    </span>

                    Kelola Konten

                </a>


                {{-- PESERTA MAGANG --}}

                <a
                    href="{{ route('superadmin.peserta-magang.index') }}"
                    class="menu-link active"
                >

                    <span class="menu-icon">
                        ♙
                    </span>

                    Data Peserta

                </a>


                {{-- KHUSUS SUPER ADMIN --}}

                @if($isSuperAdmin)

                    <a
                        href="{{ route('superadmin.pengguna.index') }}"
                        class="menu-link"
                    >

                        <span class="menu-icon">
                            ♟
                        </span>

                        Kelola Pengguna

                    </a>

                @endif


            </nav>

        </div>


        {{-- LOGOUT --}}

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

                    <span class="menu-icon">
                        ⇥
                    </span>

                    Keluar

                </button>

            </form>

        </div>


    </aside>


    {{-- =====================================================
         MAIN
    ====================================================== --}}

    <div class="main-wrapper">


        {{-- TOPBAR --}}

        <header class="topbar">


            <div>

                <div class="page-title">
                    Data Peserta Magang
                </div>

                <div class="page-subtitle">
                    Kelola data mahasiswa yang sedang atau telah melaksanakan magang.
                </div>

            </div>


            <div class="user-info">

                <div class="user-avatar">
                    {{ $userInitial }}
                </div>

                <div>

                    <div class="user-name">
                        {{ $userName }}
                    </div>

                    <div class="user-role">
                        {{ $roleLabel }}
                    </div>

                </div>

            </div>


        </header>


        {{-- CONTENT --}}

        <main class="content">


            {{-- SUCCESS MESSAGE --}}

            @if(session('success'))

                <div class="alert alert-success">
                    {{ session('success') }}
                </div>

            @endif


            {{-- HEADER --}}

            <div class="content-header">


                <div>

                    <div class="heading">
                        Peserta Magang
                    </div>

                    <div class="heading-description">
                        Daftar mahasiswa yang terdaftar sebagai peserta magang pada MAGENTA.
                    </div>

                </div>


                <a
                    href="{{ route('superadmin.peserta-magang.create') }}"
                    class="btn btn-primary"
                >

                    <span>
                        +
                    </span>

                    Tambah Peserta

                </a>


            </div>


            {{-- MAIN CARD --}}

            <section class="card">


                <div class="card-top">

                    <div>

                        <div class="card-title">
                            Daftar Peserta
                        </div>

                        <div class="card-info">

                            @if($pesertaMagangs->total() > 0)

                                Menampilkan
                                {{ $pesertaMagangs->firstItem() }}
                                -
                                {{ $pesertaMagangs->lastItem() }}
                                dari
                                {{ $pesertaMagangs->total() }}
                                peserta

                            @else

                                Belum ada data peserta.

                            @endif

                        </div>

                    </div>


                </div>


                @if($pesertaMagangs->count() > 0)

                    <div class="table-wrapper">

                        <table>

                            <thead>

                                <tr>

                                    <th>
                                        Peserta
                                    </th>

                                    <th>
                                        NIM
                                    </th>

                                    <th>
                                        Asal Kampus
                                    </th>

                                    <th>
                                        Program Studi
                                    </th>

                                    <th>
                                        Periode
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th>
                                        Aksi
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach($pesertaMagangs as $peserta)

                                    <tr>


                                        {{-- PESERTA --}}

                                        <td>

                                            <div class="name-cell">

                                                {{ $peserta->nama }}

                                            </div>

                                        </td>


                                        {{-- NIM --}}

                                        <td>

                                            {{ $peserta->nim ?: '-' }}

                                        </td>


                                        {{-- KAMPUS --}}

                                        <td>

                                            @if($peserta->asal_kampus)

                                                {{ $peserta->asal_kampus }}

                                            @else

                                                -

                                            @endif

                                        </td>


                                        {{-- PRODI --}}

                                        <td>

                                            @if($peserta->program_studi)

                                                {{ $peserta->program_studi }}

                                            @else

                                                -

                                            @endif

                                        </td>


                                        {{-- PERIODE --}}

                                        <td>

                                            @if($peserta->periode_mulai || $peserta->periode_selesai)

                                                {{ $peserta->periode_mulai
                                                    ? $peserta->periode_mulai->format('d M Y')
                                                    : '-'
                                                }}

                                                <div class="secondary-text">

                                                    s/d

                                                    {{ $peserta->periode_selesai
                                                        ? $peserta->periode_selesai->format('d M Y')
                                                        : '-'
                                                    }}

                                                </div>

                                            @else

                                                -

                                            @endif

                                        </td>


                                        {{-- STATUS --}}

                                        <td>

                                            @if($peserta->status === 'aktif')

                                                <span class="status-badge status-aktif">
                                                    Aktif
                                                </span>

                                            @else

                                                <span class="status-badge status-selesai">
                                                    Selesai
                                                </span>

                                            @endif

                                        </td>


                                        {{-- AKSI --}}

                                        <td>

                                            <div class="actions">


                                                {{-- DETAIL --}}

                                                <a
                                                    href="{{ route(
                                                        'superadmin.peserta-magang.show',
                                                        $peserta
                                                    ) }}"
                                                    class="action-link action-detail"
                                                >
                                                    Detail
                                                </a>


                                                {{-- EDIT --}}

                                                <a
                                                    href="{{ route(
                                                        'superadmin.peserta-magang.edit',
                                                        $peserta
                                                    ) }}"
                                                    class="action-link action-edit"
                                                >
                                                    Edit
                                                </a>


                                                {{-- HAPUS --}}

                                                <form
                                                    action="{{ route(
                                                        'superadmin.peserta-magang.destroy',
                                                        $peserta
                                                    ) }}"
                                                    method="POST"
                                                    onsubmit="return confirm(
                                                        'Yakin ingin menghapus data peserta ini? Data absensinya juga akan ikut terhapus.'
                                                    );"
                                                >

                                                    @csrf

                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="action-link action-delete"
                                                    >
                                                        Hapus
                                                    </button>

                                                </form>


                                            </div>

                                        </td>


                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>


                    {{-- PAGINATION --}}

                    <div class="pagination-wrapper">


                        <div class="pagination-info">

                            Halaman
                            {{ $pesertaMagangs->currentPage() }}
                            dari
                            {{ $pesertaMagangs->lastPage() }}

                        </div>


                        @if($pesertaMagangs->hasPages())

                            <div class="pagination">

                                {{-- Previous --}}

                                @if($pesertaMagangs->onFirstPage())

                                    <span>
                                        ‹
                                    </span>

                                @else

                                    <a
                                        href="{{ $pesertaMagangs->previousPageUrl() }}"
                                    >
                                        ‹
                                    </a>

                                @endif


                                {{-- Page Numbers --}}

                                @foreach(
                                    $pesertaMagangs->getUrlRange(
                                        1,
                                        $pesertaMagangs->lastPage()
                                    )
                                    as $page => $url
                                )

                                    @if(
                                        $page ===
                                        $pesertaMagangs->currentPage()
                                    )

                                        <span aria-current="page">
                                            {{ $page }}
                                        </span>

                                    @else

                                        <a href="{{ $url }}">
                                            {{ $page }}
                                        </a>

                                    @endif

                                @endforeach


                                {{-- Next --}}

                                @if($pesertaMagangs->hasMorePages())

                                    <a
                                        href="{{ $pesertaMagangs->nextPageUrl() }}"
                                    >
                                        ›
                                    </a>

                                @else

                                    <span>
                                        ›
                                    </span>

                                @endif

                            </div>

                        @endif


                    </div>


                @else


                    {{-- EMPTY STATE --}}

                    <div class="empty-state">

                        <div class="empty-icon">
                            ♙
                        </div>

                        <div class="empty-title">
                            Belum Ada Peserta Magang
                        </div>

                        <div class="empty-description">
                            Tambahkan data peserta magang untuk mulai menggunakan sistem absensi.
                        </div>

                        <a
                            href="{{ route('superadmin.peserta-magang.create') }}"
                            class="btn btn-primary"
                        >
                            + Tambah Peserta
                        </a>

                    </div>


                @endif


            </section>


        </main>


    </div>


</body>

</html>