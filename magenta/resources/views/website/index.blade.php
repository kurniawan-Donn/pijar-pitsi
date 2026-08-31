<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MAGENTA - Sistem Informasi Magang</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        body {
            font-family: Arial, sans-serif;
            color: #222;
            background: #f7f8fc;
        }

        nav {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: white;
            padding: 18px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #7c3aed;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 25px;
        }

        nav ul li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        nav ul li a:hover {
            color: #7c3aed;
        }

        section {
            min-height: 100vh;
            padding: 90px 8%;
        }

        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 50px;
            background: linear-gradient(135deg, #f5f3ff, #ffffff);
        }

        .hero-text {
            max-width: 600px;
        }

        .hero-text h1 {
            font-size: 52px;
            margin-bottom: 20px;
            color: #1f2937;
        }

        .hero-text h1 span {
            color: #7c3aed;
        }

        .hero-text p {
            font-size: 18px;
            line-height: 1.7;
            margin-bottom: 30px;
            color: #555;
        }

        .btn {
            display: inline-block;
            padding: 14px 25px;
            background: #7c3aed;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
        }

        .btn:hover {
            background: #6d28d9;
        }

        .hero-box {
            width: 380px;
            height: 300px;
            background: #ede9fe;
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: bold;
            color: #7c3aed;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 36px;
            margin-bottom: 15px;
        }

        .section-title p {
            color: #666;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.07);
        }

        .card h3 {
            margin-bottom: 15px;
            color: #7c3aed;
        }

        .card p,
        .card li {
            line-height: 1.7;
            color: #555;
        }

        .card ul {
            padding-left: 20px;
        }

        .timeline {
            max-width: 800px;
            margin: auto;
        }

        .timeline-item {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-left: 5px solid #7c3aed;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.06);
        }

        .form-container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 13px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-button {
            border: none;
            cursor: pointer;
        }

        .status-box {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            text-align: center;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-error ul {
            margin-top: 10px;
            padding-left: 20px;
        }

        footer {
            background: #1f2937;
            color: white;
            padding: 40px 8%;
            text-align: center;
        }

        @media (max-width: 900px) {
            .hero {
                flex-direction: column;
                text-align: center;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            nav {
                flex-direction: column;
                gap: 15px;
            }

            nav ul {
                gap: 12px;
                flex-wrap: wrap;
                justify-content: center;
            }

            .hero-text h1 {
                font-size: 40px;
            }

            .hero-box {
                width: 100%;
                max-width: 380px;
            }
        }
    </style>
</head>

<body>

    <!-- =========================
         NAVBAR
    ========================== -->
    <nav>
        <div class="logo">MAGENTA</div>

        <ul>
            <li><a href="#beranda">Beranda</a></li>
            <li><a href="#tentang">Tentang</a></li>
            <li><a href="#persyaratan">Persyaratan</a></li>
            <li><a href="#alur">Alur</a></li>
            <li><a href="#pengajuan">Pengajuan</a></li>
            <li><a href="#status">Cek Status</a></li>
        </ul>
    </nav>


    <!-- =========================
         BERANDA
    ========================== -->
    <section id="beranda" class="hero">

        <div class="hero-text">

            <h1>
                Sistem Informasi
                <span>Magang</span>
            </h1>

            <p>
                MAGENTA merupakan sistem informasi magang terintegrasi
                untuk membantu proses pengajuan, pengelolaan,
                dan pemantauan kegiatan magang.
            </p>

            <a href="#pengajuan" class="btn">
                Ajukan Magang
            </a>

        </div>

        <div class="hero-box">
            MAGENTA
        </div>

    </section>


    <!-- =========================
         TENTANG
    ========================== -->
    <section id="tentang">

        <div class="section-title">

            <h2>Tentang MAGENTA</h2>

            <p>
                Sistem informasi untuk mengelola kegiatan magang.
            </p>

        </div>

        <div class="cards">

            <div class="card">
                <h3>Pengajuan</h3>

                <p>
                    Mahasiswa dapat mengajukan kegiatan magang
                    melalui sistem dengan mudah.
                </p>
            </div>

            <div class="card">
                <h3>Pengelolaan</h3>

                <p>
                    Admin dapat mengelola mahasiswa,
                    perusahaan, dan pengajuan magang.
                </p>
            </div>

            <div class="card">
                <h3>Pemantauan</h3>

                <p>
                    Proses magang dapat dipantau secara
                    terstruktur melalui sistem.
                </p>
            </div>

        </div>

    </section>


    <!-- =========================
         PERSYARATAN
    ========================== -->
    <section id="persyaratan">

        <div class="section-title">

            <h2>Persyaratan Magang</h2>

            <p>
                Persyaratan yang harus dipenuhi mahasiswa.
            </p>

        </div>

        <div class="cards">

            <div class="card">

                <h3>Mahasiswa</h3>

                <ul>
                    <li>Mahasiswa aktif</li>
                    <li>Memenuhi ketentuan program studi</li>
                    <li>Melengkapi data mahasiswa</li>
                </ul>

            </div>

            <div class="card">

                <h3>Dokumen</h3>

                <ul>
                    <li>Surat pengantar</li>
                    <li>Proposal / CV</li>
                    <li>Dokumen pendukung lainnya</li>
                </ul>

            </div>

            <div class="card">

                <h3>Ketentuan</h3>

                <ul>
                    <li>Memiliki tempat magang</li>
                    <li>Mengikuti jadwal yang ditentukan</li>
                    <li>Mengikuti aturan instansi</li>
                </ul>

            </div>

        </div>

    </section>


    <!-- =========================
         ALUR MAGANG
    ========================== -->
    <section id="alur">

        <div class="section-title">

            <h2>Alur Magang</h2>

            <p>
                Proses pengajuan sampai kegiatan magang selesai.
            </p>

        </div>

        <div class="timeline">

            <div class="timeline-item">

                <h3>1. Pengajuan</h3>

                <p>
                    Mahasiswa mengisi formulir pengajuan magang.
                </p>

            </div>

            <div class="timeline-item">

                <h3>2. Verifikasi</h3>

                <p>
                    Admin memeriksa kelengkapan data pengajuan.
                </p>

            </div>

            <div class="timeline-item">

                <h3>3. Persetujuan</h3>

                <p>
                    Pengajuan disetujui atau ditolak oleh
                    pihak yang berwenang.
                </p>

            </div>

            <div class="timeline-item">

                <h3>4. Pelaksanaan</h3>

                <p>
                    Mahasiswa melaksanakan kegiatan magang.
                </p>

            </div>

            <div class="timeline-item">

                <h3>5. Laporan & Penilaian</h3>

                <p>
                    Mahasiswa mengumpulkan laporan dan
                    mendapatkan penilaian.
                </p>

            </div>

        </div>

    </section>


    <!-- =========================
         PENGAJUAN MAGANG
    ========================== -->
    <section id="pengajuan">

        <div class="section-title">

            <h2>Pengajuan Magang</h2>

            <p>
                Isi data untuk mengajukan kegiatan magang.
            </p>

        </div>

        <div class="form-container">

            @if(session('success'))

                <div class="alert-success">
                    {{ session('success') }}
                </div>

            @endif


            @if($errors->any())

                <div class="alert-error">

                    <strong>
                        Periksa data berikut:
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


            <form
                action="{{ route('admin.pengajuan.store') }}"
                method="POST"
            >

                @csrf


                <!-- NIM -->
                <div class="form-group">

                    <label for="nim">
                        NIM
                    </label>

                    <input
                        type="text"
                        id="nim"
                        name="nim"
                        placeholder="Masukkan NIM"
                        value="{{ old('nim') }}"
                        required
                    >

                </div>


                <!-- Nama -->
                <div class="form-group">

                    <label for="nama">
                        Nama Mahasiswa
                    </label>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        placeholder="Masukkan nama lengkap"
                        value="{{ old('nama') }}"
                        required
                    >

                </div>


                <!-- Email -->
                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Masukkan email"
                        value="{{ old('email') }}"
                        required
                    >

                </div>


                <!-- Perusahaan -->
                <div class="form-group">

                    <label for="nama_perusahaan">
                        Perusahaan / Instansi
                    </label>

                    <input
                        type="text"
                        id="nama_perusahaan"
                        name="nama_perusahaan"
                        placeholder="Nama perusahaan / instansi"
                        value="{{ old('nama_perusahaan') }}"
                        required
                    >

                </div>


                <!-- Tanggal Mulai -->
                <div class="form-group">

                    <label for="tanggal_mulai">
                        Tanggal Mulai
                    </label>

                    <input
                        type="date"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        value="{{ old('tanggal_mulai') }}"
                        required
                    >

                </div>


                <!-- Tanggal Selesai -->
                <div class="form-group">

                    <label for="tanggal_selesai">
                        Tanggal Selesai
                    </label>

                    <input
                        type="date"
                        id="tanggal_selesai"
                        name="tanggal_selesai"
                        value="{{ old('tanggal_selesai') }}"
                        required
                    >

                </div>


                <!-- Posisi -->
                <div class="form-group">

                    <label for="posisi">
                        Posisi
                    </label>

                    <input
                        type="text"
                        id="posisi"
                        name="posisi"
                        placeholder="Contoh: Web Developer"
                        value="{{ old('posisi') }}"
                        required
                    >

                </div>


                <!-- Keterangan -->
                <div class="form-group">

                    <label for="keterangan">
                        Keterangan
                    </label>

                    <textarea
                        id="keterangan"
                        name="keterangan"
                        placeholder="Keterangan tambahan"
                    >{{ old('keterangan') }}</textarea>

                </div>


                <!-- Tombol -->
                <button
                    type="submit"
                    class="btn form-button"
                >
                    Kirim Pengajuan
                </button>

            </form>

        </div>

    </section>


    <!-- =========================
         CEK STATUS
    ========================== -->
    <section id="status">

        <div class="section-title">

            <h2>Cek Status Pengajuan</h2>

            <p>
                Periksa status pengajuan magang menggunakan NIM.
            </p>

        </div>

        <div class="status-box">

            <div class="form-group">

                <label for="cek_nim">
                    NIM
                </label>

                <input
                    type="text"
                    id="cek_nim"
                    placeholder="Masukkan NIM"
                >

            </div>

            <button
                type="button"
                class="btn form-button"
            >
                Cek Status
            </button>

        </div>

    </section>


    <!-- =========================
         KONTAK
    ========================== -->
    <section>

        <div class="section-title">

            <h2>Kontak</h2>

            <p>
                Hubungi bagian pengelola magang untuk
                informasi lebih lanjut.
            </p>

        </div>

        <div class="cards">

            <div class="card">

                <h3>Email</h3>

                <p>
                    magang@example.com
                </p>

            </div>

            <div class="card">

                <h3>Telepon</h3>

                <p>
                    08xxxxxxxxxx
                </p>

            </div>

            <div class="card">

                <h3>Alamat</h3>

                <p>
                    Kampus / Institusi Anda
                </p>

            </div>

        </div>

    </section>


    <!-- =========================
         FOOTER
    ========================== -->
    <footer>

        <p>
            © 2026 MAGENTA -
            Sistem Informasi Magang Terintegrasi
        </p>

    </footer>

</body>
</html>