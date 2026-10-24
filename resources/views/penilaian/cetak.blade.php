{{-- resources/views/penilaian/cetak.blade.php --}}
{{-- Lembar penilaian (format sheet PENILAIAN MHS MAGANG) untuk dompdf: pakai tabel, tanpa flexbox/grid. --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Penilaian {{ $peserta->nama }}</title>

    <style>
        @page {
            margin: 28px 34px 36px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1f2937;
            font-size: 9px;
            line-height: 1.45;
        }

        h1 {
            margin: 0 0 2px;

            color: #12345b;
            font-size: 14px;
            text-align: center;
        }

        h2 {
            margin: 12px 0 5px;

            color: #12345b;
            font-size: 10.5px;
        }

        .perusahaan {
            margin-bottom: 10px;

            color: #4b5d70;
            font-size: 10px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .identitas td {
            padding: 1px 0;
        }

        .identitas .label {
            width: 110px;
            color: #4b5d70;
        }

        .bergaris th,
        .bergaris td {
            padding: 3px 5px;

            border: 1px solid #9aa7b4;

            vertical-align: top;
        }

        .bergaris th {
            background: #e8eef5;
            text-align: left;
        }

        .tengah {
            text-align: center;
        }

        .aktif td {
            background: #eaf3fb;
            font-weight: bold;
        }

        .kotak {
            min-height: 30px;
            padding: 5px 7px;

            border: 1px solid #9aa7b4;

            white-space: pre-line;
        }

        .hasil {
            margin-top: 10px;
            padding: 7px 9px;

            border: 1px solid #12345b;

            background: #f3f7fb;
        }

        .ttd {
            margin-top: 18px;

            text-align: center;
        }

        .ttd td {
            width: 33%;

            vertical-align: top;
        }

        .ttd .ruang {
            height: 46px;
        }

        .ttd .nama {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>

<body>

@php
    $angka = fn ($nilai) => number_format((float) $nilai, 2, ',', '.');
    $perKelompok = $penilaian->nilaiPerKelompok();
    $detailPerKelompok = $penilaian->details->groupBy(fn ($detail) => $detail->kelompok ?? 'Aspek');
    $daftarPredikat = $predikats->values();
@endphp

<h1>PENILAIAN MAHASISWA MAGANG</h1>
<div class="perusahaan">{{ $perusahaan?->nama_perusahaan ?? $perusahaan?->nama ?? config('app.name') }}</div>

<table class="identitas">
    <tr><td class="label">Nama</td><td>: {{ $biodata->nama_lengkap ?: $peserta->nama }}</td></tr>
    <tr><td class="label">NIM</td><td>: {{ $biodata->nim ?: ($peserta->nim ?? '-') }}</td></tr>
    <tr><td class="label">Asal Kampus</td><td>: {{ $biodata->perguruan_tinggi ?: ($peserta->asal_kampus ?? '-') }}</td></tr>
    <tr><td class="label">Fakultas / Jurusan</td><td>: {{ $biodata->fakultas_jurusan ?: ($peserta->program_studi ?? '-') }}</td></tr>
    <tr><td class="label">Program Pendidikan</td><td>: {{ $biodata->labelJenisProgram() ?? '-' }}</td></tr>
    <tr>
        <td class="label">Periode Magang</td>
        <td>: {{ $peserta->periode_mulai?->translatedFormat('d F Y') ?? '-' }} s.d. {{ $peserta->periode_selesai?->translatedFormat('d F Y') ?? '-' }}</td>
    </tr>
</table>

<h2>Bobot Penilaian</h2>
<div>
    Penilaian menggunakan skala 1–100. Nilai setiap kelompok adalah rata-rata aspek di dalamnya. Nilai akhir =
    {{ collect($perKelompok)->map(fn ($data, $nama) => '(rata-rata '.$nama.' × '.$data['bobot'].'%)')->implode(' + ') }}.
</div>

<table class="bergaris" style="margin-top: 6px;">
    <tr><th style="width: 70px;">Nilai Akhir</th><th style="width: 100px;">Predikat</th><th>Keterangan</th></tr>
    @foreach ($daftarPredikat as $i => $predikat)
        <tr @class(['aktif' => $penilaian->predikat === $predikat->nama])>
            <td class="tengah">{{ $predikat->rentang($daftarPredikat[$i + 1] ?? null) }}</td>
            <td>{{ $predikat->nama }}</td>
            <td>{{ $predikat->keterangan }}</td>
        </tr>
    @endforeach
</table>

<h2>Indikator Penilaian</h2>
<table>
    <tr>
        @foreach ($detailPerKelompok as $kelompok => $details)
            <td style="vertical-align: top; width: {{ floor(100 / max(1, $detailPerKelompok->count())) }}%; padding-right: 6px;">
                <table class="bergaris">
                    <tr><th>{{ $kelompok }} ({{ $perKelompok[$kelompok]['bobot'] }}%)</th><th style="width: 34px;" class="tengah">Nilai</th></tr>
                    @foreach ($details as $detail)
                        <tr><td>{{ $loop->iteration }}. {{ $detail->nama_aspek }}</td><td class="tengah">{{ $detail->nilai }}</td></tr>
                    @endforeach
                    <tr><th>Nilai Total</th><th class="tengah">{{ $perKelompok[$kelompok]['total'] }}</th></tr>
                    <tr><th>Nilai Rata-rata</th><th class="tengah">{{ $angka($perKelompok[$kelompok]['rata_rata']) }}</th></tr>
                </table>
            </td>
        @endforeach
    </tr>
</table>

<table class="bergaris" style="margin-top: 6px;">
    <tr><th>Grand Total Nilai (Nilai Akhir)</th><th style="width: 120px;" class="tengah">{{ $angka($penilaian->nilai_akhir) }}</th></tr>
</table>

<h2>Kesimpulan Penilaian Mahasiswa Magang</h2>
<div>Kekuatan dan keunggulan mahasiswa magang:</div>
<div class="kotak">{{ $penilaian->kekuatan }}</div>
<div style="margin-top: 5px;">Aspek utama yang diperlukan untuk perbaikan mahasiswa magang:</div>
<div class="kotak">{{ $penilaian->aspek_perbaikan }}</div>
<div style="margin-top: 5px;">Langkah penting yang harus diambil untuk perbaikan bagi mahasiswa magang:</div>
<div class="kotak">{{ $penilaian->langkah_perbaikan }}</div>

<div class="hasil">
    <strong>Hasil Penilaian:</strong>
    Berdasarkan hasil penilaian yang telah dilakukan, mahasiswa dinyatakan telah menyelesaikan Program Magang
    di {{ $perusahaan?->nama_perusahaan ?? $perusahaan?->nama ?? config('app.name') }} dengan memperoleh nilai akhir sebesar
    <strong>{{ $angka($penilaian->nilai_akhir) }}</strong> sehingga memperoleh predikat <strong>{{ $penilaian->predikat }}</strong>.
</div>

<div style="margin-top: 12px;">
    {{ $pengaturan->kota_surat ?: '........................' }}, {{ ($penilaian->dinilai_pada ?? now())->translatedFormat('d F Y') }}
</div>

<table class="ttd">
    <tr>
        <td>Penilai,</td>
        <td>Mengetahui,</td>
        <td>Menyetujui,</td>
    </tr>
    <tr><td class="ruang"></td><td class="ruang"></td><td class="ruang"></td></tr>
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

</body>

</html>
