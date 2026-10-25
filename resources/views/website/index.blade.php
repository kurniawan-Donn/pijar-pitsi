{{-- resources/views/website/index.blade.php --}}

@php
    /*
    |--------------------------------------------------------------------------
    | DATA HALAMAN
    |--------------------------------------------------------------------------
    | Semua teks bisa diubah lewat menu Kelola Konten. Nilai setelah "??"
    | adalah teks bawaan bila belum diisi.
    */

    // Link Google Form (null bila belum diatur, sehingga tombolnya disembunyikan).
    $googleFormUrl = $pengaturan?->google_form_url ?: null;

    $banner = isset($banners) && $banners->isNotEmpty() ? $banners->first() : null;

    $namaPerusahaan = $perusahaan?->nama ?: $perusahaan?->nama_perusahaan ?: 'MAGENTA';

    $informasiTerbaruJudul = $pengaturan?->informasi_terbaru_judul ?? 'Informasi Terbaru';
    $informasiTerbaruDeskripsi = $pengaturan?->informasi_terbaru_deskripsi
        ?? 'Informasi terbaru mengenai kegiatan dan penerimaan mahasiswa magang.';

    $pengumumanJudul = $pengaturan?->pengumuman_judul ?? 'Pengumuman';
    $pengumumanDeskripsi = $pengaturan?->pengumuman_deskripsi
        ?? 'Informasi terbaru mengenai kegiatan dan penerimaan mahasiswa magang.';

    $pengumumanItems = [
        [
            $pengaturan?->pengumuman_card1_judul ?? 'Persiapkan Dokumen',
            $pengaturan?->pengumuman_card1_deskripsi ?? 'Pastikan dokumen persyaratan telah disiapkan sebelum melakukan pendaftaran.',
        ],
        [
            $pengaturan?->pengumuman_card2_judul ?? 'Pendaftaran Online',
            $pengaturan?->pengumuman_card2_deskripsi ?? 'Pendaftaran dilakukan secara online melalui formulir di website ini.',
        ],
        [
            $pengaturan?->pengumuman_card3_judul ?? 'Informasi Perusahaan',
            $pengaturan?->pengumuman_card3_deskripsi ?? 'Informasi perusahaan dan kontak tersedia pada bagian Kontak.',
        ],
    ];

    $persyaratanJudul = $pengaturan?->persyaratan_judul ?? 'Persyaratan Magang';
    $persyaratanDeskripsi = $pengaturan?->persyaratan_deskripsi
        ?? 'Pastikan seluruh kebutuhan administrasi telah disiapkan sebelum mendaftar.';

    $persyaratanItems = [
        [
            '📄',
            $pengaturan?->persyaratan1_judul ?? 'Surat Pengantar',
            $pengaturan?->persyaratan1_deskripsi ?? 'Siapkan surat pengantar atau surat permohonan magang dari kampus sesuai ketentuan yang berlaku.',
        ],
        [
            '🎓',
            $pengaturan?->persyaratan2_judul ?? 'Data Mahasiswa',
            $pengaturan?->persyaratan2_deskripsi ?? 'Siapkan data diri dan informasi akademik yang diperlukan pada saat mengisi formulir pendaftaran.',
        ],
        [
            '🗂️',
            $pengaturan?->persyaratan3_judul ?? 'Dokumen Pendukung',
            $pengaturan?->persyaratan3_deskripsi ?? 'Siapkan dokumen pendukung lain sesuai dengan persyaratan yang ditentukan oleh perusahaan.',
        ],
    ];

    $alurJudul = $pengaturan?->alur_judul ?? 'Alur Magang';
    $alurDeskripsi = $pengaturan?->alur_deskripsi
        ?? 'Ikuti tahapan berikut agar proses pendaftaran magang berjalan dengan baik.';

    $alurItems = [
        [
            $pengaturan?->alur1_judul ?? 'Persiapkan',
            $pengaturan?->alur1_deskripsi ?? 'Siapkan data dan dokumen yang diperlukan.',
        ],
        [
            $pengaturan?->alur2_judul ?? 'Daftar',
            $pengaturan?->alur2_deskripsi ?? 'Isi formulir pendaftaran magang secara lengkap.',
        ],
        [
            $pengaturan?->alur3_judul ?? 'Verifikasi',
            $pengaturan?->alur3_deskripsi ?? 'Data pendaftaran akan diproses dan diverifikasi oleh pihak terkait.',
        ],
        [
            $pengaturan?->alur4_judul ?? 'Pelaksanaan',
            $pengaturan?->alur4_deskripsi ?? 'Mahasiswa melaksanakan kegiatan magang sesuai ketentuan perusahaan.',
        ],
    ];

    $websiteUrl = null;

    if ($perusahaan?->website) {
        $websiteUrl = str_starts_with($perusahaan->website, 'http://') || str_starts_with($perusahaan->website, 'https://')
            ? $perusahaan->website
            : 'https://' . $perusahaan->website;
    }

    $telepon = $perusahaan?->telepon ?: $perusahaan?->no_telepon;

    $kontakItems = array_filter([
        $perusahaan?->alamat ? ['📍', 'Alamat', trim($perusahaan->alamat . ($perusahaan->kota ? ', ' . $perusahaan->kota : '')), null] : null,
        $perusahaan?->email ? ['✉️', 'Email', $perusahaan->email, 'mailto:' . $perusahaan->email] : null,
        $telepon && $telepon !== '-' ? ['☎️', 'Telepon', $telepon, 'tel:' . preg_replace('/[^0-9+]/', '', $telepon)] : null,
        $websiteUrl ? ['🌐', 'Website', $perusahaan->website, $websiteUrl] : null,
    ]);

    $menu = [
        '#beranda' => 'Beranda',
        '#pengumuman' => 'Pengumuman',
        '#persyaratan' => 'Persyaratan',
        '#alur' => 'Alur',
        '#daftar' => 'Daftar',
        '#status' => 'Cek Status',
        '#kontak' => 'Kontak',
    ];
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pendaftaran dan informasi magang {{ $namaPerusahaan }}. Daftar online, cek status pengajuan, dan lihat persyaratan magang.">
    <meta name="theme-color" content="#0878a8">

    <title>{{ $namaPerusahaan }} - Sistem Informasi Magang</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tandai JS aktif sedini mungkin agar animasi tidak berkedip. --}}
    <script>document.documentElement.classList.add('js');</script>

    @include('website.partials.styles')
</head>

<body>

    {{-- =====================================================
         NAVBAR
    ====================================================== --}}
    <header class="navbar">
        <nav class="container nav-inner" aria-label="Navigasi utama">

            <a href="#beranda" class="logo">
                <span class="logo-mark">M</span>
                MAGENTA
            </a>

            <ul class="nav-menu" id="nav-menu">
                @foreach ($menu as $href => $label)
                    <li><a href="{{ $href }}">{{ $label }}</a></li>
                @endforeach

                {{-- Di layar kecil tombol masuk tampil di dalam menu. --}}
                <li class="nav-menu-masuk"><a href="{{ route('login') }}">Login →</a></li>
            </ul>

            <div class="nav-actions">
                <a href="{{ route('login') }}" class="btn btn-outline nav-cta" title="Login peserta magang, admin, dan super admin">Login</a>
                <a href="#daftar" class="btn btn-accent nav-cta">Daftar Magang</a>
            </div>

            <button type="button" class="nav-toggle" aria-label="Buka menu" aria-controls="nav-menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>

        </nav>
    </header>

    <main>

        {{-- =================================================
             HERO
        ================================================== --}}
        <section id="beranda" class="hero">
            <span class="blob blob-1" aria-hidden="true"></span>
            <span class="blob blob-2" aria-hidden="true"></span>

            <div class="container hero-grid">

                <div data-reveal>
                    <span class="eyebrow"><span class="pulse"></span> Pendaftaran magang dibuka</span>

                    <h1>
                        @if ($banner?->judul)
                            {{ $banner->judul }}
                        @else
                            Mulai karier lewat magang di <span class="highlight">{{ $namaPerusahaan }}</span>
                        @endif
                    </h1>

                    <p class="hero-text">
                        @if ($banner?->deskripsi)
                            {{ $banner->deskripsi }}
                        @else
                            Daftar magang secara online, unggah surat pengantar, lalu pantau status pengajuanmu
                            kapan saja tanpa perlu datang langsung.
                        @endif
                    </p>

                    <div class="hero-actions">
                        <a href="{{ $banner?->tombol_link ?: '#daftar' }}" class="btn btn-accent">
                            {{ $banner?->tombol_teks ?: 'Daftar Sekarang' }} <span class="arrow">→</span>
                        </a>
                        <a href="#status" class="btn btn-ghost">Cek Status Pengajuan</a>
                    </div>

                    <ul class="hero-points">
                        <li>100% online</li>
                        <li>Proses ±5 menit</li>
                        <li>Cek status kapan saja</li>
                    </ul>
                </div>

                <div class="hero-visual" data-reveal style="--delay: .15s">
                    <div class="hero-frame">
                        @if ($banner?->gambar)
                            <img src="{{ asset('storage/' . $banner->gambar) }}" alt="{{ $banner->judul ?: 'Banner ' . $namaPerusahaan }}">
                        @else
                            <div class="hero-placeholder">
                                <div class="big-mark">M</div>
                                <strong>MAGENTA</strong>
                                <span>Sistem Informasi Magang</span>
                            </div>
                        @endif
                    </div>

                    <div class="float-card float-card-1">
                        <span class="icon">✓</span>
                        <div>
                            @if ($statistik['peserta_aktif'] > 0)
                                <strong>{{ $statistik['peserta_aktif'] }} peserta</strong>
                                <small>sedang magang saat ini</small>
                            @else
                                <strong>Daftar online</strong>
                                <small>tanpa antre & berkas fisik</small>
                            @endif
                        </div>
                    </div>

                    <div class="float-card float-card-2">
                        <span class="icon">★</span>
                        <div>
                            @if ($statistik['alumni'] > 0)
                                <strong>{{ $statistik['alumni'] }} alumni</strong>
                                <small>telah menyelesaikan magang</small>
                            @else
                                <strong>Status real-time</strong>
                                <small>pantau hasil verifikasi</small>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </section>

        {{-- =================================================
             HIGHLIGHT
        ================================================== --}}
        <section class="highlights" aria-label="Akses cepat">
            <div class="container highlight-grid">

                <article class="card highlight-card" data-reveal>
                    <div class="icon-box">📋</div>
                    <h3>{{ $persyaratanJudul }}</h3>
                    <p>{{ $persyaratanDeskripsi }}</p>
                    <a href="#persyaratan" class="text-link">Lihat persyaratan <span>→</span></a>
                </article>

                <article class="card highlight-card" data-reveal style="--delay: .1s">
                    <div class="icon-box">📝</div>
                    <h3>Pendaftaran Online</h3>
                    <p>Isi formulir 3 langkah langsung di website{{ $googleFormUrl ? ' atau lewat Google Form' : '' }}.</p>
                    <a href="#daftar" class="text-link">Daftar sekarang <span>→</span></a>
                </article>

                <article class="card highlight-card" data-reveal style="--delay: .2s">
                    <div class="icon-box">🔎</div>
                    <h3>Cek Status</h3>
                    <p>Pantau hasil verifikasi dan catatan admin dengan kode pengajuan dan email.</p>
                    <a href="#status" class="text-link">Cek status <span>→</span></a>
                </article>

            </div>
        </section>

        {{-- =================================================
             PENGUMUMAN
        ================================================== --}}
        <section id="pengumuman" class="section">
            <div class="container">

                <div class="section-heading" data-reveal>
                    <span class="section-label">{{ $informasiTerbaruJudul }}</span>
                    <h2>{{ $pengumumanJudul }}</h2>
                    <p>{{ $informasiTerbaruDeskripsi }}</p>
                </div>

                <div class="announcement-grid">
                    <article class="announcement-main" data-reveal>
                        <span class="badge-soft">📢 PENGUMUMAN</span>
                        <h3>{{ $pengumumanJudul }}</h3>
                        <p>{{ $pengumumanDeskripsi }}</p>
                    </article>

                    <div class="announcement-list">
                        @foreach ($pengumumanItems as $index => [$judul, $deskripsi])
                            <article class="card announcement-item" data-reveal style="--delay: {{ ($index + 1) * 0.1 }}s">
                                <span class="num">{{ $index + 1 }}</span>
                                <div>
                                    <h4>{{ $judul }}</h4>
                                    <p>{{ $deskripsi }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

            </div>
        </section>

        {{-- =================================================
             PERSYARATAN
        ================================================== --}}
        <section id="persyaratan" class="section section-light">
            <div class="container">

                <div class="section-heading" data-reveal>
                    <span class="section-label">Sebelum Mendaftar</span>
                    <h2>{{ $persyaratanJudul }}</h2>
                    <p>{{ $persyaratanDeskripsi }}</p>
                </div>

                <div class="req-grid">
                    @foreach ($persyaratanItems as $index => [$ikon, $judul, $deskripsi])
                        <article class="card req-card" data-reveal style="--delay: {{ $index * 0.1 }}s">
                            <span class="step">0{{ $index + 1 }}</span>
                            <div class="icon-box">{{ $ikon }}</div>
                            <h3>{{ $judul }}</h3>
                            <p>{{ $deskripsi }}</p>
                        </article>
                    @endforeach
                </div>

            </div>
        </section>

        {{-- =================================================
             ALUR
        ================================================== --}}
        <section id="alur" class="section">
            <div class="container">

                <div class="section-heading" data-reveal>
                    <span class="section-label">Proses Magang</span>
                    <h2>{{ $alurJudul }}</h2>
                    <p>{{ $alurDeskripsi }}</p>
                </div>

                <ol class="timeline" data-reveal>
                    @foreach ($alurItems as $index => [$judul, $deskripsi])
                        <li>
                            <span class="dot">{{ $index + 1 }}</span>
                            <h3>{{ $judul }}</h3>
                            <p>{{ $deskripsi }}</p>
                        </li>
                    @endforeach
                </ol>

            </div>
        </section>

        {{-- =================================================
             PENDAFTARAN & CEK STATUS
        ================================================== --}}
        @include('website.partials.pendaftaran')

        @include('website.partials.cek-status')

        {{-- =================================================
             KONTAK
        ================================================== --}}
        <section id="kontak" class="section">
            <div class="container">

                <div class="contact-grid">
                    <article class="card about-card" data-reveal>
                        <span class="section-label">Tentang Kami</span>
                        <h2>{{ $namaPerusahaan }}</h2>
                        <p>
                            {{ $perusahaan?->deskripsi ?: 'Kami membuka kesempatan magang bagi mahasiswa untuk belajar langsung di dunia kerja, mendapatkan pengalaman nyata, dan bimbingan dari tim profesional.' }}
                        </p>
                    </article>

                    <div class="contact-list">
                        @forelse ($kontakItems as $index => [$ikon, $label, $nilai, $tautan])
                            <div class="card contact-item" data-reveal style="--delay: {{ $index * 0.08 }}s">
                                <span class="icon-box">{{ $ikon }}</span>
                                <div>
                                    <small>{{ $label }}</small>
                                    @if ($tautan)
                                        <a href="{{ $tautan }}" @if (str_starts_with($tautan, 'http')) target="_blank" rel="noopener noreferrer" @endif>
                                            <span>{{ $nilai }}</span>
                                        </a>
                                    @else
                                        <span>{{ $nilai }}</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="card contact-item" data-reveal>
                                <span class="icon-box">ℹ️</span>
                                <div>
                                    <small>Kontak</small>
                                    <span>Informasi kontak belum tersedia.</span>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="cta-band" data-reveal style="margin-top: 60px;">
                    <div>
                        <h2>Siap memulai pengalaman magangmu?</h2>
                        <p>Daftar sekarang dan dapatkan kabar hasil verifikasi secepatnya.</p>
                    </div>
                    <a href="#daftar" class="btn btn-accent">Daftar Magang <span class="arrow">→</span></a>
                </div>

            </div>
        </section>

    </main>

    {{-- =====================================================
         FOOTER
    ====================================================== --}}
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div>
                    <a href="#beranda" class="logo">
                        <span class="logo-mark">M</span>
                        MAGENTA
                    </a>
                    <p>Sistem Informasi Magang {{ $namaPerusahaan }}: pendaftaran, verifikasi, dan informasi magang dalam satu tempat.</p>
                </div>

                <div>
                    <h4>Navigasi</h4>
                    <ul class="footer-links">
                        @foreach (array_slice($menu, 1, 4, true) as $href => $label)
                            <li><a href="{{ $href }}">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h4>Pendaftar</h4>
                    <ul class="footer-links">
                        <li><a href="#daftar">Form Pendaftaran</a></li>
                        <li><a href="#status">Cek Status</a></li>
                        @if ($googleFormUrl)
                            <li><a href="{{ $googleFormUrl }}" target="_blank" rel="noopener noreferrer">Google Form ↗</a></li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                © {{ date('Y') }} {{ $namaPerusahaan }}. Hak cipta dilindungi.
            </div>
        </div>
    </footer>

    <button type="button" class="to-top" aria-label="Kembali ke atas">↑</button>

    @include('website.partials.scripts')

</body>

</html>
