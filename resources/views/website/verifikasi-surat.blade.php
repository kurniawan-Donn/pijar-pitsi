{{-- resources/views/website/verifikasi-surat.blade.php --}}
{{-- Halaman publik hasil pindai QR surat keterangan: hanya nama, periode, dan nomor surat. --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Verifikasi Surat Keterangan - {{ config('app.name') }}</title>

    <style>
        :root {
            --latar: #f7f9fb;
            --permukaan: #ffffff;
            --utama: #2688b4;
            --utama-gelap: #0b6a9c;
            --teks: #1f2937;
            --teks-sekunder: #6b7280;
            --berhasil: #067647;
            --gagal: #b42318;
        }

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

            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
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
            color: var(--berhasil);
        }

        .status.tidak {
            color: var(--gagal);
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
    <p class="perusahaan">{{ $namaPerusahaan }}</p>

    @if ($surat)
        <h1 class="status asli">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
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
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></svg>
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
