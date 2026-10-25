{{--
    Sidebar dashboard. Menu ditampilkan sesuai role pengguna yang login.
    Item tanpa "can" tampil untuk semua pengguna login; "can" berisi nama Gate
    (lihat AppServiceProvider::configureGates()), "cannot" kebalikannya.
    Grup yang semua itemnya tersembunyi tidak ditampilkan.
--}}
@php
    $routeDashboard = auth()->user()?->routeDashboard() ?? 'admin.dashboard';

    $menuSections = auth()->user()?->isPeserta() ? [
        'Utama' => [
            [
                'label' => 'Dashboard',
                'icon' => '⌂',
                'route' => 'peserta.dashboard',
                'active' => ['peserta.dashboard'],
            ],
        ],
        'Data Saya' => [
            [
                'label' => 'Biodata',
                'icon' => '☷',
                'route' => 'peserta.biodata.edit',
                'active' => ['peserta.biodata.*'],
            ],
        ],
        'Kegiatan' => [
            [
                'label' => 'Logbook Harian',
                'icon' => '📒',
                'route' => 'peserta.logbook.index',
                'active' => ['peserta.logbook.*'],
            ],
            [
                'label' => 'Ajukan Izin / Sakit',
                'icon' => '✚',
                'route' => 'peserta.izin.create',
                'active' => ['peserta.izin.*'],
            ],
            [
                'label' => 'Rekap Kehadiran',
                'icon' => '▥',
                'route' => 'peserta.rekap',
                'active' => ['peserta.rekap'],
            ],
        ],
        'Akhir Magang' => [
            [
                // Tampil terkunci (🔒) sampai sebulan sebelum magang selesai.
                'label' => \App\Models\FeedbackPeserta::sudahDibuka(auth()->user()->pesertaMagang ?? new \App\Models\PesertaMagang) ? 'Feedback' : 'Feedback 🔒',
                'icon' => '✎',
                'route' => 'peserta.feedback.edit',
                'active' => ['peserta.feedback.*'],
            ],
            [
                'label' => 'Surat Keterangan',
                'icon' => '✉',
                'route' => 'peserta.surat.show',
                'active' => ['peserta.surat.*'],
            ],
        ],
        'Akun' => [
            [
                'label' => 'Profil',
                'icon' => '♙',
                'route' => 'profil.edit',
                'active' => ['profil.*'],
            ],
        ],
    ] : [
        'Utama' => [
            [
                'label' => 'Dashboard',
                'icon' => '⌂',
                'route' => $routeDashboard,
                'active' => ['superadmin.dashboard', 'admin.dashboard'],
            ],
            [
                'label' => 'Monitoring',
                'icon' => '◉',
                'route' => 'superadmin.monitoring.index',
                'active' => ['superadmin.monitoring.*'],
            ],
        ],
        'Kelola Peserta' => [
            [
                'label' => 'Pengajuan',
                'icon' => '✉',
                'route' => 'admin.pengajuan.index',
                'active' => ['admin.pengajuan.*'],
                'badge' => \App\Models\PengajuanMagang::where('status', \App\Models\PengajuanMagang::STATUS_MENUNGGU)->count(),
                'badge_title' => 'Pengajuan menunggu diproses',
            ],
            [
                'label' => 'Data Peserta',
                'icon' => '♙',
                'route' => 'superadmin.peserta-magang.index',
                'active' => [
                    'superadmin.peserta-magang.index', 'superadmin.peserta-magang.create', 'superadmin.peserta-magang.store',
                    'superadmin.peserta-magang.show', 'superadmin.peserta-magang.edit', 'superadmin.peserta-magang.update',
                    'superadmin.peserta-magang.akun.*', 'superadmin.peserta-magang.penempatan.*',
                ],
            ],
            [
                'label' => 'Biodata',
                'icon' => '☷',
                'route' => 'superadmin.biodata.index',
                'active' => ['superadmin.biodata.*', 'superadmin.peserta-magang.biodata', 'superadmin.peserta-magang.biodata.*'],
                'badge' => \App\Models\BiodataPeserta::where('status', \App\Models\BiodataPeserta::STATUS_DIAJUKAN)->count(),
                'badge_title' => 'Biodata menunggu tinjauan',
            ],
            [
                'label' => 'Logbook & Absensi',
                'icon' => '📒',
                'route' => 'superadmin.logbook.index',
                'active' => [
                    'superadmin.logbook.*', 'superadmin.peserta-magang.logbook-harian', 'superadmin.peserta-magang.logbook-harian.*',
                    'superadmin.peserta-magang.izin.*', 'superadmin.peserta-magang.impor-logbook.*',
                ],
            ],
            [
                'label' => 'Penilaian',
                'icon' => '★',
                'route' => 'superadmin.penilaian.index',
                'active' => ['superadmin.penilaian.*', 'superadmin.peserta-magang.penilaian', 'superadmin.peserta-magang.penilaian.*'],
            ],
            [
                'label' => 'Feedback',
                'icon' => '✎',
                'route' => 'superadmin.feedback.index',
                'active' => ['superadmin.feedback.*', 'superadmin.peserta-magang.feedback'],
            ],
            [
                'label' => 'Surat Keterangan',
                'icon' => '✉',
                'route' => 'superadmin.surat.index',
                'active' => ['superadmin.surat.*', 'superadmin.peserta-magang.surat', 'superadmin.peserta-magang.surat.*'],
            ],
            [
                'label' => 'Divisi',
                'icon' => '◫',
                'route' => 'superadmin.divisi.index',
                'active' => ['superadmin.divisi.*'],
            ],
            [
                // Admin hanya mengelola hari libur; pengaturan lain ada di grup Sistem.
                'label' => 'Hari Libur',
                'icon' => '⚑',
                'route' => 'superadmin.pengaturan-magang.index',
                'anchor' => 'libur',
                'active' => ['superadmin.pengaturan-magang.*'],
                'cannot' => 'kelola-pengaturan-magang',
            ],
        ],
        'Laporan' => [
            [
                'label' => 'Rekap Uang Saku',
                'icon' => '₨',
                'route' => 'superadmin.laporan.index',
                'parameter' => ['jenis' => 'absensi'],
                'active' => request('jenis') === 'absensi' ? ['superadmin.laporan.*'] : [],
            ],
            [
                'label' => 'Laporan & Export',
                'icon' => '▥',
                'route' => 'superadmin.laporan.index',
                'active' => request('jenis') === 'absensi' ? [] : ['superadmin.laporan.*'],
            ],
        ],
        'Konten Website' => [
            [
                'label' => 'Profil Perusahaan',
                'icon' => '▤',
                'route' => 'superadmin.perusahaan.index',
                'active' => ['superadmin.perusahaan.*'],
            ],
            [
                'label' => 'Banner',
                'icon' => '▣',
                'route' => 'superadmin.banners.index',
                'active' => ['superadmin.banners.*'],
            ],
            [
                'label' => 'Konten',
                'icon' => '⚙',
                'route' => 'superadmin.pengaturan.index',
                'active' => ['superadmin.pengaturan.*'],
            ],
            [
                'label' => 'Alumni',
                'icon' => '🎓',
                'route' => 'superadmin.alumni.index',
                'active' => ['superadmin.alumni.*'],
            ],
            [
                'label' => 'FAQ',
                'icon' => '?',
                'route' => 'superadmin.faq.index',
                'active' => ['superadmin.faq.*'],
            ],
        ],
        'Sistem' => [
            [
                'label' => 'Kelola Pengguna',
                'icon' => '♟',
                'route' => 'superadmin.pengguna.index',
                'active' => ['superadmin.pengguna.*'],
                'can' => 'kelola-pengguna',
            ],
            [
                'label' => 'Aspek Penilaian',
                'icon' => '★',
                'route' => 'superadmin.aspek-penilaian.index',
                'active' => ['superadmin.aspek-penilaian.*'],
                'can' => 'kelola-aspek-penilaian',
            ],
            [
                'label' => 'Pengaturan',
                'icon' => '⚑',
                'route' => 'superadmin.pengaturan-magang.index',
                'active' => ['superadmin.pengaturan-magang.*'],
                'can' => 'kelola-pengaturan-magang',
            ],
            [
                'label' => 'Log Aktivitas',
                'icon' => '☰',
                'route' => 'superadmin.log-aktivitas.index',
                'active' => ['superadmin.log-aktivitas.*'],
                'can' => 'lihat-log-aktivitas',
            ],
        ],
    ];

    $menuSections = collect($menuSections)
        ->map(fn (array $items) => array_filter($items, fn (array $item) => (empty($item['can']) || auth()->user()?->can($item['can']))
            && (empty($item['cannot']) || auth()->user()?->cannot($item['cannot']))))
        ->filter();
@endphp

<aside class="dash-sidebar">

    {{-- BRAND --}}
    <a href="{{ route($routeDashboard) }}" class="dash-brand">

        <div class="dash-brand-logo">M</div>

        <div class="dash-brand-text">
            <div class="dash-brand-title">MAGENTA</div>
            <div class="dash-brand-subtitle">Sistem Informasi Magang</div>
        </div>

    </a>

    {{-- MENU --}}
    @foreach ($menuSections as $sectionTitle => $menuItems)
        <div class="dash-sidebar-section">

            <div class="dash-sidebar-section-title">{{ $sectionTitle }}</div>

            <nav class="dash-sidebar-menu">
                @foreach ($menuItems as $item)
                    <a
                        href="{{ route($item['route'], $item['parameter'] ?? []) }}{{ isset($item['anchor']) ? '#'.$item['anchor'] : '' }}"
                        class="dash-sidebar-link {{ $item['active'] && request()->routeIs(...$item['active']) ? 'active' : '' }}"
                    >
                        <span class="dash-sidebar-icon">{{ $item['icon'] }}</span>
                        <span>{{ $item['label'] }}</span>

                        @if (! empty($item['badge']))
                            <span class="dash-sidebar-badge" title="{{ $item['badge_title'] ?? '' }}">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>

        </div>
    @endforeach

    {{-- LOGOUT --}}
    <div class="dash-sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button type="submit" class="dash-logout-button">
                <span class="dash-sidebar-icon">↪</span>
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>
