{{-- resources/views/superadmin/banners/index.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Kelola Banner')
@section('page_title', 'Kelola Banner')
@section('page_subtitle', 'Kelola banner utama website PILARA')

@push('styles')
<style>
    body {
        font-family: var(--font);
        background: var(--latar);
        color: var(--utama-gelap);
    }

    button,
    input,
    textarea {
        font-family: var(--font);
    }

    /* =====================================================
       CONTENT
    ====================================================== */

    .content {
        padding: 28px 30px 40px;
    }

    /* =====================================================
       PAGE HEADER
    ====================================================== */

    .page-header {
        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 15px;

        margin-bottom: 18px;
    }

    .page-header-left {
        display: flex;

        align-items: center;

        gap: 13px;
    }

    .page-header-icon {
        width: 42px;

        height: 42px;

        min-width: 42px;

        border-radius: 9px;

        background: var(--utama-muda);

        border:
            1px solid var(--utama-muda);

        color: var(--utama);

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 17px;
    }

    .page-header h2 {
        color: var(--utama-gelap);

        font-size: 17px;

        line-height: 1.2;
    }

    .page-header p {
        margin-top: 4px;

        color: var(--teks-sekunder);

        font-size: 10px;

        line-height: 1.5;
    }

    /* =====================================================
       NOTICE
    ====================================================== */

    .notice {
        display: flex;

        align-items: flex-start;

        gap: 11px;

        margin-bottom: 18px;

        padding: 14px 16px;

        border:
            1px solid #f3dfb0;

        border-left:
            4px solid var(--peringatan);

        border-radius: 10px;

        background: linear-gradient(
            135deg,
            var(--peringatan-muda),
            var(--peringatan-muda)
        );
    }

    .notice-icon {
        width: 27px;

        height: 27px;

        min-width: 27px;

        border-radius: 7px;

        background: #fff0c7;

        color: var(--peringatan);

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 12px;

        font-weight: 700;
    }

    .notice strong {
        display: block;

        margin-bottom: 3px;

        color: var(--peringatan);

        font-size: 11px;
    }

    .notice p {
        color: var(--peringatan);

        font-size: 9px;

        line-height: 1.6;
    }

    /* =====================================================
       BANNER LAYOUT
    ====================================================== */

    .banner-layout {
        display: grid;

        grid-template-columns:
            minmax(0, 1.25fr)
            minmax(340px, .75fr);

        gap: 20px;

        align-items: start;
    }

    /* =====================================================
       CARD
    ====================================================== */

    .card {
        background: #ffffff;

        border:
            1px solid var(--garis);

        border-radius: 12px;

        box-shadow:
            0 4px 16px
            rgba(18, 52, 91, .05);

        overflow: hidden;
    }

    .card-header {
        padding: 17px 20px;

        border-bottom:
            1px solid var(--garis);
    }

    .card-header h3 {
        color: var(--utama-gelap);

        font-size: 12px;

        line-height: 1.2;
    }

    .card-header p {
        margin-top: 4px;

        color: var(--teks-sekunder);

        font-size: 9px;

        line-height: 1.5;
    }

    /* =====================================================
       PREVIEW
    ====================================================== */

    .preview-area {
        padding: 20px;
    }

    .current-label {
        display: inline-flex;

        align-items: center;

        gap: 6px;

        margin-bottom: 12px;

        padding: 6px 10px;

        border-radius: 20px;

        background: var(--sukses-muda);

        color: var(--sukses);

        font-size: 9px;

        font-weight: 700;
    }

    .current-banner {
        overflow: hidden;

        border:
            1px solid var(--garis);

        border-radius: 10px;

        background: var(--permukaan-redup);
    }

    .current-banner-image {
        display: block;

        width: 100%;

        height: 290px;

        object-fit: cover;

        background: var(--garis);
    }

    .no-image {
        width: 100%;

        height: 290px;

        display: flex;

        flex-direction: column;

        align-items: center;

        justify-content: center;

        background: var(--garis);

        color: var(--teks-redup);

        font-size: 10px;
    }

    .no-image-icon {
        margin-bottom: 9px;

        font-size: 34px;
    }

    .preview-info {
        padding: 16px;

        background: #ffffff;
    }

    .preview-info h4 {
        color: var(--utama-gelap);

        font-size: 15px;

        line-height: 1.35;
    }

    .preview-description {
        margin-top: 7px;

        color: var(--teks-sekunder);

        font-size: 10px;

        line-height: 1.65;

        white-space: pre-line;
    }

    .preview-button {
        display: inline-block;

        margin-top: 11px;

        padding: 7px 11px;

        border-radius: 6px;

        background: var(--utama);

        color: #ffffff;

        font-size: 9px;

        font-weight: 700;
    }

    .preview-link {
        margin-top: 7px;

        color: var(--teks-sekunder);

        font-size: 8px;

        line-height: 1.5;

        word-break: break-all;
    }

    /* =====================================================
       FORM
    ====================================================== */

    .form-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;

        margin-bottom: 6px;

        color: var(--utama-gelap);

        font-size: 10px;

        font-weight: 700;
    }

    .form-group label span {
        color: var(--bahaya);
    }

    .form-control {
        width: 100%;

        min-height: 37px;

        padding: 9px 10px;

        border:
            1px solid var(--garis-kuat);

        border-radius: 7px;

        background: #ffffff;

        color: var(--teks);

        font-size: 10px;

        outline: none;

        transition: .2s ease;
    }

    .form-control:hover {
        border-color: var(--garis-kuat);
    }

    .form-control:focus {
        border-color: var(--utama);

        box-shadow:
            0 0 0 3px
            rgba(23, 121, 201, .09);
    }

    textarea.form-control {
        min-height: 90px;

        resize: vertical;

        line-height: 1.5;
    }

    .file-input {
        padding: 8px;

        background: var(--permukaan-redup);

        cursor: pointer;
    }

    .form-help {
        margin-top: 5px;

        color: var(--teks-sekunder);

        font-size: 8px;

        line-height: 1.5;
    }

    .old-image {
        margin-top: 9px;

        padding: 9px;

        border:
            1px solid var(--garis);

        border-radius: 7px;

        background: var(--permukaan-redup);
    }

    .old-image-title {
        margin-bottom: 7px;

        color: var(--teks-sekunder);

        font-size: 8px;

        font-weight: 700;
    }

    .old-image img {
        width: 100%;

        height: 80px;

        display: block;

        object-fit: cover;

        border-radius: 5px;
    }

    /* =====================================================
       BUTTON
    ====================================================== */

    .btn {
        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 6px;

        min-height: 36px;

        padding: 0 13px;

        border: none;

        border-radius: 7px;

        cursor: pointer;

        font-size: 9px;

        font-weight: 700;

        transition: .2s ease;
    }

    .btn-primary {
        background: var(--utama);

        color: #ffffff;

        box-shadow:
            0 4px 10px
            rgba(23, 121, 201, .13);
    }

    .btn-primary:hover {
        background: var(--utama-gelap);

        transform: translateY(-1px);
    }

    .btn-danger {
        background: var(--bahaya);

        color: #ffffff;
    }

    .btn-danger:hover {
        background: var(--bahaya);
    }

    /* =====================================================
       FORM ACTION
    ====================================================== */

    .form-actions {
        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 10px;

        margin-top: 19px;

        padding-top: 16px;

        border-top:
            1px solid var(--garis);
    }

    .form-actions-note {
        color: var(--teks-redup);

        font-size: 8px;
    }

    .form-actions-right {
        display: flex;

        align-items: center;

        gap: 7px;
    }

    /* =====================================================
       DELETE
    ====================================================== */

    .delete-form {
        margin-top: 9px;
    }

    /* =====================================================
       EMPTY STATE
    ====================================================== */

    .empty-state {
        padding: 50px 25px;

        text-align: center;
    }

    .empty-icon {
        width: 58px;

        height: 58px;

        margin: 0 auto 14px;

        border-radius: 50%;

        background: var(--utama-muda);

        color: var(--utama);

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 25px;
    }

    .empty-state h3 {
        color: var(--utama-gelap);

        font-size: 15px;
    }

    .empty-state p {
        max-width: 460px;

        margin: 7px auto 0;

        color: var(--teks-sekunder);

        font-size: 9px;

        line-height: 1.7;
    }

    .add-banner-card {
        margin-top: 18px;
    }

    /* =====================================================
       FOOT NOTE
    ====================================================== */

    .foot-note {
        margin-top: 15px;

        color: var(--teks-redup);

        font-size: 8px;

        text-align: center;
    }

    /* =====================================================
       RESPONSIVE
    ====================================================== */

    @media (max-width: 1100px) {

        .banner-layout {
            grid-template-columns: 1fr;
        }

    }

    @media (max-width: 800px) {

        .content {
            padding: 22px 18px 35px;
        }

    }

    @media (max-width: 550px) {

        .page-header {
            align-items: flex-start;
        }

        .preview-area,
        .form-body {
            padding: 15px;
        }

        .current-banner-image,
        .no-image {
            height: 210px;
        }

        .form-actions {
            align-items: stretch;

            flex-direction: column;
        }

        .form-actions-right {
            width: 100%;
        }

        .form-actions-right .btn {
            flex: 1;
        }

    }

</style>
@endpush

@section('content')

{{-- =================================================
     CONTENT
================================================== --}}

<div class="content">

    {{-- =================================================
         PAGE HEADER
    ================================================== --}}

    <div class="page-header">

        <div class="page-header-left">

            <div class="page-header-icon">
                <x-icon name="image" />
            </div>

            <div>

                <h2>
                    Banner Utama Website
                </h2>

                <p>
                    Website PILARA menggunakan satu banner utama
                    yang dapat dikelola dari halaman ini.
                </p>

            </div>

        </div>

    </div>

    {{-- =================================================
         SUCCESS
    ================================================== --}}

    <x-alert type="success" :message="session('success')" />

    {{-- =================================================
         ERROR
    ================================================== --}}

    <x-alert
        type="error"
        title="Terjadi kesalahan:"
        :messages="$errors->all()"
    />

    {{-- =================================================
         NOTICE
    ================================================== --}}

    <div class="notice">

        <div class="notice-icon">
            !
        </div>

        <div>

            <strong>
                Banner utama PILARA
            </strong>

            <p>
                Banner digunakan sebagai tampilan utama pada
                halaman depan website. Karena website PILARA
                menggunakan satu banner utama, banner yang
                tersimpan akan menjadi banner aktif.
            </p>

        </div>

    </div>

    {{-- =================================================
         JIKA SUDAH ADA BANNER
    ================================================== --}}

    @if($banner)

        <div class="banner-layout">

            {{-- =================================================
                 PREVIEW
            ================================================== --}}

            <div class="card">

                <div class="card-header">

                    <h3>
                        Banner Saat Ini
                    </h3>

                    <p>
                        Tampilan banner yang sedang digunakan
                        pada halaman utama website.
                    </p>

                </div>

                <div class="preview-area">

                    <div class="current-label">

                        <x-icon name="check" />

                        Banner Aktif

                    </div>

                    <div class="current-banner">

                        @if($banner->gambar)

                            <img
                                src="{{ asset('storage/' . $banner->gambar) }}"
                                alt="{{ $banner->judul }}"
                                class="current-banner-image"
                            >

                        @else

                            <div class="no-image">

                                <div class="no-image-icon">
                                    <x-icon name="image" />
                                </div>

                                Belum ada gambar banner

                            </div>

                        @endif

                        <div class="preview-info">

                            <h4>
                                {{ $banner->judul }}
                            </h4>

                            @if($banner->deskripsi)

                                <div class="preview-description">
                                    {{ $banner->deskripsi }}
                                </div>

                            @endif

                            @if($banner->tombol_teks)

                                <div>

                                    <span class="preview-button">
                                        {{ $banner->tombol_teks }}
                                    </span>

                                </div>

                            @endif

                            @if($banner->tombol_link)

                                <div class="preview-link">

                                    Link:

                                    {{ $banner->tombol_link }}

                                </div>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

            {{-- =================================================
                 FORM UBAH BANNER
            ================================================== --}}

            <div class="card">

                <div class="card-header">

                    <h3>
                        Ubah Banner
                    </h3>

                    <p>
                        Perbarui informasi banner utama PILARA.
                    </p>

                </div>

                <div class="form-body">

                    <form
                        action="{{ route('superadmin.banners.store') }}"
                        method="POST"
                        enctype="multipart/form-data"
                    >

                        @csrf

                        {{-- JUDUL --}}

                        <div class="form-group">

                            <label for="judul">

                                Judul Banner

                                <span>
                                    *
                                </span>

                            </label>

                            <input
                                type="text"
                                id="judul"
                                name="judul"
                                class="form-control"
                                value="{{ old('judul', $banner->judul) }}"
                                placeholder="Contoh: Selamat Datang di PILARA"
                                required
                            >

                        </div>

                        {{-- DESKRIPSI --}}

                        <div class="form-group">

                            <label for="deskripsi">
                                Deskripsi
                            </label>

                            <textarea
                                id="deskripsi"
                                name="deskripsi"
                                class="form-control"
                                placeholder="Masukkan deskripsi singkat banner..."
                            >{{ old('deskripsi', $banner->deskripsi) }}</textarea>

                        </div>

                        {{-- GAMBAR --}}

                        <div class="form-group">

                            <label for="gambar">
                                Gambar Banner
                            </label>

                            <input
                                type="file"
                                id="gambar"
                                name="gambar"
                                class="form-control file-input"
                                accept=".jpg,.jpeg,.png,.webp"
                            >

                            <div class="form-help">

                                Pilih gambar baru jika ingin
                                mengganti gambar banner.

                                Format:
                                JPG, JPEG, PNG, WEBP.

                                Maksimal 2 MB.

                            </div>

                            @if($banner->gambar)

                                <div class="old-image">

                                    <div class="old-image-title">

                                        Gambar yang sedang digunakan

                                    </div>

                                    <img
                                        src="{{ asset('storage/' . $banner->gambar) }}"
                                        alt="Banner saat ini"
                                    >

                                </div>

                            @endif

                        </div>

                        {{-- TEKS TOMBOL --}}

                        <div class="form-group">

                            <label for="tombol_teks">
                                Teks Tombol
                            </label>

                            <input
                                type="text"
                                id="tombol_teks"
                                name="tombol_teks"
                                class="form-control"
                                value="{{ old('tombol_teks', $banner->tombol_teks) }}"
                                placeholder="Contoh: Daftar Magang"
                            >

                            <div class="form-help">

                                Contoh:
                                Daftar Magang

                            </div>

                        </div>

                        {{-- LINK --}}

                        <div class="form-group">

                            <label for="tombol_link">
                                Link Tombol
                            </label>

                            <input
                                type="text"
                                id="tombol_link"
                                name="tombol_link"
                                class="form-control"
                                value="{{ old('tombol_link', $banner->tombol_link) }}"
                                placeholder="Masukkan link Google Form"
                            >

                            <div class="form-help">

                                Untuk pendaftaran,
                                masukkan link Google Form.

                            </div>

                        </div>

                        {{-- ACTION --}}

                        <div class="form-actions">

                            <div class="form-actions-note">

                                Banner utama hanya satu.

                            </div>

                            <div class="form-actions-right">

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >

                                    <x-icon name="check" />

                                    Simpan Perubahan

                                </button>

                            </div>

                        </div>

                    </form>

                    {{-- DELETE --}}

                    <form
                        action="{{ route('superadmin.banners.destroy', $banner) }}"
                        method="POST"
                        class="delete-form"
                        onsubmit="return confirm('Yakin ingin menghapus banner utama?')"
                    >

                        @csrf

                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-danger"
                            style="width:100%;"
                        >

                            ×

                            Hapus Banner

                        </button>

                    </form>

                </div>

            </div>

        </div>

    @else

        {{-- =================================================
             BELUM ADA BANNER
        ================================================== --}}

        <div class="card">

            <div class="empty-state">

                <div class="empty-icon">
                    <x-icon name="image" />
                </div>

                <h3>
                    Belum Ada Banner
                </h3>

                <p>
                    Website PILARA belum memiliki banner utama.
                    Silakan tambahkan banner melalui form di bawah.
                </p>

            </div>

        </div>

        {{-- =================================================
             FORM TAMBAH
        ================================================== --}}

        <div class="card add-banner-card">

            <div class="card-header">

                <h3>
                    Tambah Banner Utama
                </h3>

                <p>
                    Banner ini akan ditampilkan pada halaman
                    utama website PILARA.
                </p>

            </div>

            <div class="form-body">

                <form
                    action="{{ route('superadmin.banners.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                >

                    @csrf

                    {{-- JUDUL --}}

                    <div class="form-group">

                        <label for="judul">

                            Judul Banner

                            <span>
                                *
                            </span>

                        </label>

                        <input
                            type="text"
                            id="judul"
                            name="judul"
                            class="form-control"
                            value="{{ old('judul') }}"
                            placeholder="Contoh: Selamat Datang di PILARA"
                            required
                        >

                    </div>

                    {{-- DESKRIPSI --}}

                    <div class="form-group">

                        <label for="deskripsi">
                            Deskripsi
                        </label>

                        <textarea
                            id="deskripsi"
                            name="deskripsi"
                            class="form-control"
                            placeholder="Masukkan deskripsi banner..."
                        >{{ old('deskripsi') }}</textarea>

                    </div>

                    {{-- GAMBAR --}}

                    <div class="form-group">

                        <label for="gambar">
                            Gambar Banner
                        </label>

                        <input
                            type="file"
                            id="gambar"
                            name="gambar"
                            class="form-control file-input"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                        <div class="form-help">

                            Format JPG, JPEG, PNG atau WEBP.

                            Maksimal 2 MB.

                        </div>

                    </div>

                    {{-- TOMBOL --}}

                    <div class="form-group">

                        <label for="tombol_teks">
                            Teks Tombol
                        </label>

                        <input
                            type="text"
                            id="tombol_teks"
                            name="tombol_teks"
                            class="form-control"
                            value="{{ old('tombol_teks') }}"
                            placeholder="Contoh: Daftar Magang"
                        >

                    </div>

                    {{-- LINK --}}

                    <div class="form-group">

                        <label for="tombol_link">
                            Link Tombol
                        </label>

                        <input
                            type="text"
                            id="tombol_link"
                            name="tombol_link"
                            class="form-control"
                            value="{{ old('tombol_link') }}"
                            placeholder="Masukkan link Google Form"
                        >

                        <div class="form-help">

                            Untuk tombol pendaftaran,
                            masukkan link Google Form.

                        </div>

                    </div>

                    {{-- ACTION --}}

                    <div class="form-actions">

                        <div class="form-actions-note">

                            <span style="color:var(--bahaya);">
                                *
                            </span>

                            Judul wajib diisi.

                        </div>

                        <div class="form-actions-right">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >

                                +

                                Simpan Banner

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    @endif

    {{-- FOOT NOTE --}}

    <div class="foot-note">

        PILARA
        &nbsp;•&nbsp;
        Sistem Informasi Magang

    </div>

</div>

@endsection
