{{-- resources/views/peserta/biodata/edit.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Biodata')
@section('page_title', 'Biodata')
@section('page_subtitle', 'Profil mahasiswa magang (pengganti sheet PROFIL MAHASISWA)')

@php
    $bisaUbah = ! $biodata->exists || $biodata->bisaDiubahPeserta();
    $progres = $biodata->progres();

    // Nilai daftar: input lama (setelah gagal validasi) atau data tersimpan, minimal satu baris.
    $daftar = fn (string $field) => array_values(old($field, $biodata->{$field} ?? [])) ?: [''];
@endphp

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .status-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 14px;

        margin-bottom: 18px;
        padding: 18px 22px;

        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .status-bar strong {
        color: #17324d;
        font-size: 15px;
    }

    .status-bar p {
        margin-top: 4px;

        color: #7b8794;
        font-size: 12px;
    }

    .progress {
        min-width: 220px;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;

        margin-bottom: 6px;

        color: #52646e;
        font-size: 12px;
        font-weight: 700;
    }

    .progress-track {
        height: 8px;

        border-radius: 999px;

        background: #edf1f5;

        overflow: hidden;
    }

    .progress-fill {
        height: 100%;

        border-radius: 999px;

        background: linear-gradient(90deg, #1779c9, #19a974);
    }

    .badge-status {
        display: inline-block;

        margin-left: 8px;
        padding: 3px 10px;

        border-radius: 999px;

        font-size: 11px;
        font-weight: 800;
        vertical-align: middle;
    }

    .badge-draft { background: #eef2f6; color: #52646e; }
    .badge-diajukan { background: #fff4e5; color: #b54708; }
    .badge-perlu_revisi { background: #fff0f0; color: #c62828; }
    .badge-disetujui { background: #e7f6ec; color: #067647; }

    fieldset.form-lock {
        min-width: 0;

        margin: 0;
        padding: 0;

        border: none;
    }

    .section {
        margin-bottom: 18px;
        padding: 22px 24px 8px;

        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .foto-section {
        display: flex;
        align-items: center;
        gap: 20px;

        padding-bottom: 22px;
    }

    .foto-pratinjau {
        border-radius: 14px;
    }

    .foto-isi h3 {
        color: #17324d;
        font-size: 15px;
    }

    .foto-isi p {
        margin: 4px 0 12px;

        color: #7b8794;
        font-size: 12px;
        line-height: 1.6;
    }

    .foto-form {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;

        font-size: 12px;
    }

    .section-head {
        display: flex;
        align-items: center;
        gap: 12px;

        margin-bottom: 16px;
    }

    .section-number {
        display: grid;
        place-items: center;

        width: 30px;
        height: 30px;

        border-radius: 9px;

        background: #e8f2fb;
        color: #1779c9;

        font-size: 13px;
        font-weight: 800;
    }

    .section-head h3 {
        color: #17324d;
        font-size: 15px;
    }

    .section-head p {
        color: #7b8794;
        font-size: 12px;
    }

    .fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0 16px;
    }

    .fields .full {
        grid-column: 1 / -1;
    }

    .program-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px 16px;

        margin-bottom: 14px;
    }

    .program-item span {
        display: block;

        color: #8a99a8;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .program-item strong {
        display: block;

        margin-top: 3px;

        color: #17324d;
        font-size: 13px;
    }

    .list-field {
        margin-bottom: 16px;
    }

    .list-rows {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .list-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .list-row .nomor {
        flex: 0 0 26px;

        color: #8a99a8;
        font-size: 12px;
        font-weight: 800;
        text-align: center;
    }

    .list-row input {
        flex: 1;
    }

    .list-remove,
    .list-add {
        border: none;
        background: none;

        font-family: inherit;
        font-weight: 700;

        cursor: pointer;
    }

    .list-remove {
        width: 34px;
        height: 34px;

        border-radius: 8px;

        color: #c62828;
        font-size: 18px;
    }

    .list-remove:hover {
        background: #fff0f0;
    }

    .list-add {
        margin-top: 8px;
        padding: 6px 0;

        color: #1779c9;
        font-size: 13px;
    }

    .x-form-select {
        appearance: auto;
    }

    .radio-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 18px;

        min-height: 44px;
        align-items: center;
    }

    .radio-group label,
    .check {
        display: flex;
        align-items: center;
        gap: 8px;

        color: #34495e;
        font-size: 13px;
        cursor: pointer;
    }

    .radio-group input,
    .check input {
        width: 17px;
        height: 17px;

        accent-color: #1779c9;
    }

    .check {
        align-items: flex-start;

        margin-bottom: 14px;

        line-height: 1.6;
    }

    .check input {
        flex: 0 0 17px;
        margin-top: 3px;
    }

    .note-sensitif {
        margin: -4px 0 14px;

        color: #7b8794;
        font-size: 12px;
    }

    .actions {
        position: sticky;
        bottom: 0;

        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 10px;

        padding: 14px 0;

        background: linear-gradient(180deg, rgba(244, 247, 251, 0), #f4f7fb 35%);
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 16px 30px;
        }

        .section {
            padding: 18px 16px 6px;
        }

        .fields,
        .program-grid {
            grid-template-columns: 1fr;
        }

        .progress {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages :validation="false" />

    @if ($errors->any())
        <x-alert type="error" title="Biodata belum tersimpan. Periksa isian berikut:" :messages="$errors->all()" />
    @endif

    {{-- STATUS & PROGRES --}}
    <div class="status-bar">
        <div>
            <strong>
                Status Biodata
                <span class="badge-status badge-{{ $biodata->status }}">{{ $biodata->labelStatus() }}</span>
            </strong>
            <p>
                @switch($biodata->status)
                    @case('diajukan')
                        Dikirim {{ $biodata->diajukan_pada?->translatedFormat('d F Y H:i') }}. Biodata terkunci selama ditinjau admin.
                        @break
                    @case('disetujui')
                        Disetujui {{ $biodata->ditinjau_pada?->translatedFormat('d F Y') }}. Hubungi admin bila ada data yang perlu diubah.
                        @break
                    @case('perlu_revisi')
                        Admin meminta perbaikan. Perbaiki data lalu kirim ulang.
                        @break
                    @default
                        Simpan sebagai draft kapan saja, lalu kirim setelah semua data wajib terisi.
                @endswitch
            </p>
        </div>

        <div class="progress">
            <div class="progress-label">
                <span>Data wajib terisi</span>
                <span>{{ $progres['terisi'] }}/{{ $progres['total'] }} ({{ $progres['persen'] }}%)</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" style="width: {{ $progres['persen'] }}%"></div>
            </div>
        </div>
    </div>

    @if ($biodata->status === 'perlu_revisi' && $biodata->catatan_revisi)
        <x-alert type="warning" title="Catatan revisi dari admin:" :message="$biodata->catatan_revisi" />
    @endif

    {{-- PAS FOTO: form terpisah, bisa diganti kapan saja (juga setelah disetujui). --}}
    <section class="section foto-section" id="pas-foto">
        <x-avatar :user="auth()->user()" :ukuran="96" class="foto-pratinjau" />

        <div class="foto-isi">
            <h3>Pas Foto *</h3>
            <p>
                Foto wajah terbaru, latar polos. JPG/PNG maksimal 2 MB, otomatis dipotong persegi.
                Foto ini juga menjadi foto profil Anda dan boleh diganti kapan saja tanpa persetujuan ulang.
            </p>

            <form method="POST" action="{{ route('profil.foto') }}" enctype="multipart/form-data" class="foto-form">
                @csrf
                @method('PUT')
                <input type="file" name="foto" accept="image/jpeg,image/png" required aria-label="Pilih pas foto">
                <x-button variant="secondary">{{ auth()->user()->foto_path ? 'Ganti Foto' : 'Unggah Foto' }}</x-button>
            </form>
        </div>
    </section>

    <form method="POST" action="{{ route('peserta.biodata.update') }}">
        @csrf
        @method('PUT')

        <fieldset class="form-lock" @disabled(! $bisaUbah)>

            {{-- 1. DATA PRIBADI --}}
            <section class="section">
                <div class="section-head">
                    <span class="section-number">1</span>
                    <div>
                        <h3>Data Pribadi</h3>
                        <p>Sesuai KTP. Tanda * wajib diisi sebelum biodata dikirim.</p>
                    </div>
                </div>

                <div class="fields">
                    <x-form.input name="nama_lengkap" label="Nama Lengkap *" :value="$biodata->nama_lengkap" maxlength="255" />
                    <x-form.input name="nama_panggilan" label="Nama Panggilan" :value="$biodata->nama_panggilan" maxlength="100" />

                    <div class="x-form-group">
                        <span class="x-form-label">Jenis Kelamin *</span>
                        <div class="radio-group">
                            @foreach (\App\Models\BiodataPeserta::JENIS_KELAMIN as $nilai => $label)
                                <label>
                                    <input type="radio" name="jenis_kelamin" value="{{ $nilai }}" @checked(old('jenis_kelamin', $biodata->jenis_kelamin) === $nilai)>
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                        @error('jenis_kelamin') <div class="x-form-error">{{ $message }}</div> @enderror
                    </div>

                    <x-form.input name="nik" label="NIK (KTP) *" :value="$biodata->nik" inputmode="numeric" maxlength="16" placeholder="16 digit" hint="Disimpan terenkripsi dan hanya terlihat oleh Anda dan admin." />

                    <x-form.input name="tempat_lahir" label="Tempat Lahir *" :value="$biodata->tempat_lahir" maxlength="100" />
                    <x-form.input name="tanggal_lahir" type="date" label="Tanggal Lahir *" :value="$biodata->tanggal_lahir?->toDateString()" />

                    <x-form.input name="alamat_ktp" type="textarea" label="Alamat KTP *" :value="$biodata->alamat_ktp" rows="3" maxlength="500" />
                    <x-form.input name="alamat_domisili" type="textarea" label="Alamat Domisili (saat ini) *" :value="$biodata->alamat_domisili" rows="3" maxlength="500" />

                    <x-form.input name="tautan_maps_domisili" type="url" label="Tautan Google Maps Domisili" :value="$biodata->tautan_maps_domisili" maxlength="500" placeholder="https://maps.app.goo.gl/..." />
                    <x-form.input name="no_hp" label="No. WhatsApp / HP *" :value="$biodata->no_hp" inputmode="tel" maxlength="20" placeholder="08xxxxxxxxxx" />

                    <x-form.input name="email" type="email" label="Email *" :value="$biodata->email" maxlength="255" />
                    <x-form.input name="tautan_portofolio" type="url" label="Tautan LinkedIn / Portofolio" :value="$biodata->tautan_portofolio" maxlength="500" placeholder="https://" />

                    <x-form.input name="tautan_instagram" type="url" label="Tautan Instagram" :value="$biodata->tautan_instagram" maxlength="500" placeholder="https://www.instagram.com/..." />
                </div>
            </section>

            {{-- 2. DATA AKADEMIK --}}
            <section class="section">
                <div class="section-head">
                    <span class="section-number">2</span>
                    <div>
                        <h3>Data Akademik</h3>
                        <p>Perguruan tinggi dan dosen pembimbing magang dari kampus.</p>
                    </div>
                </div>

                <div class="fields">
                    <x-form.input name="perguruan_tinggi" label="Nama Perguruan Tinggi *" :value="$biodata->perguruan_tinggi" maxlength="255" />
                    <x-form.input name="fakultas_jurusan" label="Fakultas / Jurusan *" :value="$biodata->fakultas_jurusan" maxlength="255" />
                    <x-form.input name="nim" label="NIM *" :value="$biodata->nim" maxlength="50" />
                    <x-form.input name="semester" type="number" label="Semester Saat Ini *" :value="$biodata->semester" min="1" max="14" />
                    <x-form.input name="ipk" label="IPK Terakhir *" :value="$biodata->ipk" inputmode="decimal" maxlength="4" placeholder="mis. 3,25" />
                    <x-form.input name="dosen_pembimbing_nama" label="Nama Dosen Pembimbing Magang *" :value="$biodata->dosen_pembimbing_nama" maxlength="255" />
                    <x-form.input name="dosen_pembimbing_kontak" label="Kontak Dosen Pembimbing *" :value="$biodata->dosen_pembimbing_kontak" inputmode="tel" maxlength="20" />
                    <x-form.input name="dosen_pembimbing_email" type="email" label="Email Dosen Pembimbing" :value="$biodata->dosen_pembimbing_email" maxlength="255" />
                </div>
            </section>

            {{-- 3. DETAIL PROGRAM (diisi admin) --}}
            <section class="section">
                <div class="section-head">
                    <span class="section-number">3</span>
                    <div>
                        <h3>Detail Program Magang</h3>
                        <p>Disiapkan oleh admin. Hubungi admin bila ada yang tidak sesuai.</p>
                    </div>
                </div>

                <div class="program-grid">
                    <div class="program-item">
                        <span>Posisi / Divisi yang Dilamar</span>
                        <strong>{{ $biodata->posisi_dilamar ?: '-' }}</strong>
                    </div>
                    <div class="program-item">
                        <span>Jenis Program</span>
                        <strong>{{ $biodata->labelJenisProgram() ?? '-' }}</strong>
                    </div>
                    <div class="program-item">
                        <span>Durasi</span>
                        <strong>{{ $biodata->durasi_bulan ? $biodata->durasi_bulan.' bulan' : '-' }}</strong>
                    </div>
                    <div class="program-item">
                        <span>Periode Magang</span>
                        <strong>
                            @if ($peserta->periode_mulai && $peserta->periode_selesai)
                                {{ $peserta->periode_mulai->translatedFormat('d F Y') }} s.d. {{ $peserta->periode_selesai->translatedFormat('d F Y') }}
                            @else
                                -
                            @endif
                        </strong>
                    </div>
                    <div class="program-item">
                        <span>Mentor Magang</span>
                        <strong>{{ $biodata->mentor_magang ?: '-' }}</strong>
                    </div>
                </div>
            </section>

            {{-- 4. KETERAMPILAN & KESEHATAN --}}
            <section class="section">
                <div class="section-head">
                    <span class="section-number">4</span>
                    <div>
                        <h3>Keterampilan dan Kondisi Kesehatan</h3>
                        <p>Tambahkan baris sesuai kebutuhan (maksimal 10 baris per daftar).</p>
                    </div>
                </div>

                @foreach ([
                    'keterampilan' => ['Keterampilan Utama (hard / soft skill) *', 'mis. Pemrograman PHP, Laravel'],
                    'bahasa' => ['Bahasa yang Dikuasai *', 'mis. Bahasa Inggris (dasar)'],
                    'riwayat_kesehatan' => ['Riwayat Penyakit / Alergi (jika ada)', 'mis. Alergi debu, atau kosongkan bila tidak ada'],
                ] as $field => [$label, $placeholder])
                    <div class="list-field x-form-group" data-list>
                        <span class="x-form-label">{{ $label }}</span>

                        <div class="list-rows" data-list-rows>
                            @foreach ($daftar($field) as $i => $baris)
                                <div class="list-row" data-list-row>
                                    <span class="nomor" data-nomor>{{ $i + 1 }}</span>
                                    <input type="text" name="{{ $field }}[]" value="{{ $baris }}" maxlength="255" placeholder="{{ $placeholder }}" class="x-form-control" aria-label="{{ $label }} baris {{ $i + 1 }}">
                                    <button type="button" class="list-remove" data-list-remove aria-label="Hapus baris">×</button>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="list-add" data-list-add>+ Tambah baris</button>

                        @error($field) <div class="x-form-error">{{ $message }}</div> @enderror
                    </div>
                @endforeach

                @if ($biodata->exists && $biodata->riwayat_kesehatan)
                    <p class="note-sensitif">Riwayat kesehatan disimpan terenkripsi.</p>
                @endif
            </section>

            {{-- 5. REKENING --}}
            <section class="section">
                <div class="section-head">
                    <span class="section-number">5</span>
                    <div>
                        <h3>Rekening Uang Saku</h3>
                        <p>Dipakai untuk pembayaran uang saku dan dicantumkan di rekap periode.</p>
                    </div>
                </div>

                <div class="fields">
                    <x-form.input name="no_rekening" label="Nomor Rekening *" :value="$biodata->no_rekening" inputmode="numeric" maxlength="30" />
                    <x-form.input name="nama_bank" label="Nama Bank *" :value="$biodata->nama_bank" maxlength="100" placeholder="mis. BCA" />
                    <x-form.input name="atas_nama_rekening" label="Atas Nama *" :value="$biodata->atas_nama_rekening" maxlength="255" />
                </div>
            </section>

            {{-- 6. PERNYATAAN --}}
            <section class="section">
                <div class="section-head">
                    <span class="section-number">6</span>
                    <div>
                        <h3>Pernyataan Kebenaran Data</h3>
                        <p>Wajib dicentang saat mengirim biodata.</p>
                    </div>
                </div>

                <input type="hidden" name="pernyataan_setuju" value="0">
                <label class="check">
                    <input type="checkbox" name="pernyataan_setuju" value="1" @checked(old('pernyataan_setuju', $biodata->pernyataan_setuju))>
                    <span>
                        Dengan ini saya menyatakan bahwa seluruh data dan informasi yang saya isikan adalah benar dan dapat
                        dipertanggungjawabkan. Apabila di kemudian hari ditemukan ketidaksesuaian, saya bersedia menerima
                        keputusan sesuai kebijakan perusahaan.
                    </span>
                </label>
                @error('pernyataan_setuju') <div class="x-form-error" style="margin: -8px 0 12px;">{{ $message }}</div> @enderror

                <div class="fields">
                    <x-form.input name="pernyataan_tempat" label="Tempat *" :value="$biodata->pernyataan_tempat" maxlength="100" placeholder="mis. Yogyakarta" hint="Tanggal pernyataan diisi otomatis saat biodata dikirim." />
                </div>
            </section>

        </fieldset>

        @if ($bisaUbah)
            <div class="actions">
                <x-button variant="secondary" name="aksi" value="simpan">Simpan Draft</x-button>
                <x-button name="aksi" value="kirim" onclick="return confirm('Kirim biodata untuk ditinjau admin? Selama ditinjau, biodata tidak dapat diubah.')">Kirim untuk Ditinjau</x-button>
            </div>
        @endif
    </form>

</div>

@endsection

@push('scripts')
<script>
(() => {
    const MAKS = 10;

    document.querySelectorAll('[data-list]').forEach((list) => {
        const rows = list.querySelector('[data-list-rows]');
        const addButton = list.querySelector('[data-list-add]');

        const renumber = () => {
            const items = rows.querySelectorAll('[data-list-row]');

            items.forEach((row, i) => {
                row.querySelector('[data-nomor]').textContent = i + 1;
            });

            addButton.hidden = items.length >= MAKS;
        };

        addButton.addEventListener('click', () => {
            const template = rows.querySelector('[data-list-row]');
            const row = template.cloneNode(true);

            row.querySelector('input').value = '';
            rows.appendChild(row);
            renumber();
            row.querySelector('input').focus();
        });

        rows.addEventListener('click', (event) => {
            const button = event.target.closest('[data-list-remove]');

            if (!button) {
                return;
            }

            const items = rows.querySelectorAll('[data-list-row]');

            if (items.length === 1) {
                items[0].querySelector('input').value = '';
            } else {
                button.closest('[data-list-row]').remove();
            }

            renumber();
        });

        renumber();
    });
})();
</script>
@endpush
