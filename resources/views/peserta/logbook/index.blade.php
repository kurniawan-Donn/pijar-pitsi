{{-- resources/views/peserta/logbook/index.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Logbook Harian')
@section('page_title', 'Logbook Harian')
@section('page_subtitle', 'Daftar hadir & daily activity per periode (28 s.d. 27)')

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

    .toolbar label {
        display: block;

        margin-bottom: 6px;

        color: var(--teks-sekunder);
        font-size: 12px;
        font-weight: 700;
    }

    .toolbar select {
        min-width: 260px;
        height: 42px;

        padding: 0 10px;

        border: 1px solid var(--garis-kuat);
        border-radius: 8px;

        background: #ffffff;

        font-family: inherit;
        font-size: 13px;
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

    .panel-note {
        margin: -6px 0 14px;

        color: var(--teks-sekunder);
        font-size: 12px;
        line-height: 1.6;
    }

    .izin-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .izin-item {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;

        padding: 10px 14px;

        border: 1px solid var(--garis);
        border-radius: 10px;

        font-size: 13px;
    }

    .izin-item form {
        margin: 0;
    }

    .izin-item small {
        display: block;

        color: var(--teks-sekunder);
    }

    .link-hapus {
        border: none;
        background: none;

        color: var(--bahaya);
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;

        cursor: pointer;
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
            Periode magang Anda belum diatur. Hubungi admin untuk mengisi tanggal mulai dan selesai magang.
        </section>
    @else

        <div class="toolbar">
            <form method="GET" action="{{ route('peserta.logbook.index') }}">
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
                <x-button variant="secondary" :href="route('peserta.izin.create')">+ Ajukan Izin / Sakit</x-button>
                <x-button variant="secondary" :href="route('peserta.logbook.cetak', ['periode' => $periode['kunci']])"><x-icon name="download" :size="16" /> Unduh PDF</x-button>
                @if (today()->between($periode['mulai'], $periode['selesai']))
                    <x-button :href="route('peserta.logbook.edit', now()->toDateString())">Isi Logbook Hari Ini</x-button>
                @endif
            </div>
        </div>

        @if ($izins->isNotEmpty())
            <section class="panel">
                <h3>Izin / Sakit Periode Ini</h3>
                <div class="izin-list">
                    @foreach ($izins as $izin)
                        <div class="izin-item">
                            <div>
                                <strong>{{ $izin->labelJenis() }} · {{ $izin->rentang() }}</strong>
                                <small>{{ $izin->keterangan }}</small>
                                @if ($izin->catatan_verifikasi)
                                    <small>Catatan admin: {{ $izin->catatan_verifikasi }}</small>
                                @endif
                            </div>
                            <div>
                                <span class="lb-badge lb-{{ $izin->status }}">{{ $izin->labelStatus() }}</span>
                                @if ($izin->status === 'menunggu')
                                    <form method="POST" action="{{ route('peserta.izin.destroy', $izin) }}" onsubmit="return confirm('Batalkan pengajuan izin/sakit ini?')" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="link-hapus">Batalkan</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="panel">
            <h3>{{ $periode['label'] }} · {{ $periode['mulai']->translatedFormat('d F') }} s.d. {{ $periode['selesai']->translatedFormat('d F Y') }}</h3>
            <p class="panel-note">
                Hari ini boleh diisi, juga tanggal mundur sampai {{ \App\Models\PengaturanMagang::ambil()->batas_isi_mundur_hari }} hari ke belakang.
                Tanggal yang belum tiba terkunci. Hari penuh = jam pagi masuk & siang keluar; setengah hari = satu sesi saja.
            </p>

            @include('logbook.partials.tabel', ['mode' => 'peserta'])
        </section>

        <section class="panel">
            <h3>Rekap Periode</h3>
            @include('logbook.partials.rekap')
        </section>

    @endif

</div>

@endsection
