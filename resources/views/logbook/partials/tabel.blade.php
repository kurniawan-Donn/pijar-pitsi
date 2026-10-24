{{--
    Tabel logbook satu periode (format sheet bulanan September).
    Variabel: $hari (KalenderLogbook::hari), $mode ('peserta' | 'admin'),
    $pesertaMagang (khusus admin, untuk form buka kunci).
--}}

@once
@push('styles')
<style>
    .lb-scroll {
        overflow-x: auto;
    }

    .lb-table {
        width: 100%;
        min-width: 920px;
        border-collapse: collapse;

        font-size: 12px;
    }

    .lb-table th {
        padding: 8px;

        background: #f2f6fa;
        color: #52646e;

        border: 1px solid #e3e9ef;

        font-size: 11px;
        font-weight: 800;
        text-align: center;
        text-transform: uppercase;
    }

    .lb-table td {
        padding: 8px;

        border: 1px solid #edf1f5;

        color: #34495e;
        vertical-align: top;
    }

    .lb-table tr.lb-libur td {
        background: #fafbfc;
        color: #9aa7b4;
    }

    .lb-table tr.lb-belum td {
        background: #fffdf5;
    }

    .lb-table tr:target td,
    .lb-table tr.lb-hari-ini td {
        background: #eef6fd;
    }

    .lb-tanggal strong {
        display: block;

        color: #17324d;
    }

    .lb-tanggal small {
        color: #8a99a8;
    }

    .lb-jam {
        width: 58px;

        text-align: center;
        font-variant-numeric: tabular-nums;
    }

    .lb-activity {
        min-width: 240px;

        line-height: 1.55;
        white-space: pre-line;
    }

    .lb-meta {
        display: block;

        margin-top: 4px;

        color: #7b8794;
        font-size: 11px;
        white-space: normal;
    }

    .lb-badge {
        display: inline-block;

        padding: 2px 8px;

        border-radius: 999px;

        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .lb-hadir { background: #e7f6ec; color: #067647; }
    .lb-izin { background: #eef4ff; color: #3538cd; }
    .lb-sakit { background: #fff0f0; color: #c62828; }
    .lb-libur-badge { background: #eef2f6; color: #52646e; }
    .lb-menunggu { background: #f2f4f7; color: #475467; }
    .lb-diterima { background: #e7f6ec; color: #067647; }
    .lb-ditolak { background: #fff0f0; color: #c62828; }
    .lb-review { background: #fff4e5; color: #b54708; }

    .lb-bukti a {
        display: block;

        color: #1779c9;
        font-size: 11px;
        text-decoration: none;
        word-break: break-all;
    }

    .lb-aksi {
        width: 110px;

        text-align: center;
    }

    .lb-aksi a,
    .lb-aksi button {
        display: inline-block;

        padding: 5px 10px;

        border: 1px solid #cfe0f1;
        border-radius: 7px;

        background: #f2f8fd;
        color: #1779c9;

        font-family: inherit;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;

        cursor: pointer;
    }

    .lb-aksi form {
        margin: 0;
    }

    .lb-kunci {
        color: #9aa7b4;
        font-size: 11px;
    }

    .lb-catatan {
        display: block;

        margin-top: 4px;

        color: #b42318;
        font-size: 11px;
    }
</style>
@endpush
@endonce

@php
    $hariIni = now()->toDateString();
@endphp

<div class="lb-scroll">
    <table class="lb-table">
        <thead>
            <tr>
                @if ($mode === 'admin')
                    <th rowspan="2" style="width: 34px;">
                        <input type="checkbox" data-pilih-semua aria-label="Pilih semua hari">
                    </th>
                @endif
                <th rowspan="2">Tanggal</th>
                <th colspan="2">Pagi</th>
                <th colspan="2">Siang</th>
                <th rowspan="2">Activity / Output / Lokasi</th>
                <th rowspan="2">Bukti</th>
                <th rowspan="2">Action</th>
                <th rowspan="2">{{ $mode === 'admin' ? 'Kunci' : 'Aksi' }}</th>
            </tr>
            <tr>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Masuk</th>
                <th>Keluar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($hari as $baris)
                @php
                    $tanggal = $baris['tanggal'];
                    $logbook = $baris['logbook'];
                    $kunciTanggal = $tanggal->toDateString();
                    $lewat = $tanggal->toDateString() <= $hariIni;
                @endphp
                <tr
                    id="hari-{{ $kunciTanggal }}"
                    @class([
                        'lb-libur' => ! $logbook && $baris['libur'],
                        'lb-belum' => ! $logbook && ! $baris['libur'] && $lewat && $baris['akses'] !== 'di_luar',
                        'lb-hari-ini' => $kunciTanggal === $hariIni,
                    ])
                >
                    @if ($mode === 'admin')
                        <td style="text-align: center;">
                            @if ($logbook && ! $logbook->izin_peserta_id)
                                <input type="checkbox" name="logbook[]" value="{{ $logbook->id }}" form="form-verifikasi" data-pilih aria-label="Pilih {{ $tanggal->translatedFormat('d M') }}">
                            @endif
                        </td>
                    @endif

                    <td class="lb-tanggal">
                        <strong>{{ $tanggal->translatedFormat('d M') }}</strong>
                        <small>{{ $tanggal->translatedFormat('l') }}</small>
                    </td>

                    @foreach (\App\Models\LogbookHarian::KOLOM_JAM as $kolom)
                        <td class="lb-jam">{{ $logbook?->jam($kolom) }}</td>
                    @endforeach

                    <td class="lb-activity">
                        @if ($logbook)
                            @if ($logbook->kehadiran !== 'hadir')
                                <span class="lb-badge lb-{{ $logbook->kehadiran === 'libur' ? 'libur-badge' : $logbook->kehadiran }}">{{ $logbook->labelKehadiran() }}</span>
                            @elseif ($logbook->jenisKehadiran() === 'setengah_hari')
                                <span class="lb-badge lb-review">Setengah hari</span>
                            @endif
                            {{ $logbook->activity }}
                            @if ($logbook->output || $logbook->lokasi)
                                <span class="lb-meta">
                                    @if ($logbook->output) Output: {{ $logbook->output }} @endif
                                    @if ($logbook->output && $logbook->lokasi) · @endif
                                    @if ($logbook->lokasi) Lokasi: {{ $logbook->lokasi }} @endif
                                </span>
                            @endif
                        @elseif ($baris['libur'])
                            {{ $baris['libur'] }}
                        @elseif ($baris['akses'] === 'di_luar')
                            <span class="lb-kunci">Di luar masa magang</span>
                        @elseif ($lewat)
                            <span class="lb-kunci">Belum diisi</span>
                        @endif
                    </td>

                    <td class="lb-bukti">
                        @if ($logbook?->izin?->bukti_path)
                            <a href="{{ route($mode === 'admin' ? 'superadmin.izin-bukti.show' : 'peserta.izin.bukti', $logbook->izin) }}" target="_blank" rel="noopener">{{ $logbook->izin->bukti_nama }}</a>
                        @endif
                        @foreach ($logbook?->buktis ?? [] as $bukti)
                            <a href="{{ route($mode === 'admin' ? 'superadmin.logbook-bukti.show' : 'peserta.logbook.bukti', $bukti) }}" target="_blank" rel="noopener">{{ $bukti->nama_asli }}</a>
                        @endforeach
                    </td>

                    <td style="text-align: center;">
                        @if ($logbook)
                            <span class="lb-badge lb-{{ $logbook->status_verifikasi }}">{{ $logbook->labelStatus() }}</span>
                            @if ($logbook->catatan_verifikasi)
                                <span class="lb-catatan">{{ $logbook->catatan_verifikasi }}</span>
                            @endif
                        @endif
                    </td>

                    <td class="lb-aksi">
                        @if ($mode === 'peserta')
                            @if ($baris['akses'] === 'bisa' && (! $logbook || $logbook->bisaDiubahPeserta()))
                                <a href="{{ route('peserta.logbook.edit', $kunciTanggal) }}">{{ $logbook ? 'Ubah' : 'Isi' }}</a>
                            @elseif ($baris['akses'] === 'belum_tiba')
                                <span class="lb-kunci" title="Tanggal belum tiba">🔒 belum tiba</span>
                            @elseif ($baris['akses'] === 'lewat_batas' && (! $logbook || $logbook->bisaDiubahPeserta()))
                                <span class="lb-kunci" title="Melewati batas pengisian">🔒 terkunci</span>
                            @endif
                        @else
                            @if ($baris['akses'] === 'lewat_batas' && (! $logbook || $logbook->bisaDiubahPeserta()))
                                <form method="POST" action="{{ route('superadmin.peserta-magang.logbook-harian.buka-kunci', $pesertaMagang) }}">
                                    @csrf
                                    <input type="hidden" name="tanggal" value="{{ $kunciTanggal }}">
                                    <button type="submit" title="Izinkan peserta mengisi tanggal ini">Buka kunci</button>
                                </form>
                            @elseif ($baris['dibuka'])
                                <span class="lb-kunci">dibuka</span>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
