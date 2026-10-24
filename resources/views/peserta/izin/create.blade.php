{{-- resources/views/peserta/izin/create.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Ajukan Izin / Sakit')
@section('page_title', 'Ajukan Izin / Sakit')
@section('page_subtitle', 'Satu bukti untuk satu rentang tanggal')

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .panel {
        max-width: 760px;

        padding: 22px 24px;

        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel > p {
        margin-bottom: 16px;

        color: #7b8794;
        font-size: 13px;
        line-height: 1.6;
    }

    .pilihan {
        display: flex;
        gap: 10px;

        margin-bottom: 16px;
    }

    .pilihan label {
        display: flex;
        align-items: center;
        gap: 8px;

        padding: 10px 18px;

        border: 1px solid #d7e0eb;
        border-radius: 10px;

        font-size: 13px;
        font-weight: 700;

        cursor: pointer;
    }

    .pilihan label:has(input:checked) {
        border-color: #1779c9;
        background: #eef6fd;
    }

    .pilihan input {
        accent-color: #1779c9;
    }

    .fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0 14px;
    }

    .actions {
        display: flex;
        justify-content: space-between;
        gap: 10px;

        margin-top: 8px;
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 16px 30px;
        }

        .fields {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages :validation="false" />

    @if ($errors->any())
        <x-alert type="error" title="Pengajuan belum tersimpan:" :messages="$errors->all()" />
    @endif

    <section class="panel">
        <p>
            Pilih rentang tanggal sesuai surat (mis. surat dokter 3 hari). Sistem mengisi status izin/sakit ke setiap
            hari kerja dalam rentang itu; Sabtu, Minggu, dan hari libur tetap tercatat libur. Hari yang sudah diisi hadir
            tidak diubah. Tanggal lampau hanya bisa sampai {{ $batasMundur }} hari ke belakang.
        </p>

        <form method="POST" action="{{ route('peserta.izin.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="pilihan">
                @foreach (\App\Models\IzinPeserta::JENIS as $nilai => $label)
                    <label>
                        <input type="radio" name="jenis" value="{{ $nilai }}" @checked(old('jenis', 'izin') === $nilai)>
                        {{ $label }}
                    </label>
                @endforeach
            </div>

            <div class="fields">
                <x-form.input name="tanggal_mulai" type="date" label="Tanggal Mulai" :value="$tanggalAwal" required />
                <x-form.input name="tanggal_selesai" type="date" label="Tanggal Selesai" :value="$tanggalAwal" required />
            </div>

            <x-form.input name="keterangan" type="textarea" label="Keterangan" rows="3" maxlength="1000" placeholder="mis. Sakit demam, istirahat sesuai surat dokter" required />

            <x-form.input name="bukti" type="file" label="Bukti (wajib untuk sakit)" accept=".jpg,.jpeg,.png,.pdf" hint="Surat dokter / surat izin. JPG, PNG, atau PDF, maksimal 2 MB." />

            <div class="actions">
                <x-button variant="secondary" :href="route('peserta.logbook.index')">← Kembali</x-button>
                <x-button>Ajukan</x-button>
            </div>
        </form>
    </section>

</div>

@endsection
