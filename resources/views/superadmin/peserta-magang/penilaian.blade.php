{{-- resources/views/superadmin/peserta-magang/penilaian.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Penilaian ' . $pesertaMagang->nama)
@section('page_title', 'Penilaian Akhir')
@section('page_subtitle', $pesertaMagang->nama . ($pesertaMagang->nim ? ' · NIM ' . $pesertaMagang->nim : ''))

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .back-link {
        display: inline-block;

        margin-bottom: 20px;

        color: #1779c9;
        font-size: 13px;
        font-weight: 700;
    }

    .grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 18px;
        align-items: start;
    }

    .panel {
        padding: 22px;

        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel + .panel {
        margin-top: 18px;
    }

    .panel h3 {
        margin-bottom: 4px;

        color: #17324d;
        font-size: 15px;
    }

    .panel > p {
        margin-bottom: 18px;

        color: #7b8794;
        font-size: 12px;
    }

    .aspek-row {
        display: grid;
        grid-template-columns: 1fr 1.3fr 70px;
        align-items: center;
        gap: 16px;

        padding: 14px 0;

        border-top: 1px solid #edf1f5;
    }

    .aspek-nama {
        color: #17324d;
        font-size: 14px;
        font-weight: 700;
    }

    .aspek-bobot {
        color: #8a96a3;
        font-size: 11px;
    }

    .aspek-row input[type="range"] {
        width: 100%;

        accent-color: #1779c9;
    }

    .aspek-row input[type="number"] {
        width: 100%;
        height: 40px;

        padding: 0 8px;

        border: 1px solid #d7e0eb;
        border-radius: 8px;

        font-family: inherit;
        font-size: 15px;
        font-weight: 800;
        text-align: center;
    }

    .aspek-row input.is-invalid {
        border-color: #e05252;
    }

    .aspek-error {
        grid-column: 1 / -1;

        margin-top: -8px;

        color: #e05252;
        font-size: 11px;
        font-weight: 600;
    }

    .result {
        position: sticky;
        top: 20px;

        text-align: center;
    }

    .result-label {
        color: #7b8794;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .5px;
        text-transform: uppercase;
    }

    .result-nilai {
        margin-top: 8px;

        color: #17324d;
        font-size: 46px;
        font-weight: 800;
        line-height: 1;
    }

    .result-predikat {
        display: inline-block;

        margin-top: 14px;
        padding: 8px 16px;

        border-radius: 999px;

        background: #1779c9;
        color: #ffffff;

        font-size: 15px;
        font-weight: 800;
    }

    .kelompok-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 10px;

        margin: 18px 0 2px;
        padding-bottom: 6px;

        border-bottom: 2px solid #e8f0f8;
    }

    .kelompok-head h4 {
        color: #17324d;
        font-size: 14px;
    }

    .kelompok-head span {
        color: #52646e;
        font-size: 12px;
        font-weight: 700;
    }

    .kelompok-rata {
        display: grid;
        gap: 6px;

        margin-top: 18px;

        font-size: 12.5px;
        text-align: left;
    }

    .kelompok-rata div {
        display: flex;
        justify-content: space-between;

        padding: 6px 10px;

        border-radius: 7px;

        background: #f7f9fc;
    }

    .scale {
        display: grid;
        gap: 6px;

        margin-top: 18px;

        font-size: 12px;
        text-align: left;
    }

    .scale div {
        display: flex;
        justify-content: space-between;
        gap: 10px;

        padding: 6px 10px;

        border-radius: 7px;

        background: #f7f9fc;
        color: #4b5d70;
    }

    .scale div.active {
        background: #eaf3fb;
        color: #1779c9;
        font-weight: 800;
    }

    .meta {
        margin-top: 18px;

        color: #8a96a3;
        font-size: 11px;
        line-height: 1.6;
    }

    .form-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 10px;

        margin-top: 18px;
    }

    @media (max-width: 1100px) {
        .grid {
            grid-template-columns: 1fr;
        }

        .result {
            position: static;
        }
    }

    @media (max-width: 600px) {
        .content {
            padding: 22px 16px 30px;
        }

        .aspek-row {
            grid-template-columns: 1fr 70px;
        }

        .aspek-row input[type="range"] {
            grid-column: 1 / -1;
            order: 3;
        }
    }
</style>
@endpush

@php
    $predikatJs = $predikats->map(fn ($predikat) => ['batas' => $predikat->batas_bawah, 'nama' => $predikat->nama])->values();
@endphp

@section('content')

<div class="content">

    <a href="{{ route('superadmin.peserta-magang.show', $pesertaMagang) }}" class="back-link">← Kembali ke detail peserta</a>

    <x-flash-messages :validation="false" />

    @if ($kelompoks->isEmpty())
        <x-alert type="warning" message="Belum ada aspek penilaian aktif. Super Admin perlu mengatur aspek penilaian terlebih dahulu." />
    @else
        <form method="POST" action="{{ route('superadmin.peserta-magang.penilaian.update', $pesertaMagang) }}" id="form-penilaian">
            @csrf
            @method('PUT')

            <div class="grid">

                <div>
                    <section class="panel">
                        <h3>Indikator Penilaian</h3>
                        <p>
                            Skala 1–100. Nilai kelompok = rata-rata aspeknya; nilai akhir =
                            {{ $kelompoks->map(fn ($kelompok) => '(rata-rata '.$kelompok->nama.' × '.$kelompok->bobot.'%)')->implode(' + ') }}.
                        </p>

                        @foreach ($kelompoks as $kelompok)
                            <div class="kelompok-head">
                                <h4>{{ $kelompok->nama }}</h4>
                                <span>Bobot {{ $kelompok->bobot }}%</span>
                            </div>

                            @foreach ($kelompok->aspekAktif as $aspek)
                                @php
                                    $nilai = old('nilai.'.$aspek->id, $nilaiTersimpan[$aspek->id] ?? '');
                                @endphp
                                <div class="aspek-row">
                                    <div class="aspek-nama">{{ $loop->iteration }}. {{ $aspek->nama }}</div>

                                    <input
                                        type="range"
                                        min="1"
                                        max="100"
                                        value="{{ $nilai === '' ? 1 : $nilai }}"
                                        data-range-for="nilai-{{ $aspek->id }}"
                                        aria-label="Geser nilai {{ $aspek->nama }}"
                                        tabindex="-1"
                                    >

                                    <input
                                        type="number"
                                        id="nilai-{{ $aspek->id }}"
                                        name="nilai[{{ $aspek->id }}]"
                                        value="{{ $nilai }}"
                                        min="1"
                                        max="100"
                                        step="1"
                                        required
                                        data-kelompok="{{ $kelompok->id }}"
                                        aria-label="Nilai {{ $aspek->nama }}"
                                        @class(['is-invalid' => $errors->has('nilai.'.$aspek->id)])
                                    >

                                    @error('nilai.'.$aspek->id)
                                        <div class="aspek-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        @endforeach
                    </section>

                    <section class="panel">
                        <h3>Kesimpulan Penilaian</h3>
                        <p>Mengikuti bagian kesimpulan pada lembar penilaian. Isian dibatasi agar lembar penilaian tetap 1 halaman A4.</p>

                        @php
                            $maksKesimpulan = \App\Http\Requests\SimpanPenilaianRequest::MAKS_KESIMPULAN;
                            $petunjukKesimpulan = 'Maksimal '.$maksKesimpulan.' karakter dan '.\App\Http\Requests\SimpanPenilaianRequest::MAKS_BARIS_KESIMPULAN.' baris.';
                        @endphp

                        <x-form.input name="kekuatan" type="textarea" label="Kekuatan dan keunggulan mahasiswa magang" :value="$penilaian?->kekuatan" rows="3" :maxlength="$maksKesimpulan" data-hitung :hint="$petunjukKesimpulan" />
                        <x-form.input name="aspek_perbaikan" type="textarea" label="Aspek utama yang diperlukan untuk perbaikan" :value="$penilaian?->aspek_perbaikan" rows="3" :maxlength="$maksKesimpulan" data-hitung :hint="$petunjukKesimpulan" />
                        <x-form.input name="langkah_perbaikan" type="textarea" label="Langkah penting yang harus diambil untuk perbaikan" :value="$penilaian?->langkah_perbaikan" rows="3" :maxlength="$maksKesimpulan" data-hitung :hint="$petunjukKesimpulan" />

                        @if ($penilaian?->catatan_pembimbing)
                            <x-alert type="info" title="Catatan pembimbing (format lama):" :message="$penilaian->catatan_pembimbing" />
                        @endif

                        <div class="form-actions">
                            <x-button variant="secondary" :href="route('superadmin.peserta-magang.show', $pesertaMagang)">Batal</x-button>
                            @if ($penilaian)
                                <x-button variant="secondary" :href="route('superadmin.peserta-magang.penilaian.cetak', $pesertaMagang)">⬇ Lembar Penilaian (PDF)</x-button>
                            @endif
                            <x-button>Simpan Penilaian</x-button>
                        </div>
                    </section>
                </div>

                <aside class="panel result" aria-live="polite">
                    <div class="result-label">Nilai Akhir</div>
                    <div class="result-nilai" id="nilai-akhir">{{ $penilaian ? number_format($penilaian->nilai_akhir, 2, ',', '.') : '–' }}</div>
                    <div class="result-predikat" id="predikat">{{ $penilaian?->predikat ?? '–' }}</div>

                    <div class="kelompok-rata">
                        @foreach ($kelompoks as $kelompok)
                            <div>
                                <span>Rata-rata {{ $kelompok->nama }} ({{ $kelompok->bobot }}%)</span>
                                <strong data-rata-kelompok="{{ $kelompok->id }}">–</strong>
                            </div>
                        @endforeach
                    </div>

                    <div class="scale" id="skala">
                        @foreach ($predikats->sortByDesc('batas_bawah') as $predikat)
                            <div data-predikat="{{ $predikat->nama }}" @class(['active' => $penilaian?->predikat === $predikat->nama])>
                                <span>{{ $predikat->nama }}</span>
                                <span>≥ {{ rtrim(rtrim(number_format($predikat->batas_bawah, 2, ',', ''), '0'), ',') }}</span>
                            </div>
                        @endforeach
                    </div>

                    @if ($penilaian)
                        <div class="meta">
                            Terakhir dinilai {{ $penilaian->dinilai_pada?->translatedFormat('d F Y, H:i') }}
                            @if ($penilaian->penilai)
                                oleh {{ $penilaian->penilai->name }}
                            @endif
                        </div>
                    @endif
                </aside>

            </div>
        </form>
    @endif

</div>

@endsection

@push('scripts')
<script>
(() => {
    const form = document.getElementById('form-penilaian');

    if (!form) {
        return;
    }

    const inputs = [...form.querySelectorAll('input[type="number"][data-kelompok]')];
    const output = document.getElementById('nilai-akhir');
    const badge = document.getElementById('predikat');
    const bobot = @json($kelompoks->pluck('bobot', 'id'));
    const predikat = @json($predikatJs);
    const angka = (nilai) => nilai.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const predikatUntuk = (nilai) => [...predikat].reverse().find((item) => nilai >= item.batas)?.nama ?? '–';

    const hitung = () => {
        const kelompok = {};
        let lengkap = true;

        inputs.forEach((input) => {
            const id = input.dataset.kelompok;
            kelompok[id] ??= { total: 0, jumlah: 0, kosong: false };

            if (input.value === '') {
                kelompok[id].kosong = true;
                lengkap = false;

                return;
            }

            kelompok[id].total += Math.min(100, Math.max(1, Number(input.value)));
            kelompok[id].jumlah++;
        });

        let totalBobot = 0;
        let totalNilai = 0;

        Object.entries(kelompok).forEach(([id, data]) => {
            const rata = data.jumlah ? data.total / data.jumlah : 0;
            const sel = document.querySelector(`[data-rata-kelompok="${id}"]`);

            if (sel) {
                sel.textContent = data.kosong ? '–' : angka(Math.round(rata * 100) / 100);
            }

            totalBobot += Number(bobot[id] ?? 0);
            totalNilai += rata * Number(bobot[id] ?? 0);
        });

        if (!lengkap || totalBobot === 0) {
            output.textContent = '–';
            badge.textContent = '–';
            document.querySelectorAll('#skala div').forEach((row) => row.classList.remove('active'));

            return;
        }

        const nilai = Math.round((totalNilai / totalBobot) * 100) / 100;
        const nama = predikatUntuk(nilai);

        output.textContent = angka(nilai);
        badge.textContent = nama;
        document.querySelectorAll('#skala div').forEach((row) => row.classList.toggle('active', row.dataset.predikat === nama));
    };

    // Slider dan kotak angka saling sinkron.
    form.querySelectorAll('input[type="range"]').forEach((range) => {
        const input = document.getElementById(range.dataset.rangeFor);

        range.addEventListener('input', () => {
            input.value = range.value;
            hitung();
        });

        input.addEventListener('input', () => {
            range.value = input.value || 1;
            hitung();
        });
    });

    hitung();
})();
</script>
@endpush
