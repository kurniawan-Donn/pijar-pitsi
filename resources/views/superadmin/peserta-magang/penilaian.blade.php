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
        display: inline-grid;
        place-items: center;

        width: 54px;
        height: 54px;

        margin-top: 14px;

        border-radius: 14px;

        background: #1779c9;
        color: #ffffff;

        font-size: 26px;
        font-weight: 800;

        transition: background .25s ease;
    }

    .result-predikat[data-predikat="C"] { background: #d99a0b; }
    .result-predikat[data-predikat="D"] { background: #e07b20; }
    .result-predikat[data-predikat="E"] { background: #c62828; }

    .scale {
        display: grid;
        gap: 6px;

        margin-top: 20px;

        font-size: 12px;
        text-align: left;
    }

    .scale div {
        display: flex;
        justify-content: space-between;

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
            padding: 22px 20px 30px;
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

@section('content')

<div class="content">

    <a href="{{ route('superadmin.peserta-magang.show', $pesertaMagang) }}" class="back-link">← Kembali ke detail peserta</a>

    <x-flash-messages :validation="false" />

    @if ($aspeks->isEmpty())
        <x-alert type="warning" message="Belum ada aspek penilaian aktif. Super Admin perlu mengatur aspek penilaian terlebih dahulu." />
    @else
        <form method="POST" action="{{ route('superadmin.peserta-magang.penilaian.update', $pesertaMagang) }}" id="form-penilaian">
            @csrf
            @method('PUT')

            <div class="grid">

                <div>
                    <section class="panel">
                        <h3>Nilai per Aspek</h3>
                        <p>Isi nilai 0–100 untuk setiap aspek. Nilai akhir dihitung otomatis berdasarkan bobot.</p>

                        @foreach ($aspeks as $aspek)
                            @php($nilai = old('nilai.' . $aspek->id, $nilaiTersimpan[$aspek->id] ?? ''))
                            <div class="aspek-row">
                                <div>
                                    <div class="aspek-nama">{{ $aspek->nama }}</div>
                                    <div class="aspek-bobot">Bobot {{ $aspek->bobot }}%</div>
                                </div>

                                <input
                                    type="range"
                                    min="0"
                                    max="100"
                                    value="{{ $nilai === '' ? 0 : $nilai }}"
                                    data-range-for="nilai-{{ $aspek->id }}"
                                    aria-label="Geser nilai {{ $aspek->nama }}"
                                    tabindex="-1"
                                >

                                <input
                                    type="number"
                                    id="nilai-{{ $aspek->id }}"
                                    name="nilai[{{ $aspek->id }}]"
                                    value="{{ $nilai }}"
                                    min="0"
                                    max="100"
                                    step="1"
                                    required
                                    data-bobot="{{ $aspek->bobot }}"
                                    aria-label="Nilai {{ $aspek->nama }}"
                                    @class(['is-invalid' => $errors->has('nilai.' . $aspek->id)])
                                >

                                @error('nilai.' . $aspek->id)
                                    <div class="aspek-error">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    </section>

                    <section class="panel">
                        <x-form.input
                            name="catatan_pembimbing"
                            type="textarea"
                            label="Catatan Pembimbing"
                            :value="$penilaian?->catatan_pembimbing"
                            rows="5"
                            maxlength="2000"
                            placeholder="Kelebihan, hal yang perlu ditingkatkan, dan rekomendasi untuk peserta."
                        />

                        <div class="form-actions">
                            <x-button variant="secondary" :href="route('superadmin.peserta-magang.show', $pesertaMagang)">Batal</x-button>
                            <x-button>Simpan Penilaian</x-button>
                        </div>
                    </section>
                </div>

                <aside class="panel result" aria-live="polite">
                    <div class="result-label">Nilai Akhir</div>
                    <div class="result-nilai" id="nilai-akhir">{{ $penilaian ? number_format($penilaian->nilai_akhir, 2, ',', '.') : '–' }}</div>
                    <div class="result-predikat" id="predikat" data-predikat="{{ $penilaian?->predikat ?? '' }}">{{ $penilaian?->predikat ?? '–' }}</div>

                    <div class="scale" id="skala">
                        @foreach ($predikat as $huruf => $batas)
                            <div data-huruf="{{ $huruf }}" @class(['active' => $penilaian?->predikat === $huruf])>
                                <span>{{ $huruf }}</span>
                                <span>{{ $batas > 0 ? '≥ ' . $batas : '< ' . $predikat['D'] }}</span>
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

    const inputs = [...form.querySelectorAll('input[type="number"][data-bobot]')];
    const output = document.getElementById('nilai-akhir');
    const badge = document.getElementById('predikat');
    const batas = @json($predikat);

    const predikatUntuk = (nilai) => Object.keys(batas).find((huruf) => nilai >= batas[huruf]) ?? 'E';

    const hitung = () => {
        let totalBobot = 0;
        let totalNilai = 0;
        let lengkap = true;

        inputs.forEach((input) => {
            const bobot = Number(input.dataset.bobot);
            totalBobot += bobot;

            if (input.value === '') {
                lengkap = false;
                return;
            }

            totalNilai += Math.min(100, Math.max(0, Number(input.value))) * bobot;
        });

        if (!lengkap || totalBobot === 0) {
            output.textContent = '–';
            badge.textContent = '–';
            badge.dataset.predikat = '';
            document.querySelectorAll('#skala div').forEach((row) => row.classList.remove('active'));
            return;
        }

        const nilai = Math.round((totalNilai / totalBobot) * 100) / 100;
        const huruf = predikatUntuk(nilai);

        output.textContent = nilai.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        badge.textContent = huruf;
        badge.dataset.predikat = huruf;
        document.querySelectorAll('#skala div').forEach((row) => row.classList.toggle('active', row.dataset.huruf === huruf));
    };

    // Slider dan kotak angka saling sinkron.
    form.querySelectorAll('input[type="range"]').forEach((range) => {
        const input = document.getElementById(range.dataset.rangeFor);

        range.addEventListener('input', () => {
            input.value = range.value;
            hitung();
        });

        input.addEventListener('input', () => {
            range.value = input.value || 0;
            hitung();
        });
    });

    hitung();
})();
</script>
@endpush
