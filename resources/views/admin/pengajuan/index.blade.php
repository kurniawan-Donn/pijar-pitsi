{{-- resources/views/admin/pengajuan/index.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Pengajuan Magang')
@section('page_title', 'Pengajuan Magang')
@section('page_subtitle', 'Kelola pendaftaran magang yang masuk dari website')

@use('App\Models\PengajuanMagang')

@php
    $totalPengajuan = $jumlahPerStatus->sum();

    // Kunci '' = tanpa filter status.
    $filterStatus = [
        '' => ['label' => 'Semua', 'jumlah' => $totalPengajuan],
    ];

    foreach (PengajuanMagang::STATUS_LABELS as $kode => $label) {
        $filterStatus[$kode] = ['label' => $label, 'jumlah' => $jumlahPerStatus[$kode] ?? 0];
    }
@endphp

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;

        margin-bottom: 22px;
    }

    .panel {
        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);

        overflow: hidden;
    }

    .toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;

        padding: 18px 20px;

        border-bottom: 1px solid #edf1f5;
    }

    .filter-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .filter-tab {
        display: inline-flex;
        align-items: center;
        gap: 7px;

        padding: 8px 13px;

        border: 1px solid #e1e8ef;
        border-radius: 999px;

        background: #ffffff;
        color: #4b5d70;

        font-size: 12px;
        font-weight: 700;

        transition: .2s ease;
    }

    .filter-tab:hover {
        border-color: #b9d3ea;
        color: #1779c9;
    }

    .filter-tab.active {
        background: #1779c9;
        border-color: #1779c9;
        color: #ffffff;
    }

    .filter-count {
        min-width: 20px;
        padding: 1px 6px;

        border-radius: 999px;

        background: rgba(15, 40, 65, .08);

        font-size: 10px;
        text-align: center;
    }

    .filter-tab.active .filter-count {
        background: rgba(255, 255, 255, .22);
    }

    .search-form {
        display: flex;
        gap: 8px;
    }

    .search-form input {
        width: 260px;
        height: 40px;

        padding: 0 13px;

        border: 1px solid #d7e0eb;
        border-radius: 7px;

        font-family: inherit;
        font-size: 12px;

        outline: none;
    }

    .search-form input:focus {
        border-color: #1779c9;
        box-shadow: 0 0 0 3px rgba(23, 121, 201, .08);
    }

    .table-wrapper {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        padding: 12px 20px;

        background: #f8fafc;
        color: #6b7c8d;

        font-size: 11px;
        font-weight: 800;
        letter-spacing: .4px;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    td {
        padding: 14px 20px;

        border-top: 1px solid #edf1f5;

        color: #33475b;

        font-size: 13px;
        vertical-align: middle;
    }

    tbody tr {
        transition: background .15s ease;
    }

    tbody tr:hover {
        background: #fafcff;
    }

    .nomor {
        color: #8a96a3;
        font-family: Consolas, monospace;
        font-size: 12px;
    }

    .nama {
        color: #17324d;
        font-weight: 700;
    }

    .sub {
        margin-top: 3px;

        color: #8a96a3;
        font-size: 11px;
    }

    .detail-link {
        color: #1779c9;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .detail-link:hover {
        text-decoration: underline;
    }

    .empty-state {
        padding: 50px 20px;

        color: #7b8794;
        text-align: center;
    }

    .empty-state strong {
        display: block;

        margin-bottom: 6px;

        color: #17324d;
        font-size: 15px;
    }

    @media (max-width: 1100px) {
        .stat-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 20px 30px;
        }

        .search-form,
        .search-form input {
            width: 100%;
        }
    }

    @media (max-width: 520px) {
        .stat-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages :validation="false" />

    {{-- RINGKASAN --}}
    <div class="stat-grid">
        <x-stat-card label="Total Pengajuan" :value="$totalPengajuan" icon="✉" />
        <x-stat-card label="Menunggu" :value="$jumlahPerStatus['menunggu'] ?? 0" icon="⏳" hint="Perlu diproses" />
        <x-stat-card label="Disetujui" :value="$jumlahPerStatus['disetujui'] ?? 0" icon="✓" />
        <x-stat-card label="Ditolak" :value="$jumlahPerStatus['ditolak'] ?? 0" icon="✕" />
    </div>

    <section class="panel">

        {{-- FILTER & PENCARIAN --}}
        <div class="toolbar">

            <nav class="filter-tabs" aria-label="Filter status">
                @foreach ($filterStatus as $kode => $filter)
                    <a
                        href="{{ route('admin.pengajuan.index', array_filter(['status' => $kode, 'cari' => $cari])) }}"
                        @class(['filter-tab', 'active' => $statusAktif === ($kode ?: null)])
                    >
                        {{ $filter['label'] }}
                        <span class="filter-count">{{ $filter['jumlah'] }}</span>
                    </a>
                @endforeach
            </nav>

            <form method="GET" action="{{ route('admin.pengajuan.index') }}" class="search-form">
                @if ($statusAktif)
                    <input type="hidden" name="status" value="{{ $statusAktif }}">
                @endif

                <input
                    type="search"
                    name="cari"
                    value="{{ $cari }}"
                    placeholder="Cari nama atau NIM..."
                    aria-label="Cari nama atau NIM"
                >

                <x-button>Cari</x-button>
            </form>

        </div>

        {{-- TABEL --}}
        @if ($pengajuans->isEmpty())
            <div class="empty-state">
                <strong>Belum ada pengajuan</strong>
                @if ($cari || $statusAktif)
                    Tidak ada pengajuan yang cocok dengan filter.
                    <a href="{{ route('admin.pengajuan.index') }}" class="detail-link">Reset filter</a>
                @else
                    Pengajuan dari form pendaftaran website akan muncul di sini.
                @endif
            </div>
        @else
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No. Pengajuan</th>
                            <th>Mahasiswa</th>
                            <th>Asal Kampus</th>
                            <th>Bidang</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($pengajuans as $pengajuan)
                            <tr>
                                <td>
                                    <div class="nomor">{{ $pengajuan->nomor() }}</div>
                                    <div class="sub">{{ $pengajuan->created_at->translatedFormat('d M Y') }}</div>
                                </td>
                                <td>
                                    <div class="nama">{{ $pengajuan->mahasiswa?->nama ?? '-' }}</div>
                                    <div class="sub">NIM {{ $pengajuan->mahasiswa?->nim ?? '-' }}</div>
                                </td>
                                <td>
                                    {{ $pengajuan->mahasiswa?->asal_kampus ?: '-' }}
                                    <div class="sub">{{ $pengajuan->mahasiswa?->program_studi }}</div>
                                </td>
                                <td>{{ $pengajuan->posisi }}</td>
                                <td>
                                    {{ $pengajuan->tanggal_mulai->translatedFormat('d M Y') }}
                                    <div class="sub">s/d {{ $pengajuan->tanggal_selesai->translatedFormat('d M Y') }}</div>
                                </td>
                                <td>
                                    <x-status-badge :status="$pengajuan->status" :label="$pengajuan->statusLabel()" />
                                </td>
                                <td>
                                    <a href="{{ route('admin.pengajuan.show', $pengajuan) }}" class="detail-link">
                                        Detail →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <x-pagination :paginator="$pengajuans" />

    </section>

</div>

@endsection
