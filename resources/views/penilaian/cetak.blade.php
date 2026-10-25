{{-- resources/views/penilaian/cetak.blade.php --}}
{{--
    Lembar penilaian (format sheet PENILAIAN MHS MAGANG) untuk dompdf: pakai tabel, tanpa flexbox/grid.
    Harus selalu muat 1 halaman A4; panjang kolom kesimpulan dibatasi SimpanPenilaianRequest::MAKS_KESIMPULAN.
--}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Penilaian {{ $peserta->nama }}</title>

    <style>
        @page {
            margin: 22px 30px 20px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1f2937;
            font-size: 8px;
            line-height: 1.35;
        }

        h1 {
            margin: 0;

            color: #0b6a9c;
            font-size: 12.5px;
            text-align: center;
            letter-spacing: .5px;
        }

        h2 {
            margin: 8px 0 3px;

            color: #0b6a9c;
            font-size: 9px;
        }

        .perusahaan {
            margin-bottom: 6px;
            padding-bottom: 5px;

            border-bottom: 1.5px solid #0b6a9c;

            color: #6b7280;
            font-size: 8.5px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .identitas td {
            padding: 0.5px 0;
            vertical-align: top;
        }

        .identitas .label {
            width: 92px;
            color: #6b7280;
        }

        .bergaris th,
        .bergaris td {
            padding: 2px 4px;

            border: 0.75px solid #9ca3af;

            vertical-align: top;
        }

        .bergaris th {
            background: #e8f1f7;
            text-align: left;
        }

        .tengah {
            text-align: center !important;
        }

        .predikat td {
            padding: 2px 3px;

            border: 0.75px solid #9ca3af;

            font-size: 7.5px;
            text-align: center;
        }

        .predikat .aktif {
            background: #0b6a9c;
            color: #ffffff;
            font-weight: bold;
        }

        .catatan {
            margin-top: 2px;

            color: #6b7280;
            font-size: 7.5px;
        }

        .label-kotak {
            margin-top: 3px;

            color: #6b7280;
        }

        .kotak {
            min-height: 22px;
            padding: 3px 5px;

            border: 0.75px solid #9ca3af;

            white-space: pre-line;
        }

        .hasil {
            margin-top: 6px;
            padding: 4px 6px;

            border: 0.75px solid #0b6a9c;

            background: #f1f7fb;
        }

        .blok-ttd {
            margin-top: 6px;

            page-break-inside: avoid;
        }

        .ttd td {
            width: 33%;

            text-align: center;
            vertical-align: top;
        }

        .ttd .ruang {
            position: relative;

            height: 44px;
        }

        .ttd .gambar-ttd {
            position: absolute;
            top: 2px;
            left: 50%;

            height: 40px;
            margin-left: -50px;
        }

        .ttd .gambar-stempel {
            position: absolute;
            top: -6px;
            left: 50%;

            height: 56px;
            margin-left: -95px;

            opacity: .85;
        }

        .ttd .nama {
            font-weight: bold;
            text-decoration: underline;
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
    $angka = fn ($nilai) => number_format((float) $nilai, 2, ',', '.');
    $perKelompok = $penilaian->nilaiPerKelompok();
    $detailPerKelompok = $penilaian->details->groupBy(fn ($detail) => $detail->kelompok ?? 'Aspek');
    $daftarPredikat = $predikats->values();
    $predikatAktif = $daftarPredikat->firstWhere('nama', $penilaian->predikat);
    $namaPerusahaan = $perusahaan?->nama_perusahaan ?? $perusahaan?->nama ?? config('app.name');
@endphp

<table class="kop-logo">
    <tr>
        <td class="kop-logo-gambar">@include('partials.logo-pdf', ['tinggi' => 30])</td>
        <td class="kop-logo-teks">
            <h1>PENILAIAN MAHASISWA MAGANG</h1>
            <div class="perusahaan" style="border: 0; margin: 0; padding: 0;">{{ $namaPerusahaan }}</div>
        </td>
        <td class="kop-logo-gambar"></td>
    </tr>
</table>
<div class="perusahaan"></div>

{{-- Identitas dua kolom agar hemat tempat. --}}
<table class="identitas">
    <tr>
        <td class="label">Nama</td><td style="width: 42%;">: {{ $biodata->nama_lengkap ?: $peserta->nama }}</td>
        <td class="label">Fakultas / Jurusan</td><td>: {{ $biodata->fakultas_jurusan ?: ($peserta->program_studi ?? '-') }}</td>
    </tr>
    <tr>
        <td class="label">NIM</td><td>: {{ $biodata->nim ?: ($peserta->nim ?? '-') }}</td>
        <td class="label">Program Pendidikan</td><td>: {{ $biodata->labelJenisProgram() ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Asal Kampus</td><td>: {{ $biodata->perguruan_tinggi ?: ($peserta->asal_kampus ?? '-') }}</td>
        <td class="label">Periode Magang</td>
        <td>: {{ $peserta->periode_mulai?->translatedFormat('d F Y') ?? '-' }} s.d. {{ $peserta->periode_selesai?->translatedFormat('d F Y') ?? '-' }}</td>
    </tr>
</table>

<h2>Bobot dan Predikat Penilaian</h2>
<div>
    Skala 1–100. Nilai kelompok = rata-rata aspek di dalamnya. Nilai akhir =
    {{ collect($perKelompok)->map(fn ($data, $nama) => '(rata-rata '.$nama.' × '.$data['bobot'].'%)')->implode(' + ') }}.
</div>

{{-- Predikat diringkas menjadi satu baris; keterangan hanya untuk predikat yang diperoleh. --}}
<table class="predikat" style="margin-top: 3px;">
    <tr>
        @foreach ($daftarPredikat as $i => $predikat)
            <td @class(['aktif' => $penilaian->predikat === $predikat->nama])>
                {{ $predikat->rentang($daftarPredikat[$i + 1] ?? null) }}<br>{{ $predikat->nama }}
            </td>
        @endforeach
    </tr>
</table>
@if ($predikatAktif?->keterangan)
    <div class="catatan"><strong>{{ $predikatAktif->nama }}:</strong> {{ $predikatAktif->keterangan }}</div>
@endif

<h2>Indikator Penilaian</h2>
<table>
    <tr>
        @foreach ($detailPerKelompok as $kelompok => $details)
            <td style="vertical-align: top; width: {{ floor(100 / max(1, $detailPerKelompok->count())) }}%; padding: 0 {{ $loop->last ? 0 : 4 }}px 0 {{ $loop->first ? 0 : 4 }}px;">
                <table class="bergaris">
                    <tr><th>{{ $kelompok }} ({{ $perKelompok[$kelompok]['bobot'] }}%)</th><th style="width: 28px;" class="tengah">Nilai</th></tr>
                    @foreach ($details as $detail)
                        <tr><td>{{ $loop->iteration }}. {{ $detail->nama_aspek }}</td><td class="tengah">{{ $detail->nilai }}</td></tr>
                    @endforeach
                    <tr><th>Total / Rata-rata</th><th class="tengah" style="white-space: nowrap;">{{ $perKelompok[$kelompok]['total'] }} / {{ $angka($perKelompok[$kelompok]['rata_rata']) }}</th></tr>
                </table>
            </td>
        @endforeach
    </tr>
</table>

<table class="bergaris" style="margin-top: 4px;">
    <tr>
        <th>Grand Total Nilai (Nilai Akhir)</th>
        <th style="width: 90px;" class="tengah">{{ $angka($penilaian->nilai_akhir) }}</th>
        <th style="width: 110px;" class="tengah">{{ $penilaian->predikat }}</th>
    </tr>
</table>

<h2>Kesimpulan Penilaian Mahasiswa Magang</h2>
<div class="label-kotak">Kekuatan dan keunggulan mahasiswa magang:</div>
<div class="kotak">{{ $penilaian->kekuatan }}</div>
<div class="label-kotak">Aspek utama yang diperlukan untuk perbaikan mahasiswa magang:</div>
<div class="kotak">{{ $penilaian->aspek_perbaikan }}</div>
<div class="label-kotak">Langkah penting yang harus diambil untuk perbaikan bagi mahasiswa magang:</div>
<div class="kotak">{{ $penilaian->langkah_perbaikan }}</div>

<div class="hasil">
    <strong>Hasil Penilaian:</strong>
    Berdasarkan hasil penilaian yang telah dilakukan, mahasiswa dinyatakan telah menyelesaikan Program Magang
    di {{ $namaPerusahaan }} dengan memperoleh nilai akhir sebesar
    <strong>{{ $angka($penilaian->nilai_akhir) }}</strong> sehingga memperoleh predikat <strong>{{ $penilaian->predikat }}</strong>.
</div>

{{-- Blok tanggal + tanda tangan tidak boleh terpotong ke halaman berikutnya. --}}
<div class="blok-ttd">
    <div style="margin-bottom: 4px; text-align: right;">
        {{ $pengaturan->kota_surat ?: '........................' }}, {{ ($penilaian->dinilai_pada ?? now())->translatedFormat('d F Y') }}
    </div>

    <table class="ttd">
        <tr>
            <td>Penilai,</td>
            <td>Mengetahui,</td>
            <td>Menyetujui,</td>
        </tr>
        <tr>
            <td><div class="ruang"></div></td>
            <td>
                <div class="ruang">
                    @if ($ttdMengetahui)
                        <img src="{{ $ttdMengetahui }}" alt="" class="gambar-ttd">
                    @endif
                </div>
            </td>
            <td>
                <div class="ruang">
                    @if ($stempel)
                        <img src="{{ $stempel }}" alt="" class="gambar-stempel">
                    @endif
                    @if ($ttdMenyetujui)
                        <img src="{{ $ttdMenyetujui }}" alt="" class="gambar-ttd">
                    @endif
                </div>
            </td>
        </tr>
        <tr>
            <td class="nama">{{ $penempatan?->mentor_nama ?: ($biodata->mentor_magang ?: '(Pembimbing / Mentor)') }}</td>
            <td class="nama">{{ $pengaturan->mengetahui_nama ?: '(........................)' }}</td>
            <td class="nama">{{ $pengaturan->menyetujui_nama ?: '(........................)' }}</td>
        </tr>
        <tr>
            <td>{{ $penempatan?->mentor_jabatan ?: '' }}</td>
            <td>{{ $pengaturan->mengetahui_jabatan }}</td>
            <td>{{ $pengaturan->menyetujui_jabatan }}</td>
        </tr>
    </table>
</div>

</body>

</html>
