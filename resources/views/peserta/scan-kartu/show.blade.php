{{-- resources/views/peserta/scan-kartu/show.blade.php — pratinjau hasil scan sebelum disimpan. --}}

@extends('layouts.dashboard')

@section('title', 'Periksa Hasil Scan')
@section('page_title', 'Periksa Hasil Scan Kartu')
@section('page_subtitle', $scan->labelBulan().' · '.$scan->labelSisi())

@php
    $alasan = [
        'diterima' => 'Sudah diterima admin, tidak bisa diubah.',
        'belum_tiba' => 'Tanggal belum tiba.',
        'di_luar' => 'Di luar masa magang.',
        'izin' => 'Tercatat izin/sakit.',
    ];
    $bisaSimpan = ! $scan->sudahDisimpan();
@endphp

@push('styles')
<style>
    .tata-pratinjau {
        display: grid;
        grid-template-columns: 340px minmax(0, 1fr);
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

    .foto-kartu small {
        display: block;

        margin-top: 6px;

        color: var(--teks-sekunder);
        font-size: 12px;
        text-align: center;
    }

    .ringkas {
        margin-bottom: 14px;

        color: var(--teks-sekunder);
        font-size: 13px;
        line-height: 1.6;
    }

    .legenda {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;

        margin-bottom: 12px;

        font-size: 12px;
    }

    .legenda i {
        display: inline-block;

        width: 12px;
        height: 12px;
        margin-right: 5px;

        border-radius: 3px;

        vertical-align: -1px;
    }

    .tabel-scan {
        width: 100%;

        border-collapse: collapse;

        font-size: 13px;
    }

    .tabel-scan th {
        padding: 8px 8px;

        border-bottom: 1px solid var(--garis);

        color: var(--teks-sekunder);
        font-size: 11.5px;
        font-weight: 700;
        text-align: left;
        text-transform: uppercase;
    }

    .tabel-scan td {
        padding: 7px 8px;

        border-bottom: 1px solid var(--garis);

        vertical-align: top;
    }

    .tabel-scan tr.ragu td {
        background: var(--peringatan-muda);
    }

    .tabel-scan tr.terkunci td {
        background: var(--permukaan-redup);
        color: var(--teks-redup);
    }

    .tabel-scan tr.kosong td {
        color: var(--teks-redup);
    }

    .tabel-scan input[type="time"] {
        width: 108px;
        height: 34px;
        padding: 0 6px;

        border: 1px solid var(--garis-kuat);
        border-radius: 6px;

        font-family: inherit;
        font-size: 13px;
    }

    .tanggal strong {
        display: block;
    }

    .tanggal small,
    .ket {
        color: var(--teks-sekunder);
        font-size: 12px;
    }

    .ket li {
        margin-left: 14px;
    }

    .lama-baru label {
        display: block;

        font-size: 12.5px;
        white-space: nowrap;
    }

    .tanda {
        display: inline-block;

        margin: 2px 4px 0 0;
        padding: 1px 7px;

        border-radius: 999px;

        background: var(--info-muda);
        color: var(--info);

        font-size: 11px;
        font-weight: 600;
    }

    .tabel-wrap {
        overflow-x: auto;
    }

    .actions {
        display: flex;
        justify-content: space-between;
        gap: 10px;

        margin-top: 16px;
    }

    @media (max-width: 1000px) {
        .tata-pratinjau {
            grid-template-columns: 1fr;
        }

        .foto-kartu {
            position: static;

            max-width: 340px;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages />

    @if ($scan->sudahDisimpan())
        <x-alert type="success" :message="'Hasil scan ini sudah disimpan ke logbook pada '.$scan->disimpan_pada->translatedFormat('d F Y H:i').' ('.$scan->jumlah_disimpan.' tanggal).'" />
    @endif

    <div class="tata-pratinjau">

        <aside class="panel foto-kartu">
            <a href="{{ route('scan-kartu.foto', $scan) }}" target="_blank" rel="noopener">
                <img src="{{ route('scan-kartu.foto', $scan) }}" alt="Foto kartu absensi {{ $scan->labelBulan() }}">
            </a>
            <small>Klik foto untuk memperbesar</small>
        </aside>

        <section class="panel">
            <p class="ringkas">
                Terbaca <b>{{ $scan->jumlah_terbaca }}</b> tanggal. Cocokkan dengan foto, perbaiki jam bila perlu, centang
                tanggal yang akan disimpan, lalu klik <b>Simpan ke Logbook</b>. Jam masuk mengisi <b>Pagi Masuk</b> dan jam keluar
                mengisi <b>Siang Keluar</b>.
            </p>

            <div class="legenda">
                <span><i style="background: var(--peringatan-muda); box-shadow: inset 0 0 0 1px #fedf89;"></i>Perlu diperiksa</span>
                <span><i style="background: var(--permukaan-redup); box-shadow: inset 0 0 0 1px var(--garis);"></i>Tidak bisa diubah</span>
            </div>

            <form method="POST" action="{{ route('peserta.scan-kartu.simpan', $scan) }}">
                @csrf
                @method('PUT')

                <div class="tabel-wrap">
                    <table class="tabel-scan">
                        <thead>
                            <tr>
                                <th style="width: 34px;">Pakai</th>
                                <th>Tanggal</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($baris as $b)
                                @php
                                    $hari = $b['tanggal']->day;
                                    $bacaan = $b['bacaan'];
                                    $masuk = old("baris.$hari.jam_masuk", $bacaan['jam_masuk'] ?? '');
                                    $keluar = old("baris.$hari.jam_keluar", $bacaan['jam_keluar'] ?? '');
                                    $pakai = old("baris.$hari.pakai", $bacaan && $b['bisa'] ? '1' : null);
                                @endphp

                                <tr @class(['ragu' => $b['ragu'] && $b['bisa'], 'terkunci' => ! $b['bisa'], 'kosong' => ! $bacaan && $b['bisa']]) id="baris-{{ $hari }}">
                                    <td>
                                        @if ($b['bisa'] && $bisaSimpan)
                                            <input type="checkbox" name="baris[{{ $hari }}][pakai]" value="1" @checked($pakai) aria-label="Simpan tanggal {{ $hari }}" data-pakai>
                                        @endif
                                    </td>
                                    <td class="tanggal">
                                        <strong>{{ $hari }}</strong>
                                        <small>{{ $b['tanggal']->translatedFormat('D') }}{{ $b['libur'] ? ' · libur' : '' }}</small>
                                    </td>

                                    @if ($b['bisa'] && $bisaSimpan)
                                        <td><input type="time" name="baris[{{ $hari }}][jam_masuk]" value="{{ $masuk }}" aria-label="Jam masuk tanggal {{ $hari }}"></td>
                                        <td><input type="time" name="baris[{{ $hari }}][jam_keluar]" value="{{ $keluar }}" aria-label="Jam keluar tanggal {{ $hari }}"></td>
                                    @else
                                        <td>{{ $bacaan['jam_masuk'] ?? '–' }}</td>
                                        <td>{{ $bacaan['jam_keluar'] ?? '–' }}</td>
                                    @endif

                                    <td class="ket">
                                        @unless ($b['bisa'])
                                            <div>{{ $alasan[$b['status']] ?? '' }}</div>
                                        @endunless

                                        @if ($b['jam_lama'] && $b['bisa'] && $bisaSimpan && $bacaan)
                                            <div class="lama-baru">
                                                <label><input type="radio" name="baris[{{ $hari }}][pilihan]" value="lama" @checked(old("baris.$hari.pilihan") === 'lama')> Pakai jam lama ({{ $b['jam_lama']['jam_masuk'] ?: '–' }} – {{ $b['jam_lama']['jam_keluar'] ?: '–' }})</label>
                                                <label><input type="radio" name="baris[{{ $hari }}][pilihan]" value="baru" @checked(old("baris.$hari.pilihan") === 'baru')> Pakai jam dari kartu</label>
                                            </div>
                                        @endif

                                        @if ($b['lewat_batas'] && $bacaan)
                                            <span class="tanda">Di luar batas isi mundur: akan ditandai untuk admin</span>
                                        @endif

                                        @if ($b['catatan'])
                                            <ul>
                                                @foreach ($b['catatan'] as $catatan)
                                                    <li>{{ $catatan }}</li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        @if ($bacaan && ! $b['ragu'])
                                            <span>Yakin {{ round($bacaan['keyakinan'] * 100) }}%</span>
                                        @elseif ($bacaan)
                                            <span>Yakin {{ round($bacaan['keyakinan'] * 100) }}%, periksa dengan foto</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="actions">
                    <x-button variant="secondary" :href="route('peserta.scan-kartu.create')">Scan Ulang</x-button>
                    @if ($bisaSimpan)
                        <x-button><x-icon name="check" :size="16" /> Simpan ke Logbook</x-button>
                    @else
                        <x-button :href="route('peserta.logbook.index')">Ke Logbook</x-button>
                    @endif
                </div>
            </form>
        </section>

    </div>

</div>

@endsection

@push('scripts')
<script>
    // Mengubah jam otomatis mencentang "Pakai" pada baris itu.
    document.querySelectorAll('.tabel-scan input[type="time"]').forEach(function (input) {
        input.addEventListener('input', function () {
            const pakai = input.closest('tr').querySelector('[data-pakai]');

            if (pakai && input.value) {
                pakai.checked = true;
            }
        });
    });
</script>
@endpush
