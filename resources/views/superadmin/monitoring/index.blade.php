{{-- resources/views/superadmin/monitoring/index.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Monitoring Peserta')
@section('page_title', 'Monitoring Peserta')
@section('page_subtitle', 'Biodata, logbook periode berjalan, dan verifikasi yang menunggu')

@php
    $rupiah = fn (int $nilai) => 'Rp '.number_format($nilai, 0, ',', '.');
@endphp

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .stat-row {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;

        margin-bottom: 18px;
    }

    .panel {
        margin-bottom: 18px;
        padding: 20px 22px;

        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel h3 {
        margin-bottom: 12px;

        color: #17324d;
        font-size: 15px;
    }

    .filter {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 10px;

        margin-bottom: 16px;
    }

    .filter label {
        display: block;

        margin-bottom: 5px;

        color: #52646e;
        font-size: 12px;
        font-weight: 700;
    }

    .filter select,
    .filter input[type="search"] {
        height: 40px;

        padding: 0 10px;

        border: 1px solid #d7e0eb;
        border-radius: 8px;

        background: #ffffff;

        font-family: inherit;
        font-size: 13px;
    }

    .filter .check {
        display: flex;
        align-items: center;
        gap: 6px;

        height: 40px;

        font-size: 13px;
        font-weight: 700;
    }

    .tabel-scroll {
        overflow-x: auto;
    }

    table.monitor {
        width: 100%;
        min-width: 1040px;
        border-collapse: collapse;

        font-size: 12.5px;
    }

    table.monitor th {
        padding: 9px 10px;

        background: #f8fafc;
        color: #6b7c8d;

        font-size: 11px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
    }

    table.monitor td {
        padding: 10px;

        border-top: 1px solid #edf1f5;

        color: #34495e;
        vertical-align: top;
    }

    .nama strong {
        display: block;

        color: #17324d;
    }

    .kecil {
        display: block;

        color: #8a99a8;
        font-size: 11px;
    }

    .bar {
        width: 90px;
        height: 6px;
        margin: 5px 0 3px;

        border-radius: 999px;

        background: #edf1f5;

        overflow: hidden;
    }

    .bar span {
        display: block;

        height: 100%;

        background: linear-gradient(90deg, #1779c9, #19a974);
    }

    .badge {
        display: inline-block;

        padding: 2px 8px;

        border-radius: 999px;

        font-size: 10.5px;
        font-weight: 800;
        white-space: nowrap;
    }

    .b-hijau { background: #e7f6ec; color: #067647; }
    .b-oranye { background: #fff4e5; color: #b54708; }
    .b-merah { background: #fff0f0; color: #c62828; }
    .b-abu { background: #eef2f6; color: #52646e; }

    .angka {
        font-variant-numeric: tabular-nums;
    }

    .aksi a {
        display: block;

        color: #1779c9;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .riwayat {
        display: flex;
        flex-direction: column;
    }

    .riwayat a {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 6px 14px;

        padding: 10px 0;

        border-top: 1px solid #edf1f5;

        color: #34495e;
        font-size: 13px;
        text-decoration: none;
    }

    .riwayat a:first-child {
        border-top: none;
    }

    .riwayat .isi {
        flex: 1 1 320px;
        min-width: 0;

        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .empty {
        padding: 24px;

        color: #8a99a8;
        text-align: center;
    }

    @media (max-width: 1000px) {
        .stat-row {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 16px 30px;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    {{-- RINGKASAN --}}
    <div class="stat-row">
        <x-stat-card label="Peserta Aktif" :value="$ringkasan['peserta_aktif']" icon="♙" :hint="$ringkasan['punya_akun'].' punya akun login aktif'" />
        <x-stat-card label="Logbook Hari Ini" :value="$ringkasan['sudah_isi_hari_ini'].'/'.$ringkasan['wajib_hari_ini']" icon="📒" :hint="$ringkasan['wajib_hari_ini'] ? 'peserta sudah mengisi' : 'hari ini bukan hari kerja'" />
        <x-stat-card label="Menunggu Verifikasi" :value="$ringkasan['logbook_menunggu']" icon="✓" :hint="$ringkasan['izin_menunggu'].' izin/sakit menunggu'" />
        <x-stat-card label="Biodata Disetujui" :value="$ringkasan['biodata_disetujui'].'/'.$ringkasan['peserta_aktif']" icon="☷" :hint="$ringkasan['biodata_diajukan'].' menunggu tinjau · '.$ringkasan['biodata_revisi'].' revisi · '.$ringkasan['biodata_belum'].' belum'" />
    </div>

    {{-- TABEL PESERTA --}}
    <section class="panel">
        <form method="GET" action="{{ route('superadmin.monitoring.index') }}" class="filter">
            <div>
                <label for="cari">Cari</label>
                <input type="search" id="cari" name="cari" value="{{ $filter['cari'] ?? '' }}" placeholder="Nama, NIM, nomor peserta">
            </div>
            <div>
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="aktif" @selected($filter['status'] === 'aktif')>Aktif</option>
                    <option value="selesai" @selected($filter['status'] === 'selesai')>Selesai</option>
                    <option value="semua" @selected($filter['status'] === 'semua')>Semua</option>
                </select>
            </div>
            <div>
                <label for="divisi">Divisi (periode ini)</label>
                <select id="divisi" name="divisi">
                    <option value="">Semua divisi</option>
                    @foreach ($divisis as $divisi)
                        <option value="{{ $divisi->id }}" @selected((int) ($filter['divisi'] ?? 0) === $divisi->id)>{{ $divisi->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="urut">Urutkan</label>
                <select id="urut" name="urut">
                    @foreach (\App\Services\MonitoringPeserta::URUTAN as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(($filter['urut'] ?? 'nama') === $nilai)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <label class="check">
                <input type="checkbox" name="belum_hari_ini" value="1" @checked($filter['belum_hari_ini'] ?? false)>
                Belum isi hari ini
            </label>
            <x-button variant="secondary">Terapkan</x-button>
        </form>

        <div class="tabel-scroll">
            <table class="monitor">
                <thead>
                    <tr>
                        <th>Peserta</th>
                        <th>Divisi</th>
                        <th>Biodata</th>
                        <th>Logbook Periode Ini</th>
                        <th>Kehadiran</th>
                        <th>Menunggu</th>
                        <th>Uang Saku</th>
                        <th>Terakhir Isi</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tabel as $baris)
                        @php
                            $peserta = $baris['peserta'];
                            $biodata = $baris['biodata'];
                            $progres = $biodata->progres();
                            $rekap = $baris['rekap'];
                            $warnaBiodata = ['disetujui' => 'b-hijau', 'diajukan' => 'b-oranye', 'perlu_revisi' => 'b-merah'][$biodata->status] ?? 'b-abu';
                        @endphp
                        <tr>
                            <td class="nama">
                                <strong>{{ $peserta->nama }}</strong>
                                <span class="kecil">{{ $peserta->nomor_peserta }}{{ $peserta->asal_kampus ? ' · '.$peserta->asal_kampus : '' }}</span>
                                <span class="kecil">
                                    @if (! $peserta->user)
                                        Belum punya akun
                                    @elseif (! $peserta->user->aktif)
                                        Akun nonaktif
                                    @endif
                                </span>
                            </td>
                            <td>
                                {{ $baris['divisi']?->nama ?? '—' }}
                                @if ($baris['mentor'])
                                    <span class="kecil">Mentor: {{ $baris['mentor'] }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $warnaBiodata }}">{{ $biodata->exists ? $biodata->labelStatus() : 'Belum diisi' }}</span>
                                <div class="bar"><span style="width: {{ $progres['persen'] }}%"></span></div>
                                <span class="kecil">{{ $progres['persen'] }}% data wajib</span>
                            </td>
                            <td class="angka">
                                @if ($baris['periode'])
                                    {{ $baris['terisi'] }} hari terisi
                                    <span class="kecil">{{ $rekap['belum_diisi'] }} hari kerja belum diisi · {{ $baris['periode']['label'] }}</span>
                                    @if ($baris['isi_hari_ini'] === true)
                                        <span class="badge b-hijau">Hari ini ✓</span>
                                    @elseif ($baris['isi_hari_ini'] === false)
                                        <span class="badge b-merah">Hari ini belum</span>
                                    @endif
                                @else
                                    <span class="kecil">Periode magang belum diatur</span>
                                @endif
                            </td>
                            <td class="angka">
                                {{ $rekap['hari_penuh'] }} penuh · {{ $rekap['setengah_hari'] }} setengah
                                <span class="kecil">izin {{ $rekap['izin'] }} · sakit {{ $rekap['sakit'] }}</span>
                            </td>
                            <td class="angka">
                                @if ($rekap['menunggu'])
                                    <span class="badge b-oranye">{{ $rekap['menunggu'] }} hari</span>
                                @else
                                    <span class="kecil">—</span>
                                @endif
                            </td>
                            <td class="angka">{{ $rupiah($rekap['uang_saku']) }}</td>
                            <td>{{ $baris['terakhir']?->translatedFormat('d M Y') ?? '—' }}</td>
                            <td class="aksi">
                                <a href="{{ route('superadmin.peserta-magang.logbook-harian', $peserta) }}">Logbook</a>
                                <a href="{{ route('superadmin.peserta-magang.biodata', $peserta) }}">Biodata</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="empty">Tidak ada peserta yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- RIWAYAT LOGBOOK TERBARU --}}
    <section class="panel">
        <h3>Logbook Terbaru</h3>

        @if ($riwayat->isEmpty())
            <div class="empty">Belum ada logbook yang diisi peserta.</div>
        @else
            <div class="riwayat">
                @foreach ($riwayat as $logbook)
                    <a href="{{ route('superadmin.peserta-magang.logbook-harian', [$logbook->pesertaMagang, 'periode' => app(\App\Services\PeriodeMagang::class)->kunciUntuk($logbook->tanggal)]) }}#hari-{{ $logbook->tanggal->toDateString() }}">
                        <span><strong>{{ $logbook->pesertaMagang->nama }}</strong> · {{ $logbook->tanggal->translatedFormat('D, d M') }}</span>
                        <span class="isi">{{ $logbook->kehadiran === 'hadir' ? $logbook->activity : $logbook->labelKehadiran().': '.$logbook->activity }}</span>
                        <span class="badge {{ ['diterima' => 'b-hijau', 'ditolak' => 'b-merah', 'review' => 'b-oranye'][$logbook->status_verifikasi] ?? 'b-abu' }}">{{ $logbook->labelStatus() }}</span>
                        <span class="kecil">{{ $logbook->updated_at->diffForHumans() }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

</div>

@endsection
