{{-- resources/views/superadmin/peserta-magang/logbook-harian.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Logbook Harian Peserta')
@section('page_title', 'Logbook Harian')
@section('page_subtitle', $pesertaMagang->nama.($penempatan ? ' · '.$penempatan->divisi->nama : ''))

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

    .toolbar label,
    .verif label {
        display: block;

        margin-bottom: 6px;

        color: #52646e;
        font-size: 12px;
        font-weight: 700;
    }

    .toolbar select,
    .verif select,
    .verif input[type="text"] {
        height: 40px;

        padding: 0 10px;

        border: 1px solid #d7e0eb;
        border-radius: 8px;

        background: #ffffff;

        font-family: inherit;
        font-size: 13px;
    }

    .toolbar select {
        min-width: 260px;
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

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel h3 {
        margin-bottom: 12px;

        color: #17324d;
        font-size: 15px;
    }

    .verif {
        position: sticky;
        top: 0;
        z-index: 5;

        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 10px;

        margin-bottom: 14px;
        padding: 12px 14px;

        border: 1px solid #dbe7f3;
        border-radius: 10px;

        background: #f5f9fd;
    }

    .verif .catatan {
        flex: 1 1 260px;
    }

    .verif .catatan input {
        width: 100%;
    }

    .verif-info {
        align-self: center;

        color: #52646e;
        font-size: 12px;
        font-weight: 700;
    }

    .izin-item {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;

        padding: 12px 14px;
        margin-bottom: 8px;

        border: 1px solid #e5eaf0;
        border-radius: 10px;

        font-size: 13px;
    }

    .izin-item small {
        display: block;

        color: #7b8794;
    }

    .izin-item a {
        color: #1779c9;
    }

    .izin-aksi {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .izin-aksi input[type="text"] {
        height: 34px;

        padding: 0 10px;

        border: 1px solid #d7e0eb;
        border-radius: 7px;

        font-family: inherit;
        font-size: 12px;
    }

    .izin-aksi form {
        display: flex;
        gap: 6px;

        margin: 0;
    }

    .x-btn {
        min-height: 34px;
    }

    .empty {
        padding: 30px;

        color: #7b8794;
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

    <x-flash-messages />

    @if (! $periode)
        <section class="panel empty">
            Tanggal mulai dan selesai magang peserta belum diisi.
            <a href="{{ route('superadmin.peserta-magang.edit', $pesertaMagang) }}">Lengkapi data peserta</a>.
        </section>
    @else

        <div class="toolbar">
            <form method="GET" action="{{ route('superadmin.peserta-magang.logbook-harian', $pesertaMagang) }}">
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
                <x-button variant="secondary" :href="route('superadmin.peserta-magang.show', $pesertaMagang)">← Detail peserta</x-button>
                <x-button variant="secondary" :href="route('superadmin.peserta-magang.logbook-harian.cetak', [$pesertaMagang, 'periode' => $periode['kunci']])">⬇ Unduh PDF</x-button>
            </div>
        </div>

        @if ($izins->isNotEmpty())
            <section class="panel">
                <h3>Izin / Sakit</h3>

                @foreach ($izins as $izin)
                    <div class="izin-item">
                        <div>
                            <strong>{{ $izin->labelJenis() }} · {{ $izin->rentang() }}</strong>
                            <span class="lb-badge lb-{{ $izin->status }}">{{ $izin->labelStatus() }}</span>
                            <small>{{ $izin->keterangan }}</small>
                            @if ($izin->bukti_path)
                                <small><a href="{{ route('superadmin.izin-bukti.show', $izin) }}" target="_blank" rel="noopener">Lihat bukti: {{ $izin->bukti_nama }}</a></small>
                            @else
                                <small>Tanpa bukti</small>
                            @endif
                            @if ($izin->catatan_verifikasi)
                                <small>Catatan: {{ $izin->catatan_verifikasi }}</small>
                            @endif
                        </div>

                        <div class="izin-aksi">
                            <form method="POST" action="{{ route('superadmin.peserta-magang.izin.verifikasi', [$pesertaMagang, $izin]) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="diterima">
                                <x-button variant="secondary">✓ Terima</x-button>
                            </form>
                            <form method="POST" action="{{ route('superadmin.peserta-magang.izin.verifikasi', [$pesertaMagang, $izin]) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="ditolak">
                                <input type="text" name="catatan_verifikasi" placeholder="Alasan ditolak" maxlength="1000" aria-label="Alasan menolak izin" required>
                                <x-button variant="danger">Tolak</x-button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </section>
        @endif

        <section class="panel">
            <h3>{{ $periode['label'] }} · {{ $periode['mulai']->translatedFormat('d F') }} s.d. {{ $periode['selesai']->translatedFormat('d F Y') }}</h3>

            <form method="POST" action="{{ route('superadmin.peserta-magang.logbook-harian.verifikasi', $pesertaMagang) }}" id="form-verifikasi" class="verif">
                @csrf
                @method('PUT')
                <input type="hidden" name="periode" value="{{ $periode['kunci'] }}">

                <span class="verif-info" data-jumlah-pilih>0 hari dipilih</span>

                <div>
                    <label for="status">Tandai sebagai</label>
                    <select id="status" name="status" required>
                        <option value="diterima">Diterima</option>
                        <option value="review">Review</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                </div>

                <div class="catatan">
                    <label for="catatan_verifikasi">Catatan untuk peserta</label>
                    <input type="text" id="catatan_verifikasi" name="catatan_verifikasi" maxlength="1000" placeholder="Wajib bila ditolak">
                </div>

                <x-button>Simpan Verifikasi</x-button>
            </form>

            @include('logbook.partials.tabel', ['mode' => 'admin'])
        </section>

        <section class="panel">
            <h3>Rekap Periode</h3>
            @include('logbook.partials.rekap')
        </section>

    @endif

</div>

@endsection

@push('scripts')
<script>
(() => {
    const semua = document.querySelector('[data-pilih-semua]');
    const info = document.querySelector('[data-jumlah-pilih]');
    const pilihan = () => document.querySelectorAll('[data-pilih]');

    const perbarui = () => {
        if (info) {
            info.textContent = [...pilihan()].filter((el) => el.checked).length + ' hari dipilih';
        }
    };

    semua?.addEventListener('change', () => {
        pilihan().forEach((el) => { el.checked = semua.checked; });
        perbarui();
    });

    document.addEventListener('change', (event) => {
        if (event.target.matches('[data-pilih]')) {
            perbarui();
        }
    });
})();
</script>
@endpush
