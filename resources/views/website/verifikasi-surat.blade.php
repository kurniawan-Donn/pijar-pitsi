{{-- resources/views/website/verifikasi-surat.blade.php --}}
{{-- Halaman publik hasil pindai QR surat keterangan: hanya nama, periode, dan nomor surat. --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Verifikasi Surat Keterangan - {{ config('app.name') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-pitsi-128.png') }}">

    @include('partials.font')
    @include('partials.tema')

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 32px 16px;

            display: grid;
            place-items: center;

            background: var(--latar);
            color: var(--teks);

            font-family: var(--font);
            line-height: 1.6;
        }

        .kartu {
            width: 100%;
            max-width: 460px;
            padding: 28px 26px;

            border: 1px solid #e5eaf0;
            border-radius: 14px;

            background: var(--permukaan);

            box-shadow: 0 6px 20px rgba(15, 40, 65, .06);
        }

        .perusahaan {
            display: flex;
            align-items: center;
            gap: 10px;

            margin: 0 0 18px;

            color: var(--teks-sekunder);
            font-size: 13px;
        }

        .status {
            display: flex;
            align-items: center;
            gap: 10px;

            margin: 0 0 6px;

            font-size: 19px;
        }

        .status.asli {
            color: var(--sukses);
        }

        .status.tidak {
            color: var(--bahaya);
        }

        .status svg {
            flex-shrink: 0;
        }

        .keterangan {
            margin: 0 0 20px;

            color: var(--teks-sekunder);
            font-size: 13.5px;
        }

        dl {
            margin: 0;
        }

        dt {
            color: var(--teks-sekunder);
            font-size: 12px;
        }

        dd {
            margin: 0 0 12px;

            color: var(--utama-gelap);
            font-size: 15px;
            font-weight: 700;
        }

        .kembali {
            display: inline-block;

            margin-top: 6px;

            color: var(--utama);
            font-size: 13px;
            text-decoration: none;
        }
    </style>
</head>

<body>

<main class="kartu">
    <p class="perusahaan">
        <img src="{{ asset('images/logo-pitsi-128.png') }}" alt="" width="32" height="32">
        <span>{{ $namaPerusahaan }} · {{ config('app.name') }}</span>
    </p>

    @if ($surat)
        <h1 class="status asli">
            <x-icon name="circle-check" :size="24" />
            Surat keterangan asli
        </h1>
        <p class="keterangan">Surat ini tercatat di sistem magang dan diterbitkan oleh {{ $namaPerusahaan }}.</p>

        <dl>
            <dt>Nama</dt>
            <dd>{{ $surat->data['nama'] ?? '-' }}</dd>

            <dt>Periode Magang</dt>
            <dd>
                {{ isset($surat->data['periode_mulai']) ? \Carbon\CarbonImmutable::parse($surat->data['periode_mulai'])->translatedFormat('d F Y') : '-' }}
                s.d.
                {{ isset($surat->data['periode_selesai']) ? \Carbon\CarbonImmutable::parse($surat->data['periode_selesai'])->translatedFormat('d F Y') : '-' }}
            </dd>

            <dt>Nomor Surat</dt>
            <dd>{{ $surat->nomor_surat }}</dd>
        </dl>
    @else
        <h1 class="status tidak">
            <x-icon name="circle-x" :size="24" />
            Surat tidak ditemukan
        </h1>
        <p class="keterangan">
            Kode verifikasi tidak cocok dengan surat keterangan mana pun yang diterbitkan. Pastikan QR dipindai dari
            surat aslinya, atau hubungi {{ $namaPerusahaan }} untuk konfirmasi.
        </p>
    @endif

    <a href="{{ route('home') }}" class="kembali">Kembali ke beranda</a>
</main>

</body>

</html>
