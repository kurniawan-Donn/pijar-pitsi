{{-- resources/views/superadmin/laporan/pdf.blade.php --}}
{{-- Template untuk dompdf: hindari flexbox/grid, pakai tabel. --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $laporan['judul'] }}</title>

    <style>
        @page {
            margin: 28px 30px 40px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1f2937;
            font-size: 9px;
        }

        .kop {
            width: 100%;
            border-bottom: 2px solid #12345b;
            margin-bottom: 10px;
        }

        .kop td {
            vertical-align: bottom;
            padding-bottom: 6px;
        }

        h1 {
            margin: 0;
            color: #12345b;
            font-size: 15px;
        }

        .sub {
            color: #4b5d70;
            font-size: 10px;
        }

        .meta {
            color: #6b7c8d;
            text-align: right;
            font-size: 8px;
        }

        .ringkasan {
            margin-bottom: 10px;
            color: #33475b;
        }

        .ringkasan b {
            color: #12345b;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th,
        table.data td {
            padding: 4px 5px;
            border: 1px solid #c9d3dd;
            text-align: left;
        }

        table.data th {
            background: #eaf1f7;
            font-size: 8px;
            text-transform: uppercase;
        }

        table.data tr:nth-child(even) td {
            background: #f8fafc;
        }

        table.data td.angka {
            text-align: right;
        }

        .ttd {
            width: 100%;
            margin-top: 30px;
        }

        .ttd td {
            text-align: center;
        }

        .garis {
            margin: 50px auto 4px;
            width: 180px;
            border-top: 1px solid #1f2937;
        }

        .footer {
            position: fixed;
            bottom: -24px;
            left: 0;
            right: 0;
            color: #8a96a3;
            font-size: 8px;
        }
    </style>
</head>

<body>

    <div class="footer">MAGENTA · Sistem Informasi Magang — dicetak {{ now()->translatedFormat('d F Y, H:i') }}</div>

    <table class="kop">
        <tr>
            <td>
                <h1>{{ $laporan['judul'] }}</h1>
                <div class="sub">{{ $laporan['keterangan'] }}</div>
            </td>
            <td class="meta">
                MAGENTA · Sistem Informasi Magang<br>
                Oleh {{ auth()->user()->name }}
            </td>
        </tr>
    </table>

    <div class="ringkasan">
        @foreach ($laporan['ringkasan'] as $label => $nilai)
            {{ $label }}: <b>{{ $nilai }}</b>@if (! $loop->last) &nbsp;·&nbsp; @endif
        @endforeach
    </div>

    <table class="data">
        <thead>
            <tr>
                @foreach ($laporan['kolom'] as $kolom)
                    <th>{{ $kolom }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($laporan['baris'] as $baris)
                <tr>
                    @foreach ($baris as $index => $sel)
                        <td @class(['angka' => is_int($sel) || is_float($sel)])>
                            @if (($laporan['kolom'][$index] ?? '') === 'Uang Saku (Rp)' && is_int($sel))
                                {{ number_format($sel, 0, ',', '.') }}
                            @else
                                {{ $sel }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($laporan['kolom']) }}" style="text-align: center;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="ttd">
        <tr>
            <td style="width: 65%;"></td>
            <td>
                Mengetahui,
                <div class="garis"></div>
                Pembimbing / Mentor
            </td>
        </tr>
    </table>

</body>

</html>
