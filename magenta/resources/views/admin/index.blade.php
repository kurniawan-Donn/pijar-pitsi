<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - MAGENTA</title>

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

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: #7c3aed;
            color: white;
            padding: 25px 18px;
        }

        .logo {
            text-align: center;
            font-size: 25px;
            font-weight: bold;
            margin-bottom: 35px;
        }

        .menu a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 13px 15px;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .menu a:hover {
            background: rgba(255,255,255,0.15);
        }

        .main {
            margin-left: 250px;
            min-height: 100vh;
        }

        .topbar {
            background: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .topbar h1 {
            font-size: 24px;
        }

        .user-info {
            color: #666;
        }

        .content {
            padding: 30px;
        }

        .welcome {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .welcome h2 {
            margin-bottom: 10px;
            color: #7c3aed;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .card h3 {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }

        .number {
            font-size: 32px;
            font-weight: bold;
            color: #7c3aed;
        }

        .logout-btn {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #ef4444;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        @media (max-width: 1000px) {
            .cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 700px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main {
                margin-left: 0;
            }

            .cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="logo">
            MAGENTA
        </div>

        <div class="menu">

            <a href="{{ route('admin.dashboard') }}">
                Dashboard
            </a>

            <a href="#">
                Data Mahasiswa
            </a>

            <a href="{{ route('admin.pengajuan.index') }}">
                Pengajuan Magang
            </a>

            <a href="#">
                Data Perusahaan
            </a>

            <a href="#">
                Data Magang
            </a>

            <a href="#">
                Absensi
            </a>

            <a href="#">
                Logbook
            </a>

            <a href="#">
                Laporan
            </a>

            <a href="#">
                Penilaian
            </a>

        </div>

        <form action="{{ route('logout') }}" method="POST">

            @csrf

            <button type="submit" class="logout-btn">
                Logout
            </button>

        </form>

    </aside>


    <!-- MAIN -->
    <main class="main">

        <header class="topbar">

            <h1>
                Dashboard Admin
            </h1>

            <div class="user-info">

                {{ auth()->user()->name }}

            </div>

        </header>


        <div class="content">

            <div class="welcome">

                <h2>
                    Selamat Datang di MAGENTA
                </h2>

                <p>
                    Kelola kegiatan magang mahasiswa melalui
                    dashboard Admin.
                </p>

            </div>


            <div class="cards">

                <div class="card">

                    <h3>
                        Total Mahasiswa
                    </h3>

                    <div class="number">
                        0
                    </div>

                </div>


                <div class="card">

                    <h3>
                        Total Perusahaan
                    </h3>

                    <div class="number">
                        0
                    </div>

                </div>


                <div class="card">

                    <h3>
                        Pengajuan Menunggu
                    </h3>

                    <div class="number">
                        0
                    </div>

                </div>


                <div class="card">

                    <h3>
                        Magang Aktif
                    </h3>

                    <div class="number">
                        0
                    </div>

                </div>

            </div>

        </div>

    </main>

</body>
</html>