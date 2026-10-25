{{-- resources/views/superadmin/peserta-magang/biodata.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Biodata Peserta')
@section('page_title', 'Biodata Peserta')
@section('page_subtitle', $pesertaMagang->nama)

@php
    $progres = $biodata->progres();
    $kosong = '—';

    $bagian = [
        'Data Pribadi' => [
            'Nama Lengkap' => $biodata->nama_lengkap,
            'Nama Panggilan' => $biodata->nama_panggilan,
            'Jenis Kelamin' => \App\Models\BiodataPeserta::JENIS_KELAMIN[$biodata->jenis_kelamin] ?? null,
            'Tempat, Tanggal Lahir' => collect([$biodata->tempat_lahir, $biodata->tanggal_lahir?->translatedFormat('d F Y')])->filter()->implode(', '),
            'NIK' => ['nik', null],
            'Alamat KTP' => $biodata->alamat_ktp,
            'Alamat Domisili' => $biodata->alamat_domisili,
            'Tautan Maps Domisili' => ['tautan', $biodata->tautan_maps_domisili],
            'No. WhatsApp / HP' => $biodata->no_hp,
            'Email' => $biodata->email,
            'LinkedIn / Portofolio' => ['tautan', $biodata->tautan_portofolio],
            'Instagram' => ['tautan', $biodata->tautan_instagram],
        ],
        'Data Akademik' => [
            'Perguruan Tinggi' => $biodata->perguruan_tinggi,
            'Fakultas / Jurusan' => $biodata->fakultas_jurusan,
            'NIM' => $biodata->nim,
            'Semester' => $biodata->semester,
            'IPK' => $biodata->ipk !== null ? number_format((float) $biodata->ipk, 2, ',', '.') : null,
            'Dosen Pembimbing' => $biodata->dosen_pembimbing_nama,
            'Kontak Dosen' => $biodata->dosen_pembimbing_kontak,
            'Email Dosen' => $biodata->dosen_pembimbing_email,
        ],
        'Keterampilan dan Kesehatan' => [
            'Keterampilan' => ['daftar', $biodata->keterampilan],
            'Bahasa' => ['daftar', $biodata->bahasa],
            'Riwayat Penyakit / Alergi' => ['daftar', $biodata->riwayat_kesehatan ?: ['Tidak ada']],
        ],
        'Rekening Uang Saku' => [
            'Nomor Rekening' => $biodata->no_rekening,
            'Nama Bank' => $biodata->nama_bank,
            'Atas Nama' => $biodata->atas_nama_rekening,
        ],
    ];
@endphp

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .top {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;

        margin-bottom: 18px;
    }

    .grid {
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }

    .panel {
        margin-bottom: 18px;
        padding: 22px 24px;

        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);

        scroll-margin-top: 20px;
    }

    .panel h3 {
        margin-bottom: 14px;

        color: #17324d;
        font-size: 15px;
    }

    .panel > p {
        margin: -8px 0 14px;

        color: #7b8794;
        font-size: 12px;
        line-height: 1.6;
    }

    dl {
        display: grid;
        grid-template-columns: 190px minmax(0, 1fr);
        gap: 0;

        margin: 0;
    }

    dt,
    dd {
        padding: 9px 0;

        border-top: 1px solid #edf1f5;

        font-size: 13px;
    }

    dt {
        color: #7b8794;
        font-weight: 700;
    }

    dd {
        margin: 0;

        color: #17324d;
        overflow-wrap: anywhere;
    }

    dd ol {
        margin: 0;
        padding-left: 18px;
    }

    dd a {
        color: #1779c9;
    }

    .kosong {
        color: #b4c0cc;
    }

    details.nik summary {
        cursor: pointer;
        list-style: none;
    }

    details.nik summary::after {
        content: " · tampilkan";

        color: #1779c9;
        font-size: 12px;
        font-weight: 700;
    }

    details.nik[open] summary {
        display: none;
    }

    .badge-status {
        display: inline-block;

        padding: 3px 10px;

        border-radius: 999px;

        font-size: 11px;
        font-weight: 800;
    }

    .badge-draft { background: #eef2f6; color: #52646e; }
    .badge-diajukan { background: #fff4e5; color: #b54708; }
    .badge-perlu_revisi { background: #fff0f0; color: #c62828; }
    .badge-disetujui { background: #e7f6ec; color: #067647; }

    .ringkas {
        display: flex;
        flex-direction: column;
        gap: 8px;

        margin-bottom: 14px;

        color: #52646e;
        font-size: 13px;
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

    .kosong-list {
        margin: 0 0 14px;
        padding: 10px 14px 10px 30px;

        border-radius: 9px;

        background: #fff8f1;
        color: #7a4a17;

        font-size: 12px;
        line-height: 1.7;
    }

    .keputusan {
        display: flex;
        flex-direction: column;
        gap: 8px;

        margin-bottom: 12px;
    }

    .keputusan label {
        display: flex;
        align-items: center;
        gap: 8px;

        font-size: 13px;
        cursor: pointer;
    }

    .keputusan input {
        accent-color: #1779c9;
    }

    .x-form-select {
        appearance: auto;
    }

    .actions {
        display: flex;
        justify-content: flex-end;
    }

    @media (max-width: 1000px) {
        .grid {
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

        dl {
            grid-template-columns: 1fr;
        }

        dd {
            padding-top: 0;

            border-top: none;
        }
    }
</style>
@endpush

@push('styles')
<style>
    .pas-foto {
        display: flex;
        align-items: center;
        gap: 12px;

        margin-bottom: 14px;

        color: #7b8794;
        font-size: 12px;
    }

    .pas-foto-gambar {
        border-radius: 12px;
    }
</style>
@endpush

@section('content')

<div class="content">

    <div class="top">
        <x-button variant="secondary" :href="route('superadmin.peserta-magang.show', $pesertaMagang)">← Kembali ke detail peserta</x-button>
    </div>

    <x-flash-messages :validation="false" />

    <div class="grid">

        {{-- ISI BIODATA --}}
        <div>
            @if (! $biodata->exists)
                <x-alert type="info" message="Peserta belum mengisi biodata. Data di bawah terisi otomatis dari data pendaftaran." />
            @endif

            @foreach ($bagian as $judul => $isi)
                <section class="panel">
                    <h3>{{ $judul }}</h3>

                    <dl>
                        @foreach ($isi as $label => $nilai)
                            <dt>{{ $label }}</dt>
                            <dd>
                                @if (is_array($nilai) && $nilai[0] === 'nik')
                                    @if ($biodata->nik)
                                        <details class="nik">
                                            <summary>{{ $biodata->nikTersamar() }}</summary>
                                            {{ $biodata->nik }}
                                        </details>
                                    @else
                                        <span class="kosong">{{ $kosong }}</span>
                                    @endif
                                @elseif (is_array($nilai) && $nilai[0] === 'tautan')
                                    @if ($nilai[1])
                                        <a href="{{ $nilai[1] }}" target="_blank" rel="noopener noreferrer">{{ $nilai[1] }}</a>
                                    @else
                                        <span class="kosong">{{ $kosong }}</span>
                                    @endif
                                @elseif (is_array($nilai) && $nilai[0] === 'daftar')
                                    @if (! empty($nilai[1]))
                                        <ol>
                                            @foreach ($nilai[1] as $baris)
                                                <li>{{ $baris }}</li>
                                            @endforeach
                                        </ol>
                                    @else
                                        <span class="kosong">{{ $kosong }}</span>
                                    @endif
                                @elseif (filled($nilai))
                                    {{ $nilai }}
                                @else
                                    <span class="kosong">{{ $kosong }}</span>
                                @endif
                            </dd>
                        @endforeach
                    </dl>
                </section>
            @endforeach

            @if ($biodata->pernyataan_setuju)
                <section class="panel">
                    <h3>Pernyataan</h3>
                    <p style="margin: 0;">
                        Peserta menyatakan data benar ·
                        {{ $biodata->pernyataan_tempat }}{{ $biodata->pernyataan_tanggal ? ', '.$biodata->pernyataan_tanggal->translatedFormat('d F Y') : '' }}
                    </p>
                </section>
            @endif
        </div>

        {{-- STATUS, TINJAU, PROGRAM --}}
        <div>
            <section class="panel" id="tinjau">
                <h3>Status &amp; Tinjauan</h3>

                <div class="pas-foto">
                    <x-avatar :user="$pesertaMagang->user" :nama="$pesertaMagang->nama" :ukuran="88" class="pas-foto-gambar" />
                    <span>{{ $pesertaMagang->user?->foto_path ? 'Pas foto / foto profil' : 'Belum ada pas foto' }}</span>
                </div>

                <div class="ringkas">
                    <div>
                        <span class="badge-status badge-{{ $biodata->status }}">{{ $biodata->labelStatus() }}</span>
                        @if ($biodata->diajukan_pada)
                            · dikirim {{ $biodata->diajukan_pada->translatedFormat('d M Y H:i') }}
                        @endif
                    </div>
                    <div class="progress-track"><div class="progress-fill" style="width: {{ $progres['persen'] }}%"></div></div>
                    <div>{{ $progres['terisi'] }}/{{ $progres['total'] }} data wajib terisi ({{ $progres['persen'] }}%)</div>
                    @if ($biodata->peninjau)
                        <div>Terakhir ditinjau {{ $biodata->peninjau->name }}, {{ $biodata->ditinjau_pada?->translatedFormat('d M Y H:i') }}</div>
                    @endif
                </div>

                @if ($biodata->status === 'perlu_revisi' && $biodata->catatan_revisi)
                    <x-alert type="warning" title="Catatan revisi terakhir:" :message="$biodata->catatan_revisi" />
                @endif

                @if ($biodata->fieldWajibKosong())
                    <ul class="kosong-list">
                        @foreach ($biodata->fieldWajibKosong() as $label)
                            <li>{{ $label }} belum diisi</li>
                        @endforeach
                    </ul>
                @endif

                <x-alert type="error" :messages="$errors->tinjau->all()" />

                @if (in_array($biodata->status, ['diajukan', 'disetujui'], true))
                    <form method="POST" action="{{ route('superadmin.peserta-magang.biodata.tinjau', $pesertaMagang) }}">
                        @csrf
                        @method('PUT')

                        <div class="keputusan">
                            @if ($biodata->status === 'diajukan')
                                <label>
                                    <input type="radio" name="keputusan" value="setujui" @checked(old('keputusan', 'setujui') === 'setujui')>
                                    Setujui biodata
                                </label>
                            @endif
                            <label>
                                <input type="radio" name="keputusan" value="revisi" @checked(old('keputusan') === 'revisi' || $biodata->status === 'disetujui')>
                                {{ $biodata->status === 'disetujui' ? 'Buka kembali & minta revisi' : 'Minta revisi' }}
                            </label>
                        </div>

                        <x-form.input name="catatan_revisi" type="textarea" label="Catatan untuk peserta" rows="3" maxlength="1000" hint="Wajib diisi bila meminta revisi." />

                        <div class="actions">
                            <x-button>Simpan Tinjauan</x-button>
                        </div>
                    </form>
                @else
                    <p style="margin: 0;">Tinjauan tersedia setelah peserta mengirim biodata.</p>
                @endif
            </section>

            <section class="panel" id="program">
                <h3>Detail Program (diisi admin)</h3>
                <p>Bagian ini tampil di biodata peserta tetapi tidak bisa diubah oleh peserta.</p>

                <x-alert type="error" :messages="$errors->program->all()" />

                <form method="POST" action="{{ route('superadmin.peserta-magang.biodata.program', $pesertaMagang) }}">
                    @csrf
                    @method('PUT')

                    <x-form.input name="posisi_dilamar" label="Posisi / Divisi yang Dilamar" :value="$biodata->posisi_dilamar" maxlength="255" />

                    <div class="x-form-group">
                        <label for="jenis_program" class="x-form-label">Jenis Program</label>
                        <select id="jenis_program" name="jenis_program" class="x-form-control x-form-select">
                            <option value="">— pilih —</option>
                            @foreach (\App\Models\BiodataPeserta::JENIS_PROGRAM as $nilai => $label)
                                <option value="{{ $nilai }}" @selected(old('jenis_program', $biodata->jenis_program) === $nilai)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <x-form.input name="jenis_program_lainnya" label="Keterangan (bila Lainnya)" :value="$biodata->jenis_program_lainnya" maxlength="255" />
                    <x-form.input name="durasi_bulan" type="number" label="Durasi (bulan)" :value="$biodata->durasi_bulan" min="1" max="24" hint="mis. 3 atau 6 bulan" />
                    <x-form.input name="mentor_magang" label="Mentor Magang" :value="$biodata->mentor_magang" maxlength="255" hint="Mentor per periode diatur di kartu Divisi & Mentor pada detail peserta." />

                    <div class="actions">
                        <x-button>Simpan Detail Program</x-button>
                    </div>
                </form>
            </section>
        </div>

    </div>

</div>

@endsection
