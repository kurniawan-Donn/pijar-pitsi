{{-- resources/views/superadmin/laporan/index.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Laporan')
@section('page_title', 'Laporan')
@section('page_subtitle', 'Rekap peserta, kehadiran & uang saku, nilai akhir, dan feedback magang')

@php
    $queryExport = array_filter(['jenis' => $jenis] + $filter);
@endphp

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .tabs {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 4px;

        margin-bottom: 18px;
        padding: 5px;

        border-radius: 12px;

        background: #e9eef4;
    }

    .tab {
        padding: 9px 16px;

        border-radius: 9px;

        color: #4b5d70;

        font-size: 13px;
        font-weight: 700;

        transition: .2s ease;
    }

    .tab:hover {
        color: #1779c9;
    }

    .tab.active {
        background: #ffffff;
        color: #1779c9;
        box-shadow: 0 2px 8px rgba(15, 40, 65, .08);
    }

    .panel {
        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .filter-bar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;

        padding: 18px 20px;

        border-bottom: 1px solid #edf1f5;
    }

    .filter-form {
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 12px;
    }

    .filter-form label {
        display: block;

        margin-bottom: 6px;

        color: #4b5d70;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .filter-form input,
    .filter-form select {
        height: 40px;

        padding: 0 11px;

        border: 1px solid #d7e0eb;
        border-radius: 7px;

        background: #ffffff;

        font-family: inherit;
        font-size: 13px;
    }

    .export-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .report-head {
        padding: 18px 20px 4px;
    }

    .report-head h2 {
        color: #17324d;
        font-size: 18px;
    }

    .report-head p {
        margin-top: 3px;

        color: #7b8794;
        font-size: 13px;
    }

    .summary {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;

        padding: 14px 20px 18px;
    }

    .summary div {
        padding: 10px 14px;

        border-radius: 10px;

        background: #f7f9fc;
    }

    .summary span {
        display: block;

        color: #7b8794;
        font-size: 11px;
        font-weight: 700;
    }

    .summary strong {
        color: #17324d;
        font-size: 16px;
    }

    .table-wrapper {
        overflow-x: auto;

        border-top: 1px solid #edf1f5;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        padding: 11px 14px;

        background: #f8fafc;
        color: #6b7c8d;

        font-size: 11px;
        font-weight: 800;
        letter-spacing: .3px;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    td {
        padding: 11px 14px;

        border-top: 1px solid #edf1f5;

        color: #33475b;

        font-size: 13px;
        white-space: nowrap;
    }

    tbody tr:hover {
        background: #fafcff;
    }

    td.angka {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    td.catatan {
        color: #b42318;
        font-weight: 600;
    }

    .empty-state {
        padding: 46px 20px;

        color: #7b8794;
        text-align: center;
    }

    .hint {
        padding: 12px 20px;

        color: #8a96a3;
        font-size: 12px;
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 20px 30px;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages />

    <nav class="tabs" aria-label="Jenis laporan">
        @foreach ($daftarJenis as $kode => $label)
            <a href="{{ route('superadmin.laporan.index', ['jenis' => $kode]) }}" @class(['tab', 'active' => $jenis === $kode])>{{ $label }}</a>
        @endforeach
    </nav>

    <section class="panel">

        <div class="filter-bar">

            <form method="GET" action="{{ route('superadmin.laporan.index') }}" class="filter-form">
                <input type="hidden" name="jenis" value="{{ $jenis }}">

                @if ($jenis === 'absensi')
                    <div>
                        <label for="periode">Periode (28–27)</label>
                        <input type="month" id="periode" name="periode" value="{{ $filter['periode'] }}" title="Bulan tanggal akhir periode, mis. Agustus = 28 Juli – 27 Agustus">
                    </div>
                @else
                    <div>
                        <label for="dari">Dari</label>
                        <input type="date" id="dari" name="dari" value="{{ $filter['dari'] }}">
                    </div>
                    <div>
                        <label for="sampai">Sampai</label>
                        <input type="date" id="sampai" name="sampai" value="{{ $filter['sampai'] }}">
                    </div>
                @endif

                @if ($jenis === 'peserta')
                    <div>
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="">Semua</option>
                            <option value="aktif" @selected($filter['status'] === 'aktif')>Aktif</option>
                            <option value="selesai" @selected($filter['status'] === 'selesai')>Selesai</option>
                        </select>
                    </div>
                @endif

                <x-button>Tampilkan</x-button>
            </form>

            <div class="export-actions">
                <x-button variant="secondary" :href="route('superadmin.laporan.xlsx', $queryExport)">⬇ Excel (.xlsx)</x-button>
                <x-button variant="secondary" :href="route('superadmin.laporan.pdf', $queryExport)">⬇ PDF</x-button>
                <x-button variant="secondary" :href="route('superadmin.laporan.csv', $queryExport)">CSV</x-button>
                <x-button variant="secondary" :href="route('superadmin.laporan.cetak', $queryExport)" target="_blank" rel="noopener">🖨 Cetak</x-button>
            </div>

        </div>

        <div class="report-head">
            <h2>{{ $laporan['judul'] }}</h2>
            <p>{{ $laporan['keterangan'] }}</p>
        </div>

        <div class="summary">
            @foreach ($laporan['ringkasan'] as $label => $nilai)
                <div><span>{{ $label }}</span><strong>{{ $nilai }}</strong></div>
            @endforeach
        </div>

        @if (empty($laporan['baris']))
            <div class="empty-state">Tidak ada data untuk filter ini.</div>
        @else
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            @foreach ($laporan['kolom'] as $kolom)
                                <th>{{ $kolom }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($laporan['baris'] as $baris)
                            <tr>
                                @foreach ($baris as $index => $sel)
                                    @php($namaKolom = $laporan['kolom'][$index] ?? '')
                                    <td @class([
                                        'angka' => is_int($sel) || is_float($sel),
                                        'catatan' => $namaKolom === 'Keterangan' && $sel !== '',
                                    ])>
                                        @if ($namaKolom === 'Uang Saku (Rp)' && is_int($sel))
                                            {{ number_format($sel, 0, ',', '.') }}
                                        @else
                                            {{ $sel }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($jenis === 'absensi')
            <div class="hint">
                Data dari logbook harian peserta. Hari penuh = jam pagi masuk & siang keluar; setengah hari = satu sesi.
                Hari yang ditolak tidak dihitung; uang saku memakai tarif yang berlaku pada tanggal masing-masing (Pengaturan Magang).
            </div>
        @endif

    </section>

</div>

@endsection
