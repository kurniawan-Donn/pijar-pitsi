{{--
    Diagram alur magang versi HTML (padanan blok Mermaid di docs/panduan/alur-magang.md).
    Warna kiri kartu = pelaku: mahasiswa/peserta, admin, atau sistem.
--}}
@php
    $langkah = [
        ['mahasiswa', 'Daftar di website', 'Formulir 3 langkah + surat pengantar → kode MG-00000x'],
        ['mahasiswa', 'Cek status pengajuan', 'Kode pengajuan + email'],
        ['admin', 'Setujui / tolak pengajuan', 'Ditolak: pendaftar melihat alasannya di Cek Status'],
        ['admin', 'Buat data peserta + periode magang', 'Tombol Buat Data Peserta, isi tanggal mulai & selesai'],
        ['admin', 'Buat akun login', 'Password sementara tampil sekali'],
        ['mahasiswa', 'Login & ganti password', 'Wajib saat login pertama'],
        ['mahasiswa', 'Isi biodata + pas foto', 'Simpan Draft → Kirim untuk Ditinjau'],
        ['admin', 'Tinjau biodata', 'Setujui, atau minta revisi (kembali ke peserta)'],
        ['mahasiswa', 'Logbook harian / izin & sakit', 'Setiap hari kerja'],
        ['admin', 'Verifikasi logbook & izin', 'Ditolak/review kembali ke peserta; diterima terkunci'],
        ['sistem', 'Rekap kehadiran & uang saku', 'Per periode tanggal 28 s.d. 27'],
        ['admin', 'Rotasi divisi & mentor', 'Diatur per periode'],
        ['mahasiswa', 'Isi feedback', 'Terbuka 1 bulan sebelum selesai; bisa bersedia tampil sebagai alumni'],
        ['admin', 'Penilaian akhir + PDF', 'Hard Skill & Soft Skill, lembar 1 halaman'],
        ['admin', 'Ubah status menjadi Selesai', 'Sistem membuat draft surat keterangan'],
        ['admin', 'Terbitkan surat keterangan', 'Nomor surat, tanda tangan, stempel, QR verifikasi'],
        ['mahasiswa', 'Unduh surat keterangan', 'Feedback terkunci'],
        ['admin', 'Tampilkan sebagai alumni', 'Hanya bila peserta bersedia'],
    ];

    $label = ['mahasiswa' => 'Mahasiswa / Peserta', 'admin' => 'Admin', 'sistem' => 'Sistem'];
@endphp

<div class="diagram-alur" role="img" aria-label="Diagram alur magang dari mendaftar sampai menjadi alumni">
    <div class="diagram-legenda">
        @foreach ($label as $kunci => $teks)
            <span class="pelaku-{{ $kunci }}"><i></i>{{ $teks }}</span>
        @endforeach
    </div>

    <ol class="diagram-langkah">
        @foreach ($langkah as [$pelaku, $judul, $keterangan])
            <li class="pelaku-{{ $pelaku }}">
                <span class="nomor">{{ $loop->iteration }}</span>
                <div>
                    <small>{{ $label[$pelaku] }}</small>
                    <strong>{{ $judul }}</strong>
                    <span>{{ $keterangan }}</span>
                </div>
            </li>
        @endforeach
    </ol>
</div>
