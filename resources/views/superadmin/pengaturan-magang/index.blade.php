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

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);

        scroll-margin-top: 20px;
    }

    .panel-wide {
        grid-column: 1 / -1;
    }

    .panel h3 {
        color: #17324d;
        font-size: 16px;
    }

    .panel > p {
        margin: 4px 0 18px;

        color: #7b8794;
        font-size: 12px;
        line-height: 1.6;
    }

    .panel h4 {
        margin: 6px 0 12px;

        color: #52646e;
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

        background: #f7f9fc;
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

        background: #f8fafc;
        color: #6b7c8d;

        font-size: 11px;
        font-weight: 800;
        letter-spacing: .4px;
        text-align: left;
        text-transform: uppercase;
    }

    td {
        padding: 9px 10px;

        border-top: 1px solid #edf1f5;

        color: #34495e;
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

        background: #e7f6ec;
        color: #067647;

        font-size: 11px;
        font-weight: 800;
    }

    .muted {
        color: #9aa7b4;
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

        background: #f4f7fb;
        color: #52646e !important;
        font-size: 12.5px;
    }

    .link-hapus {
        padding: 0;

        border: none;
        background: none;

        color: #c62828;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;

        cursor: pointer;
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
        color: #1779c9;
        text-decoration: none;
    }

    .empty {
        padding: 18px;

        color: #9aa7b4;
        font-size: 13px;
        text-align: center;
    }

    @media (max-width: 1000px) {
        .grid,
        .fields {
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
                <p class="hanya-baca">🔒 Hanya Super Admin yang dapat mengubah aturan umum dan tarif. Admin dapat mengelola hari libur.</p>
            @endcannot

            <form method="POST" action="{{ route('superadmin.pengaturan-magang.update') }}">
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
                    <div class="actions">
                        <x-button>Simpan Pengaturan</x-button>
                    </div>
                @endcan
                </fieldset>
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
