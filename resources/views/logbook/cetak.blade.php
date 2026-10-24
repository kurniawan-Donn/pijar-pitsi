{{-- resources/views/logbook/cetak.blade.php --}}
{{-- Template dompdf (format sheet bulanan "DAILY AKTIVITY"): hindari flexbox/grid, pakai tabel. --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Logbook {{ $peserta->nama }} - {{ $periode['label'] }}</title>

    <style>
        @page {
            margin: 26px 28px 34px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1f2937;
            font-size: 8.5px;
        }

        h1 {
            margin: 0 0 2px;

            color: #12345b;
            font-size: 14px;
            text-align: center;
        }

        .perusahaan {
            margin-bottom: 10px;

            color: #4b5d70;
            font-size: 10px;
            text-align: center;
        }

        .identitas {
            width: 100%;
            margin-bottom: 8px;

            font-size: 9px;
        }

        .identitas td {
            padding: 1px 0;
        }

        .identitas .label {
            width: 70px;
            color: #4b5d70;
        }

        table.harian {
            width: 100%;
            border-collapse: collapse;
        }

        table.harian th,
        table.harian td {
            padding: 3px 4px;

            border: 1px solid #9aa7b4;

            vertical-align: top;
        }

        table.harian th {
            background: #e8eef5;

            font-size: 8px;
            text-align: center;
        }

        .tengah {
            text-align: center;
        }

        .libur td {
            background: #f3f4f6;
            color: #6b7280;
        }

        .footer {
            width: 100%;
            margin-top: 10px;
        }

        .footer td {
            padding: 1px 0;

            font-size: 9px;
            vertical-align: top;
        }

        .ttd {
            width: 100%;
            margin-top: 16px;

            text-align: center;
        }

        .ttd td {
            width: 25%;

            font-size: 9px;
            vertical-align: top;
        }

        .ttd .ruang {
            height: 48px;
        }

        .ttd .nama {
            font-weight: bold;
            text-decoration: underline;
        }

        .catatan {
            margin-top: 8px;

            color: #6b7280;
            font-size: 7.5px;
        }
    </style>
</head>

<body>

@php
    $rupiah = fn (int $nilai) => 'Rp '.number_format($nilai, 0, ',', '.');
    $no = 0;
@endphp

<h1>DAILY AKTIVITY MAHASISWA MAGANG</h1>
<div class="perusahaan">{{ $perusahaan?->nama_perusahaan ?? $perusahaan?->nama ?? config('app.name') }}</div>

<table class="identitas">
    <tr>
        <td class="label">Nama</td><td>: {{ $biodata->nama_lengkap ?: $peserta->nama }}</td>
        <td class="label">Periode</td><td>: {{ $periode['label'] }} ({{ $periode['mulai']->translatedFormat('d M Y') }} – {{ $periode['selesai']->translatedFormat('d M Y') }})</td>
    </tr>
    <tr>
        <td class="label">Divisi</td><td>: {{ $penempatan?->divisi->nama ?? '-' }}</td>
        <td class="label">No. Peserta</td><td>: {{ $peserta->nomor_peserta ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Kampus</td><td>: {{ $biodata->perguruan_tinggi ?: ($peserta->asal_kampus ?? '-') }}</td>
        <td class="label">NIM</td><td>: {{ $biodata->nim ?: ($peserta->nim ?? '-') }}</td>
    </tr>
</table>

<table class="harian">
    <thead>
        <tr>
            <th rowspan="2" style="width: 16px;">No</th>
            <th rowspan="2" style="width: 52px;">Tanggal</th>
            <th colspan="2">Pagi</th>
            <th colspan="2">Siang</th>
            <th rowspan="2">Activity</th>
            <th rowspan="2" style="width: 90px;">Output</th>
            <th rowspan="2" style="width: 60px;">Lokasi</th>
            <th rowspan="2" style="width: 44px;">Action</th>
        </tr>
        <tr>
            <th style="width: 30px;">Masuk</th>
            <th style="width: 30px;">Keluar</th>
            <th style="width: 30px;">Masuk</th>
            <th style="width: 30px;">Keluar</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($hari as $baris)
            @continue($baris['akses'] === 'di_luar')
            @php
                $logbook = $baris['logbook'];
                $no++;
            @endphp
            <tr @class(['libur' => ! $logbook && $baris['libur']])>
                <td class="tengah">{{ $no }}</td>
                <td>{{ $baris['tanggal']->translatedFormat('D, d/m') }}</td>
                @foreach (\App\Models\LogbookHarian::KOLOM_JAM as $kolom)
                    <td class="tengah">{{ $logbook?->jam($kolom) }}</td>
                @endforeach
                <td>
                    @if ($logbook)
                        @if ($logbook->kehadiran !== 'hadir')
                            [{{ $logbook->labelKehadiran() }}]
                        @endif
                        {{ $logbook->activity }}
                    @else
                        {{ $baris['libur'] }}
                    @endif
                </td>
                <td>{{ $logbook?->output }}</td>
                <td>{{ $logbook?->lokasi }}</td>
                <td class="tengah">{{ $logbook && $logbook->status_verifikasi !== 'menunggu' ? $logbook->labelStatus() : '' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="footer">
    <tr>
        <td style="width: 55%;">
            <table>
                <tr><td style="width: 150px;">Total Masuk Full</td><td>: {{ $rekap['hari_penuh'] }} hari</td></tr>
                <tr><td>Total Masuk Setengah Hari</td><td>: {{ $rekap['setengah_hari'] }} hari</td></tr>
                <tr><td>Total Absen / Izin / Sakit</td><td>: {{ $rekap['izin'] + $rekap['sakit'] }} hari</td></tr>
                <tr><td>Upah Kedatangan Full</td><td>: {{ $rupiah($tarif->tarif_hari_penuh) }}</td></tr>
                <tr><td>Upah Kedatangan Setengah Hari</td><td>: {{ $rupiah($tarif->tarif_setengah_hari) }}</td></tr>
                <tr><td><strong>Total Uang Saku</strong></td><td><strong>: {{ $rupiah($rekap['uang_saku']) }}</strong></td></tr>
            </table>
        </td>
        <td>
            <table>
                <tr><td style="width: 90px;">Nomor Rekening</td><td>: {{ $biodata->no_rekening ?: '-' }}</td></tr>
                <tr><td>Nama Bank</td><td>: {{ $biodata->nama_bank ?: '-' }}</td></tr>
                <tr><td>Atas Nama</td><td>: {{ $biodata->atas_nama_rekening ?: '-' }}</td></tr>
            </table>
        </td>
    </tr>
</table>

<table class="ttd">
    <tr>
        <td>Peserta Magang,</td>
        <td>Mengetahui,</td>
        <td>Mengetahui,</td>
        <td>Menyetujui,</td>
    </tr>
    <tr>
        <td class="ruang"></td><td class="ruang"></td><td class="ruang"></td><td class="ruang"></td>
    </tr>
    <tr>
        <td class="nama">{{ $biodata->nama_lengkap ?: $peserta->nama }}</td>
        <td class="nama">{{ $penempatan?->mentor_nama ?: '(Pembimbing / Mentor)' }}</td>
        <td class="nama">{{ $pengaturan->mengetahui_nama ?: '(........................)' }}</td>
        <td class="nama">{{ $pengaturan->menyetujui_nama ?: '(........................)' }}</td>
    </tr>
    <tr>
        <td>{{ $biodata->nim ?: $peserta->nim }}</td>
        <td>{{ $penempatan?->mentor_jabatan ?: ($penempatan ? $penempatan->divisi->nama : '') }}</td>
        <td>{{ $pengaturan->mengetahui_jabatan }}</td>
        <td>{{ $pengaturan->menyetujui_jabatan }}</td>
    </tr>
</table>

@if (count($rekap['tarif']) > 1)
    <div class="catatan">
        Tarif berubah dalam periode ini; uang saku dihitung per hari dengan tarif yang berlaku pada tanggalnya.
    </div>
@endif

</body>

</html>
