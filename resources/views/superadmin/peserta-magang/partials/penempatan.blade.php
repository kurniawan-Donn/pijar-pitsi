{{--
    Divisi & mentor peserta per periode (rotasi).
    Variabel: $pesertaMagang (dengan penempatans.divisi), $periodeMagang, $pilihanDivisi.
--}}

@push('styles')
<style>
    .penempatan-card {
        margin-bottom: 20px;

        scroll-margin-top: 20px;
    }

    .penempatan-table {
        width: 100%;
        border-collapse: collapse;

        font-size: 13px;
    }

    .penempatan-table th {
        padding: 9px 10px;

        background: var(--permukaan-redup);
        color: var(--teks-sekunder);

        font-size: 11px;
        font-weight: 800;
        letter-spacing: .4px;
        text-align: left;
        text-transform: uppercase;
    }

    .penempatan-table td {
        padding: 9px 10px;

        border-top: 1px solid var(--garis);

        vertical-align: middle;
    }

    .penempatan-table select,
    .penempatan-table input {
        width: 100%;
        height: 38px;

        padding: 0 10px;

        border: 1px solid var(--garis-kuat);
        border-radius: 7px;

        background: #ffffff;

        font-family: inherit;
        font-size: 13px;
    }

    .penempatan-periode strong {
        display: block;

        color: var(--utama-gelap);
    }

    .penempatan-periode small,
    .penempatan-warisan {
        color: var(--teks-redup);
        font-size: 11px;
    }

    .penempatan-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;

        margin-top: 14px;
    }

    .penempatan-note {
        color: var(--teks-sekunder);
        font-size: 12px;
        line-height: 1.6;
    }

    .penempatan-scroll {
        overflow-x: auto;
    }

    .penempatan-table {
        min-width: 640px;
    }
</style>
@endpush

<section class="card penempatan-card" id="penempatan">

    <div class="card-header">

        <div class="card-title">
            Divisi &amp; Mentor per Periode
        </div>

        <div class="card-description">
            Rotasi divisi dicatat per periode. Periode yang dikosongkan mengikuti periode sebelumnya.
        </div>

    </div>

    <div class="card-body">

        <x-alert type="success" :message="session('penempatan_status')" />
        <x-alert type="error" title="Penempatan belum disimpan:" :messages="$errors->penempatan->all()" />

        @if (empty($periodeMagang))

            <div class="sheets-missing">
                Isi tanggal mulai dan selesai magang pada data peserta untuk menentukan periode penempatan.
            </div>

        @elseif ($pilihanDivisi->isEmpty())

            <div class="sheets-missing">
                Belum ada divisi. Tambahkan dulu di menu
                <a href="{{ route('superadmin.divisi.index') }}">Divisi</a>.
            </div>

        @else

            <form method="POST" action="{{ route('superadmin.peserta-magang.penempatan.update', $pesertaMagang) }}">
                @csrf
                @method('PUT')

                <div class="penempatan-scroll">
                    <table class="penempatan-table">
                        <thead>
                            <tr>
                                <th style="width: 190px;">Periode</th>
                                <th>Divisi</th>
                                <th>Mentor</th>
                                <th>Jabatan Mentor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($periodeMagang as $periode)
                                @php
                                    $kunci = $periode['kunci'];
                                    $tersimpan = $pesertaMagang->penempatans->firstWhere('periode', $kunci);
                                    $warisan = $tersimpan ? null : $pesertaMagang->penempatanUntuk($kunci);
                                @endphp
                                <tr>
                                    <td class="penempatan-periode">
                                        <strong>{{ $periode['label'] }}</strong>
                                        <small>{{ $periode['mulai']->translatedFormat('d M') }} – {{ $periode['selesai']->translatedFormat('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <select name="penempatan[{{ $kunci }}][divisi_id]" aria-label="Divisi periode {{ $periode['label'] }}">
                                            <option value="">
                                                {{ $warisan ? '↳ ikut sebelumnya ('.$warisan->divisi->nama.')' : '— belum ditentukan —' }}
                                            </option>
                                            @foreach ($pilihanDivisi as $divisi)
                                                <option value="{{ $divisi->id }}" @selected(old("penempatan.{$kunci}.divisi_id", $tersimpan?->divisi_id) == $divisi->id)>
                                                    {{ $divisi->nama }}{{ $divisi->aktif ? '' : ' (nonaktif)' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input
                                            type="text"
                                            name="penempatan[{{ $kunci }}][mentor_nama]"
                                            value="{{ old("penempatan.{$kunci}.mentor_nama", $tersimpan?->mentor_nama) }}"
                                            placeholder="{{ $warisan?->mentor_nama }}"
                                            maxlength="255"
                                            aria-label="Mentor periode {{ $periode['label'] }}"
                                        >
                                    </td>
                                    <td>
                                        <input
                                            type="text"
                                            name="penempatan[{{ $kunci }}][mentor_jabatan]"
                                            value="{{ old("penempatan.{$kunci}.mentor_jabatan", $tersimpan?->mentor_jabatan) }}"
                                            placeholder="{{ $warisan?->mentor_jabatan }}"
                                            maxlength="255"
                                            aria-label="Jabatan mentor periode {{ $periode['label'] }}"
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="penempatan-actions">
                    <span class="penempatan-note">
                        Mentor hanya disimpan bila divisi periode itu dipilih.
                    </span>
                    <button type="submit" class="btn btn-orange">Simpan Penempatan</button>
                </div>
            </form>

        @endif

    </div>

</section>
