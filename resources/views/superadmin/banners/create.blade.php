{{-- resources/views/superadmin/banners/create.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Kelola Banner')
@section('page_title', 'Kelola Banner')
@section('page_subtitle', 'Atur banner yang tampil pada halaman utama MAGENTA')

@push('styles')
<style>
    /* =====================================================
       CONTENT
    ====================================================== */

    .content {

        padding:
            30px 35px 50px;
    }

    /* =====================================================
       PAGE HEADER
    ====================================================== */

    .page-header {

        display: flex;

        align-items: center;

        justify-content:
            space-between;

        margin-bottom: 25px;
    }

    .page-header-left {

        display: flex;

        align-items: center;

        gap: 15px;
    }

    .page-header-icon {

        width: 52px;
        height: 52px;

        border-radius: 13px;

        background: #e8f3fc;

        color: #1769aa;

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 23px;

        border:
            1px solid #d6eafa;
    }

    .page-header h2 {

        font-size: 21px;

        color: #173b68;

        margin-bottom: 5px;
    }

    .page-header p {

        font-size: 13px;

        color: #7b8794;
    }

    /* =====================================================
       CURRENT BANNER
    ====================================================== */

    .banner-card {

        background: white;

        border:
            1px solid #e4eaf1;

        border-radius: 16px;

        box-shadow:
            0 5px 20px
            rgba(23,59,104,.05);

        overflow: hidden;

        margin-bottom: 22px;
    }

    .banner-card-header {

        padding: 20px 25px;

        border-bottom:
            1px solid #edf1f5;

        display: flex;

        align-items: center;

        justify-content:
            space-between;

        gap: 15px;
    }

    .banner-card-title {

        display: flex;

        align-items: center;

        gap: 12px;
    }

    .banner-icon {

        width: 38px;
        height: 38px;

        border-radius: 9px;

        background: #edf6fd;

        color: #1769aa;

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 17px;
    }

    .banner-card-header h3 {

        color: #173b68;

        font-size: 15px;

        margin-bottom: 3px;
    }

    .banner-card-header p {

        color: #8995a5;

        font-size: 11px;
    }

    .status-badge {

        display: inline-flex;

        align-items: center;

        gap: 6px;

        padding:
            7px 11px;

        border-radius: 20px;

        background: #ecfdf3;

        color: #167342;

        font-size: 11px;

        font-weight: bold;
    }

    .status-dot {

        width: 7px;
        height: 7px;

        border-radius: 50%;

        background: #22a35a;
    }

    .banner-body {

        padding: 25px;
    }

    .banner-preview {

        position: relative;

        overflow: hidden;

        width: 100%;

        min-height: 260px;

        border-radius: 12px;

        background:
            linear-gradient(
                135deg,
                #e9f3fb,
                #f5f8fc
            );

        margin-bottom: 20px;
    }

    .banner-preview img {

        display: block;

        width: 100%;

        height: 300px;

        object-fit: cover;

        border-radius: 12px;
    }

    .no-image {

        min-height: 260px;

        display: flex;

        flex-direction: column;

        align-items: center;

        justify-content: center;

        color: #8b99a9;

        gap: 10px;

        border:
            1px dashed #cbd5e1;

        border-radius: 12px;
    }

    .no-image-icon {

        width: 55px;
        height: 55px;

        border-radius: 13px;

        background: white;

        display: flex;

        align-items: center;

        justify-content: center;

        color: #1769aa;

        font-size: 23px;
    }

    .banner-info {

        display: grid;

        grid-template-columns:
            1fr auto;

        gap: 20px;

        align-items: center;
    }

    .banner-info h2 {

        color: #173b68;

        font-size: 20px;

        margin-bottom: 7px;
    }

    .banner-info p {

        color: #7b8794;

        font-size: 13px;

        line-height: 1.6;
    }

    /* =====================================================
       EMPTY STATE
    ====================================================== */

    .empty-banner {

        background: white;

        border:
            1px solid #e4eaf1;

        border-radius: 16px;

        padding: 30px;

        margin-bottom: 22px;

        box-shadow:
            0 5px 20px
            rgba(23,59,104,.05);
    }

    .empty-box {

        border:
            1px dashed #cbd5e1;

        border-radius: 12px;

        padding: 35px 25px;

        text-align: center;

        background: #fafcfe;
    }

    .empty-icon {

        width: 58px;
        height: 58px;

        border-radius: 14px;

        margin:
            0 auto 15px;

        background: #eaf4fc;

        color: #1769aa;

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 25px;
    }

    .empty-box h3 {

        color: #173b68;

        font-size: 17px;

        margin-bottom: 7px;
    }

    .empty-box p {

        color: #8995a5;

        font-size: 12px;

        line-height: 1.6;

        max-width: 500px;

        margin: auto;
    }

    /* =====================================================
       FORM CARD
    ====================================================== */

    .form-card {

        background: white;

        border:
            1px solid #e4eaf1;

        border-radius: 16px;

        box-shadow:
            0 5px 20px
            rgba(23,59,104,.05);

        overflow: hidden;
    }

    .form-header {

        padding: 21px 25px;

        border-bottom:
            1px solid #edf1f5;

        display: flex;

        align-items: center;

        gap: 12px;
    }

    .form-header-icon {

        width: 38px;
        height: 38px;

        border-radius: 9px;

        background: #fff5df;

        color: #d88b00;

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 17px;
    }

    .form-header h3 {

        color: #173b68;

        font-size: 15px;

        margin-bottom: 3px;
    }

    .form-header p {

        color: #8995a5;

        font-size: 11px;
    }

    .form-body {

        padding: 25px;
    }

    /* =====================================================
       FORM
    ====================================================== */

    .section-label {

        font-size: 11px;

        text-transform: uppercase;

        letter-spacing: 1px;

        color: #1769aa;

        font-weight: bold;

        margin-bottom: 17px;
    }

    .grid {

        display: grid;

        grid-template-columns:
            repeat(2, 1fr);

        gap: 20px;
    }

    .full {

        grid-column:
            1 / -1;
    }

    .form-group {

        margin-bottom: 3px;
    }

    .form-group label {

        display: block;

        font-size: 12px;

        font-weight: bold;

        color: #334155;

        margin-bottom: 8px;
    }

    .required {

        color: #e05252;
    }

    input,
    textarea,
    select {

        width: 100%;

        padding:
            12px 13px;

        border:
            1px solid #d5dce5;

        border-radius: 9px;

        font-size: 13px;

        font-family:
            Arial,
            Helvetica,
            sans-serif;

        color: #1f2937;

        background: white;

        transition: .2s;
    }

    input:hover,
    textarea:hover,
    select:hover {

        border-color: #b9c5d2;
    }

    input:focus,
    textarea:focus,
    select:focus {

        outline: none;

        border-color: #1769aa;

        box-shadow:
            0 0 0 3px
            rgba(23,105,170,.10);
    }

    textarea {

        min-height: 115px;

        resize: vertical;

        line-height: 1.6;
    }

    input[type="file"] {

        padding: 10px;

        cursor: pointer;

        background: #fafcfe;
    }

    input[type="number"] {

        max-width: 180px;
    }

    .help {

        margin-top: 6px;

        color: #8995a5;

        font-size: 11px;

        line-height: 1.5;
    }

    .form-divider {

        border: none;

        border-top:
            1px solid #edf1f5;

        margin:
            27px 0 22px;
    }

    /* =====================================================
       IMAGE PREVIEW
    ====================================================== */

    .preview-wrapper {

        display: none;

        margin-top: 15px;

        padding: 10px;

        background: #f7f9fc;

        border:
            1px solid #e4eaf1;

        border-radius: 11px;
    }

    .preview-label {

        font-size: 10px;

        font-weight: bold;

        color: #7b8794;

        margin-bottom: 8px;

        text-transform: uppercase;

        letter-spacing: .7px;
    }

    .preview {

        display: block;

        width: 100%;

        max-height: 280px;

        object-fit: cover;

        border-radius: 8px;
    }

    /* =====================================================
       ACTION
    ====================================================== */

    .actions {

        display: flex;

        align-items: center;

        justify-content:
            space-between;

        gap: 10px;

        padding-top: 23px;

        margin-top: 25px;

        border-top:
            1px solid #edf1f5;
    }

    .action-left {

        color: #8995a5;

        font-size: 11px;
    }

    .action-buttons {

        display: flex;

        gap: 10px;
    }

    .btn {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 7px;

        padding:
            11px 17px;

        border: none;

        border-radius: 9px;

        cursor: pointer;

        font-size: 12px;

        font-weight: bold;

        transition: .2s;
    }

    .btn-secondary {

        background: #eef1f5;

        color: #526171;
    }

    .btn-secondary:hover {

        background: #dfe5eb;
    }

    .btn-primary {

        background: #1769aa;

        color: white;

        box-shadow:
            0 4px 10px
            rgba(23,105,170,.18);
    }

    .btn-primary:hover {

        background: #0f4c81;

        transform:
            translateY(-1px);
    }

    .btn-danger {

        background: #fff0f0;

        color: #c53030;

        border:
            1px solid #ffd5d5;
    }

    .btn-danger:hover {

        background: #dc3545;

        color: white;
    }

    /* =====================================================
       FOOT NOTE
    ====================================================== */

    .foot-note {

        margin-top: 18px;

        color: #98a2b0;

        font-size: 11px;

        text-align: center;
    }

    /* =====================================================
       RESPONSIVE
    ====================================================== */

    @media (max-width: 900px) {

        .grid {

            grid-template-columns:
                1fr;
        }

        .full {

            grid-column: auto;
        }

        .banner-info {

            grid-template-columns: 1fr;
        }

    }

    @media (max-width: 800px) {

        .content {

            padding:
                22px 18px 35px;
        }

        .page-header {

            align-items: flex-start;

            flex-direction: column;
        }

        .form-body,
        .banner-body {

            padding: 20px;
        }

        .banner-card-header {

            align-items:
                flex-start;

            flex-direction: column;
        }

        .actions {

            align-items:
                flex-start;

            flex-direction: column;
        }

        .action-buttons {

            width: 100%;
        }

        .action-buttons .btn {

            flex: 1;
        }

    }

    @media (max-width: 500px) {

        .page-header-icon {

            width: 45px;
            height: 45px;
        }

        .page-header h2 {

            font-size: 19px;
        }

        .form-header,
        .banner-card-header {

            padding: 18px;
        }

        .form-body,
        .banner-body {

            padding: 18px;
        }

        .banner-preview img {

            height: 210px;
        }

    }

</style>
@endpush

@section('content')

<!-- CONTENT -->

<div class="content">

    <!-- PAGE HEADER -->

    <div class="page-header">

        <div class="page-header-left">

            <div class="page-header-icon">
                ▧
            </div>

            <div>

                <h2>
                    Kelola Banner Website
                </h2>

                <p>
                    Kelola gambar dan informasi banner
                    yang ditampilkan pada halaman utama.
                </p>

            </div>

        </div>

    </div>

    <!-- SUCCESS -->

    <x-alert type="success" :message="session('success')" />

    <!-- ERROR -->

    <x-alert
        type="error"
        title="Periksa data berikut:"
        :messages="$errors->all()"
    />

    <!-- =================================================
         BANNER SAAT INI
    ================================================== -->

    @if($banner)

        <div class="banner-card">

            <div class="banner-card-header">

                <div class="banner-card-title">

                    <div class="banner-icon">
                        ▧
                    </div>

                    <div>

                        <h3>
                            Banner Saat Ini
                        </h3>

                        <p>
                            Banner yang sedang digunakan
                            pada website MAGENTA.
                        </p>

                    </div>

                </div>

                <span class="status-badge">

                    <span class="status-dot"></span>

                    AKTIF

                </span>

            </div>

            <div class="banner-body">

                <div class="banner-preview">

                    @if($banner->gambar)

                        <img
                            src="{{ asset('storage/' . $banner->gambar) }}"
                            alt="{{ $banner->judul }}"
                        >

                    @else

                        <div class="no-image">

                            <div class="no-image-icon">
                                ▧
                            </div>

                            <span>
                                Belum ada gambar banner
                            </span>

                        </div>

                    @endif

                </div>

                <div class="banner-info">

                    <div>

                        <h2>
                            {{ $banner->judul }}
                        </h2>

                        @if($banner->deskripsi)

                            <p>
                                {{ $banner->deskripsi }}
                            </p>

                        @else

                            <p>
                                Tidak ada deskripsi banner.
                            </p>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    @else

        <!-- EMPTY -->

        <div class="empty-banner">

            <div class="empty-box">

                <div class="empty-icon">
                    ▧
                </div>

                <h3>
                    Belum Ada Banner
                </h3>

                <p>
                    Banner yang kamu simpan pertama kali
                    akan menjadi banner utama yang tampil
                    pada halaman website MAGENTA.
                </p>

            </div>

        </div>

    @endif

    <!-- =================================================
         FORM
    ================================================== -->

    <div class="form-card">

        <div class="form-header">

            <div class="form-header-icon">
                ✎
            </div>

            <div>

                <h3>

                    @if($banner)
                        Ganti Banner
                    @else
                        Buat Banner Pertama
                    @endif

                </h3>

                <p>
                    Masukkan informasi banner yang
                    ingin ditampilkan.
                </p>

            </div>

        </div>

        <div class="form-body">

            <form
                action="{{ route('superadmin.banners.store') }}"
                method="POST"
                enctype="multipart/form-data"
            >

                @csrf

                <!-- INFORMASI BANNER -->

                <div class="section-label">
                    Informasi Banner
                </div>

                <div class="grid">

                    <!-- JUDUL -->

                    <div class="form-group full">

                        <label for="judul">

                            Judul Banner

                            <span class="required">
                                *
                            </span>

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

                    <div class="form-group full">

                        <label for="deskripsi">
                            Deskripsi Banner
                        </label>

                        <textarea
                            id="deskripsi"
                            name="deskripsi"
                            placeholder="Masukkan deskripsi singkat banner"
                        >{{ old('deskripsi', $banner?->deskripsi ?? '') }}</textarea>

                        <div class="help">

                            Gunakan deskripsi singkat
                            yang mudah dibaca oleh
                            pengunjung website.

                        </div>

                    </div>

                    <!-- GAMBAR -->

                    <div class="form-group full">

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

                            Format:
                            JPG, JPEG, PNG, atau WEBP.

                            Maksimal 2 MB.

                            @if($banner && $banner->gambar)

                                <br>

                                Kosongkan jika tidak ingin
                                mengganti gambar yang sekarang.

                            @endif

                        </div>

                        <!-- PREVIEW -->

                        <div
                            class="preview-wrapper"
                            id="previewWrapper"
                        >

                            <div class="preview-label">
                                Preview gambar baru
                            </div>

                            <img
                                id="preview"
                                class="preview"
                                alt="Preview gambar baru"
                            >

                        </div>

                    </div>

                </div>

                <hr class="form-divider">

                <!-- TOMBOL -->

                <div class="section-label">
                    Tombol Banner
                </div>

                <div class="grid">

                    <!-- TEKS -->

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

                        <div class="help">

                            Contoh:
                            #pengajuan,
                            #persyaratan,
                            atau URL halaman tertentu.

                        </div>

                    </div>

                </div>

                <hr class="form-divider">

                <!-- PENGATURAN TAMPILAN -->

                <div class="section-label">
                    Pengaturan Tampilan
                </div>

                <div class="grid">

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

                        <div class="help">

                            Banner aktif akan dapat
                            ditampilkan pada halaman utama.

                        </div>

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

                            Angka lebih kecil akan
                            ditampilkan lebih dahulu.

                        </div>

                    </div>

                </div>

                <!-- ACTION -->

                <div class="actions">

                    <div class="action-left">

                        <span>

                            <b>*</b>
                            Wajib diisi

                        </span>

                    </div>

                    <div class="action-buttons">

                        <a
                            href="{{ route('superadmin.banners.index') }}"
                            class="btn btn-secondary"
                        >

                            ←
                            Kembali

                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >

                            ✓

                            @if($banner)

                                Simpan Perubahan

                            @else

                                Simpan Banner

                            @endif

                        </button>

                    </div>

                </div>

            </form>

            <!-- HAPUS -->

            @if($banner)

                <div
                    style="
                        margin-top: 18px;
                        padding-top: 18px;
                        border-top: 1px solid #edf1f5;
                    "
                >

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

                            🗑
                            Hapus Banner

                        </button>

                    </form>

                </div>

            @endif

        </div>

    </div>

    <div class="foot-note">

        MAGENTA &nbsp;•&nbsp;
        Sistem Informasi Magang

    </div>

</div>

@endsection

@push('scripts')
<script>

    const gambarInput =
        document.getElementById('gambar');

    const preview =
        document.getElementById('preview');

    const previewWrapper =
        document.getElementById('previewWrapper');

    gambarInput.addEventListener(
        'change',
        function () {

            const file = this.files[0];

            if (!file) {

                previewWrapper.style.display =
                    'none';

                preview.removeAttribute('src');

                return;
            }

            const allowedTypes = [

                'image/jpeg',

                'image/png',

                'image/webp'

            ];

            if (
                !allowedTypes.includes(
                    file.type
                )
            ) {

                alert(
                    'Format gambar harus JPG, JPEG, PNG, atau WEBP.'
                );

                this.value = '';

                previewWrapper.style.display =
                    'none';

                preview.removeAttribute('src');

                return;
            }

            if (
                file.size >
                2 * 1024 * 1024
            ) {

                alert(
                    'Ukuran gambar maksimal 2 MB.'
                );

                this.value = '';

                previewWrapper.style.display =
                    'none';

                preview.removeAttribute('src');

                return;
            }

            const reader =
                new FileReader();

            reader.onload =
                function (event) {

                    preview.src =
                        event.target.result;

                    previewWrapper.style.display =
                        'block';

                };

            reader.readAsDataURL(file);

        }
    );

</script>
@endpush
