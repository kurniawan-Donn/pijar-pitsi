{{-- resources/views/superadmin/daftar-peserta/index.blade.php --}}

{{--
    Daftar peserta per fitur. $jenis: biodata | logbook | penilaian | surat.
    Lihat SuperAdmin\DaftarPesertaController.
--}}

@extends('layouts.dashboard')

@section('title', $judul)
@section('page_title', $judul)
@section('page_subtitle', $subjudul)

@php
    $rupiah = fn (int $nilai) => 'Rp '.number_format($nilai, 0, ',', '.');
    $route = 'superadmin.'.$jenis.'.index';
    $jumlahKeadaan = $baris->countBy('keadaan');
    $warnaBiodata = ['disetujui' => 'b-hijau', 'diajukan' => 'b-oranye', 'perlu_revisi' => 'b-merah'];
    $warnaSurat = ['terbit' => 'b-hijau', 'siap' => 'b-oranye', 'belum_siap' => 'b-abu'];
@endphp

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .panel {
        margin-bottom: 18px;
        padding: 20px 22px;

        background: #ffffff;

        border: 1px solid var(--garis);
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
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

        color: var(--teks-sekunder);
        font-size: 12px;
        font-weight: 700;
    }

    .filter select,
    .filter input[type="search"] {
        height: 40px;

        padding: 0 10px;

        border: 1px solid var(--garis-kuat);
        border-radius: 8px;

        background: #ffffff;

        font-family: inherit;
        font-size: 13px;
    }

    .tabel-scroll {
        overflow-x: auto;
    }

    table.daftar {
        width: 100%;
        min-width: 820px;
        border-collapse: collapse;

        font-size: 12.5px;
    }

    table.daftar th {
        padding: 9px 10px;

        background: var(--permukaan-redup);
        color: var(--teks-sekunder);

        font-size: 11px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
    }

    table.daftar td {
        padding: 10px;

        border-top: 1px solid var(--garis);

        color: var(--teks);
        vertical-align: top;
    }

    .nama strong {
        display: block;

        color: var(--utama-gelap);
    }

    .kecil {
        display: block;

        color: var(--teks-sekunder);
        font-size: 11px;
    }

    .bar {
        width: 90px;
        height: 6px;
        margin: 5px 0 3px;

        border-radius: 999px;

        background: var(--garis);

        overflow: hidden;
    }

    .bar span {
        display: block;

        height: 100%;

        background: linear-gradient(90deg, var(--utama), var(--sukses));
    }

    .badge {
        display: inline-block;

        padding: 2px 8px;

        border-radius: 999px;

        font-size: 10.5px;
        font-weight: 800;
        white-space: nowrap;
    }

    .b-hijau { background: var(--sukses-muda); color: var(--sukses); }
    .b-oranye { background: var(--peringatan-muda); color: var(--peringatan); }
    .b-merah { background: var(--bahaya-muda); color: var(--bahaya); }
    .b-abu { background: var(--garis); color: var(--teks-sekunder); }

    .angka {
        font-variant-numeric: tabular-nums;
    }

    .syarat {
        margin: 0;
        padding-left: 16px;

        color: var(--peringatan);
        font-size: 11.5px;
    }

    .aksi a {
        color: var(--utama);
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .empty {
        padding: 24px;

        color: var(--teks-sekunder);
        text-align: center;
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

    <section class="panel">
        <form method="GET" action="{{ route($route) }}" class="filter">
            <div>
                <label for="cari">Cari</label>
                <input type="search" id="cari" name="cari" value="{{ $filter['cari'] ?? '' }}" placeholder="Nama, NIM, nomor peserta">
            </div>
            <div>
                <label for="status">Status Peserta</label>
                <select id="status" name="status">
                    <option value="aktif" @selected($filter['status'] === 'aktif')>Aktif</option>
                    <option value="selesai" @selected($filter['status'] === 'selesai')>Selesai</option>
                    <option value="semua" @selected($filter['status'] === 'semua')>Semua</option>
                </select>
            </div>
            <div>
                <label for="keadaan">Tampilkan</label>
                <select id="keadaan" name="keadaan">
                    <option value="">Semua</option>
                    @foreach ($pilihanKeadaan as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(($filter['keadaan'] ?? null) === $nilai)>
                            {{ $label }}{{ $jenis !== 'logbook' && empty($filter['keadaan']) ? ' ('.($jumlahKeadaan[$nilai] ?? 0).')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <x-button variant="secondary">Terapkan</x-button>
        </form>

        <div class="tabel-scroll">
            <table class="daftar">
                <thead>
                    <tr>
                        <th>Peserta</th>
                        @switch($jenis)
                            @case('biodata')
                                <th>Status Biodata</th>
                                <th>Progres</th>
                                <th>Diajukan / Ditinjau</th>
                                @break
                            @case('logbook')
                                <th>Logbook Periode Ini</th>
                                <th>Kehadiran</th>
                                <th>Menunggu Verifikasi</th>
                                <th>Uang Saku</th>
                                @break
                            @case('penilaian')
                                <th>Masa Magang</th>
                                <th>Nilai Akhir</th>
                                <th>Predikat</th>
                                <th>Dinilai</th>
                                @break
                            @case('surat')
                                <th>Masa Magang</th>
                                <th>Status Surat</th>
                                <th>Syarat</th>
                                @break
                        @endswitch
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($baris as $item)
                        @php
                            $peserta = $item['peserta'];
                        @endphp
                        <tr>
                            <td class="nama">
                                <strong>{{ $peserta->nama }}</strong>
                                <span class="kecil">{{ $peserta->nomor_peserta }}{{ $peserta->asal_kampus ? ' · '.$peserta->asal_kampus : '' }}</span>
                                <span class="kecil">Status: {{ ucfirst($peserta->status) }}</span>
                            </td>

                            @switch($jenis)
                                @case('biodata')
                                    @php
                                        $biodata = $item['biodata'];
                                    @endphp
                                    @php
                                        $progres = $biodata->progres();
                                    @endphp
                                    <td><span class="badge {{ $warnaBiodata[$biodata->status] ?? 'b-abu' }}">{{ $biodata->exists ? $biodata->labelStatus() : 'Belum diisi' }}</span></td>
                                    <td>
                                        <div class="bar"><span style="width: {{ $progres['persen'] }}%"></span></div>
                                        <span class="kecil">{{ $progres['persen'] }}% data wajib</span>
                                    </td>
                                    <td>
                                        {{ $biodata->diajukan_pada?->translatedFormat('d M Y') ?? '—' }}
                                        @if ($biodata->ditinjau_pada)
                                            <span class="kecil">ditinjau {{ $biodata->ditinjau_pada->translatedFormat('d M Y') }}</span>
                                        @endif
                                    </td>
                                    <td class="aksi"><a href="{{ route('superadmin.peserta-magang.biodata', $peserta) }}">{{ $item['keadaan'] === 'diajukan' ? 'Tinjau' : 'Buka' }} →</a></td>
                                    @break

                                @case('logbook')
                                    @php
                                        $rekap = $item['rekap'];
                                    @endphp
                                    <td class="angka">
                                        @if ($item['periode'])
                                            {{ $item['terisi'] }} hari terisi
                                            <span class="kecil">{{ $rekap['belum_diisi'] }} hari kerja belum diisi · {{ $item['periode']['label'] }}</span>
                                            @if ($item['isi_hari_ini'] === true)
                                                <span class="badge b-hijau">Hari ini</span>
                                            @elseif ($item['isi_hari_ini'] === false)
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
                                        @endif
                                        @if ($item['izin_menunggu'])
                                            <span class="badge b-oranye">{{ $item['izin_menunggu'] }} izin</span>
                                        @endif
                                        @if (! $rekap['menunggu'] && ! $item['izin_menunggu'])
                                            <span class="kecil">—</span>
                                        @endif
                                    </td>
                                    <td class="angka">{{ $rupiah($rekap['uang_saku']) }}</td>
                                    <td class="aksi"><a href="{{ route('superadmin.peserta-magang.logbook-harian', $peserta) }}">Verifikasi →</a></td>
                                    @break

                                @case('penilaian')
                                    @php
                                        $penilaian = $item['penilaian'];
                                    @endphp
                                    <td>
                                        {{ $peserta->periode_mulai?->translatedFormat('d M Y') ?? '—' }}
                                        <span class="kecil">s.d. {{ $peserta->periode_selesai?->translatedFormat('d M Y') ?? '—' }}</span>
                                    </td>
                                    <td class="angka">
                                        @if ($penilaian)
                                            <strong>{{ number_format($penilaian->nilai_akhir, 2, ',', '.') }}</strong>
                                        @else
                                            <span class="badge b-abu">Belum dinilai</span>
                                        @endif
                                    </td>
                                    <td>{{ $penilaian?->predikat ?? '—' }}</td>
                                    <td>
                                        {{ $penilaian?->dinilai_pada?->translatedFormat('d M Y') ?? '—' }}
                                        @if ($penilaian?->penilai)
                                            <span class="kecil">oleh {{ $penilaian->penilai->name }}</span>
                                        @endif
                                    </td>
                                    <td class="aksi"><a href="{{ route('superadmin.peserta-magang.penilaian', $peserta) }}">{{ $penilaian ? 'Ubah' : 'Nilai' }} →</a></td>
                                    @break

                                @case('surat')
                                    @php
                                        $surat = $item['surat'];
                                    @endphp
                                    <td>
                                        {{ $peserta->periode_mulai?->translatedFormat('d M Y') ?? '—' }}
                                        <span class="kecil">s.d. {{ $peserta->periode_selesai?->translatedFormat('d M Y') ?? '—' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $warnaSurat[$item['keadaan']] }}">{{ $pilihanKeadaan[$item['keadaan']] }}</span>
                                        @if ($surat?->sudahTerbit())
                                            <span class="kecil">{{ $surat->nomor_surat }}</span>
                                        @elseif ($surat)
                                            <span class="kecil">Draft tersedia</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item['keadaan'] === 'terbit')
                                            <span class="kecil">—</span>
                                        @elseif ($item['syarat'])
                                            <ul class="syarat">
                                                @foreach ($item['syarat'] as $syarat)
                                                    <li>{{ $syarat }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="badge b-hijau">Lengkap</span>
                                        @endif
                                    </td>
                                    <td class="aksi"><a href="{{ route('superadmin.peserta-magang.surat', $peserta) }}">{{ $item['keadaan'] === 'siap' ? 'Terbitkan' : 'Buka' }} →</a></td>
                                    @break
                            @endswitch
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty">Tidak ada peserta yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>

@endsection
