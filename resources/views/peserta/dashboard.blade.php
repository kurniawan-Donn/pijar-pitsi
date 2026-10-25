{{-- resources/views/peserta/dashboard.blade.php --}}

{{-- $r: hasil App\Services\RingkasanPeserta::untuk() --}}

@extends('layouts.dashboard')

@section('title', 'Dashboard Peserta')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan magang Anda')

@php
    $rupiah = fn (int $nilai) => 'Rp '.number_format($nilai, 0, ',', '.');
    $biodata = $r['biodata'];
    $progres = $biodata->progres();
    $rekap = $r['rekap'];
    $hariIni = $r['hari_ini'];
    $logbookHariIni = $hariIni['logbook'];
    $warnaBiodata = ['disetujui' => 'b-hijau', 'diajukan' => 'b-oranye', 'perlu_revisi' => 'b-merah'];
    $namaHari = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
@endphp

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .welcome-card {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;

        padding: 24px 28px;

        border-radius: 14px;

        background: linear-gradient(135deg, #12345b 0%, #174c7e 100%);

        color: #ffffff;

        box-shadow: 0 8px 24px rgba(18, 52, 91, .10);
    }

    .welcome-card h2 {
        font-size: 20px;
    }

    .welcome-card p {
        margin-top: 6px;

        color: rgba(255, 255, 255, .78);
        font-size: 13px;
        line-height: 1.6;
    }

    .welcome-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 22px;

        margin-top: 12px;

        font-size: 12px;
    }

    .welcome-meta span {
        color: rgba(255, 255, 255, .65);
    }

    .welcome-meta strong {
        display: block;

        color: #ffffff;
        font-size: 14px;
    }

    .hari-ini {
        min-width: 230px;
        padding: 14px 18px;

        border-radius: 12px;

        background: rgba(255, 255, 255, .1);
    }

    .hari-ini small {
        display: block;

        color: rgba(255, 255, 255, .7);
        font-size: 11px;
        text-transform: uppercase;
    }

    .hari-ini strong {
        display: block;

        margin: 4px 0 10px;

        font-size: 15px;
    }

    .hari-ini .x-btn {
        width: 100%;
    }

    .pengingat {
        display: flex;
        flex-direction: column;
        gap: 10px;

        margin-top: 18px;
    }

    .pengingat a {
        display: flex;
        align-items: center;
        gap: 12px;

        padding: 12px 16px;

        border: 1px solid;
        border-radius: 11px;

        font-size: 13px;
        line-height: 1.5;
    }

    .pengingat a strong {
        margin-left: auto;

        white-space: nowrap;
    }

    .p-info { background: #eaf3fb; border-color: #c4dcf1; color: #175c95; }
    .p-hijau { background: #ecfdf3; border-color: #abefc6; color: #067647; }
    .p-oranye { background: #fff8f0; border-color: #fbd7ae; color: #a85a12; }
    .p-merah { background: #fef3f2; border-color: #fecdca; color: #b42318; }

    .grid-dua {
        display: grid;
        grid-template-columns: minmax(0, 3fr) minmax(0, 2fr);
        gap: 18px;

        margin-top: 18px;
    }

    .panel {
        padding: 20px 22px;

        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel + .panel {
        margin-top: 18px;
    }

    .panel-head {
        display: flex;
        flex-wrap: wrap;
        align-items: baseline;
        justify-content: space-between;
        gap: 8px;

        margin-bottom: 14px;
    }

    .panel h3 {
        color: #17324d;
        font-size: 15px;
    }

    .panel-head a {
        color: #1779c9;
        font-size: 12px;
        font-weight: 700;
    }

    .panel p {
        color: #7b8794;
        font-size: 12px;
        line-height: 1.7;
    }

    .angka-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }

    .angka {
        padding: 12px 14px;

        border: 1px solid #e5eaf0;
        border-radius: 11px;

        background: #fbfcfe;
    }

    .angka span {
        display: block;

        color: #7b8794;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .angka strong {
        display: block;

        margin-top: 3px;

        color: #17324d;
        font-size: 19px;
    }

    .angka-uang {
        grid-column: span 4;

        background: #fff8f1;
        border-color: #f9d9bd;
    }

    .angka-uang strong {
        color: #b54708;
    }

    .angka-uang small {
        color: #8a99a8;
        font-size: 11px;
    }

    /* KALENDER MINI */
    .kalender {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 4px;
    }

    .kalender .nama-hari {
        color: #8a99a8;
        font-size: 10px;
        font-weight: 800;
        text-align: center;
        text-transform: uppercase;
    }

    .kalender .sel {
        display: flex;
        align-items: center;
        justify-content: center;

        aspect-ratio: 1;

        border-radius: 7px;

        background: #f4f7fb;
        color: #8a99a8;

        font-size: 12px;
        font-weight: 700;
    }

    a.sel:hover {
        outline: 2px solid #1779c9;
    }

    .sel.hari-ini-sel {
        outline: 2px solid #17324d;
    }

    .k-diterima { background: #19a974 !important; color: #ffffff !important; }
    .k-menunggu { background: #fdb022 !important; color: #ffffff !important; }
    .k-ditolak { background: #e5484d !important; color: #ffffff !important; }
    .k-izin { background: #7a5af8 !important; color: #ffffff !important; }
    .k-libur { background: #d7e0eb !important; color: #52646e !important; }
    .k-belum { background: #ffffff !important; color: #c62828 !important; box-shadow: inset 0 0 0 1.5px #f3a7a7; }
    .k-terkunci { background: #eef2f6 !important; color: #8a99a8 !important; text-decoration: line-through; }

    .legenda {
        display: flex;
        flex-wrap: wrap;
        gap: 6px 12px;

        margin-top: 12px;

        color: #52646e;
        font-size: 11px;
    }

    .legenda span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .legenda i {
        width: 11px;
        height: 11px;

        border-radius: 3px;
    }

    /* DAFTAR */
    .daftar {
        display: flex;
        flex-direction: column;
    }

    .daftar a {
        display: block;

        padding: 10px 0;

        border-top: 1px solid #edf1f5;

        color: #34495e;
        font-size: 13px;
    }

    .daftar a:first-child {
        border-top: none;
        padding-top: 0;
    }

    .daftar small {
        display: block;

        margin-top: 2px;

        color: #7b8794;
        font-size: 12px;
    }

    .badge {
        display: inline-block;

        padding: 2px 8px;

        border-radius: 999px;

        font-size: 10.5px;
        font-weight: 800;
        white-space: nowrap;
    }

    .b-hijau { background: #e7f6ec; color: #067647; }
    .b-oranye { background: #fff4e5; color: #b54708; }
    .b-merah { background: #fff0f0; color: #c62828; }
    .b-abu { background: #eef2f6; color: #52646e; }

    .progress-track {
        height: 8px;
        margin-top: 10px;

        border-radius: 999px;

        background: #edf1f5;

        overflow: hidden;
    }

    .progress-fill {
        height: 100%;

        border-radius: 999px;

        background: linear-gradient(90deg, #1779c9, #19a974);
    }

    .info-baris {
        display: flex;
        justify-content: space-between;
        gap: 12px;

        padding: 8px 0;

        border-top: 1px solid #edf1f5;

        color: #52646e;
        font-size: 13px;
    }

    .info-baris:first-of-type {
        border-top: none;
    }

    .info-baris strong {
        color: #17324d;
        text-align: right;
    }

    .aksi-cepat {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;

        margin-top: 14px;
    }

    .kosong {
        color: #8a99a8;
        font-size: 13px;
    }

    @media (max-width: 1000px) {
        .grid-dua {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 16px 30px;
        }

        .angka-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .angka-uang {
            grid-column: span 2;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages :validation="false" />

    {{-- SAMBUTAN & LOGBOOK HARI INI --}}
    <section class="welcome-card">
        <div>
            <h2>Halo, {{ $peserta->nama ?? auth()->user()->name }}</h2>
            <p>{{ $hariIni['tanggal']->translatedFormat('l, d F Y') }}</p>

            <div class="welcome-meta">
                <div><span>Nomor peserta</span><strong>{{ $peserta->nomor_peserta ?? '—' }}</strong></div>
                <div><span>Divisi periode ini</span><strong>{{ $r['divisi'] ?? 'Belum ditentukan' }}</strong></div>
                <div><span>Mentor</span><strong>{{ $r['mentor'] ?? '—' }}</strong></div>
                <div>
                    <span>Sisa magang</span>
                    <strong>
                        @if ($r['sisa_hari'] === null)
                            —
                        @elseif ($peserta->status === 'selesai' || $r['sisa_hari'] === 0)
                            Selesai
                        @else
                            {{ $r['sisa_hari'] }} hari
                        @endif
                    </strong>
                </div>
            </div>
        </div>

        <div class="hari-ini">
            <small>Logbook hari ini</small>
            @if ($logbookHariIni)
                <strong>✓ Sudah diisi · {{ $logbookHariIni->labelStatus() }}</strong>
                <x-button variant="secondary" :href="route('peserta.logbook.edit', $hariIni['tanggal']->toDateString())">Lihat isian</x-button>
            @elseif ($hariIni['wajib'])
                <strong>Belum diisi</strong>
                <x-button :href="route('peserta.logbook.edit', $hariIni['tanggal']->toDateString())">Isi sekarang</x-button>
            @elseif ($hariIni['libur'])
                <strong>Hari libur ({{ $hariIni['libur'] }})</strong>
            @else
                <strong>Di luar masa magang</strong>
            @endif
        </div>
    </section>

    {{-- PENGINGAT --}}
    @php
        $adaPengingat = $r['surat_terbit']
            || ($r['feedback']['dibuka'] && ! $r['feedback']['terkunci'])
            || $r['hampir_terkunci']->isNotEmpty()
            || $r['perlu_diperbaiki']->isNotEmpty()
            || $biodata->status === 'perlu_revisi';
    @endphp

    @if ($adaPengingat)
        <div class="pengingat">
            @if ($r['surat_terbit'])
                <a href="{{ route('peserta.surat.show') }}" class="p-hijau">
                    🎓 Surat keterangan magang Anda sudah terbit.
                    <strong>Unduh surat →</strong>
                </a>
            @endif

            @if ($r['feedback']['dibuka'] && ! $r['feedback']['terkunci'])
                <a href="{{ route('peserta.feedback.edit') }}" class="{{ $r['feedback']['terkirim'] ? 'p-info' : 'p-oranye' }}">
                    ✎
                    @if ($r['feedback']['terkirim'])
                        Feedback sudah dikirim. Anda masih bisa mengubahnya sampai surat keterangan terbit.
                    @else
                        Form feedback magang sudah dibuka. Isi feedback agar surat keterangan bisa diterbitkan.
                    @endif
                    <strong>{{ $r['feedback']['terkirim'] ? 'Lihat' : 'Isi feedback' }} →</strong>
                </a>
            @endif

            @if ($biodata->status === 'perlu_revisi')
                <a href="{{ route('peserta.biodata.edit') }}" class="p-merah">
                    ☷ Admin meminta perbaikan biodata.
                    <strong>Perbaiki →</strong>
                </a>
            @endif

            @if ($r['hampir_terkunci']->isNotEmpty())
                <a href="{{ route('peserta.logbook.edit', $r['hampir_terkunci']->first()->toDateString()) }}" class="p-oranye">
                    ⏳ {{ $r['hampir_terkunci']->count() }} hari kerja belum diisi dan akan terkunci dalam {{ \App\Services\RingkasanPeserta::HARI_PERINGATAN_KUNCI }} hari:
                    {{ $r['hampir_terkunci']->map(fn ($tanggal) => $tanggal->translatedFormat('d M'))->implode(', ') }}.
                    <strong>Isi sekarang →</strong>
                </a>
            @endif

            @if ($r['perlu_diperbaiki']->isNotEmpty())
                <a href="#perlu-diperbaiki" class="p-merah">
                    ✗ {{ $r['perlu_diperbaiki']->count() }} logbook ditolak / perlu review admin.
                    <strong>Lihat →</strong>
                </a>
            @endif
        </div>
    @endif

    <div class="grid-dua">

        <div>
            {{-- RINGKASAN PERIODE --}}
            <section class="panel">
                <div class="panel-head">
                    <h3>{{ $r['periode'] ? 'Periode '.$r['periode']['label'] : 'Periode Berjalan' }}</h3>
                    @if ($r['periode'])
                        <a href="{{ route('peserta.rekap') }}">Rekap lengkap →</a>
                    @endif
                </div>

                @if (! $r['periode'])
                    <p>Periode magang Anda belum diatur. Hubungi admin.</p>
                @else
                    <p style="margin: -8px 0 12px;">{{ $r['periode']['mulai']->translatedFormat('d M') }} – {{ $r['periode']['selesai']->translatedFormat('d M Y') }}</p>

                    <div class="angka-grid">
                        <div class="angka"><span>Hadir penuh</span><strong>{{ $rekap['hari_penuh'] }}</strong></div>
                        <div class="angka"><span>Setengah hari</span><strong>{{ $rekap['setengah_hari'] }}</strong></div>
                        <div class="angka"><span>Izin / sakit</span><strong>{{ $rekap['izin'] + $rekap['sakit'] }}</strong></div>
                        <div class="angka"><span>Belum diisi</span><strong>{{ $rekap['belum_diisi'] }}</strong></div>
                        <div class="angka angka-uang">
                            <span>Perkiraan uang saku</span>
                            <strong>{{ $rupiah($rekap['uang_saku']) }}</strong>
                            <small>{{ $rekap['menunggu'] }} hari masih menunggu verifikasi · hari ditolak tidak dihitung</small>
                        </div>
                    </div>
                @endif
            </section>

            {{-- PERLU DIPERBAIKI --}}
            <section class="panel" id="perlu-diperbaiki">
                <div class="panel-head">
                    <h3>Logbook Ditolak / Review</h3>
                </div>

                @if ($r['perlu_diperbaiki']->isEmpty())
                    <p class="kosong">Tidak ada. Semua logbook Anda menunggu atau sudah diterima admin.</p>
                @else
                    <div class="daftar">
                        @foreach ($r['perlu_diperbaiki'] as $logbook)
                            <a href="{{ route('peserta.logbook.edit', $logbook->tanggal->toDateString()) }}">
                                <span class="badge {{ $logbook->status_verifikasi === 'ditolak' ? 'b-merah' : 'b-oranye' }}">{{ $logbook->labelStatus() }}</span>
                                <strong>{{ $logbook->tanggal->translatedFormat('l, d M Y') }}</strong>
                                <small>Catatan admin: {{ $logbook->catatan_verifikasi ?: '—' }}</small>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

            @if ($r['izin_menunggu']->isNotEmpty())
                <section class="panel">
                    <div class="panel-head">
                        <h3>Izin / Sakit Menunggu Verifikasi</h3>
                    </div>
                    <div class="daftar">
                        @foreach ($r['izin_menunggu'] as $izin)
                            <a href="{{ route('peserta.logbook.index', ['periode' => app(\App\Services\PeriodeMagang::class)->kunciUntuk($izin->tanggal_mulai)]) }}">
                                <strong>{{ $izin->labelJenis() }} · {{ $izin->rentang() }}</strong>
                                <small>{{ $izin->keterangan }}</small>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <div>
            {{-- KALENDER MINI --}}
            @if ($r['periode'])
                <section class="panel">
                    <div class="panel-head">
                        <h3>Kalender Kehadiran</h3>
                        <a href="{{ route('peserta.logbook.index') }}">Buka logbook →</a>
                    </div>

                    <div class="kalender" role="grid" aria-label="Kalender kehadiran {{ $r['periode']['label'] }}">
                        @foreach ($namaHari as $nama)
                            <div class="nama-hari">{{ $nama }}</div>
                        @endforeach

                        @for ($i = 1; $i < $r['kalender'][0]['tanggal']->dayOfWeekIso; $i++)
                            <div></div>
                        @endfor

                        @foreach ($r['kalender'] as $sel)
                            @php
                                $kelas = 'sel'.($sel['status'] ? ' k-'.$sel['status'] : '').($sel['hari_ini'] ? ' hari-ini-sel' : '');
                                $judul = $sel['tanggal']->translatedFormat('d F').($sel['status'] ? ': '.\App\Services\RingkasanPeserta::LEGENDA[$sel['status']] : '');
                            @endphp
                            @if ($sel['bisa_diisi'])
                                <a href="{{ route('peserta.logbook.edit', $sel['tanggal']->toDateString()) }}" class="{{ $kelas }}" title="{{ $judul }}">{{ $sel['tanggal']->day }}</a>
                            @else
                                <div class="{{ $kelas }}" title="{{ $judul }}">{{ $sel['tanggal']->day }}</div>
                            @endif
                        @endforeach
                    </div>

                    <div class="legenda">
                        @foreach (\App\Services\RingkasanPeserta::LEGENDA as $kunci => $label)
                            <span><i class="k-{{ $kunci }}"></i>{{ $label }}</span>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- BIODATA --}}
            <section class="panel">
                <div class="panel-head">
                    <h3>Biodata</h3>
                    <span class="badge {{ $warnaBiodata[$biodata->status] ?? 'b-abu' }}">{{ $biodata->labelStatus() }}</span>
                </div>

                <div class="progress-track" aria-label="Progres biodata {{ $progres['persen'] }}%">
                    <div class="progress-fill" style="width: {{ $progres['persen'] }}%"></div>
                </div>
                <p>{{ $progres['terisi'] }} dari {{ $progres['total'] }} data wajib terisi ({{ $progres['persen'] }}%)</p>

                <div class="aksi-cepat">
                    <x-button :href="route('peserta.biodata.edit')" :variant="$biodata->bisaDiubahPeserta() ? 'primary' : 'secondary'">
                        {{ $biodata->bisaDiubahPeserta() ? 'Isi Biodata' : 'Lihat Biodata' }}
                    </x-button>
                </div>
            </section>

            {{-- AKSI CEPAT --}}
            <section class="panel">
                <div class="panel-head">
                    <h3>Aksi Cepat</h3>
                </div>

                <div class="info-baris"><span>Masa magang</span><strong>{{ $peserta->periode_mulai?->translatedFormat('d M Y') ?? '—' }} – {{ $peserta->periode_selesai?->translatedFormat('d M Y') ?? '—' }}</strong></div>
                <div class="info-baris"><span>Status</span><strong>{{ ucfirst($peserta->status ?? 'aktif') }}</strong></div>

                <div class="aksi-cepat">
                    <x-button variant="secondary" :href="route('peserta.izin.create')">+ Ajukan Izin / Sakit</x-button>
                    @if ($r['periode'])
                        <x-button variant="secondary" :href="route('peserta.logbook.cetak', ['periode' => $r['periode']['kunci']])">⬇ Rekap PDF</x-button>
                    @endif
                </div>
            </section>
        </div>

    </div>

</div>

@endsection
