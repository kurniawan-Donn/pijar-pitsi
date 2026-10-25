{{-- resources/views/superadmin/aspek-penilaian/index.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Aspek Penilaian')
@section('page_title', 'Aspek Penilaian')
@section('page_subtitle', 'Kelompok, aspek, dan predikat penilaian akhir peserta magang')

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .panel {
        max-width: 980px;

        margin-bottom: 18px;
        padding: 22px;

        background: #ffffff;

        border: 1px solid var(--garis);
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);

        scroll-margin-top: 20px;
    }

    .panel h3 {
        margin-bottom: 6px;

        color: var(--utama-gelap);
        font-size: 16px;
    }

    .panel > p {
        margin-bottom: 18px;

        color: var(--teks-sekunder);
        font-size: 13px;
        line-height: 1.6;
    }

    .kelompok {
        margin-bottom: 16px;
        padding: 16px;

        border: 1px solid var(--garis);
        border-radius: 11px;

        background: var(--permukaan-redup);
    }

    .kelompok-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;

        margin-bottom: 12px;
    }

    .kelompok-head input[type="text"] {
        flex: 1 1 220px;

        font-weight: 800;
    }

    .kelompok-head label {
        color: var(--teks-sekunder);
        font-size: 12px;
        font-weight: 700;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        padding: 8px 10px;

        background: var(--garis);
        color: var(--teks-sekunder);

        font-size: 11px;
        font-weight: 800;
        letter-spacing: .4px;
        text-align: left;
        text-transform: uppercase;
    }

    td {
        padding: 8px 10px;

        border-top: 1px solid var(--garis);

        vertical-align: middle;
    }

    tr.nonaktif td {
        opacity: .5;
    }

    input[type="text"],
    input[type="number"],
    textarea {
        width: 100%;
        height: 38px;

        padding: 0 10px;

        border: 1px solid var(--garis-kuat);
        border-radius: 7px;

        background: #ffffff;

        font-family: inherit;
        font-size: 13px;
    }

    textarea {
        height: 56px;
        padding: 8px 10px;

        resize: vertical;
    }

    input[type="number"] {
        width: 90px;

        text-align: center;
        font-weight: 800;
    }

    input[type="checkbox"] {
        width: 18px;
        height: 18px;

        accent-color: var(--utama);
    }

    .row-new td {
        background: var(--permukaan-redup);
    }

    .total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;

        margin-top: 6px;
        padding: 14px 16px;

        border-radius: 10px;

        background: var(--permukaan-redup);

        font-size: 14px;
    }

    .total strong {
        font-size: 20px;
    }

    .total.ok strong {
        color: var(--sukses);
    }

    .total.salah strong {
        color: var(--bahaya);
    }

    .kelompok-baru {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;

        margin-bottom: 14px;

        font-size: 13px;
    }

    .kelompok-baru input[type="text"] {
        flex: 1 1 240px;
    }

    .actions {
        display: flex;
        justify-content: flex-end;

        margin-top: 14px;
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 16px 30px;
        }

        .tabel-scroll {
            overflow-x: auto;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages :validation="false" />

    @error('total_bobot')
        <x-alert type="error" :message="$message" />
    @enderror

    @if ($errors->any() && ! $errors->has('total_bobot'))
        <x-alert type="error" title="Periksa kembali data penilaian:" :messages="$errors->all()" />
    @endif

    {{-- KELOMPOK & ASPEK --}}
    <section class="panel">
        <h3>Kelompok &amp; Aspek</h3>
        <p>
            Nilai kelompok = rata-rata aspek aktif di dalamnya (skala 1–100). Nilai akhir = jumlah (rata-rata kelompok × bobot kelompok),
            mis. (Hard Skill × 60%) + (Soft Skill × 40%). Total bobot kelompok harus <strong>100%</strong>.
            Aspek yang tidak dipakai cukup dinonaktifkan; nilai lama tidak berubah.
        </p>

        <form method="POST" action="{{ route('superadmin.aspek-penilaian.update') }}" id="form-aspek">
            @csrf
            @method('PUT')

            @foreach ($kelompoks as $kelompok)
                <div class="kelompok">
                    <div class="kelompok-head">
                        <input type="text" name="kelompok[{{ $kelompok->id }}][nama]" value="{{ old("kelompok.{$kelompok->id}.nama", $kelompok->nama) }}" maxlength="100" required aria-label="Nama kelompok">
                        <label>
                            Bobot
                            <input type="number" name="kelompok[{{ $kelompok->id }}][bobot]" value="{{ old("kelompok.{$kelompok->id}.bobot", $kelompok->bobot) }}" min="0" max="100" required data-bobot aria-label="Bobot {{ $kelompok->nama }}"> %
                        </label>
                    </div>

                    <div class="tabel-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th>Aspek</th>
                                    <th style="width: 70px;">Aktif</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kelompok->aspeks as $aspek)
                                    @php
                                        $aktif = (bool) old("aspek.{$aspek->id}.aktif", $aspek->aktif);
                                    @endphp
                                    <tr @class(['nonaktif' => ! $aktif])>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <input type="text" name="aspek[{{ $aspek->id }}][nama]" value="{{ old("aspek.{$aspek->id}.nama", $aspek->nama) }}" maxlength="255" required aria-label="Nama aspek">
                                        </td>
                                        <td>
                                            <input type="hidden" name="aspek[{{ $aspek->id }}][aktif]" value="0">
                                            <input type="checkbox" name="aspek[{{ $aspek->id }}][aktif]" value="1" @checked($aktif) data-aktif aria-label="Aktifkan {{ $aspek->nama }}">
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="row-new">
                                    <td>+</td>
                                    <td colspan="2">
                                        <input type="text" name="baru[{{ $kelompok->id }}]" value="{{ old("baru.{$kelompok->id}") }}" maxlength="255" placeholder="Tambah aspek {{ $kelompok->nama }} (opsional)" aria-label="Aspek baru {{ $kelompok->nama }}">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            <div class="kelompok-baru">
                <span>Kelompok baru (opsional):</span>
                <input type="text" name="kelompok_baru[nama]" value="{{ old('kelompok_baru.nama') }}" maxlength="100" placeholder="Nama kelompok" aria-label="Nama kelompok baru">
                <input type="number" name="kelompok_baru[bobot]" value="{{ old('kelompok_baru.bobot') }}" min="1" max="100" placeholder="%" data-bobot aria-label="Bobot kelompok baru">
            </div>

            <div class="total" id="total-box">
                <span>Total bobot kelompok: <strong id="total-bobot">0</strong><strong>%</strong></span>
                <x-button>Simpan Kelompok &amp; Aspek</x-button>
            </div>
        </form>
    </section>

    {{-- PREDIKAT --}}
    <section class="panel" id="predikat">
        <h3>Tabel Predikat</h3>
        <p>
            Predikat berlaku untuk nilai akhir sama dengan atau di atas batas bawah. Harus ada predikat dengan batas bawah 0.
            Keterangan dicantumkan di lembar penilaian.
        </p>

        <x-alert type="error" :messages="$errors->predikat->all()" />

        <form method="POST" action="{{ route('superadmin.aspek-penilaian.predikat') }}">
            @csrf
            @method('PUT')

            <div class="tabel-scroll">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 110px;">Batas Bawah</th>
                            <th style="width: 190px;">Predikat</th>
                            <th>Keterangan</th>
                            <th style="width: 60px;">Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($predikats as $predikat)
                            <tr>
                                <td><input type="number" name="predikat[{{ $predikat->id }}][batas_bawah]" value="{{ old("predikat.{$predikat->id}.batas_bawah", $predikat->batas_bawah) }}" min="0" max="100" step="0.01" required aria-label="Batas bawah {{ $predikat->nama }}"></td>
                                <td><input type="text" name="predikat[{{ $predikat->id }}][nama]" value="{{ old("predikat.{$predikat->id}.nama", $predikat->nama) }}" maxlength="50" required aria-label="Nama predikat"></td>
                                <td><textarea name="predikat[{{ $predikat->id }}][keterangan]" maxlength="500" aria-label="Keterangan {{ $predikat->nama }}">{{ old("predikat.{$predikat->id}.keterangan", $predikat->keterangan) }}</textarea></td>
                                <td>
                                    <input type="hidden" name="predikat[{{ $predikat->id }}][hapus]" value="0">
                                    <input type="checkbox" name="predikat[{{ $predikat->id }}][hapus]" value="1" aria-label="Hapus {{ $predikat->nama }}">
                                </td>
                            </tr>
                        @endforeach
                        <tr class="row-new">
                            <td><input type="number" name="baru[batas_bawah]" value="{{ old('baru.batas_bawah') }}" min="0" max="100" step="0.01" placeholder="+" aria-label="Batas bawah predikat baru"></td>
                            <td><input type="text" name="baru[nama]" value="{{ old('baru.nama') }}" maxlength="50" placeholder="Predikat baru (opsional)" aria-label="Nama predikat baru"></td>
                            <td colspan="2"><textarea name="baru[keterangan]" maxlength="500" aria-label="Keterangan predikat baru">{{ old('baru.keterangan') }}</textarea></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="actions">
                <x-button>Simpan Predikat</x-button>
            </div>
        </form>
    </section>

</div>

@endsection

@push('scripts')
<script>
(() => {
    const form = document.getElementById('form-aspek');
    const totalEl = document.getElementById('total-bobot');
    const box = document.getElementById('total-box');

    const hitung = () => {
        let total = 0;

        form.querySelectorAll('[data-bobot]').forEach((input) => {
            total += Number(input.value || 0);
        });

        form.querySelectorAll('tbody tr').forEach((row) => {
            const aktif = row.querySelector('[data-aktif]');

            if (aktif) {
                row.classList.toggle('nonaktif', !aktif.checked);
            }
        });

        totalEl.textContent = total;
        box.classList.toggle('ok', total === 100);
        box.classList.toggle('salah', total !== 100);
    };

    form.addEventListener('input', hitung);
    form.addEventListener('change', hitung);
    hitung();
})();
</script>
@endpush
