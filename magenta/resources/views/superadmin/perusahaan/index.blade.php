<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Profil Perusahaan - MAGENTA
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
            max-width: 950px;
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

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .full {
            grid-column: 1 / -1;
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
            min-height: 130px;
            resize: vertical;
        }

        .help {
            margin-top: 6px;
            color: #6b7280;
            font-size: 13px;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 30px;
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

            .header,
            .card {
                padding: 20px;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .full {
                grid-column: auto;
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
            Profil Perusahaan
        </h1>

        <p>
            Kelola informasi perusahaan MAGENTA.
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


    <div class="card">


        <!-- INFO -->

        <div class="info-box">

            Data perusahaan dapat diubah langsung dari halaman ini.
            Kamu tidak perlu mengubah kode program setiap kali
            informasi perusahaan berubah.

        </div>


        <!-- FORM -->

        <form
            action="{{ route('superadmin.perusahaan.update') }}"
            method="POST"
        >

            @csrf

            @method('PUT')


            <div class="grid">


                <!-- NAMA PERUSAHAAN -->

                <div class="form-group full">

                    <label for="nama">
                        Nama Perusahaan
                    </label>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="{{ old('nama', $perusahaan?->nama ?? '') }}"
                        placeholder="Masukkan nama perusahaan"
                        required
                    >

                </div>


                <!-- ALAMAT -->

                <div class="form-group full">

                    <label for="alamat">
                        Alamat
                    </label>

                    <textarea
                        id="alamat"
                        name="alamat"
                        placeholder="Masukkan alamat lengkap perusahaan"
                    >{{ old('alamat', $perusahaan?->alamat ?? '') }}</textarea>

                </div>


                <!-- KOTA -->

                <div class="form-group">

                    <label for="kota">
                        Kota
                    </label>

                    <input
                        type="text"
                        id="kota"
                        name="kota"
                        value="{{ old('kota', $perusahaan?->kota ?? '') }}"
                        placeholder="Contoh: Jakarta"
                    >

                </div>


                <!-- TELEPON -->

                <div class="form-group">

                    <label for="telepon">
                        Nomor Telepon
                    </label>

                    <input
                        type="text"
                        id="telepon"
                        name="telepon"
                        value="{{ old('telepon', $perusahaan?->telepon ?? '') }}"
                        placeholder="Contoh: 08123456789"
                    >

                </div>


                <!-- EMAIL -->

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $perusahaan?->email ?? '') }}"
                        placeholder="Contoh: info@perusahaan.com"
                    >

                </div>


                <!-- WEBSITE -->

                <div class="form-group">

                    <label for="website">
                        Website
                    </label>

                    <input
                        type="text"
                        id="website"
                        name="website"
                        value="{{ old('website', $perusahaan?->website ?? '') }}"
                        placeholder="Contoh: https://www.perusahaan.com"
                    >

                    <div class="help">
                        Kosongkan jika perusahaan tidak memiliki website.
                    </div>

                </div>


                <!-- DESKRIPSI -->

                <div class="form-group full">

                    <label for="deskripsi">
                        Deskripsi Perusahaan
                    </label>

                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        placeholder="Masukkan deskripsi singkat perusahaan"
                    >{{ old('deskripsi', $perusahaan?->deskripsi ?? '') }}</textarea>

                </div>


            </div>


            <!-- BUTTON -->

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

</div>


</body>
</html>