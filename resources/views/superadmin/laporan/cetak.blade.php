{{-- resources/views/superadmin/laporan/cetak.blade.php --}}
{{-- Versi cetak laporan: gunakan "Cetak" lalu "Simpan sebagai PDF" di browser. --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $laporan['judul'] }} - MAGENTA</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 14mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            padding: 24px;

            font-family: Arial, Helvetica, sans-serif;
            color: #1f2937;
            font-size: 11px;
        }

        .toolbar {
            display: flex;
            gap: 8px;

            margin-bottom: 18px;
        }

        .toolbar button {
            padding: 9px 16px;

            border: 1px solid #1779c9;
            border-radius: 7px;

            background: #1779c9;
            color: #ffffff;

            font-weight: 700;

            cursor: pointer;
        }

        .toolbar button.secondary {
            background: #ffffff;
            color: #1779c9;
        }

        header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;

            padding-bottom: 10px;
            margin-bottom: 12px;

            border-bottom: 2px solid #12345b;
        }

        h1 {
            color: #12345b;
            font-size: 18px;
        }

        header p {
            margin-top: 3px;
            color: #4b5d70;
        }

        .meta {
            color: #6b7c8d;
            text-align: right;
        }

        .summary {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;

            margin-bottom: 12px;
        }

        .summary strong {
            color: #12345b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 6px 7px;

            border: 1px solid #c9d3dd;

            text-align: left;
        }

        th {
            background: #eaf1f7;
            font-size: 10px;
            text-transform: uppercase;
        }

        tr:nth-child(even) td {
            background: #f8fafc;
        }

        td.angka {
            text-align: right;
        }

        .ttd {
            display: flex;
            justify-content: flex-end;

            margin-top: 40px;
        }

        .ttd div {
            width: 220px;
            text-align: center;
        }

        .ttd .garis {
            margin-top: 60px;
            border-top: 1px solid #1f2937;
        }

        @media print {
            body {
                padding: 0;
            }

            .toolbar {
                display: none;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }

            th,
            tr:nth-child(even) td {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    <div class="toolbar">
        <button type="button" onclick="window.print()">🖨 Cetak / Simpan PDF</button>
        <button type="button" class="secondary" onclick="window.close()">Tutup</button>
    </div>

    <header>
        <div>
            <h1>{{ $laporan['judul'] }}</h1>
            <p>{{ $laporan['keterangan'] }}</p>
        </div>
        <div class="meta">
            MAGENTA · Sistem Informasi Magang<br>
            Dicetak {{ now()->translatedFormat('d F Y, H:i') }} oleh {{ auth()->user()->name }}
        </div>
    </header>

    <div class="summary">
        @foreach ($laporan['ringkasan'] as $label => $nilai)
            <span>{{ $label }}: <strong>{{ $nilai }}</strong></span>
        @endforeach
    </div>

    <table>
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

    <div class="ttd">
        <div>
            Mengetahui,
            <div class="garis"></div>
            Pembimbing / Mentor
        </div>
    </div>

</body>

</html>
