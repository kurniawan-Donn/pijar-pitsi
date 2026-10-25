{{--
    Rekap kehadiran & uang saku satu periode.
    Variabel: $rekap (KalenderLogbook::rekap), $tarif (tarif akhir periode), $biodata.
--}}

@once
@push('styles')
<style>
    .lb-rekap {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .lb-rekap-item {
        padding: 14px 16px;

        border: 1px solid var(--garis);
        border-radius: 11px;

        background: var(--permukaan-redup);
    }

    .lb-rekap-item span {
        display: block;

        color: var(--teks-sekunder);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .lb-rekap-item strong {
        display: block;

        margin-top: 4px;

        color: var(--utama-gelap);
        font-size: 20px;
    }

    .lb-rekap-item small {
        color: var(--teks-sekunder);
        font-size: 11px;
    }

    .lb-rekap-total {
        grid-column: span 2;

        background: var(--peringatan-muda);
        border-color: #f9d9bd;
    }

    .lb-rekap-total strong {
        color: var(--peringatan);
    }

    @media (max-width: 900px) {
        .lb-rekap {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>
@endpush
@endonce

@php
    $rupiah = fn (int $nilai) => 'Rp '.number_format($nilai, 0, ',', '.');
    $daftarTarif = $rekap['tarif'] ?: [['hari_penuh' => $tarif->tarif_hari_penuh, 'setengah_hari' => $tarif->tarif_setengah_hari]];
@endphp

<div class="lb-rekap">
    <div class="lb-rekap-item">
        <span>Masuk penuh</span>
        <strong>{{ $rekap['hari_penuh'] }} hari</strong>
    </div>
    <div class="lb-rekap-item">
        <span>Setengah hari</span>
        <strong>{{ $rekap['setengah_hari'] }} hari</strong>
    </div>
    <div class="lb-rekap-item">
        <span>Izin / sakit</span>
        <strong>{{ $rekap['izin'] + $rekap['sakit'] }} hari</strong>
        <small>izin {{ $rekap['izin'] }} · sakit {{ $rekap['sakit'] }}</small>
    </div>
    <div class="lb-rekap-item">
        <span>Libur</span>
        <strong>{{ $rekap['libur'] }} hari</strong>
    </div>
    <div class="lb-rekap-item">
        <span>Belum diisi</span>
        <strong>{{ $rekap['belum_diisi'] }} hari</strong>
        @if ($rekap['tidak_lengkap'])
            <small>{{ $rekap['tidak_lengkap'] }} hari jam tidak lengkap</small>
        @endif
    </div>
    <div class="lb-rekap-item">
        <span>Menunggu verifikasi</span>
        <strong>{{ $rekap['menunggu'] }} hari</strong>
        @if ($rekap['ditolak'])
            <small>{{ $rekap['ditolak'] }} hari ditolak (tidak dihitung)</small>
        @endif
    </div>
    <div class="lb-rekap-item lb-rekap-total">
        <span>Total uang saku</span>
        <strong>{{ $rupiah($rekap['uang_saku']) }}</strong>
        <small>
            Tarif
            @foreach ($daftarTarif as $item)
                {{ $rupiah($item['hari_penuh']) }} (penuh) / {{ $rupiah($item['setengah_hari']) }} (setengah){{ $loop->last ? '' : ';' }}
            @endforeach
            @if ($biodata->no_rekening)
                · Rekening {{ $biodata->nama_bank }} {{ $biodata->no_rekening }} a.n. {{ $biodata->atas_nama_rekening }}
            @endif
        </small>
    </div>
</div>
