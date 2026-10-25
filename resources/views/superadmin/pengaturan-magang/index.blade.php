{{-- resources/views/superadmin/pengaturan-magang/index.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Pengaturan Magang')
@section('page_title', 'Pengaturan Magang')
@section('page_subtitle', 'Aturan logbook, periode, tarif uang saku, hari libur, dan penanda tangan')

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
        align-items: start;
    }

    .panel {
        padding: 22px 24px;

        background: #ffffff;

        border: 1px solid var(--garis);
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);

        scroll-margin-top: 20px;
    }

    .panel-wide {
        grid-column: 1 / -1;
    }

    .panel h3 {
        color: var(--utama-gelap);
        font-size: 16px;
    }

    .panel > p {
        margin: 4px 0 18px;

        color: var(--teks-sekunder);
        font-size: 12px;
        line-height: 1.6;
    }

    .panel h4 {
        margin: 6px 0 12px;

        color: var(--teks-sekunder);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .4px;
        text-transform: uppercase;
    }

    .fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0 14px;
    }

    .actions {
        display: flex;
        justify-content: flex-end;
    }

    .inline-form {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        gap: 0 12px;

        padding: 14px 16px 0;
        margin-bottom: 16px;

        border-radius: 10px;

        background: var(--permukaan-redup);
    }

    .inline-form .x-form-group {
        flex: 1 1 150px;
    }

    .inline-form .x-btn {
        margin-top: 24px;
    }

    table {
        width: 100%;
        border-collapse: collapse;

        font-size: 13px;
    }

    th {
        padding: 9px 10px;

        background: var(--permukaan-redup);
        color: var(--teks-sekunder);

        font-size: 11px;
        font-weight: 800;
        letter-spacing: .4px;
        text-align: left;
        text-transform: uppercase;
    }

    td {
        padding: 9px 10px;

        border-top: 1px solid var(--garis);

        color: var(--teks);
        vertical-align: middle;
    }

    td form {
        margin: 0;
    }

    .badge-berlaku {
        display: inline-block;

        margin-left: 6px;
        padding: 2px 8px;

        border-radius: 999px;

        background: var(--sukses-muda);
        color: var(--sukses);

        font-size: 11px;
        font-weight: 800;
    }

    .muted {
        color: var(--teks-redup);
        font-size: 12px;
    }

    .fieldset-polos {
        min-width: 0;
        margin: 0;
        padding: 0;

        border: none;
    }

    .hanya-baca {
        padding: 10px 14px;

        border-radius: 9px;

        background: var(--latar);
        color: var(--teks-sekunder) !important;
        font-size: 12.5px;
    }

    .link-hapus {
        padding: 0;

        border: none;
        background: none;

        color: var(--bahaya);
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;

        cursor: pointer;
    }

    .scan-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 0 14px;
    }

    @media (max-width: 1000px) {
        .scan-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    .ttd-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;

        margin-bottom: 14px;
    }

    .ttd-slot {
        padding: 12px 14px 4px;

        border: 1px solid var(--garis);
        border-radius: 10px;

        background: var(--permukaan-redup);
    }

    .ttd-pratinjau {
        display: grid;
        place-items: center;

        height: 90px;
        margin-bottom: 10px;

        border: 1px dashed var(--garis-kuat);
        border-radius: 8px;

        background: #ffffff;
        color: var(--teks-redup);
        font-size: 12px;
    }

    .ttd-pratinjau img {
        max-width: 100%;
        max-height: 80px;

        object-fit: contain;
    }

    .check-kecil {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        margin-bottom: 10px;

        color: var(--teks-sekunder);
        font-size: 12px;
    }

    .tahun-nav {
        display: flex;
        align-items: center;
        gap: 10px;

        margin-bottom: 12px;

        font-size: 13px;
        font-weight: 800;
    }

    .tahun-nav a {
        color: var(--utama);
        text-decoration: none;
    }

    .empty {
        padding: 18px;

        color: var(--teks-redup);
        font-size: 13px;
        text-align: center;
    }

    @media (max-width: 1000px) {
        .grid,
        .fields,
        .ttd-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 16px 30px;
        }

        .panel {
            padding: 18px 16px;
        }

        .table-scroll {
            overflow-x: auto;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages />

    <div class="grid">

        {{-- ATURAN UMUM & PENANDA TANGAN --}}
        <section class="panel panel-wide" id="umum">
            <h3>Aturan Umum</h3>
            <p>
                Periode bulanan berjalan dari tanggal awal periode sampai sehari sebelumnya di bulan berikutnya
                (contoh tanggal 28: 28 Juli – 27 Agustus = Periode Agustus). Isi 1 untuk memakai bulan kalender.
                Mengubah tanggal awal periode memengaruhi pengelompokan penempatan divisi yang sudah ada.
            </p>

            @cannot('kelola-pengaturan-magang')
                <p class="hanya-baca"><x-icon name="lock" :size="15" /> Hanya Super Admin yang dapat mengubah aturan umum dan tarif. Admin dapat mengelola scan kartu absensi dan hari libur.</p>
            @endcannot

            <form method="POST" action="{{ route('superadmin.pengaturan-magang.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <fieldset class="fieldset-polos" @disabled(auth()->user()->cannot('kelola-pengaturan-magang'))>

                <div class="fields">
                    <x-form.input name="batas_isi_mundur_hari" type="number" label="Batas Isi Logbook Mundur (hari)" :value="$pengaturan->batas_isi_mundur_hari" min="0" max="60" required hint="0 = hanya hari ini. Tanggal yang lebih lama dikunci kecuali dibuka admin." />
                    <x-form.input name="tanggal_awal_periode" type="number" label="Tanggal Awal Periode" :value="$pengaturan->tanggal_awal_periode" min="1" max="28" required hint="Bawaan 28 (28 s.d. 27 bulan berikutnya)." />
                </div>

                <h4>Surat & Tanda Tangan</h4>

                <div class="fields">
                    <x-form.input name="kota_surat" label="Kota Surat" :value="$pengaturan->kota_surat" maxlength="100" placeholder="mis. Yogyakarta" />
                    <x-form.input name="format_nomor_surat" label="Format Nomor Surat Keterangan" :value="$pengaturan->format_nomor_surat" maxlength="100" required hint="Penanda: {urut}, {bulan_romawi}, {bulan}, {tahun}." />
                    <x-form.input name="mengetahui_nama" label="Mengetahui — Nama" :value="$pengaturan->mengetahui_nama" maxlength="255" />
                    <x-form.input name="mengetahui_jabatan" label="Mengetahui — Jabatan" :value="$pengaturan->mengetahui_jabatan" maxlength="255" />
                    <x-form.input name="menyetujui_nama" label="Menyetujui — Nama" :value="$pengaturan->menyetujui_nama" maxlength="255" />
                    <x-form.input name="menyetujui_jabatan" label="Menyetujui — Jabatan" :value="$pengaturan->menyetujui_jabatan" maxlength="255" />
                </div>

                @can('kelola-pengaturan-magang')
                    <h4>Gambar Tanda Tangan &amp; Stempel</h4>
                    <p class="muted" style="margin: -6px 0 12px;">
                        Tercetak otomatis di lembar penilaian (Mengetahui, Menyetujui + stempel) dan surat keterangan
                        (Menyetujui + stempel). Pakai PNG berlatar transparan, maksimal 1 MB. Surat yang sudah terbit tetap
                        memakai gambar saat diterbitkan.
                    </p>

                    <div class="ttd-grid">
                        @foreach (\App\Services\TandaTanganDokumen::LABEL as $jenis => $label)
                            @php
                                $pathGambar = $pengaturan->{\App\Services\TandaTanganDokumen::KOLOM[$jenis]};
                            @endphp

                            <div class="ttd-slot">
                                <div class="ttd-pratinjau">
                                    @if ($pathGambar)
                                        <img src="{{ route('superadmin.pengaturan-magang.gambar', ['jenis' => $jenis, 'v' => substr(md5($pathGambar), 0, 8)]) }}" alt="{{ $label }}" id="pratinjau-{{ $jenis }}">
                                    @else
                                        <span id="pratinjau-{{ $jenis }}">Belum ada</span>
                                    @endif
                                </div>
                                <x-form.input :name="$jenis" type="file" :label="$label" accept="image/png,image/jpeg" :data-pratinjau="'#pratinjau-'.$jenis" data-maks-mb="1" />
                                @if ($pathGambar)
                                    <label class="check-kecil"><input type="checkbox" name="hapus_{{ $jenis }}" value="1"> Hapus gambar</label>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endcan

                @can('kelola-pengaturan-magang')
                    <div class="actions">
                        <x-button>Simpan Pengaturan</x-button>
                    </div>
                @endcan
                </fieldset>
            </form>
        </section>

        {{-- SCAN KARTU ABSENSI (admin & super admin) --}}
        <section class="panel panel-wide" id="scan">
            <h3>Scan Kartu Absensi</h3>
            <p>
                Peserta bisa mengisi jam logbook dari foto kartu absensi. Tombol scan hanya aktif pada rentang tanggal
                di bawah; jendela yang melewati akhir bulan (mis. 28 s.d. 2) berarti tanggal 1–2 dipakai untuk kartu
                bulan sebelumnya. Hasil scan selalu diperiksa peserta sebelum disimpan.
            </p>

            <x-alert type="error" title="Pengaturan scan belum disimpan:" :messages="$errors->scan->all()" />

            <form method="POST" action="{{ route('superadmin.pengaturan-magang.scan') }}">
                @csrf
                @method('PUT')

                <label class="check-kecil" style="margin-bottom: 14px; font-size: 13px; font-weight: 600;">
                    <input type="checkbox" name="scan_aktif" value="1" @checked(old('scan_aktif', $pengaturan->scan_aktif))>
                    Fitur scan kartu absensi aktif
                </label>

                <div class="scan-grid">
                    <x-form.input name="scan_jendela_1_mulai" type="number" label="Sisi 1–15: mulai tanggal" :value="$pengaturan->scan_jendela_1_mulai" min="1" max="31" required />
                    <x-form.input name="scan_jendela_1_selesai" type="number" label="Sisi 1–15: sampai tanggal" :value="$pengaturan->scan_jendela_1_selesai" min="1" max="31" required />
                    <x-form.input name="scan_jendela_2_mulai" type="number" label="Sisi 16–31: mulai tanggal" :value="$pengaturan->scan_jendela_2_mulai" min="1" max="31" required />
                    <x-form.input name="scan_jendela_2_selesai" type="number" label="Sisi 16–31: sampai tanggal" :value="$pengaturan->scan_jendela_2_selesai" min="1" max="31" required hint="Lebih kecil dari tanggal mulai = bulan berikutnya." />
                    <x-form.input name="scan_batas_harian" type="number" label="Batas scan per peserta per hari" :value="$pengaturan->scan_batas_harian" min="1" max="50" required />
                </div>

                <div class="actions">
                    <x-button>Simpan Scan Kartu</x-button>
                </div>
            </form>
        </section>

        {{-- TARIF UANG SAKU --}}
        <section class="panel" id="tarif">
            <h3>Tarif Uang Saku</h3>
            <p>
                Setiap hari dihitung dengan tarif yang berlaku pada tanggal itu, sehingga perubahan tarif
                tidak mengubah rekap periode yang sudah lewat. Tarif saat ini:
                <strong>Rp {{ number_format($tarifBerlaku->tarif_hari_penuh, 0, ',', '.') }}</strong> (hari penuh) /
                <strong>Rp {{ number_format($tarifBerlaku->tarif_setengah_hari, 0, ',', '.') }}</strong> (setengah hari).
            </p>

            <x-alert type="error" title="Tarif belum disimpan:" :messages="$errors->tarif->all()" />

            @can('kelola-pengaturan-magang')
                <form method="POST" action="{{ route('superadmin.pengaturan-magang.tarif.store') }}" class="inline-form">
                    @csrf
                    <x-form.input name="berlaku_mulai" type="date" label="Berlaku Mulai" :value="now()->toDateString()" required />
                    <x-form.input name="tarif_hari_penuh" type="number" label="Hari Penuh (Rp)" min="0" step="500" required />
                    <x-form.input name="tarif_setengah_hari" type="number" label="Setengah Hari (Rp)" min="0" step="500" required />
                    <x-button>+ Tambah</x-button>
                </form>
            @endcan

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Berlaku Mulai</th>
                            <th>Hari Penuh</th>
                            <th>Setengah Hari</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tarifs as $tarif)
                            <tr>
                                <td>
                                    {{ $tarif->berlaku_mulai->year <= 2000 ? 'Awal (tarif bawaan)' : $tarif->berlaku_mulai->translatedFormat('d M Y') }}
                                    @if ($tarif->is($tarifBerlaku))
                                        <span class="badge-berlaku">berlaku</span>
                                    @endif
                                    @if ($tarif->pembuat)
                                        <div class="muted">oleh {{ $tarif->pembuat->name }}</div>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($tarif->tarif_hari_penuh, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($tarif->tarif_setengah_hari, 0, ',', '.') }}</td>
                                <td>
                                    @if ($tarifs->count() > 1 && auth()->user()->can('kelola-pengaturan-magang'))
                                        <form method="POST" action="{{ route('superadmin.pengaturan-magang.tarif.destroy', $tarif) }}" onsubmit="return confirm('Hapus tarif ini? Rekap pada tanggal terkait akan memakai tarif sebelumnya.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="link-hapus">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        {{-- HARI LIBUR --}}
        <section class="panel" id="libur">
            <h3>Hari Libur Nasional &amp; Cuti Bersama</h3>
            <p>
                Sabtu dan Minggu otomatis dianggap libur, jadi tidak perlu dicatat. Untuk cuti bersama
                beberapa hari, isi tanggal selesai.
            </p>

            <x-alert type="error" title="Hari libur belum disimpan:" :messages="$errors->libur->all()" />

            <form method="POST" action="{{ route('superadmin.pengaturan-magang.libur.store') }}" class="inline-form">
                @csrf
                <x-form.input name="tanggal_mulai" type="date" label="Tanggal" required />
                <x-form.input name="tanggal_selesai" type="date" label="Sampai (opsional)" />
                <x-form.input name="keterangan" label="Keterangan" maxlength="255" placeholder="mis. HUT Kemerdekaan RI" required />
                <x-button>+ Tambah</x-button>
            </form>

            <div class="tahun-nav">
                <a href="{{ route('superadmin.pengaturan-magang.index', ['tahun' => $tahun - 1]) }}#libur" aria-label="Tahun sebelumnya">‹</a>
                <span>{{ $tahun }}</span>
                <a href="{{ route('superadmin.pengaturan-magang.index', ['tahun' => $tahun + 1]) }}#libur" aria-label="Tahun berikutnya">›</a>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hariLiburs as $libur)
                            <tr>
                                <td>{{ $libur->tanggal->translatedFormat('D, d M Y') }}</td>
                                <td>{{ $libur->keterangan }}</td>
                                <td>
                                    <form method="POST" action="{{ route('superadmin.pengaturan-magang.libur.destroy', $libur) }}" onsubmit="return confirm('Hapus hari libur ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="link-hapus">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="empty">Belum ada hari libur di tahun {{ $tahun }}.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>

</div>

@endsection
