{{-- resources/views/surat/cetak.blade.php --}}
{{-- Surat keterangan magang (dompdf): pakai tabel, tanpa flexbox/grid. --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Magang - {{ $data['nama'] }}</title>

    <style>
        @page {
            margin: 40px 56px 50px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #111827;
            font-size: 11px;
            line-height: 1.7;
        }

        .kop {
            padding-bottom: 8px;
            margin-bottom: 18px;

            border-bottom: 3px double #0b6a9c;

            text-align: center;
        }

        .kop .nama {
            color: #0b6a9c;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: .5px;
        }

        .kop .alamat {
            color: #6b7280;
            font-size: 9.5px;
            line-height: 1.5;
        }

        h1 {
            margin: 0;

            font-size: 13px;
            text-align: center;
            text-decoration: underline;
        }

        .nomor {
            margin-bottom: 18px;

            text-align: center;
        }

        table.data {
            margin: 6px 0 10px 28px;
        }

        table.data td {
            padding: 1px 6px 1px 0;

            vertical-align: top;
        }

        table.data .label {
            width: 140px;
        }

        p {
            margin: 8px 0;

            text-align: justify;
        }

        .penutup {
            width: 100%;
            margin-top: 22px;

            border-collapse: collapse;

            page-break-inside: avoid;
        }

        .penutup td {
            vertical-align: bottom;
        }

        .verifikasi {
            width: 50%;

            color: #6b7280;
            font-size: 8px;
            line-height: 1.4;
        }

        .verifikasi img {
            width: 78px;
            height: 78px;
        }

        .ttd {
            width: 260px;
            margin-left: auto;
        }

        .ttd .ruang {
            position: relative;

            height: 70px;
        }

        .ttd .ruang .gambar-ttd {
            position: absolute;
            top: 4px;
            left: 40px;

            height: 62px;
        }

        .ttd .ruang .gambar-stempel {
            position: absolute;
            top: -6px;
            left: -6px;

            height: 82px;

            opacity: .85;
        }

        .ttd .nama {
            font-weight: bold;
            text-decoration: underline;
        }

        .draft {
            position: fixed;
            top: 300px;
            left: 0;
            right: 0;

            color: rgba(198, 40, 40, .15);
            font-size: 90px;
            font-weight: bold;
            text-align: center;

            transform: rotate(-30deg);
        }

        .kop-logo {
            width: 100%;

            border-collapse: collapse;
        }

        .kop-logo td {
            vertical-align: middle;
        }

        .kop-logo-gambar {
            width: 60px;
        }

        .kop-logo-teks {
            text-align: center;
        }
    </style>
</head>

<body>

@php
    $tanggal = fn (?string $nilai) => $nilai ? \Carbon\CarbonImmutable::parse($nilai)->translatedFormat('d F Y') : '-';
    $perusahaan = $data['perusahaan'];
    $kontak = collect([
        $perusahaan['telepon'] ? 'Telp. '.$perusahaan['telepon'] : null,
        $perusahaan['email'],
        $perusahaan['website'],
    ])->filter()->implode(' · ');
@endphp

@if ($draft)
    <div class="draft">DRAFT</div>
@endif

<div class="kop">
    <table class="kop-logo">
        <tr>
            <td class="kop-logo-gambar">@include('partials.logo-pdf', ['tinggi' => 50])</td>
            <td class="kop-logo-teks">
            <div class="nama">{{ strtoupper($perusahaan['nama']) }}</div>
            @if ($perusahaan['alamat'])
                <div class="alamat">{{ $perusahaan['alamat'] }}{{ $perusahaan['kota'] ? ', '.$perusahaan['kota'] : '' }}</div>
            @endif
            @if ($kontak)
                <div class="alamat">{{ $kontak }}</div>
            @endif
            </td>
            <td class="kop-logo-gambar"></td>
        </tr>
    </table>
</div>

<h1>SURAT KETERANGAN MAHASISWA MAGANG</h1>
<div class="nomor">Nomor: {{ $nomor ?? '…/…/…/…' }}</div>

<p>
    Yang bertanda tangan di bawah ini{{ $data['penanda_tangan']['jabatan'] ? ', '.$data['penanda_tangan']['jabatan'] : '' }}
    {{ $perusahaan['nama'] }}, menerangkan bahwa:
</p>

<table class="data">
    <tr><td class="label">Nama</td><td>: {{ $data['nama'] }}</td></tr>
    <tr><td class="label">NIM</td><td>: {{ $data['nim'] ?: '-' }}</td></tr>
    <tr><td class="label">Perguruan Tinggi</td><td>: {{ $data['perguruan_tinggi'] ?: '-' }}</td></tr>
    <tr><td class="label">Fakultas / Jurusan</td><td>: {{ $data['fakultas_jurusan'] ?: '-' }}</td></tr>
    <tr><td class="label">Nomor Peserta</td><td>: {{ $data['nomor_peserta'] ?: '-' }}</td></tr>
</table>

@php
    $kalimat = e('telah melaksanakan Program Magang di '.$perusahaan['nama'].' sejak tanggal ')
        .'<strong>'.e($tanggal($data['periode_mulai'])).'</strong> sampai dengan <strong>'.e($tanggal($data['periode_selesai'])).'</strong>';

    if (! empty($data['divisi'])) {
        $kalimat .= e(' dengan penempatan di '.implode(', ', $data['divisi']));
    }

    if ($data['nilai_akhir'] !== null) {
        $kalimat .= ', dan memperoleh nilai akhir <strong>'.e(number_format((float) $data['nilai_akhir'], 2, ',', '.'))
            .'</strong> dengan predikat <strong>'.e($data['predikat']).'</strong>';
    }
@endphp

<p>{!! $kalimat !!}.</p>

<p>
    Selama menjalankan program magang, yang bersangkutan telah melaksanakan tugas dan kewajibannya dengan baik.
    Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.
</p>

{{-- QR verifikasi (kiri) dan tanda tangan + stempel (kanan); tidak boleh terpotong ke halaman berikutnya. --}}
<table class="penutup">
    <tr>
        <td class="verifikasi">
            @if ($qr)
                <img src="{{ $qr }}" alt="QR verifikasi"><br>
                Pindai QR untuk memeriksa keaslian surat ini:<br>
                {{ $urlVerifikasi }}
            @endif
        </td>
        <td>
            <table class="ttd">
                <tr><td>{{ $data['kota_surat'] ?: '........................' }}, {{ $tanggal($data['tanggal_surat']) }}</td></tr>
                <tr><td>{{ $data['penanda_tangan']['jabatan'] ?: '' }}</td></tr>
                <tr>
                    <td>
                        <div class="ruang">
                            @if ($stempel)
                                <img src="{{ $stempel }}" alt="" class="gambar-stempel">
                            @endif
                            @if ($ttd)
                                <img src="{{ $ttd }}" alt="" class="gambar-ttd">
                            @endif
                        </div>
                    </td>
                </tr>
                <tr><td class="nama">{{ $data['penanda_tangan']['nama'] ?: '(........................................)' }}</td></tr>
            </table>
        </td>
    </tr>
</table>

</body>

</html>
