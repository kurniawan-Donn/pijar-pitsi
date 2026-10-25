{{-- resources/views/panduan/index.blade.php — buku panduan sesuai role (isi dari docs/panduan). --}}

@extends('layouts.dashboard')

@section('title', 'Panduan')
@section('page_title', 'Panduan')
@section('page_subtitle', 'Cara memakai PILARA sesuai peran Anda')

@push('styles')
<style>
    .tab-panduan {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;

        margin-bottom: 18px;
    }

    .tab-panduan a {
        display: inline-flex;
        align-items: center;
        gap: 7px;

        padding: 8px 14px;

        border: 1px solid var(--garis);
        border-radius: 999px;

        background: var(--permukaan);
        color: var(--teks-sekunder);

        font-size: 13px;
        font-weight: 600;
    }

    .tab-panduan a:hover {
        color: var(--teks);
    }

    .tab-panduan a.aktif {
        border-color: var(--utama);
        background: var(--utama-muda);
        color: var(--utama-gelap);
    }

    .tata-panduan {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 230px;
        gap: 22px;
        align-items: start;

        max-width: 1180px;
    }

    .isi-panduan {
        padding: 28px 32px 34px;

        color: var(--teks);
        font-size: 14.5px;
        line-height: 1.75;
    }

    .isi-panduan h1 {
        margin-bottom: 10px;

        color: var(--utama-gelap);
        font-size: 24px;
        line-height: 1.3;
    }

    .isi-panduan h2 {
        margin: 34px 0 10px;
        padding-top: 18px;

        border-top: 1px solid var(--garis);

        color: var(--utama-gelap);
        font-size: 18px;

        scroll-margin-top: 90px;
    }

    .isi-panduan h3 {
        margin: 22px 0 8px;

        font-size: 15px;
    }

    .isi-panduan p,
    .isi-panduan ul,
    .isi-panduan ol,
    .isi-panduan table,
    .isi-panduan blockquote,
    .isi-panduan pre {
        margin-bottom: 12px;
    }

    .isi-panduan ul,
    .isi-panduan ol {
        padding-left: 22px;
    }

    .isi-panduan li {
        margin-bottom: 4px;
    }

    .isi-panduan a {
        color: var(--utama-gelap);
        font-weight: 600;
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    .isi-panduan code {
        padding: 1px 6px;

        border-radius: 5px;

        background: var(--permukaan-redup);
        box-shadow: inset 0 0 0 1px var(--garis);

        font-size: 13px;
    }

    .isi-panduan pre {
        overflow-x: auto;

        padding: 12px 14px;

        border-radius: var(--radius-kecil);

        background: var(--permukaan-redup);
        box-shadow: inset 0 0 0 1px var(--garis);
    }

    .isi-panduan pre code {
        padding: 0;

        box-shadow: none;
    }

    .isi-panduan blockquote {
        padding: 10px 14px;

        border-left: 4px solid var(--utama);
        border-radius: 0 var(--radius-kecil) var(--radius-kecil) 0;

        background: var(--utama-muda);
    }

    .isi-panduan blockquote p:last-child {
        margin-bottom: 0;
    }

    .isi-panduan table {
        display: block;
        overflow-x: auto;

        width: 100%;

        border-collapse: collapse;

        font-size: 13.5px;
        line-height: 1.55;
    }

    .isi-panduan th,
    .isi-panduan td {
        padding: 8px 10px;

        border: 1px solid var(--garis);

        text-align: left;
        vertical-align: top;
    }

    .isi-panduan th {
        background: var(--permukaan-redup);
        font-weight: 700;
    }

    .gambar-panduan {
        display: block;

        margin: 14px 0 20px;

        text-decoration: none !important;
    }

    .gambar-panduan img {
        width: 100%;
        height: auto;

        display: block;

        border: 1px solid var(--garis);
        border-radius: var(--radius);

        box-shadow: 0 8px 24px rgba(17, 24, 39, .08);
    }

    .gambar-panduan span {
        display: block;

        margin-top: 6px;

        color: var(--teks-sekunder);
        font-size: 12.5px;
        font-weight: 500;
        text-align: center;
    }

    .daftar-isi {
        position: sticky;
        top: 88px;

        padding: 16px 16px 12px;
    }

    .daftar-isi strong {
        display: block;

        margin-bottom: 8px;

        color: var(--teks-redup);
        font-size: 11px;
        letter-spacing: .6px;
        text-transform: uppercase;
    }

    .daftar-isi a {
        display: block;

        padding: 5px 8px;

        border-radius: 6px;

        color: var(--teks-sekunder);
        font-size: 13px;
        line-height: 1.4;
    }

    .daftar-isi a:hover {
        background: var(--permukaan-redup);
        color: var(--teks);
    }

    /* Diagram alur (pengganti Mermaid). */
    .diagram-alur {
        margin: 16px 0 22px;
    }

    .diagram-legenda {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;

        margin-bottom: 12px;

        font-size: 12.5px;
    }

    .diagram-legenda span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .diagram-legenda i {
        width: 12px;
        height: 12px;

        border-radius: 3px;

        background: var(--warna-pelaku);
    }

    .pelaku-mahasiswa { --warna-pelaku: var(--aksen); }
    .pelaku-admin { --warna-pelaku: var(--utama); }
    .pelaku-sistem { --warna-pelaku: var(--sukses); }

    .diagram-langkah {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;

        margin: 0;
        padding: 0 !important;

        list-style: none;
        counter-reset: langkah;
    }

    .diagram-langkah li {
        position: relative;

        display: flex;
        gap: 10px;

        margin: 0;
        padding: 10px 12px;

        border: 1px solid var(--garis);
        border-left: 4px solid var(--warna-pelaku);
        border-radius: var(--radius-kecil);

        background: var(--permukaan);

        line-height: 1.4;
    }

    .diagram-langkah .nomor {
        flex-shrink: 0;

        width: 24px;
        height: 24px;

        display: grid;
        place-items: center;

        border-radius: 50%;

        background: var(--warna-pelaku);
        color: #ffffff;

        font-size: 12px;
        font-weight: 700;
    }

    .diagram-langkah small {
        display: block;

        color: var(--teks-redup);
        font-size: 11px;
    }

    .diagram-langkah strong {
        display: block;

        font-size: 13.5px;
    }

    .diagram-langkah span:not(.nomor) {
        display: block;

        margin-top: 2px;

        color: var(--teks-sekunder);
        font-size: 12px;
    }

    @media (max-width: 1100px) {
        .tata-panduan {
            grid-template-columns: 1fr;
        }

        .daftar-isi {
            display: none;
        }

        .diagram-langkah {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .isi-panduan {
            padding: 20px 16px 24px;

            font-size: 14px;
        }

        .diagram-langkah {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <nav class="tab-panduan" aria-label="Bagian panduan">
        @foreach ($tab as $slug => $judul)
            <a href="{{ route('panduan', $slug) }}" @class(['aktif' => $slug === $bagian]) @if ($slug === $bagian) aria-current="page" @endif>
                <x-icon :name="$slug === 'alur' ? 'network' : 'book-open'" :size="16" />
                {{ $judul }}
            </a>
        @endforeach
    </nav>

    <div class="tata-panduan">
        <article class="panel isi-panduan">
            {!! $html !!}
        </article>

        @if ($daftarIsi)
            <aside class="panel daftar-isi" aria-label="Daftar isi">
                <strong>Daftar Isi</strong>
                @foreach ($daftarIsi as $item)
                    <a href="#{{ $item['id'] }}">{{ $item['judul'] }}</a>
                @endforeach
            </aside>
        @endif
    </div>

</div>

@endsection
