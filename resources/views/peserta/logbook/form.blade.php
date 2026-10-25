{{-- resources/views/peserta/logbook/form.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Isi Logbook')
@section('page_title', 'Logbook '.$tanggal->translatedFormat('l, d F Y'))
@section('page_subtitle', 'Isi jam kerja dan kegiatan hari ini')

@php
    $kehadiran = old('kehadiran', $logbook->kehadiran);
@endphp

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .panel {
        max-width: 900px;

        margin-bottom: 18px;
        padding: 22px 24px;

        background: #ffffff;

        border: 1px solid var(--garis);
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel h3 {
        margin-bottom: 6px;

        color: var(--utama-gelap);
        font-size: 15px;
    }

    .panel > p {
        margin-bottom: 16px;

        color: var(--teks-sekunder);
        font-size: 12px;
        line-height: 1.6;
    }

    .pilihan {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;

        margin-bottom: 18px;
    }

    .pilihan label {
        display: flex;
        align-items: center;
        gap: 8px;

        padding: 10px 16px;

        border: 1px solid var(--garis-kuat);
        border-radius: 10px;

        font-size: 13px;
        font-weight: 700;

        cursor: pointer;
    }

    .pilihan label:has(input:checked) {
        border-color: var(--utama);
        background: var(--utama-muda);
        color: var(--utama-gelap);
    }

    .pilihan input {
        accent-color: var(--utama);
    }

    .pilihan a {
        align-self: center;

        color: var(--utama);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
    }

    .jam-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;

        margin-bottom: 6px;
    }

    .sesi {
        padding: 14px 16px 2px;

        border: 1px solid var(--garis);
        border-radius: 11px;

        background: var(--permukaan-redup);
    }

    .sesi h4 {
        margin-bottom: 10px;

        color: var(--teks-sekunder);
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .sesi .baris {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0 12px;
    }

    .jam-hint {
        margin-bottom: 16px;

        color: var(--teks-sekunder);
        font-size: 12px;
        line-height: 1.6;
    }

    .bukti-lama {
        display: flex;
        flex-direction: column;
        gap: 6px;

        margin-bottom: 12px;
    }

    .bukti-lama label {
        display: flex;
        align-items: center;
        gap: 8px;

        font-size: 13px;
    }

    .bukti-lama a {
        color: var(--utama);
    }

    .actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 10px;

        max-width: 900px;
    }

    .actions-right {
        display: flex;
        gap: 10px;
    }

    [data-hanya-hadir][hidden] {
        display: none;
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 16px 30px;
        }

        .jam-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages :validation="false" />

    @if ($errors->any())
        <x-alert type="error" title="Logbook belum tersimpan:" :messages="$errors->all()" />
    @endif

    @if ($logbook->exists && in_array($logbook->status_verifikasi, ['ditolak', 'review'], true))
        <x-alert type="warning" :title="'Status: '.$logbook->labelStatus()" :message="$logbook->catatan_verifikasi ?: 'Perbaiki isian lalu simpan untuk diperiksa ulang.'" />
    @endif

    <form method="POST" action="{{ route('peserta.logbook.update', $tanggal->toDateString()) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <section class="panel">
            <h3>Kehadiran</h3>
            <p>Untuk izin atau sakit (termasuk beberapa hari dengan satu surat), gunakan form izin.</p>

            @if ($logbook->exists && $logbook->dariScan())
                <x-alert type="info">
                    Jam diisi dari <a href="{{ route('scan-kartu.foto', $logbook->scan_kartu_absensi_id) }}" target="_blank" rel="noopener">foto kartu absensi</a>
                    (hasil scan: {{ ($logbook->jam_scan_asli['pagi_masuk'] ?? '') ?: '–' }} – {{ ($logbook->jam_scan_asli['siang_keluar'] ?? '') ?: '–' }}).
                    Bila jam diubah, admin melihat tanda "diubah setelah scan".
                    @if ($logbook->activityBelumDiisi())
                        <br><strong>Lengkapi activity agar logbook ini bisa diterima admin.</strong>
                    @endif
                </x-alert>
            @endif

            <div class="pilihan">
                <label><input type="radio" name="kehadiran" value="hadir" @checked($kehadiran === 'hadir') data-kehadiran> Hadir</label>
                <label><input type="radio" name="kehadiran" value="libur" @checked($kehadiran === 'libur') data-kehadiran> Libur</label>
                <a href="{{ route('peserta.izin.create', ['tanggal' => $tanggal->toDateString()]) }}">Izin / Sakit →</a>
            </div>

            <div data-hanya-hadir>
                <div class="jam-grid">
                    <div class="sesi">
                        <h4>Pagi</h4>
                        <div class="baris">
                            <x-form.input name="pagi_masuk" type="time" label="Masuk" :value="$logbook->jam('pagi_masuk')" />
                            <x-form.input name="pagi_keluar" type="time" label="Keluar" :value="$logbook->jam('pagi_keluar')" />
                        </div>
                    </div>
                    <div class="sesi">
                        <h4>Siang</h4>
                        <div class="baris">
                            <x-form.input name="siang_masuk" type="time" label="Masuk" :value="$logbook->jam('siang_masuk')" />
                            <x-form.input name="siang_keluar" type="time" label="Keluar" :value="$logbook->jam('siang_keluar')" />
                        </div>
                    </div>
                </div>
                <p class="jam-hint">
                    <strong>Hari penuh:</strong> isi pagi masuk dan siang keluar (jam istirahat boleh kosong).
                    <strong>Setengah hari:</strong> isi satu sesi saja, pagi masuk–keluar (pulang siang) atau siang masuk–keluar (datang siang).
                </p>
            </div>
        </section>

        <section class="panel">
            <h3>Kegiatan</h3>
            <p>Sama seperti kolom Activity, Output, dan Lokasi di sheet logbook.</p>

            <x-form.input name="activity" type="textarea" label="Activity" :value="$logbook->activity" rows="4" maxlength="2000" placeholder="Kegiatan yang dikerjakan hari ini" />

            <div data-hanya-hadir>
                <x-form.input name="output" type="textarea" label="Output (opsional)" :value="$logbook->output" rows="2" maxlength="2000" placeholder="Hasil kerja, mis. dokumen, fitur, laporan" />
                <x-form.input name="lokasi" label="Lokasi" :value="$logbook->lokasi" maxlength="255" list="saran-lokasi" placeholder="mis. Kantor Jogja" />
                <datalist id="saran-lokasi">
                    @foreach ($saranLokasi as $lokasi)
                        <option value="{{ $lokasi }}"></option>
                    @endforeach
                </datalist>
            </div>
        </section>

        <section class="panel">
            <h3>Bukti (opsional)</h3>
            <p>Foto atau dokumen pendukung. JPG, PNG, atau PDF, maksimal 2 MB per file dan 3 file per hari.</p>

            @if ($logbook->exists && $logbook->scan_kartu_absensi_id)
                <p><x-icon name="camera" :size="15" /> Foto kartu absensi otomatis menjadi bukti:
                    <a href="{{ route('scan-kartu.foto', $logbook->scan_kartu_absensi_id) }}" target="_blank" rel="noopener">lihat kartu</a>
                    (tidak mengurangi jatah 3 file).</p>
            @endif

            @if ($logbook->exists && $logbook->buktis->isNotEmpty())
                <div class="bukti-lama">
                    @foreach ($logbook->buktis as $bukti)
                        <label>
                            <input type="checkbox" name="hapus_bukti[]" value="{{ $bukti->id }}">
                            Hapus
                            <a href="{{ route('peserta.logbook.bukti', $bukti) }}" target="_blank" rel="noopener">{{ $bukti->nama_asli }}</a>
                            <small>({{ $bukti->ukuranTampil() }})</small>
                        </label>
                    @endforeach
                </div>
            @endif

            <input type="file" name="bukti[]" accept=".jpg,.jpeg,.png,.pdf" multiple class="x-form-control" aria-label="Unggah bukti">
        </section>

        <div class="actions">
            <x-button variant="secondary" :href="route('peserta.logbook.index', ['periode' => app(\App\Services\PeriodeMagang::class)->kunciUntuk($tanggal)])">← Kembali</x-button>

            <div class="actions-right">
                <x-button>Simpan Logbook</x-button>
            </div>
        </div>
    </form>

    @if ($logbook->exists)
        <form method="POST" action="{{ route('peserta.logbook.destroy', $tanggal->toDateString()) }}" onsubmit="return confirm('Hapus isian logbook tanggal ini?')" style="max-width: 900px; margin-top: 12px; text-align: right;">
            @csrf
            @method('DELETE')
            <x-button variant="danger">Hapus Isian</x-button>
        </form>
    @endif

</div>

@endsection

@push('scripts')
<script>
(() => {
    const pilihan = document.querySelectorAll('[data-kehadiran]');
    const bagianHadir = document.querySelectorAll('[data-hanya-hadir]');

    const perbarui = () => {
        const hadir = document.querySelector('[data-kehadiran]:checked')?.value === 'hadir';
        bagianHadir.forEach((el) => { el.hidden = !hadir; });
    };

    pilihan.forEach((el) => el.addEventListener('change', perbarui));
    perbarui();
})();
</script>
@endpush
