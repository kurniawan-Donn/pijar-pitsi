{{-- resources/views/superadmin/scan-kartu/show.blade.php — foto kartu berdampingan dengan jam. --}}

@extends('layouts.dashboard')

@section('title', 'Kartu Absensi '.$scan->pesertaMagang->nama)
@section('page_title', 'Kartu Absensi')
@section('page_subtitle', $scan->pesertaMagang->nama.' · '.$scan->labelBulan().' · '.$scan->labelSisi())

@push('styles')
<style>
    .tata-kartu {
        display: grid;
        grid-template-columns: 360px minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }

    .panel {
        padding: 18px 20px;
    }

    .foto-kartu {
        position: sticky;
        top: 88px;
    }

    .foto-kartu img {
        width: 100%;

        display: block;

        border: 1px solid var(--garis);
        border-radius: var(--radius-kecil);
    }

    .ringkas {
        margin-bottom: 12px;

        color: var(--teks-sekunder);
        font-size: 13px;
        line-height: 1.6;
    }

    table {
        width: 100%;

        border-collapse: collapse;

        font-size: 13px;
    }

    th {
        padding: 8px;

        border-bottom: 1px solid var(--garis);

        color: var(--teks-sekunder);
        font-size: 11.5px;
        text-align: left;
        text-transform: uppercase;
    }

    td {
        padding: 8px;

        border-bottom: 1px solid var(--garis);

        vertical-align: top;
    }

    tr.beda td {
        background: var(--peringatan-muda);
    }

    .redup {
        color: var(--teks-redup);
    }

    .actions {
        margin-bottom: 14px;
    }

    @media (max-width: 1000px) {
        .tata-kartu {
            grid-template-columns: 1fr;
        }

        .foto-kartu {
            position: static;

            max-width: 360px;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <div class="actions">
        <x-button variant="secondary" :href="route('superadmin.peserta-magang.logbook-harian', [$scan->peserta_magang_id, 'periode' => app(\App\Services\KalenderLogbook::class)->kunciPeriode($scan->tanggal($scan->rentang()[0]))])">
            <x-icon name="arrow-left" :size="16" /> Logbook peserta
        </x-button>
    </div>

    <div class="tata-kartu">

        <aside class="panel foto-kartu">
            <a href="{{ route('scan-kartu.foto', $scan) }}" target="_blank" rel="noopener">
                <img src="{{ route('scan-kartu.foto', $scan) }}" alt="Foto kartu absensi {{ $scan->pesertaMagang->nama }} {{ $scan->labelBulan() }}">
            </a>
        </aside>

        <section class="panel">
            <p class="ringkas">
                Di-scan {{ $scan->created_at->translatedFormat('d F Y H:i') }} ·
                @if ($scan->sudahDisimpan())
                    disimpan peserta {{ $scan->disimpan_pada->translatedFormat('d F Y H:i') }} ({{ $scan->jumlah_disimpan }} tanggal).
                @else
                    <b>belum disimpan</b> peserta.
                @endif
                Baris kuning: jam di logbook berbeda dari hasil scan.
            </p>

            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Tgl</th>
                            <th>Hasil scan</th>
                            <th>Keyakinan</th>
                            <th>Di logbook sekarang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($scan->hasil ?? [] as $baris)
                            @php
                                $logbook = $logbooks->get($baris['tanggal']);
                                $beda = $logbook && $logbook->diubahSetelahScan();
                            @endphp
                            <tr @class(['beda' => $beda])>
                                <td><strong>{{ $baris['tanggal'] }}</strong></td>
                                <td>{{ $baris['jam_masuk'] ?: '–' }} – {{ $baris['jam_keluar'] ?: '–' }}</td>
                                <td @class(['redup' => ! $baris['ragu']])>{{ round($baris['keyakinan'] * 100) }}%{{ $baris['ragu'] ? ' · ragu' : '' }}</td>
                                <td>
                                    @if ($logbook)
                                        {{ $logbook->jam('pagi_masuk') ?: '–' }} – {{ $logbook->jam('siang_keluar') ?: '–' }}
                                        @if ($beda) <br><small>diubah peserta</small> @endif
                                        @if ($logbook->di_luar_batas_isi) <br><small>di luar batas isi mundur</small> @endif
                                    @else
                                        <span class="redup">tidak disimpan</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($logbook)
                                        <x-status-badge :status="$logbook->status_verifikasi === 'diterima' ? 'aktif' : ($logbook->status_verifikasi === 'ditolak' ? 'ditolak' : 'menunggu')" :label="$logbook->labelStatus()" />
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><x-empty icon="camera" title="Tidak ada jam yang terbaca" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>

</div>

@endsection
