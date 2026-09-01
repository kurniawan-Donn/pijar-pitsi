<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Kelola Banner - MAGENTA
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
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .header {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
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
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .current-banner {
            background: #faf5ff;
            border: 1px solid #e9d5ff;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .current-banner h2 {
            color: #6d28d9;
            margin-bottom: 15px;
            font-size: 19px;
        }

        .current-image {
            width: 100%;
            max-height: 320px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .current-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .current-description {
            color: #6b7280;
            line-height: 1.6;
        }

        .status {
            display: inline-block;
            margin-top: 12px;
            padding: 6px 12px;
            border-radius: 20px;
            background: #dcfce7;
            color: #166534;
            font-size: 13px;
            font-weight: bold;
        }

        .form-title {
            margin-bottom: 20px;
            color: #374151;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 13px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            font-family: Arial, sans-serif;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #7c3aed;
        }

        textarea {
            min-height: 130px;
            resize: vertical;
        }

        .help {
            margin-top: 6px;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.5;
        }

        .preview {
            display: none;
            width: 100%;
            max-height: 320px;
            object-fit: cover;
            border-radius: 10px;
            margin-top: 15px;
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
            padding-left: 20px;
            margin-top: 8px;
        }

        .info {
            padding: 15px;
            background: #f3f4f6;
            border-radius: 10px;
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 25px;
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

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        @media (max-width: 600px) {

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
            Kelola Banner MAGENTA
        </h1>

        <p>
            Dari halaman ini Super Admin dapat mengganti banner
            yang tampil pada halaman utama website.
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


        <!-- BANNER SAAT INI -->

        @if($banner)

            <div class="current-banner">

                <h2>
                    Banner Saat Ini
                </h2>


                @if($banner->gambar)

                    <img
                        src="{{ asset('storage/' . $banner->gambar) }}"
                        alt="{{ $banner->judul }}"
                        class="current-image"
                    >

                @endif


                <div class="current-title">
                    {{ $banner->judul }}
                </div>


                @if($banner->deskripsi)

                    <div class="current-description">
                        {{ $banner->deskripsi }}
                    </div>

                @endif


                <span class="status">
                    AKTIF
                </span>

            </div>

        @else

            <div class="info">

                <strong>
                    Belum ada banner.
                </strong>

                <br>

                Banner yang kamu simpan pertama kali akan menjadi
                banner utama website.

            </div>

        @endif


        <!-- FORM KELOLA BANNER -->

        <h2 class="form-title">

            @if($banner)
                Ganti Banner
            @else
                Buat Banner Pertama
            @endif

        </h2>


        <form
            action="{{ route('superadmin.banners.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf


            <!-- JUDUL -->

            <div class="form-group">

                <label for="judul">
                    Judul Banner
                </label>

                <input
                    type="text"
                    id="judul"
                    name="judul"
                    value="{{ old('judul', $banner?->judul ?? '') }}"
                    placeholder="Contoh: Selamat Datang di MAGENTA"
                    required
                >

            </div>


            <!-- DESKRIPSI -->

            <div class="form-group">

                <label for="deskripsi">
                    Deskripsi Banner
                </label>

                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    placeholder="Masukkan deskripsi banner"
                >{{ old('deskripsi', $banner?->deskripsi ?? '') }}</textarea>

            </div>


            <!-- GAMBAR -->

            <div class="form-group">

                <label for="gambar">

                    @if($banner && $banner->gambar)
                        Ganti Gambar
                    @else
                        Upload Gambar
                    @endif

                </label>

                <input
                    type="file"
                    id="gambar"
                    name="gambar"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <div class="help">

                    JPG, JPEG, PNG, atau WEBP.
                    Maksimal 2 MB.

                    @if($banner && $banner->gambar)
                        <br>
                        Kosongkan jika tidak ingin mengganti gambar.
                    @endif

                </div>

                <img
                    id="preview"
                    class="preview"
                    alt="Preview gambar baru"
                >

            </div>


            <!-- TOMBOL -->

            <div class="form-group">

                <label for="tombol_teks">
                    Teks Tombol
                </label>

                <input
                    type="text"
                    id="tombol_teks"
                    name="tombol_teks"
                    value="{{ old('tombol_teks', $banner?->tombol_teks ?? '') }}"
                    placeholder="Contoh: Ajukan Magang"
                >

            </div>


            <!-- LINK -->

            <div class="form-group">

                <label for="tombol_link">
                    Link Tombol
                </label>

                <input
                    type="text"
                    id="tombol_link"
                    name="tombol_link"
                    value="{{ old('tombol_link', $banner?->tombol_link ?? '#pengajuan') }}"
                    placeholder="Contoh: #pengajuan"
                >

            </div>


            <!-- STATUS -->

            <div class="form-group">

                <label for="status">
                    Status
                </label>

                <select
                    name="status"
                    id="status"
                >

                    <option
                        value="aktif"
                        {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}
                    >
                        Aktif
                    </option>

                    <option
                        value="nonaktif"
                        {{ old('status') === 'nonaktif' ? 'selected' : '' }}
                    >
                        Nonaktif
                    </option>

                </select>

            </div>


            <!-- URUTAN -->

            <div class="form-group">

                <label for="urutan">
                    Urutan
                </label>

                <input
                    type="number"
                    id="urutan"
                    name="urutan"
                    value="{{ old('urutan', $banner?->urutan ?? 0) }}"
                    min="0"
                >

                <div class="help">
                    Gunakan 0 untuk banner utama.
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

                    @if($banner)
                        Simpan Perubahan
                    @else
                        Simpan Banner
                    @endif

                </button>

            </div>

        </form>


        <!-- HAPUS BANNER -->

        @if($banner)

            <div class="actions">

                <form
                    action="{{ route('superadmin.banners.destroy', $banner) }}"
                    method="POST"
                    onsubmit="return confirm('Yakin ingin menghapus banner ini?')"
                >

                    @csrf

                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn btn-danger"
                    >
                        Hapus Banner
                    </button>

                </form>

            </div>

        @endif


    </div>

</div>


<script>

    const gambarInput = document.getElementById('gambar');
    const preview = document.getElementById('preview');

    gambarInput.addEventListener('change', function () {

        const file = this.files[0];

        if (!file) {

            preview.style.display = 'none';
            preview.removeAttribute('src');

            return;
        }


        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];


        if (!allowedTypes.includes(file.type)) {

            alert(
                'Format gambar harus JPG, JPEG, PNG, atau WEBP.'
            );

            this.value = '';

            preview.style.display = 'none';
            preview.removeAttribute('src');

            return;
        }


        if (file.size > 2 * 1024 * 1024) {

            alert(
                'Ukuran gambar maksimal 2 MB.'
            );

            this.value = '';

            preview.style.display = 'none';
            preview.removeAttribute('src');

            return;
        }


        const reader = new FileReader();


        reader.onload = function (event) {

            preview.src = event.target.result;

            preview.style.display = 'block';

        };


        reader.readAsDataURL(file);

    });

</script>


</body>
</html>