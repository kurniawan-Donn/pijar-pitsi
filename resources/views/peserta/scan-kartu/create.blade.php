{{-- resources/views/peserta/scan-kartu/create.blade.php — unggah / potret kartu absensi. --}}

@extends('layouts.dashboard')

@section('title', 'Isi dari Kartu Absensi')
@section('page_title', 'Isi dari Kartu Absensi')
@section('page_subtitle', 'Potret kartu ceklok, periksa hasilnya, lalu simpan ke logbook')

@push('styles')
<style>
    .tata-scan {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 18px;
        align-items: start;

        max-width: 1080px;
    }

    .panel {
        padding: 22px 24px;
    }

    .panel h3 {
        margin-bottom: 6px;

        font-size: 15px;
    }

    .panel > p {
        margin-bottom: 14px;

        color: var(--teks-sekunder);
        font-size: 13px;
        line-height: 1.6;
    }

    .pilihan-kartu {
        display: grid;
        gap: 8px;

        margin-bottom: 16px;
    }

    .pilihan-kartu label {
        display: flex;
        align-items: center;
        gap: 10px;

        padding: 12px 14px;

        border: 1px solid var(--garis-kuat);
        border-radius: var(--radius-kecil);

        cursor: pointer;
    }

    .pilihan-kartu label:has(input:checked) {
        border-color: var(--utama);
        background: var(--utama-muda);
    }

    .pilihan-kartu strong {
        display: block;

        font-size: 13.5px;
    }

    .pilihan-kartu small {
        color: var(--teks-sekunder);
        font-size: 12px;
    }

    .area-foto {
        display: grid;
        grid-template-columns: 180px minmax(0, 1fr);
        gap: 16px;
        align-items: start;
    }

    .pratinjau-kartu {
        width: 100%;
        aspect-ratio: 3 / 4;

        display: grid;
        place-items: center;

        overflow: hidden;

        border: 1px dashed var(--garis-kuat);
        border-radius: var(--radius-kecil);

        background: var(--permukaan-redup);
        color: var(--teks-redup);

        font-size: 12px;
        text-align: center;

        object-fit: cover;
    }

    .tips li {
        margin: 0 0 6px 18px;

        color: var(--teks-sekunder);
        font-size: 13px;
        line-height: 1.55;
    }

    .info-sisa {
        display: flex;
        align-items: center;
        gap: 8px;

        margin-bottom: 14px;

        color: var(--teks-sekunder);
        font-size: 13px;
    }

    .riwayat a {
        display: flex;
        justify-content: space-between;
        gap: 10px;

        padding: 9px 0;

        border-top: 1px solid var(--garis);

        color: var(--teks);
        font-size: 13px;
    }

    .riwayat a:first-of-type {
        border-top: 0;
    }

    .actions {
        display: flex;
        justify-content: space-between;
        gap: 10px;

        margin-top: 6px;
    }

    .x-btn.memuat {
        pointer-events: none;
        opacity: .7;
    }

    @media (max-width: 900px) {
        .tata-scan,
        .area-foto {
            grid-template-columns: 1fr;
        }

        .pratinjau-kartu {
            max-width: 220px;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages />

    <div class="tata-scan">

        <section class="panel">
            @if (! $aktif)
                <x-empty icon="camera" title="Fitur scan kartu sedang dinonaktifkan" message="Isi jam logbook secara manual seperti biasa." />
            @elseif ($pilihan === [])
                <x-empty icon="clock" title="Scan kartu belum dibuka" :message="'Scan kartu hanya bisa dipakai pada tanggal tertentu. '.$keterangan.($bukaBerikutnya ? ' Berikutnya mulai '.$bukaBerikutnya->translatedFormat('l, d F Y').'.' : '')" />
            @else
                <h3>1. Pilih kartu</h3>
                <p>Bulan ditulis tangan di kartu, jadi pilih bulan dan sisinya di sini.</p>

                <form method="POST" action="{{ route('peserta.scan-kartu.store') }}" enctype="multipart/form-data" id="form-scan">
                    @csrf

                    <div class="pilihan-kartu">
                        @foreach ($pilihan as $kartu)
                            @php
                                $nilai = $kartu['sisi'].'|'.$kartu['tahun'].'|'.$kartu['bulan'];
                            @endphp
                            <label>
                                <input type="radio" name="kartu" value="{{ $nilai }}" @checked(old('kartu', $loop->first ? $nilai : null) === $nilai) required>
                                <span>
                                    <strong>{{ $kartu['label'] }}</strong>
                                    <small>{{ $kartu['sisi'] === '1-15' ? 'Sisi depan kartu' : 'Sisi belakang kartu' }}</small>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <h3>2. Potret atau unggah kartu</h3>

                    <div class="info-sisa">
                        <x-icon name="info" :size="16" />
                        Sisa scan hari ini: <strong>{{ $sisa }}</strong>
                    </div>

                    <div class="area-foto">
                        <span class="pratinjau-kartu" id="pratinjau-kartu">Pratinjau foto kartu</span>

                        <div>
                            <x-form.input
                                name="foto"
                                type="file"
                                label="Foto kartu absensi"
                                accept="image/jpeg,image/png,image/webp"
                                capture="environment"
                                data-pratinjau="#pratinjau-kartu"
                                data-maks-mb="10"
                                required
                                hint="Di HP, kamera langsung terbuka. JPG/PNG/WebP maksimal 10 MB."
                            />

                            <ul class="tips">
                                <li>Letakkan kartu di permukaan rata dan terang, tanpa bayangan.</li>
                                <li>Potret tegak lurus, seluruh tabel (kolom Tgl sampai Jam) masuk bingkai.</li>
                                <li>Hasil scan <b>tidak langsung tersimpan</b>; Anda memeriksanya dulu.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="actions">
                        <x-button variant="secondary" :href="route('peserta.logbook.index')">Batal</x-button>
                        <x-button id="tombol-baca" :disabled="$sisa < 1"><x-icon name="search" :size="16" /> Baca Kartu</x-button>
                    </div>
                </form>
            @endif
        </section>

        <aside class="panel">
            <h3>Cara kerja</h3>
            <p>
                Sistem membaca kolom <b>Pagi Masuk</b> dan <b>Siang Keluar</b>. Anda bisa memperbaiki hasilnya sebelum
                menyimpan. Activity, output, dan lokasi tetap diisi di logbook seperti biasa.
            </p>
            <p>{{ $keterangan }}</p>

            @if ($riwayat->isNotEmpty())
                <h3>Scan terakhir</h3>
                <div class="riwayat">
                    @foreach ($riwayat as $scan)
                        <a href="{{ $scan->status === 'gagal' ? route('peserta.scan-kartu.create') : route('peserta.scan-kartu.show', $scan) }}">
                            <span>{{ $scan->labelBulan() }} · {{ $scan->sisi }}</span>
                            <x-status-badge
                                :status="['dipratinjau' => 'menunggu', 'disimpan' => 'aktif', 'gagal' => 'ditolak'][$scan->status] ?? 'menunggu'"
                                :label="['dipratinjau' => 'Belum disimpan', 'disimpan' => 'Disimpan', 'gagal' => 'Gagal'][$scan->status] ?? $scan->status"
                            />
                        </a>
                    @endforeach
                </div>
            @endif
        </aside>

    </div>

</div>

@endsection

@push('scripts')
<script>
    // Membaca kartu butuh beberapa detik: cegah klik ganda (scan dihitung per percobaan).
    document.getElementById('form-scan')?.addEventListener('submit', function () {
        const tombol = document.getElementById('tombol-baca');
        tombol.classList.add('memuat');
        tombol.textContent = 'Membaca kartu…';
    });
</script>
@endpush
