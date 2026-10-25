{{-- resources/views/admin/pengajuan/show.blade.php --}}

@extends('layouts.dashboard')

@use('App\Models\PengajuanMagang')

@section('title', 'Detail Pengajuan ' . $pengajuanMagang->nomor())
@section('page_title', 'Detail Pengajuan')
@section('page_subtitle', $pengajuanMagang->nomor() . ' · diajukan ' . $pengajuanMagang->created_at->translatedFormat('d F Y, H:i'))

@php
    $mahasiswa = $pengajuanMagang->mahasiswa;
    $durasiHari = $pengajuanMagang->tanggal_mulai->diffInDays($pengajuanMagang->tanggal_selesai) + 1;
@endphp

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .page-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;

        margin-bottom: 20px;
    }

    .back-link {
        color: var(--utama);
        font-size: 13px;
        font-weight: 700;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 18px;
        align-items: start;
    }

    .panel {
        padding: 22px;

        background: #ffffff;

        border: 1px solid var(--garis);
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel + .panel {
        margin-top: 18px;
    }

    .panel-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;

        margin-bottom: 16px;
        padding-bottom: 14px;

        border-bottom: 1px solid var(--garis);
    }

    .panel-title h3 {
        color: var(--utama-gelap);
        font-size: 15px;
        font-weight: 750;
    }

    .profile {
        display: flex;
        align-items: center;
        gap: 14px;

        margin-bottom: 18px;
    }

    .profile-avatar {
        width: 52px;
        height: 52px;

        flex-shrink: 0;

        border-radius: 14px;

        background: linear-gradient(135deg, var(--utama), var(--utama-gelap));
        color: #ffffff;

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 20px;
        font-weight: 800;
    }

    .profile h2 {
        color: var(--utama-gelap);
        font-size: 19px;
    }

    .profile p {
        margin-top: 3px;

        color: var(--teks-sekunder);
        font-size: 13px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px 22px;
    }

    .info-item.full {
        grid-column: 1 / -1;
    }

    .info-label {
        color: var(--teks-sekunder);

        font-size: 11px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
    }

    .info-value {
        margin-top: 4px;

        color: var(--utama-gelap);

        font-size: 14px;
        line-height: 1.6;
        word-break: break-word;
    }

    .info-value a {
        color: var(--utama);
        font-weight: 700;
    }

    .note {
        padding: 13px 15px;

        border-radius: 10px;

        background: var(--permukaan-redup);
        color: var(--teks);

        font-size: 13px;
        line-height: 1.7;
        white-space: pre-line;
    }

    .doc-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;

        padding: 14px 16px;

        border: 1px dashed var(--garis-kuat);
        border-radius: 10px;

        background: var(--utama-muda);
    }

    .doc-box strong {
        display: block;

        color: var(--utama-gelap);
        font-size: 13px;
    }

    .doc-box span {
        color: var(--teks-sekunder);
        font-size: 11px;
    }

    .status-options {
        display: grid;
        gap: 8px;

        margin-bottom: 16px;
    }

    .status-option {
        display: flex;
        align-items: center;
        gap: 10px;

        padding: 11px 13px;

        border: 1px solid var(--garis);
        border-radius: 9px;

        cursor: pointer;

        font-size: 13px;
        font-weight: 700;
        color: var(--teks);

        transition: .15s ease;
    }

    .status-option:hover {
        border-color: var(--garis-kuat);
    }

    .status-option:has(input:checked) {
        border-color: var(--utama);
        background: var(--utama-muda);
    }

    .status-option input {
        accent-color: var(--utama);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
    }

    .peserta-box {
        padding: 15px;

        border-radius: 10px;

        background: var(--sukses-muda);
        color: var(--sukses);

        font-size: 13px;
        line-height: 1.6;
    }

    .peserta-box a {
        font-weight: 800;
        text-decoration: underline;
    }

    .danger-zone p {
        margin-bottom: 14px;

        color: var(--teks-sekunder);
        font-size: 12px;
        line-height: 1.6;
    }

    .muted {
        color: var(--teks-sekunder);
        font-size: 12px;
        line-height: 1.6;
    }

    @media (max-width: 1100px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 20px 30px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <div class="page-actions">
        <a href="{{ route('admin.pengajuan.index') }}" class="back-link">← Kembali ke daftar pengajuan</a>

        <x-status-badge :status="$pengajuanMagang->status" :label="$pengajuanMagang->statusLabel()" />
    </div>

    <x-flash-messages />

    <div class="detail-grid">

        {{-- KOLOM KIRI: DATA PENGAJUAN --}}
        <div>

            <section class="panel">

                <div class="profile">
                    <div class="profile-avatar">{{ strtoupper(substr($mahasiswa?->nama ?? '?', 0, 1)) }}</div>
                    <div>
                        <h2>{{ $mahasiswa?->nama ?? 'Mahasiswa tidak ditemukan' }}</h2>
                        <p>NIM {{ $mahasiswa?->nim ?? '-' }}</p>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value">
                            @if ($mahasiswa?->email)
                                <a href="mailto:{{ $mahasiswa->email }}">{{ $mahasiswa->email }}</a>
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">No. HP / WhatsApp</div>
                        <div class="info-value">{{ $mahasiswa?->no_hp ?: '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Asal Kampus</div>
                        <div class="info-value">{{ $mahasiswa?->asal_kampus ?: '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Program Studi / Fakultas</div>
                        <div class="info-value">
                            {{ $mahasiswa?->program_studi ?: '-' }}
                            @if ($mahasiswa?->fakultas && $mahasiswa->fakultas !== '-')
                                / {{ $mahasiswa->fakultas }}
                            @endif
                        </div>
                    </div>
                </div>

            </section>

            <section class="panel">

                <div class="panel-title">
                    <h3>Rencana Magang</h3>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Bidang / Posisi</div>
                        <div class="info-value">{{ $pengajuanMagang->posisi }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Perusahaan</div>
                        <div class="info-value">
                            {{ $pengajuanMagang->perusahaan?->nama ?: $pengajuanMagang->perusahaan?->nama_perusahaan ?: '-' }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Periode</div>
                        <div class="info-value">
                            {{ $pengajuanMagang->tanggal_mulai->translatedFormat('d F Y') }}
                            – {{ $pengajuanMagang->tanggal_selesai->translatedFormat('d F Y') }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Durasi</div>
                        <div class="info-value">{{ $durasiHari }} hari</div>
                    </div>

                    <div class="info-item full">
                        <div class="info-label">Motivasi / Keterangan Pendaftar</div>
                        <div class="info-value">
                            @if ($pengajuanMagang->keterangan)
                                <div class="note">{{ $pengajuanMagang->keterangan }}</div>
                            @else
                                <span class="muted">Tidak ada keterangan.</span>
                            @endif
                        </div>
                    </div>

                    <div class="info-item full">
                        <div class="info-label">Surat Pengantar</div>
                        <div class="info-value">
                            @if ($pengajuanMagang->dokumen_path)
                                <div class="doc-box">
                                    <div>
                                        <strong>Surat pengantar {{ $pengajuanMagang->nomor() }}</strong>
                                        <span>{{ strtoupper(pathinfo($pengajuanMagang->dokumen_path, PATHINFO_EXTENSION)) }}</span>
                                    </div>
                                    <x-button
                                        variant="secondary"
                                        :href="route('admin.pengajuan.dokumen', $pengajuanMagang)"
                                        target="_blank"
                                        rel="noopener"
                                    >
                                        Lihat Dokumen
                                    </x-button>
                                </div>
                            @else
                                <span class="muted">Tidak ada dokumen (pengajuan lama / via Google Form).</span>
                            @endif
                        </div>
                    </div>
                </div>

            </section>

        </div>

        {{-- KOLOM KANAN: AKSI --}}
        <div>

            <section class="panel">

                <div class="panel-title">
                    <h3>Proses Pengajuan</h3>
                </div>

                <form method="POST" action="{{ route('admin.pengajuan.status', $pengajuanMagang) }}">
                    @csrf
                    @method('PUT')

                    <div class="status-options">
                        @foreach (PengajuanMagang::STATUS_LABELS as $kode => $label)
                            <label class="status-option">
                                <input
                                    type="radio"
                                    name="status"
                                    value="{{ $kode }}"
                                    @checked(old('status', $pengajuanMagang->status) === $kode)
                                >
                                <x-status-badge :status="$kode" :label="$label" />
                            </label>
                        @endforeach
                    </div>

                    <x-form.input
                        name="catatan_admin"
                        type="textarea"
                        label="Catatan untuk pendaftar"
                        :value="$pengajuanMagang->catatan_admin"
                        rows="4"
                        placeholder="Contoh: Silakan datang pada 1 Oktober pukul 08.00 dengan membawa KTM."
                        hint="Catatan ini terlihat oleh pendaftar saat mengecek status. Wajib diisi bila ditolak."
                    />

                    <div class="form-actions">
                        <x-button>Simpan Status</x-button>
                    </div>
                </form>

            </section>

            @if ($pengajuanMagang->status === PengajuanMagang::STATUS_DISETUJUI)
                <section class="panel">

                    <div class="panel-title">
                        <h3>Data Peserta Magang</h3>
                    </div>

                    @if ($pengajuanMagang->pesertaMagang)
                        <div class="peserta-box">
                            Pengajuan ini sudah dibuatkan data peserta.
                            <a href="{{ route('superadmin.peserta-magang.show', $pengajuanMagang->pesertaMagang) }}">Lihat data peserta →</a>
                        </div>
                    @else
                        <p class="muted" style="margin-bottom: 14px;">
                            Pengajuan sudah disetujui. Buat data peserta magang dari data pengajuan ini
                            (nama, NIM, kampus, prodi, dan periode terisi otomatis).
                        </p>

                        <form method="POST" action="{{ route('admin.pengajuan.peserta', $pengajuanMagang) }}">
                            @csrf
                            <x-button>Buat Data Peserta</x-button>
                        </form>
                    @endif

                </section>
            @endif

            <section class="panel danger-zone">

                <div class="panel-title">
                    <h3>Hapus Pengajuan</h3>
                </div>

                <p>Pengajuan dan surat pengantarnya akan dihapus permanen. Data mahasiswa tetap disimpan.</p>

                <form
                    method="POST"
                    action="{{ route('admin.pengajuan.destroy', $pengajuanMagang) }}"
                    onsubmit="return confirm('Hapus pengajuan {{ $pengajuanMagang->nomor() }} secara permanen?');"
                >
                    @csrf
                    @method('DELETE')
                    <x-button variant="danger">Hapus Pengajuan</x-button>
                </form>

            </section>

        </div>

    </div>

</div>

@endsection
