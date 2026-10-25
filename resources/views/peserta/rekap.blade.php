{{-- resources/views/peserta/rekap.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Rekap Kehadiran')
@section('page_title', 'Rekap Kehadiran')
@section('page_subtitle', 'Kehadiran dan perkiraan uang saku per periode (28 s.d. 27)')

@php
    $rupiah = fn (int $nilai) => 'Rp '.number_format($nilai, 0, ',', '.');
@endphp

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;

        margin-bottom: 16px;
    }

    .toolbar form {
        display: flex;
        align-items: flex-end;
        gap: 10px;
    }

    .toolbar label {
        display: block;

        margin-bottom: 6px;

        color: var(--teks-sekunder);
        font-size: 12px;
        font-weight: 700;
    }

    .toolbar select {
        min-width: 260px;
        height: 42px;

        padding: 0 10px;

        border: 1px solid var(--garis-kuat);
        border-radius: 8px;

        background: #ffffff;

        font-family: inherit;
        font-size: 13px;
    }

    .toolbar-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .panel {
        margin-bottom: 18px;
        padding: 20px 22px;

        background: #ffffff;

        border: 1px solid var(--garis);
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel h3 {
        margin-bottom: 12px;

        color: var(--utama-gelap);
        font-size: 15px;
    }

    .panel-note {
        margin: -6px 0 14px;

        color: var(--teks-sekunder);
        font-size: 12px;
        line-height: 1.6;
    }

    .tabel-scroll {
        overflow-x: auto;
    }

    table.rekap-periode {
        width: 100%;
        min-width: 640px;
        border-collapse: collapse;

        font-size: 13px;
    }

    table.rekap-periode th {
        padding: 9px 10px;

        background: var(--permukaan-redup);
        color: var(--teks-sekunder);

        font-size: 11px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
    }

    table.rekap-periode td {
        padding: 10px;

        border-top: 1px solid var(--garis);

        color: var(--teks);
        font-variant-numeric: tabular-nums;
    }

    table.rekap-periode tr.aktif td {
        background: var(--utama-muda);
    }

    table.rekap-periode tfoot td {
        border-top: 2px solid var(--garis-kuat);

        color: var(--utama-gelap);
        font-weight: 800;
    }

    table.rekap-periode a {
        color: var(--utama);
        font-weight: 700;
        text-decoration: none;
    }

    .kecil {
        display: block;

        color: var(--teks-sekunder);
        font-size: 11px;
    }

    .empty {
        padding: 30px;

        color: var(--teks-sekunder);
        text-align: center;
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 16px 30px;
        }

        .toolbar select {
            min-width: 0;
            width: 100%;
        }

        .toolbar form {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    @if (! $periode)
        <section class="panel empty">
            Periode magang Anda belum diatur. Hubungi admin untuk mengisi tanggal mulai dan selesai magang.
        </section>
    @else

        <div class="toolbar">
            <form method="GET" action="{{ route('peserta.rekap') }}">
                <div>
                    <label for="periode">Periode</label>
                    <select id="periode" name="periode" onchange="this.form.submit()">
                        @foreach ($daftarPeriode as $item)
                            <option value="{{ $item['kunci'] }}" @selected($item['kunci'] === $periode['kunci'])>
                                {{ $item['label'] }} ({{ $item['mulai']->translatedFormat('d M') }} – {{ $item['selesai']->translatedFormat('d M') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <noscript><x-button variant="secondary">Tampilkan</x-button></noscript>
            </form>

            <div class="toolbar-actions">
                <x-button variant="secondary" :href="route('peserta.logbook.index', ['periode' => $periode['kunci']])">Lihat Logbook</x-button>
                <x-button variant="secondary" :href="route('peserta.logbook.cetak', ['periode' => $periode['kunci']])"><x-icon name="download" :size="16" /> Unduh PDF</x-button>
            </div>
        </div>

        <section class="panel">
            <h3>{{ $periode['label'] }} · {{ $periode['mulai']->translatedFormat('d F') }} s.d. {{ $periode['selesai']->translatedFormat('d F Y') }}</h3>
            <p class="panel-note">
                Uang saku dihitung dari hari hadir yang tidak ditolak, dengan tarif yang berlaku pada tanggal masing-masing.
                Angka ini masih perkiraan sampai semua hari diverifikasi admin.
            </p>

            @include('logbook.partials.rekap')
        </section>

        <section class="panel">
            <h3>Semua Periode</h3>

            @if ($ringkasan->isEmpty())
                <div class="empty">Belum ada periode yang berjalan.</div>
            @else
                <div class="tabel-scroll">
                    <table class="rekap-periode">
                        <thead>
                            <tr>
                                <th>Periode</th>
                                <th>Penuh</th>
                                <th>Setengah</th>
                                <th>Izin / Sakit</th>
                                <th>Belum Diisi</th>
                                <th>Menunggu</th>
                                <th>Uang Saku</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ringkasan as $item)
                                <tr @class(['aktif' => $item['kunci'] === $periode['kunci']])>
                                    <td>
                                        <a href="{{ route('peserta.rekap', ['periode' => $item['kunci']]) }}">{{ $item['label'] }}</a>
                                        <span class="kecil">{{ $item['mulai']->translatedFormat('d M') }} – {{ $item['selesai']->translatedFormat('d M Y') }}</span>
                                    </td>
                                    <td>{{ $item['rekap']['hari_penuh'] }}</td>
                                    <td>{{ $item['rekap']['setengah_hari'] }}</td>
                                    <td>{{ $item['rekap']['izin'] }} / {{ $item['rekap']['sakit'] }}</td>
                                    <td>{{ $item['rekap']['belum_diisi'] }}</td>
                                    <td>{{ $item['rekap']['menunggu'] }}</td>
                                    <td>{{ $rupiah($item['rekap']['uang_saku']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total</td>
                                <td>{{ $total['hari_penuh'] }}</td>
                                <td>{{ $total['setengah_hari'] }}</td>
                                <td>{{ $total['izin'] }} / {{ $total['sakit'] }}</td>
                                <td>{{ $total['belum_diisi'] }}</td>
                                <td>{{ $total['menunggu'] }}</td>
                                <td>{{ $rupiah($total['uang_saku']) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </section>

    @endif

</div>

@endsection
