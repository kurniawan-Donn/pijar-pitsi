{{-- resources/views/superadmin/peserta-magang/index.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Data Peserta Magang')
@section('page_title', 'Data Peserta Magang')
@section('page_subtitle', 'Kelola data mahasiswa yang sedang atau telah melaksanakan magang.')

@push('styles')
<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        background: #f4f7fb;
        color: #263238;
        min-height: 100vh;
    }

    button,
    input,
    select {
        font-family: inherit;
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

        display: flex;
        align-items: center;
        gap: 10px;

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

        .content {

            padding: 22px;
        }

    }

    @media (max-width: 700px) {

        .content {

            padding: 18px;
        }

        .content-header {

            align-items: flex-start;

            flex-direction: column;
        }

    }

</style>
@endpush

@section('content')

    {{-- CONTENT --}}

    <div class="content">

        {{-- SUCCESS MESSAGE --}}

        <x-alert type="success" :message="session('success')" />

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
                                    Nilai Akhir
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

                                            <x-avatar :user="$peserta->user" :nama="$peserta->nama" :ukuran="32" />

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

                                    {{-- NILAI AKHIR --}}

                                    <td>

                                        @if($peserta->penilaian)
                                            <strong>{{ number_format($peserta->penilaian->nilai_akhir, 2, ',', '.') }}</strong>
                                            <span class="secondary-text">({{ $peserta->penilaian->predikat }})</span>
                                        @else
                                            <a href="{{ route('superadmin.peserta-magang.penilaian', $peserta) }}" class="secondary-text">Belum dinilai</a>
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

    </div>

@endsection
