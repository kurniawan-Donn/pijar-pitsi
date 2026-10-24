{{-- resources/views/superadmin/aspek-penilaian/index.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Aspek Penilaian')
@section('page_title', 'Aspek Penilaian')
@section('page_subtitle', 'Atur aspek dan bobot penilaian akhir peserta magang')

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .panel {
        max-width: 860px;

        padding: 22px;

        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel > p {
        margin-bottom: 18px;

        color: #7b8794;
        font-size: 13px;
        line-height: 1.6;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        padding: 10px 12px;

        background: #f8fafc;
        color: #6b7c8d;

        font-size: 11px;
        font-weight: 800;
        letter-spacing: .4px;
        text-align: left;
        text-transform: uppercase;
    }

    td {
        padding: 10px 12px;

        border-top: 1px solid #edf1f5;

        vertical-align: middle;
    }

    tr.nonaktif td {
        opacity: .5;
    }

    td input[type="text"],
    td input[type="number"] {
        width: 100%;
        height: 40px;

        padding: 0 11px;

        border: 1px solid #d7e0eb;
        border-radius: 7px;

        font-family: inherit;
        font-size: 13px;
    }

    td input[type="number"] {
        max-width: 90px;
        text-align: center;
        font-weight: 800;
    }

    td input[type="checkbox"] {
        width: 18px;
        height: 18px;

        accent-color: #1779c9;
    }

    .row-new td {
        background: #f8fbfe;
    }

    .total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;

        margin-top: 18px;
        padding: 14px 16px;

        border-radius: 10px;

        background: #f7f9fc;

        font-size: 14px;
    }

    .total strong {
        font-size: 20px;
    }

    .total.ok strong {
        color: #067647;
    }

    .total.salah strong {
        color: #b42318;
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages :validation="false" />

    @error('total_bobot')
        <x-alert type="error" :message="$message" />
    @enderror

    @if ($errors->hasAny(['aspek.*.nama', 'aspek.*.bobot', 'baru.nama', 'baru.bobot']))
        <x-alert type="error" title="Periksa kembali data aspek:" :messages="$errors->all()" />
    @endif

    <section class="panel">
        <p>
            Total bobot aspek yang aktif harus <strong>100%</strong>. Aspek yang tidak dipakai cukup
            dinonaktifkan; nilai lama tetap memakai nama dan bobot saat peserta dinilai.
        </p>

        <form method="POST" action="{{ route('superadmin.aspek-penilaian.update') }}" id="form-aspek">
            @csrf
            @method('PUT')

            <table>
                <thead>
                    <tr>
                        <th>Aspek</th>
                        <th style="width: 120px;">Bobot (%)</th>
                        <th style="width: 70px;">Aktif</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($aspeks as $aspek)
                        @php($aktif = (bool) old("aspek.{$aspek->id}.aktif", $aspek->aktif))
                        <tr @class(['nonaktif' => ! $aktif])>
                            <td>
                                <input type="text" name="aspek[{{ $aspek->id }}][nama]" value="{{ old("aspek.{$aspek->id}.nama", $aspek->nama) }}" maxlength="100" required aria-label="Nama aspek">
                            </td>
                            <td>
                                <input type="number" name="aspek[{{ $aspek->id }}][bobot]" value="{{ old("aspek.{$aspek->id}.bobot", $aspek->bobot) }}" min="1" max="100" required data-bobot aria-label="Bobot {{ $aspek->nama }}">
                            </td>
                            <td>
                                <input type="hidden" name="aspek[{{ $aspek->id }}][aktif]" value="0">
                                <input type="checkbox" name="aspek[{{ $aspek->id }}][aktif]" value="1" @checked($aktif) data-aktif aria-label="Aktifkan {{ $aspek->nama }}">
                            </td>
                        </tr>
                    @endforeach

                    <tr class="row-new">
                        <td>
                            <input type="text" name="baru[nama]" value="{{ old('baru.nama') }}" maxlength="100" placeholder="+ Tambah aspek baru (opsional)" aria-label="Nama aspek baru">
                        </td>
                        <td>
                            <input type="number" name="baru[bobot]" value="{{ old('baru.bobot') }}" min="1" max="100" data-bobot-baru aria-label="Bobot aspek baru">
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <div class="total" id="total-box">
                <span>Total bobot aktif: <strong id="total-bobot">0</strong><strong>%</strong></span>
                <x-button>Simpan Aspek</x-button>
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

        form.querySelectorAll('tbody tr').forEach((row) => {
            const bobot = row.querySelector('[data-bobot]');
            const aktif = row.querySelector('[data-aktif]');

            if (bobot && aktif) {
                row.classList.toggle('nonaktif', !aktif.checked);
                total += aktif.checked ? Number(bobot.value || 0) : 0;
            }
        });

        total += Number(form.querySelector('[data-bobot-baru]').value || 0);

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
