{{-- resources/views/superadmin/pengguna/index.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Kelola Pengguna')
@section('page_title', 'Kelola Pengguna')
@section('page_subtitle', 'Kelola akun Admin dan Super Admin PILARA')

@push('styles')
<style>
    body {
        font-family: var(--font);
        background: var(--latar);
        color: var(--utama-gelap);
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

        background: var(--utama-muda);

        border:
            1px solid var(--utama-muda);

        color: var(--utama);

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 18px;

        font-weight: 700;
    }

    .info-title {
        font-size: 20px;

        color: var(--utama-gelap);

        font-weight: 700;
    }

    .info-description {
        margin-top: 4px;

        font-size: 11px;

        color: var(--teks-sekunder);
    }

    /* =========================================================
       MAIN CARD
    ========================================================= */

    .card {
        background: #ffffff;

        border:
            1px solid var(--garis);

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
            1px solid var(--garis);

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

        background: var(--utama-muda);

        border:
            1px solid var(--utama-muda);

        color: var(--utama);

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 14px;

        font-weight: 700;
    }

    .card-title {
        font-size: 14px;

        font-weight: 700;

        color: var(--utama-gelap);
    }

    .card-description {
        margin-top: 3px;

        font-size: 10px;

        color: var(--teks-sekunder);
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

        background: var(--utama);

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
        background: var(--utama-gelap);
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
        background: var(--permukaan-redup);

        color: var(--teks-sekunder);

        font-size: 9px;

        font-weight: 800;

        letter-spacing: .7px;

        text-transform: uppercase;

        padding:
            13px 18px;

        text-align: left;

        border-bottom:
            1px solid var(--garis);

        white-space: nowrap;
    }

    tbody td {
        padding:
            16px 18px;

        border-bottom:
            1px solid var(--garis);

        font-size: 11px;

        color: var(--teks-sekunder);

        vertical-align: middle;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    tbody tr:hover {
        background: var(--utama-muda);
    }

    .number-column {
        width: 55px;

        color: var(--teks-sekunder);
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

        background: var(--utama-muda);

        color: var(--utama-gelap);

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

        color: var(--utama-gelap);
    }

    .user-email {
        margin-top: 3px;

        font-size: 10px;

        color: var(--teks-sekunder);
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
        background: var(--peringatan-muda);

        color: var(--peringatan);
    }

    .role-admin {
        background: var(--utama-muda);

        color: var(--utama-gelap);
    }

    /* =========================================================
       DATE
    ========================================================= */

    .date-text {
        color: var(--teks-sekunder);

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
            1px solid var(--garis-kuat);

        background: var(--permukaan-redup);

        color: var(--utama-gelap);

        display: inline-flex;

        align-items: center;

        justify-content: center;

        font-size: 10px;

        font-weight: 700;

        cursor: pointer;

        transition: .2s ease;
    }

    .action-button:hover {
        background: var(--utama-muda);
    }

    .delete-button {
        border-color: #f0d4d4;

        background: var(--bahaya-muda);

        color: var(--bahaya);
    }

    .delete-button:hover {
        background: var(--bahaya-muda);
    }

    .disabled-action {
        min-height: 30px;

        padding:
            0 9px;

        border-radius: 6px;

        background: var(--permukaan-redup);

        color: var(--teks-redup);

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

        background: var(--utama-muda);

        color: var(--teks-sekunder);

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 18px;
    }

    .empty-title {
        font-size: 13px;

        font-weight: 700;

        color: var(--teks-sekunder);
    }

    .empty-description {
        margin-top: 5px;

        font-size: 10px;

        color: var(--teks-sekunder);
    }

    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 1000px) {

        .content {
            padding:
                24px 20px 35px;
        }

    }

    @media (max-width: 760px) {

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
@endpush

@section('content')

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
                <x-icon name="user-cog" />
            </div>

            <div>

                <div class="info-title">
                    Manajemen Pengguna
                </div>

                <div class="info-description">
                    Kelola akun pengguna yang memiliki akses
                    ke sistem PILARA.
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

        <x-alert type="error" :message="session('error')" />

        {{-- =================================================
             VALIDATION ERROR
        ================================================== --}}

        <x-alert
            type="error"
            title="Periksa kembali data"
            :messages="$errors->all()"
        />

        {{-- =================================================
             USER CARD
        ================================================== --}}

        <div class="card">

            <div class="card-header">

                <div class="card-heading">

                    <div class="card-heading-icon">
                        <x-icon name="user-cog" />
                    </div>

                    <div>

                        <div class="card-title">
                            Daftar Pengguna
                        </div>

                        <div class="card-description">
                            Daftar akun yang terdaftar di sistem PILARA.
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
                        <x-icon name="user-cog" />
                    </div>

                    <div class="empty-title">
                        Belum ada pengguna
                    </div>

                    <div class="empty-description">
                        Belum terdapat akun pengguna lain
                        di sistem PILARA.
                    </div>

                </div>

            @endif

        </div>

    </div>

</section>

@endsection
