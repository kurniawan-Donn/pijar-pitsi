<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Pengaturan Website - MAGENTA
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

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .header {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .header h1 {
            color: #7c3aed;
            margin-bottom: 8px;
        }

        .header p {
            color: #6b7280;
            line-height: 1.6;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .card h2 {
            margin-bottom: 20px;
            color: #374151;
        }

        .info-box {
            background: #faf5ff;
            border: 1px solid #e9d5ff;
            padding: 18px;
            border-radius: 10px;
            margin-bottom: 25px;
            color: #4b5563;
            line-height: 1.6;
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 13px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            font-family: Arial, sans-serif;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.10);
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .card-editor {
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 15px;
            background: #fafafa;
        }

        .card-editor h3 {
            color: #7c3aed;
            margin-bottom: 18px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn {
            display: inline-block;
            padding: 13px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
        }

        .btn-primary {
            background: #7c3aed;
            color: white;
        }

        .btn-primary:hover {
            background: #6d28d9;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        @media (max-width: 700px) {

            .container {
                margin: 20px auto;
            }

            .card,
            .header {
                padding: 20px;
            }

            .actions {
                flex-direction: column;
            }

            .actions .btn {
                width: 100%;
                text-align: center;
            }

        }

    </style>

</head>


<body>


<div class="container">


    <!-- HEADER -->

    <div class="header">

        <h1>
            Pengaturan Website
        </h1>

        <p>
            Ubah tampilan dan isi bagian Tentang Sistem MAGENTA
            tanpa perlu mengubah kode website.
        </p>

    </div>


    <!-- SUCCESS -->

    @if(session('success'))

        <div class="alert-success">
            {{ session('success') }}
        </div>

    @endif


    <!-- ERROR -->

    @if($errors->any())

        <div class="alert-error">

            <strong>
                Terdapat kesalahan:
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


    <!-- FORM -->

    <form
        action="{{ route('superadmin.pengaturan.update') }}"
        method="POST"
    >

        @csrf

        @method('PUT')


        <!-- =================================================
             TENTANG SISTEM
        ================================================== -->

        <div class="card">

            <h2>
                Tentang Sistem MAGENTA
            </h2>

            <div class="info-box">

                Bagian ini digunakan untuk menjelaskan
                sistem MAGENTA kepada pengunjung website.

            </div>


            <div class="form-group">

                <label for="tentang_judul">
                    Judul
                </label>

                <input
                    type="text"
                    id="tentang_judul"
                    name="tentang_judul"
                    value="{{ old('tentang_judul', $pengaturan?->tentang_judul ?? 'Tentang Sistem MAGENTA') }}"
                    placeholder="Contoh: Tentang Sistem MAGENTA"
                    required
                >

            </div>


            <div class="form-group">

                <label for="tentang_deskripsi">
                    Deskripsi
                </label>

                <textarea
                    id="tentang_deskripsi"
                    name="tentang_deskripsi"
                    placeholder="Masukkan deskripsi sistem MAGENTA"
                >{{ old('tentang_deskripsi', $pengaturan?->tentang_deskripsi ?? '') }}</textarea>

            </div>

        </div>


        <!-- =================================================
             KARTU 1
        ================================================== -->

        <div class="card">

            <h2>
                Kartu 1
            </h2>

            <div class="card-editor">

                <h3>
                    Informasi Kartu 1
                </h3>


                <div class="form-group">

                    <label for="tentang_card1_judul">
                        Judul Kartu
                    </label>

                    <input
                        type="text"
                        id="tentang_card1_judul"
                        name="tentang_card1_judul"
                        value="{{ old('tentang_card1_judul', $pengaturan?->tentang_card1_judul ?? 'Pengajuan') }}"
                        placeholder="Contoh: Pengajuan"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="tentang_card1_deskripsi">
                        Deskripsi Kartu
                    </label>

                    <textarea
                        id="tentang_card1_deskripsi"
                        name="tentang_card1_deskripsi"
                        placeholder="Deskripsi kartu pertama"
                    >{{ old('tentang_card1_deskripsi', $pengaturan?->tentang_card1_deskripsi ?? '') }}</textarea>

                </div>

            </div>

        </div>


        <!-- =================================================
             KARTU 2
        ================================================== -->

        <div class="card">

            <h2>
                Kartu 2
            </h2>

            <div class="card-editor">

                <h3>
                    Informasi Kartu 2
                </h3>


                <div class="form-group">

                    <label for="tentang_card2_judul">
                        Judul Kartu
                    </label>

                    <input
                        type="text"
                        id="tentang_card2_judul"
                        name="tentang_card2_judul"
                        value="{{ old('tentang_card2_judul', $pengaturan?->tentang_card2_judul ?? 'Pengelolaan') }}"
                        placeholder="Contoh: Pengelolaan"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="tentang_card2_deskripsi">
                        Deskripsi Kartu
                    </label>

                    <textarea
                        id="tentang_card2_deskripsi"
                        name="tentang_card2_deskripsi"
                        placeholder="Deskripsi kartu kedua"
                    >{{ old('tentang_card2_deskripsi', $pengaturan?->tentang_card2_deskripsi ?? '') }}</textarea>

                </div>

            </div>

        </div>


        <!-- =================================================
             KARTU 3
        ================================================== -->

        <div class="card">

            <h2>
                Kartu 3
            </h2>

            <div class="card-editor">

                <h3>
                    Informasi Kartu 3
                </h3>


                <div class="form-group">

                    <label for="tentang_card3_judul">
                        Judul Kartu
                    </label>

                    <input
                        type="text"
                        id="tentang_card3_judul"
                        name="tentang_card3_judul"
                        value="{{ old('tentang_card3_judul', $pengaturan?->tentang_card3_judul ?? 'Pemantauan') }}"
                        placeholder="Contoh: Pemantauan"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="tentang_card3_deskripsi">
                        Deskripsi Kartu
                    </label>

                    <textarea
                        id="tentang_card3_deskripsi"
                        name="tentang_card3_deskripsi"
                        placeholder="Deskripsi kartu ketiga"
                    >{{ old('tentang_card3_deskripsi', $pengaturan?->tentang_card3_deskripsi ?? '') }}</textarea>

                </div>

            </div>

        </div>


        <!-- =================================================
             BUTTON
        ================================================== -->

        <div class="actions">

            <a
                href="{{ route('superadmin.dashboard') }}"
                class="btn btn-secondary"
            >
                Kembali
            </a>


            <button
                type="submit"
                class="btn btn-primary"
            >
                Simpan Perubahan
            </button>

        </div>


    </form>


</div>


</body>

</html>