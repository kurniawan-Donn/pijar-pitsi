{{-- resources/views/peserta/dashboard.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Dashboard Peserta')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan magang Anda')

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .welcome-card {
        padding: 26px 28px;

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

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;

        margin-top: 20px;
    }

    .panel {
        margin-top: 20px;
        padding: 22px 24px;

        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel h3 {
        color: #17324d;
        font-size: 15px;
    }

    .panel p {
        margin-top: 6px;

        color: #7b8794;
        font-size: 12px;
        line-height: 1.7;
    }

    .panel-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .panel-row > div {
        flex: 1 1 320px;
    }

    .progress-track {
        height: 8px;
        margin-top: 12px;

        border-radius: 999px;

        background: #edf1f5;

        overflow: hidden;
    }

    .progress-fill {
        height: 100%;

        border-radius: 999px;

        background: linear-gradient(90deg, #1779c9, #19a974);
    }

    .progress-text {
        display: block;

        margin-top: 6px;

        color: #7b8794;
        font-size: 12px;
    }

    @media (max-width: 900px) {
        .stat-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 20px 30px;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages :validation="false" />

    <section class="welcome-card">
        <h2>Halo, {{ $peserta->nama ?? auth()->user()->name }}</h2>
        <p>
            Selamat datang di MAGENTA. Dari sini Anda akan mengisi biodata dan logbook harian magang.
        </p>
    </section>

    <div class="stat-grid">
        <x-stat-card label="Nomor Peserta" :value="$peserta->nomor_peserta ?? '-'" icon="#" />

        <x-stat-card
            label="Periode Magang"
            :value="$peserta->periode_mulai ? $peserta->periode_mulai->translatedFormat('d M Y') : '-'"
            icon="◷"
            :hint="$peserta->periode_selesai ? 's.d. '.$peserta->periode_selesai->translatedFormat('d M Y') : null"
        />

        <x-stat-card label="Status" :value="ucfirst($peserta->status ?? 'aktif')" icon="✓" />
    </div>

    @php
        $progres = $biodata->progres();
    @endphp

    <section class="panel">
        <div class="panel-row">
            <div>
                <h3>Biodata · {{ $biodata->labelStatus() }}</h3>
                <p>
                    @if ($biodata->status === 'perlu_revisi')
                        Admin meminta perbaikan biodata. Buka biodata untuk melihat catatannya.
                    @elseif ($biodata->status === 'disetujui')
                        Biodata Anda sudah disetujui admin.
                    @elseif ($biodata->status === 'diajukan')
                        Biodata sudah dikirim dan sedang ditinjau admin.
                    @else
                        Lengkapi biodata Anda, lalu kirim untuk ditinjau admin.
                    @endif
                </p>
                <div class="progress-track" aria-label="Progres biodata {{ $progres['persen'] }}%">
                    <div class="progress-fill" style="width: {{ $progres['persen'] }}%"></div>
                </div>
                <small class="progress-text">{{ $progres['terisi'] }} dari {{ $progres['total'] }} data wajib terisi ({{ $progres['persen'] }}%)</small>
            </div>
            <x-button :href="route('peserta.biodata.edit')" :variant="$biodata->bisaDiubahPeserta() ? 'primary' : 'secondary'">
                {{ $biodata->bisaDiubahPeserta() ? 'Isi Biodata' : 'Lihat Biodata' }}
            </x-button>
        </div>
    </section>

    <section class="panel">
        <div class="panel-row">
            <div>
                <h3>Logbook Harian</h3>
                <p>
                    Isi jam kerja dan kegiatan setiap hari. Izin atau sakit beberapa hari cukup diajukan sekali
                    dengan satu bukti. Rekap uang saku per periode (28 s.d. 27) ada di halaman logbook.
                </p>
            </div>
            <x-button :href="route('peserta.logbook.index')">Buka Logbook</x-button>
        </div>
    </section>

</div>

@endsection
