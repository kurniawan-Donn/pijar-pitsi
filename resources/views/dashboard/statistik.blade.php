{{--
    Statistik dashboard (dipakai Admin & Super Admin).
    Data dari DashboardController::dataDashboard().
--}}
@php
    $maksBulanan = max(1, max(array_column($pengajuanPerBulan, 'jumlah')));
    $totalPengajuan = array_sum($pengajuanPerStatus);
    $totalDinilai = array_sum($sebaranPredikat);
    $maksPredikat = max(1, max($sebaranPredikat ?: [0]));
    $warnaStatus = ['menunggu' => '#f59e0b', 'disetujui' => '#16a34a', 'ditolak' => '#dc2626'];
@endphp

@push('styles')
<style>
    .stat-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;

        margin-top: 22px;
    }

    .stat-link {
        display: block;

        transition: transform .2s ease;
    }

    .stat-link:hover {
        transform: translateY(-2px);
    }

    .chart-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 18px;

        margin-top: 18px;
    }

    .chart-grid.three {
        grid-template-columns: repeat(3, 1fr);
    }

    .panel {
        padding: 22px;

        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;

        padding-bottom: 14px;
        margin-bottom: 16px;

        border-bottom: 1px solid #edf1f5;
    }

    .panel-title h3 {
        color: #1e3a56;
        font-size: 15px;
        font-weight: 750;
    }

    .panel-title a,
    .panel-title span {
        color: #8a96a3;
        font-size: 11px;
    }

    .panel-title a {
        color: #1779c9;
        font-weight: 700;
    }

    /* Grafik batang pengajuan per bulan (tanpa library) */
    .bar-chart {
        display: flex;
        align-items: flex-end;
        gap: 14px;

        height: 190px;
        padding-top: 20px;
    }

    .bar {
        flex: 1;

        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;

        height: 100%;
    }

    .bar-fill {
        position: relative;

        width: 100%;
        max-width: 46px;
        min-height: 4px;

        border-radius: 8px 8px 3px 3px;

        background: linear-gradient(180deg, #1f8ad6, #12345b);

        animation: grow .8s cubic-bezier(.22, .8, .24, 1) both;
        transform-origin: bottom;
    }

    .bar-fill.kosong {
        background: #e8eef4;
    }

    .bar-value {
        position: absolute;
        top: -20px;
        left: 50%;

        transform: translateX(-50%);

        color: #17324d;
        font-size: 12px;
        font-weight: 800;
    }

    .bar-label {
        color: #7b8794;
        font-size: 11px;
        white-space: nowrap;
    }

    @keyframes grow {
        from {
            transform: scaleY(0);
        }
    }

    /* Komposisi status pengajuan */
    .stack {
        display: flex;

        height: 14px;

        overflow: hidden;

        border-radius: 999px;

        background: #eef2f6;
    }

    .stack span {
        height: 100%;
    }

    .legend {
        display: grid;
        gap: 10px;

        margin-top: 18px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;

        font-size: 13px;
        color: #33475b;
    }

    .legend-item i {
        display: inline-block;

        width: 10px;
        height: 10px;

        margin-right: 8px;

        border-radius: 3px;
    }

    .legend-item strong {
        color: #17324d;
    }

    .list {
        display: grid;
    }

    .list-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;

        padding: 11px 0;

        border-top: 1px solid #f0f3f6;

        font-size: 13px;
    }

    .list-item:first-child {
        border-top: 0;
        padding-top: 0;
    }

    .list-item strong {
        display: block;

        color: #17324d;
    }

    .list-item small {
        color: #8a96a3;
        font-size: 11px;
    }

    .list-empty {
        padding: 20px 0;

        color: #8a96a3;
        font-size: 13px;
        text-align: center;
    }

    .mini-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .mini-stat {
        padding: 12px;

        border-radius: 10px;

        background: #f7f9fc;
    }

    .mini-stat span {
        display: block;

        color: #7b8794;
        font-size: 11px;
        font-weight: 700;
    }

    .mini-stat strong {
        color: #17324d;
        font-size: 20px;
    }

    .note-small {
        margin-top: 12px;

        color: #8a96a3;
        font-size: 11px;
        line-height: 1.6;
    }

    .predikat-bars {
        display: grid;
        gap: 9px;
    }

    .predikat-bar {
        display: grid;
        grid-template-columns: 22px 1fr 28px;
        align-items: center;
        gap: 10px;

        font-size: 13px;
    }

    .predikat-bar b {
        color: #17324d;
    }

    .predikat-track {
        height: 10px;

        overflow: hidden;

        border-radius: 999px;

        background: #eef2f6;
    }

    .predikat-track span {
        display: block;

        height: 100%;

        border-radius: 999px;

        background: #1779c9;
    }

    .predikat-bar small {
        color: #7b8794;
        text-align: right;
    }

    @media (max-width: 1200px) {
        .stat-row {
            grid-template-columns: repeat(2, 1fr);
        }

        .chart-grid,
        .chart-grid.three {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 520px) {
        .stat-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

{{-- ANGKA UTAMA --}}
<div class="stat-row">
    <a href="{{ route('superadmin.peserta-magang.index') }}" class="stat-link">
        <x-stat-card label="Peserta Aktif" :value="$peserta['aktif']" icon="♙" hint="{{ $peserta['total'] }} peserta terdata" />
    </a>
    <a href="{{ route('superadmin.peserta-magang.index') }}" class="stat-link">
        <x-stat-card label="Peserta Selesai" :value="$peserta['selesai']" icon="🎓" hint="{{ $peserta['belum_dinilai'] }} belum dinilai" />
    </a>
    <a href="{{ route('admin.pengajuan.index', ['status' => 'menunggu']) }}" class="stat-link">
        <x-stat-card label="Pengajuan Menunggu" :value="$pengajuanPerStatus['menunggu']" icon="✉" hint="Perlu diproses" />
    </a>
    <a href="{{ route('superadmin.laporan.index') }}" class="stat-link">
        <x-stat-card label="Berakhir Bulan Ini" :value="$berakhirBulanIni->count()" icon="⏱" hint="Periode magang selesai" />
    </a>
</div>

{{-- GRAFIK --}}
<div class="chart-grid">

    <section class="panel">
        <div class="panel-title">
            <h3>Pengajuan Masuk</h3>
            <span>6 bulan terakhir</span>
        </div>

        <div class="bar-chart" role="img" aria-label="Grafik jumlah pengajuan per bulan">
            @foreach ($pengajuanPerBulan as $index => $bulan)
                <div class="bar">
                    <div
                        @class(['bar-fill', 'kosong' => $bulan['jumlah'] === 0])
                        style="height: {{ max(2, round($bulan['jumlah'] / $maksBulanan * 100)) }}%; animation-delay: {{ $index * 0.06 }}s;"
                        title="{{ $bulan['label'] }}: {{ $bulan['jumlah'] }} pengajuan"
                    >
                        <span class="bar-value">{{ $bulan['jumlah'] }}</span>
                    </div>
                    <span class="bar-label">{{ $bulan['label'] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="panel">
        <div class="panel-title">
            <h3>Status Pengajuan</h3>
            <a href="{{ route('admin.pengajuan.index') }}">Lihat semua →</a>
        </div>

        <div class="stack" role="img" aria-label="Komposisi status pengajuan">
            @foreach ($pengajuanPerStatus as $status => $jumlah)
                @if ($totalPengajuan > 0 && $jumlah > 0)
                    <span style="width: {{ $jumlah / $totalPengajuan * 100 }}%; background: {{ $warnaStatus[$status] }};"></span>
                @endif
            @endforeach
        </div>

        <div class="legend">
            @foreach ($pengajuanPerStatus as $status => $jumlah)
                <a href="{{ route('admin.pengajuan.index', ['status' => $status]) }}" class="legend-item">
                    <span><i style="background: {{ $warnaStatus[$status] }};"></i>{{ \App\Models\PengajuanMagang::STATUS_LABELS[$status] }}</span>
                    <strong>{{ $jumlah }}</strong>
                </a>
            @endforeach
            <div class="legend-item" style="border-top: 1px solid #edf1f5; padding-top: 10px;">
                <span>Total</span>
                <strong>{{ $totalPengajuan }}</strong>
            </div>
        </div>
    </section>

</div>

<div class="chart-grid three">

    {{-- BERAKHIR BULAN INI --}}
    <section class="panel">
        <div class="panel-title">
            <h3>Berakhir Bulan Ini</h3>
            <span>{{ now()->translatedFormat('F Y') }}</span>
        </div>

        <div class="list">
            @forelse ($berakhirBulanIni as $item)
                <div class="list-item">
                    <div>
                        <a href="{{ route('superadmin.peserta-magang.show', $item) }}"><strong>{{ $item->nama }}</strong></a>
                        <small>Selesai {{ $item->periode_selesai->translatedFormat('d M Y') }}</small>
                    </div>
                    @if ($item->penilaian)
                        <x-status-badge status="disetujui" :label="'Nilai ' . $item->penilaian->predikat" />
                    @else
                        <a href="{{ route('superadmin.peserta-magang.penilaian', $item) }}"><x-status-badge status="menunggu" label="Belum dinilai" /></a>
                    @endif
                </div>
            @empty
                <div class="list-empty">Tidak ada peserta yang selesai bulan ini.</div>
            @endforelse
        </div>
    </section>

    {{-- PENGAJUAN TERBARU --}}
    <section class="panel">
        <div class="panel-title">
            <h3>Pengajuan Terbaru</h3>
            <a href="{{ route('admin.pengajuan.index') }}">Semua →</a>
        </div>

        <div class="list">
            @forelse ($pengajuanTerbaru as $pengajuan)
                <div class="list-item">
                    <div>
                        <a href="{{ route('admin.pengajuan.show', $pengajuan) }}"><strong>{{ $pengajuan->mahasiswa?->nama ?? '-' }}</strong></a>
                        <small>{{ $pengajuan->posisi }} · {{ $pengajuan->created_at->diffForHumans() }}</small>
                    </div>
                    <x-status-badge :status="$pengajuan->status" :label="$pengajuan->statusLabel()" />
                </div>
            @empty
                <div class="list-empty">Belum ada pengajuan.</div>
            @endforelse
        </div>
    </section>

    {{-- ABSENSI & NILAI --}}
    <section class="panel">
        <div class="panel-title">
            <h3>Absensi {{ now()->translatedFormat('F') }}</h3>
            <span>{{ $absensi['peserta_tersedia'] }}/{{ $absensi['peserta_aktif'] }} peserta</span>
        </div>

        @if ($absensi['peserta_tersedia'] > 0)
            <div class="mini-stats">
                <div class="mini-stat"><span>Hadir</span><strong>{{ $absensi['hadir'] }}</strong></div>
                <div class="mini-stat"><span>Izin + Sakit</span><strong>{{ $absensi['izin'] + $absensi['sakit'] }}</strong></div>
                <div class="mini-stat" style="grid-column: 1 / -1;"><span>Uang saku berjalan</span><strong>Rp {{ number_format($absensi['uang_saku'], 0, ',', '.') }}</strong></div>
            </div>
        @else
            <div class="list-empty">Belum ada data absensi tersimpan.</div>
        @endif

        <p class="note-small">
            Diambil dari data Google Sheets yang sudah dibuka dalam {{ intdiv(config('absensi.cache_seconds'), 60) }} menit terakhir.
            Rekap lengkap ada di <a href="{{ route('superadmin.laporan.index', ['jenis' => 'absensi']) }}" style="color:#1779c9;font-weight:700;">Laporan Absensi</a>.
        </p>

        @if ($totalDinilai > 0)
            <div class="panel-title" style="margin-top: 18px;">
                <h3>Sebaran Nilai</h3>
                <span>{{ $totalDinilai }} dinilai</span>
            </div>

            <div class="predikat-bars">
                @foreach ($sebaranPredikat as $predikat => $jumlah)
                    <div class="predikat-bar">
                        <b>{{ $predikat }}</b>
                        <div class="predikat-track"><span style="width: {{ $jumlah / $maksPredikat * 100 }}%;"></span></div>
                        <small>{{ $jumlah }}</small>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

</div>
