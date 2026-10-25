{{-- resources/views/superadmin/peserta-magang/surat.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Surat Keterangan Magang')
@section('page_title', 'Surat Keterangan Magang')
@section('page_subtitle', $pesertaMagang->nama)

@php
    $tanggal = fn (?string $nilai) => $nilai ? \Carbon\CarbonImmutable::parse($nilai)->translatedFormat('d F Y') : '-';
    $isi = $surat?->sudahTerbit() ? $surat->data : $pratinjau;
@endphp

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .top {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;

        margin-bottom: 16px;
    }

    .grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.2fr);
        gap: 18px;
        align-items: start;
    }

    .panel {
        margin-bottom: 18px;
        padding: 20px 22px;

        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel h3 {
        margin-bottom: 12px;

        color: #17324d;
        font-size: 15px;
    }

    .status {
        display: inline-block;

        padding: 3px 10px;

        border-radius: 999px;

        font-size: 11px;
        font-weight: 800;
    }

    .status-draft { background: #fff4e5; color: #b54708; }
    .status-terbit { background: #e7f6ec; color: #067647; }

    .syarat {
        margin: 12px 0;
        padding: 0;

        list-style: none;

        font-size: 13px;
    }

    .syarat li {
        padding: 5px 0;
    }

    .syarat .ok { color: #067647; }
    .syarat .belum { color: #c62828; }

    .info {
        color: #52646e;
        font-size: 13px;
        line-height: 1.7;
    }

    .check {
        display: flex;
        align-items: center;
        gap: 8px;

        margin-bottom: 14px;

        font-size: 13px;
    }

    .check input {
        width: 17px;
        height: 17px;

        accent-color: #1779c9;
    }

    .aksi {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .kertas {
        padding: 26px 30px;

        border: 1px solid #e5eaf0;
        border-radius: 8px;

        background: #fffefb;

        font-size: 13px;
        line-height: 1.75;
    }

    .kertas h4 {
        margin: 0;

        font-size: 14px;
        text-align: center;
        text-decoration: underline;
    }

    .kertas .nomor {
        margin-bottom: 14px;

        text-align: center;
    }

    .kertas table td {
        padding: 1px 10px 1px 0;

        vertical-align: top;
    }

    @media (max-width: 1000px) {
        .grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 16px 30px;
        }

        .kertas {
            padding: 18px;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <div class="top">
        <x-button variant="secondary" :href="route('superadmin.peserta-magang.show', $pesertaMagang)">← Detail peserta</x-button>
    </div>

    <x-flash-messages />

    <div class="grid">

        <div>
            <section class="panel">
                <h3>
                    Status
                    @if ($surat)
                        <span class="status status-{{ $surat->status }}">{{ $surat->sudahTerbit() ? 'Terbit' : 'Draft' }}</span>
                    @endif
                </h3>

                @if (! $surat)
                    <p class="info">
                        Draft surat dibuat otomatis saat status peserta diubah menjadi <strong>selesai</strong>.
                    </p>
                    @if ($pesertaMagang->status === 'selesai')
                        <form method="POST" action="{{ route('superadmin.peserta-magang.surat.draft', $pesertaMagang) }}">
                            @csrf
                            <x-button>Buat Draft Surat</x-button>
                        </form>
                    @endif
                @else
                    @if ($surat->sudahTerbit())
                        <p class="info">
                            Nomor <strong>{{ $surat->nomor_surat }}</strong>, tanggal {{ $surat->tanggal_surat?->translatedFormat('d F Y') }}.<br>
                            Diterbitkan {{ $surat->diterbitkan_pada?->translatedFormat('d F Y H:i') }}{{ $surat->penerbit ? ' oleh '.$surat->penerbit->name : '' }}.
                            Peserta sudah bisa mengunduh surat, dan feedback peserta terkunci.
                            @if ($surat->urlVerifikasi())
                                <br>Halaman verifikasi (QR di surat): <a href="{{ $surat->urlVerifikasi() }}" target="_blank" rel="noopener">buka</a>
                            @endif
                        </p>
                    @else
                        <p class="info">Nomor diberikan saat terbit, mis. <strong>{{ $contohNomor }}</strong> (format di Pengaturan Magang).</p>
                    @endif

                    <ul class="syarat">
                        @foreach ([
                            'Status peserta selesai' => $pesertaMagang->status === 'selesai',
                            'Biodata disetujui' => $pesertaMagang->biodata?->status === 'disetujui',
                            'Penilaian akhir diisi' => (bool) $pesertaMagang->penilaian,
                            'Feedback dikirim peserta' => (bool) $pesertaMagang->feedback,
                        ] as $label => $ok)
                            <li class="{{ $ok ? 'ok' : 'belum' }}">{{ $ok ? '✓' : '✗' }} {{ $label }}</li>
                        @endforeach
                    </ul>

                    @if (! $syarat)
                        <form method="POST" action="{{ route('superadmin.peserta-magang.surat.terbitkan', $pesertaMagang) }}" onsubmit="return confirm('{{ $surat->sudahTerbit() ? 'Terbitkan ulang dengan data terbaru? Nomor surat tetap.' : 'Terbitkan surat keterangan sekarang?' }}')">
                            @csrf
                            @method('PUT')

                            <x-form.input name="tanggal_surat" type="date" label="Tanggal Surat" :value="$surat->tanggal_surat?->toDateString() ?? now()->toDateString()" required />

                            <input type="hidden" name="tampilkan_nilai" value="0">
                            <label class="check">
                                <input type="checkbox" name="tampilkan_nilai" value="1" @checked(old('tampilkan_nilai', $surat->tampilkan_nilai))>
                                Cantumkan nilai akhir dan predikat
                            </label>

                            <div class="aksi">
                                <x-button>{{ $surat->sudahTerbit() ? 'Terbitkan Ulang' : 'Terbitkan Surat' }}</x-button>
                                <x-button variant="secondary" :href="route('superadmin.peserta-magang.surat.pdf', $pesertaMagang)">⬇ {{ $surat->sudahTerbit() ? 'Unduh PDF' : 'Pratinjau PDF' }}</x-button>
                            </div>
                        </form>
                    @else
                        <div class="aksi">
                            <x-button variant="secondary" :href="route('superadmin.peserta-magang.surat.pdf', $pesertaMagang)">⬇ {{ $surat->sudahTerbit() ? 'Unduh PDF' : 'Pratinjau PDF' }}</x-button>
                        </div>
                    @endif
                @endif
            </section>
        </div>

        <section class="panel">
            <h3>{{ $surat?->sudahTerbit() ? 'Isi Surat (saat terbit)' : 'Pratinjau Isi Surat' }}</h3>

            <div class="kertas">
                <h4>SURAT KETERANGAN MAHASISWA MAGANG</h4>
                <div class="nomor">Nomor: {{ $surat?->nomor_surat ?? '…' }}</div>

                <p>Menerangkan bahwa:</p>
                <table>
                    <tr><td>Nama</td><td>: {{ $isi['nama'] }}</td></tr>
                    <tr><td>NIM</td><td>: {{ $isi['nim'] ?: '-' }}</td></tr>
                    <tr><td>Perguruan Tinggi</td><td>: {{ $isi['perguruan_tinggi'] ?: '-' }}</td></tr>
                    <tr><td>Fakultas / Jurusan</td><td>: {{ $isi['fakultas_jurusan'] ?: '-' }}</td></tr>
                </table>
                <p>
                    telah magang di {{ $isi['perusahaan']['nama'] }} sejak {{ $tanggal($isi['periode_mulai']) }} sampai dengan {{ $tanggal($isi['periode_selesai']) }}{{ $isi['divisi'] ? ', penempatan: '.implode(', ', $isi['divisi']) : '' }}.
                </p>
                <p style="text-align: right;">
                    {{ $isi['kota_surat'] ?: '…' }}, {{ $tanggal($isi['tanggal_surat']) }}<br>
                    {{ $isi['penanda_tangan']['jabatan'] ?: '(jabatan penanda tangan)' }}<br><br>
                    <strong>{{ $isi['penanda_tangan']['nama'] ?: '(nama penanda tangan)' }}</strong>
                </p>
            </div>

            @if ($profilPerusahaanKosong)
                <x-alert type="warning" style="margin-top: 12px;">
                    Nama perusahaan untuk kop surat belum diisi. Lengkapi di
                    <a href="{{ route('superadmin.perusahaan.index') }}">Profil Perusahaan</a>.
                </x-alert>
            @endif

            @if (! $isi['penanda_tangan']['nama'] || ! $isi['kota_surat'])
                <x-alert type="warning" message="Nama penanda tangan atau kota surat belum diisi di Pengaturan Magang." style="margin-top: 12px;" />
            @endif
        </section>

    </div>

</div>

@endsection
