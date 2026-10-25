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

        color: var(--teks-sekunder);
        font-size: 12px;
        font-weight: 700;
    }

    .toolbar select,
    .verif select,
    .verif input[type="text"] {
        height: 40px;

        padding: 0 10px;

        border: 1px solid var(--garis-kuat);
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

        border: 1px solid var(--garis);
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel h3 {
        margin-bottom: 12px;

        color: var(--utama-gelap);
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

        border: 1px solid var(--utama-muda);
        border-radius: 10px;

        background: var(--permukaan-redup);
    }

    .verif .catatan {
        flex: 1 1 260px;
    }

    .verif .catatan input {
        width: 100%;
    }

    .verif-info {
        align-self: center;

        color: var(--teks-sekunder);
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

        border: 1px solid var(--garis);
        border-radius: 10px;

        font-size: 13px;
    }

    .izin-item small {
        display: block;

        color: var(--teks-sekunder);
    }

    .izin-item a {
        color: var(--utama);
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

        border: 1px solid var(--garis-kuat);
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

    .impor-note {
        margin: -6px 0 14px;

        color: var(--teks-sekunder);
        font-size: 12px;
        line-height: 1.6;
    }

    .impor-hasil {
        margin-bottom: 14px;
        padding: 14px 16px;

        border: 1px solid var(--garis);
        border-radius: 10px;

        background: var(--permukaan-redup);

        font-size: 13px;
    }

    .impor-hasil table {
        width: 100%;
        margin-bottom: 10px;

        border-collapse: collapse;
    }

    .impor-hasil th,
    .impor-hasil td {
        padding: 6px 8px;

        border-bottom: 1px solid var(--garis);

        text-align: left;
    }

    .impor-hasil th {
        color: var(--teks-sekunder);
        font-size: 11px;
        text-transform: uppercase;
    }

    .impor-aksi {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
    }

    .impor-aksi form {
        margin: 0;
    }

    .impor-xlsx {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .impor-xlsx input[type="file"] {
        max-width: 280px;
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
                <x-button variant="secondary" :href="route('superadmin.peserta-magang.logbook-harian.cetak', [$pesertaMagang, 'periode' => $periode['kunci']])"><x-icon name="download" :size="16" /> Unduh PDF</x-button>
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
                                <x-button variant="secondary"><x-icon name="check" :size="16" /> Terima</x-button>
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

    {{-- IMPOR RIWAYAT DARI BUKU KERJA LAMA --}}
    <section class="panel" id="impor">
        <h3>Impor Riwayat dari Buku Kerja Lama</h3>
        <p class="impor-note">
            Membaca tab bulanan format lama (Keterangan + Paraf) maupun format baru (Activity, Output, Lokasi, Action).
            Tanggal yang sudah ada di website tidak ditimpa; hanya tanggal dalam masa magang yang disimpan.
            Jam yang tidak terbaca dikosongkan dan dicantumkan di laporan untuk dicek.
        </p>

        @if (session('hasil_impor'))
            @php
                $hasil = session('hasil_impor');
            @endphp
            <div class="impor-hasil">
                <table>
                    <thead>
                        <tr><th>Tab</th><th>Format</th><th>Baris terbaca</th><th>Ditambahkan</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($hasil['tab'] as $tab)
                            <tr>
                                <td>{{ $tab['nama'] }}</td>
                                <td>{{ $tab['format'] === 'baru' ? 'Baru (Activity)' : 'Lama (Keterangan)' }}</td>
                                <td>{{ $tab['baris'] }}</td>
                                <td>{{ $tab['dibuat'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p>
                    Total ditambahkan <strong>{{ $hasil['dibuat'] }}</strong> hari ·
                    sudah ada (dilewati) {{ $hasil['sudah_ada'] }} ·
                    di luar masa magang {{ $hasil['di_luar'] }}
                    @if ($hasil['dilewati'])
                        · tab lain dilewati: {{ implode(', ', $hasil['dilewati']) }}
                    @endif
                </p>
                @if ($hasil['peringatan'])
                    <x-alert type="warning" :title="count($hasil['peringatan']).' catatan perlu dicek:'" :messages="array_slice($hasil['peringatan'], 0, 50)" />
                @endif
            </div>
        @endif

        <x-alert type="error" :messages="$errors->impor->all()" />

        <div class="impor-aksi">
            @if ($pesertaMagang->google_sheet_url)
                <form method="POST" action="{{ route('superadmin.peserta-magang.impor-logbook.sheets', $pesertaMagang) }}" onsubmit="return confirm('Impor semua tab bulanan dari Google Sheets peserta?')">
                    @csrf
                    <x-button variant="secondary">Impor dari Google Sheets Peserta</x-button>
                </form>
            @endif

            <form method="POST" action="{{ route('superadmin.peserta-magang.impor-logbook.xlsx', $pesertaMagang) }}" enctype="multipart/form-data" class="impor-xlsx">
                @csrf
                <input type="file" name="berkas" accept=".xlsx" required class="x-form-control" aria-label="File buku kerja .xlsx">
                <x-button variant="secondary">Impor dari File .xlsx</x-button>
            </form>

            @if ($jumlahImpor > 0)
                <form method="POST" action="{{ route('superadmin.peserta-magang.impor-logbook.hapus', $pesertaMagang) }}" onsubmit="return confirm('Hapus semua hari hasil impor yang belum punya bukti?')">
                    @csrf
                    @method('DELETE')
                    <x-button variant="danger">Hapus Hasil Impor ({{ $jumlahImpor }} hari)</x-button>
                </form>
            @endif
        </div>
    </section>

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
