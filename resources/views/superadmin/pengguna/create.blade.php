{{-- resources/views/superadmin/pengguna/create.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Tambah Pengguna')
@section('page_title', 'Tambah Pengguna')
@section('page_subtitle', 'Tambahkan akun Admin atau Super Admin ke sistem MAGENTA')

@push('styles')
<style>
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

        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 700px) {

        .content {
            padding: 18px;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .x-btn {
            width: 100%;
        }
    }

</style>
@endpush

@section('content')

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
    <x-alert
        type="error"
        title="Data belum dapat disimpan."
        :messages="$errors->all()"
    />

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
                <x-form.input
                    name="name"
                    label="Nama Lengkap"
                    placeholder="Masukkan nama lengkap"
                    maxlength="255"
                    required
                />

                <!-- EMAIL -->
                <x-form.input
                    name="email"
                    type="email"
                    label="Email"
                    placeholder="contoh@email.com"
                    maxlength="255"
                    hint="Email digunakan untuk login ke sistem MAGENTA."
                    required
                />

                <!-- PASSWORD -->
                <div class="form-grid">

                    <x-form.input
                        name="password"
                        type="password"
                        label="Password"
                        placeholder="Minimal 8 karakter"
                        minlength="8"
                        required
                    />

                    <!-- KONFIRMASI PASSWORD -->
                    <x-form.input
                        name="password_confirmation"
                        type="password"
                        label="Konfirmasi Password"
                        placeholder="Ulangi password"
                        minlength="8"
                        required
                    />

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

                <x-button variant="secondary" href="{{ route('superadmin.pengguna.index') }}">Batal</x-button>

                <x-button>Simpan Pengguna</x-button>

            </div>

        </form>

    </div>

</section>

@endsection
