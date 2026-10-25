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

        background: var(--garis);
        color: var(--teks-sekunder);

        border: 1px solid var(--garis);

        font-size: 11px;
        font-weight: 800;
        text-align: center;
        text-transform: uppercase;
    }

    .lb-table td {
        padding: 8px;

        border: 1px solid var(--garis);

        color: var(--teks);
        vertical-align: top;
    }

    .lb-table tr.lb-libur td {
        background: var(--permukaan-redup);
        color: var(--teks-redup);
    }

    .lb-table tr.lb-belum td {
        background: var(--peringatan-muda);
    }

    .lb-table tr:target td,
    .lb-table tr.lb-hari-ini td {
        background: var(--utama-muda);
    }

    .lb-tanggal strong {
        display: block;

        color: var(--utama-gelap);
    }

    .lb-tanggal small {
        color: var(--teks-sekunder);
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

        color: var(--teks-sekunder);
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

    .lb-hadir { background: var(--sukses-muda); color: var(--sukses); }
    .lb-izin { background: var(--utama-muda); color: #3538cd; }
    .lb-sakit { background: var(--bahaya-muda); color: var(--bahaya); }
    .lb-libur-badge { background: var(--garis); color: var(--teks-sekunder); }
    .lb-menunggu { background: var(--garis); color: var(--teks); }
    .lb-diterima { background: var(--sukses-muda); color: var(--sukses); }
    .lb-ditolak { background: var(--bahaya-muda); color: var(--bahaya); }
    .lb-review { background: var(--peringatan-muda); color: var(--peringatan); }
    .lb-scan { background: var(--utama-muda); color: var(--utama-gelap); }
    .lb-manual { background: var(--permukaan-redup); color: var(--teks-sekunder); box-shadow: inset 0 0 0 1px var(--garis); }

    .lb-tanda {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;

        margin-top: 4px;
    }

    .lb-bukti a {
        display: block;

        color: var(--utama);
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

        border: 1px solid var(--garis-kuat);
        border-radius: 7px;

        background: var(--utama-muda);
        color: var(--utama);

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
        color: var(--teks-redup);
        font-size: 11px;
    }

    .lb-catatan {
        display: block;

        margin-top: 4px;

        color: var(--bahaya);
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

                            {{-- Sumber jam & penanda dari scan kartu absensi --}}
                            @if ($logbook->kehadiran === 'hadir')
                                <span class="lb-tanda">
                                    @if ($logbook->activityBelumDiisi())
                                        <span class="lb-badge lb-review">Activity belum diisi</span>
                                    @endif
                                    @if ($logbook->dariScan())
                                        <span class="lb-badge lb-scan">Scan kartu</span>
                                    @elseif ($mode === 'admin')
                                        <span class="lb-badge lb-manual">Manual</span>
                                    @endif
                                    @if ($mode === 'admin' && $logbook->diubahSetelahScan())
                                        <span class="lb-badge lb-review" title="Hasil scan asli: {{ ($logbook->jam_scan_asli['pagi_masuk'] ?? '') ?: '–' }} – {{ ($logbook->jam_scan_asli['siang_keluar'] ?? '') ?: '–' }}">Diubah setelah scan (asli {{ ($logbook->jam_scan_asli['pagi_masuk'] ?? '') ?: '–' }}–{{ ($logbook->jam_scan_asli['siang_keluar'] ?? '') ?: '–' }})</span>
                                    @endif
                                    @if ($mode === 'admin' && $logbook->di_luar_batas_isi)
                                        <span class="lb-badge lb-ditolak">Di luar batas isi mundur</span>
                                    @endif
                                </span>
                            @endif

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
                        @if ($logbook?->scan_kartu_absensi_id)
                            <a href="{{ $mode === 'admin' ? route('superadmin.scan-kartu.show', $logbook->scan_kartu_absensi_id) : route('scan-kartu.foto', $logbook->scan_kartu_absensi_id) }}" target="_blank" rel="noopener">Kartu absensi</a>
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
                                <span class="lb-kunci" title="Tanggal belum tiba"><x-icon name="lock" :size="13" /> belum tiba</span>
                            @elseif ($baris['akses'] === 'lewat_batas' && (! $logbook || $logbook->bisaDiubahPeserta()))
                                <span class="lb-kunci" title="Melewati batas pengisian"><x-icon name="lock" :size="13" /> terkunci</span>
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
