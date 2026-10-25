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
                'icon' => 'layout-dashboard',
                'route' => 'peserta.dashboard',
                'active' => ['peserta.dashboard'],
            ],
        ],
        'Data Saya' => [
            [
                'label' => 'Biodata',
                'icon' => 'id-card',
                'route' => 'peserta.biodata.edit',
                'active' => ['peserta.biodata.*'],
            ],
        ],
        'Kegiatan' => [
            [
                'label' => 'Logbook Harian',
                'icon' => 'clipboard-list',
                'route' => 'peserta.logbook.index',
                'active' => ['peserta.logbook.*'],
            ],
            [
                'label' => 'Ajukan Izin / Sakit',
                'icon' => 'calendar-plus',
                'route' => 'peserta.izin.create',
                'active' => ['peserta.izin.*'],
            ],
            [
                'label' => 'Rekap Kehadiran',
                'icon' => 'chart-column',
                'route' => 'peserta.rekap',
                'active' => ['peserta.rekap'],
            ],
        ],
        'Akhir Magang' => [
            [
                // Tampil dengan ikon gembok sampai sebulan sebelum magang selesai.
                'label' => 'Feedback',
                'icon' => 'message-square',
                'terkunci' => ! \App\Models\FeedbackPeserta::sudahDibuka(auth()->user()->pesertaMagang ?? new \App\Models\PesertaMagang),
                'route' => 'peserta.feedback.edit',
                'active' => ['peserta.feedback.*'],
            ],
            [
                'label' => 'Surat Keterangan',
                'icon' => 'file-check',
                'route' => 'peserta.surat.show',
                'active' => ['peserta.surat.*'],
            ],
        ],
        'Akun' => [
            [
                'label' => 'Profil',
                'icon' => 'user',
                'route' => 'profil.edit',
                'active' => ['profil.*'],
            ],
        ],
    ] : [
        'Utama' => [
            [
                'label' => 'Dashboard',
                'icon' => 'layout-dashboard',
                'route' => $routeDashboard,
                'active' => ['superadmin.dashboard', 'admin.dashboard'],
            ],
            [
                'label' => 'Monitoring',
                'icon' => 'activity',
                'route' => 'superadmin.monitoring.index',
                'active' => ['superadmin.monitoring.*'],
            ],
        ],
        'Kelola Peserta' => [
            [
                'label' => 'Pengajuan',
                'icon' => 'inbox',
                'route' => 'admin.pengajuan.index',
                'active' => ['admin.pengajuan.*'],
                'badge' => \App\Models\PengajuanMagang::where('status', \App\Models\PengajuanMagang::STATUS_MENUNGGU)->count(),
                'badge_title' => 'Pengajuan menunggu diproses',
            ],
            [
                'label' => 'Data Peserta',
                'icon' => 'users',
                'route' => 'superadmin.peserta-magang.index',
                'active' => [
                    'superadmin.peserta-magang.index', 'superadmin.peserta-magang.create', 'superadmin.peserta-magang.store',
                    'superadmin.peserta-magang.show', 'superadmin.peserta-magang.edit', 'superadmin.peserta-magang.update',
                    'superadmin.peserta-magang.akun.*', 'superadmin.peserta-magang.penempatan.*',
                ],
            ],
            [
                'label' => 'Biodata',
                'icon' => 'id-card',
                'route' => 'superadmin.biodata.index',
                'active' => ['superadmin.biodata.*', 'superadmin.peserta-magang.biodata', 'superadmin.peserta-magang.biodata.*'],
                'badge' => \App\Models\BiodataPeserta::where('status', \App\Models\BiodataPeserta::STATUS_DIAJUKAN)->count(),
                'badge_title' => 'Biodata menunggu tinjauan',
            ],
            [
                'label' => 'Logbook & Absensi',
                'icon' => 'clipboard-list',
                'route' => 'superadmin.logbook.index',
                'active' => [
                    'superadmin.logbook.*', 'superadmin.peserta-magang.logbook-harian', 'superadmin.peserta-magang.logbook-harian.*',
                    'superadmin.peserta-magang.izin.*', 'superadmin.peserta-magang.impor-logbook.*',
                ],
            ],
            [
                'label' => 'Penilaian',
                'icon' => 'award',
                'route' => 'superadmin.penilaian.index',
                'active' => ['superadmin.penilaian.*', 'superadmin.peserta-magang.penilaian', 'superadmin.peserta-magang.penilaian.*'],
            ],
            [
                'label' => 'Feedback',
                'icon' => 'message-square',
                'route' => 'superadmin.feedback.index',
                'active' => ['superadmin.feedback.*', 'superadmin.peserta-magang.feedback'],
            ],
            [
                'label' => 'Surat Keterangan',
                'icon' => 'file-check',
                'route' => 'superadmin.surat.index',
                'active' => ['superadmin.surat.*', 'superadmin.peserta-magang.surat', 'superadmin.peserta-magang.surat.*'],
            ],
            [
                'label' => 'Divisi',
                'icon' => 'network',
                'route' => 'superadmin.divisi.index',
                'active' => ['superadmin.divisi.*'],
            ],
            [
                // Admin hanya mengelola hari libur; pengaturan lain ada di grup Sistem.
                'label' => 'Hari Libur',
                'icon' => 'calendar-x',
                'route' => 'superadmin.pengaturan-magang.index',
                'anchor' => 'libur',
                'active' => ['superadmin.pengaturan-magang.*'],
                'cannot' => 'kelola-pengaturan-magang',
            ],
        ],
        'Laporan' => [
            [
                'label' => 'Rekap Uang Saku',
                'icon' => 'wallet',
                'route' => 'superadmin.laporan.index',
                'parameter' => ['jenis' => 'absensi'],
                'active' => request('jenis') === 'absensi' ? ['superadmin.laporan.*'] : [],
            ],
            [
                'label' => 'Laporan & Export',
                'icon' => 'file-spreadsheet',
                'route' => 'superadmin.laporan.index',
                'active' => request('jenis') === 'absensi' ? [] : ['superadmin.laporan.*'],
            ],
        ],
        'Konten Website' => [
            [
                'label' => 'Profil Perusahaan',
                'icon' => 'building',
                'route' => 'superadmin.perusahaan.index',
                'active' => ['superadmin.perusahaan.*'],
            ],
            [
                'label' => 'Banner',
                'icon' => 'image',
                'route' => 'superadmin.banners.index',
                'active' => ['superadmin.banners.*'],
            ],
            [
                'label' => 'Konten',
                'icon' => 'layout-template',
                'route' => 'superadmin.pengaturan.index',
                'active' => ['superadmin.pengaturan.*'],
            ],
            [
                'label' => 'Alumni',
                'icon' => 'graduation-cap',
                'route' => 'superadmin.alumni.index',
                'active' => ['superadmin.alumni.*'],
            ],
            [
                'label' => 'Dokumentasi',
                'icon' => 'images',
                'route' => 'superadmin.dokumentasi.index',
                'active' => ['superadmin.dokumentasi.*'],
            ],
            [
                'label' => 'FAQ',
                'icon' => 'circle-help',
                'route' => 'superadmin.faq.index',
                'active' => ['superadmin.faq.*'],
            ],
        ],
        'Sistem' => [
            [
                'label' => 'Kelola Pengguna',
                'icon' => 'user-cog',
                'route' => 'superadmin.pengguna.index',
                'active' => ['superadmin.pengguna.*'],
                'can' => 'kelola-pengguna',
            ],
            [
                'label' => 'Aspek Penilaian',
                'icon' => 'list-checks',
                'route' => 'superadmin.aspek-penilaian.index',
                'active' => ['superadmin.aspek-penilaian.*'],
                'can' => 'kelola-aspek-penilaian',
            ],
            [
                'label' => 'Pengaturan',
                'icon' => 'sliders',
                'route' => 'superadmin.pengaturan-magang.index',
                'active' => ['superadmin.pengaturan-magang.*'],
                'can' => 'kelola-pengaturan-magang',
            ],
            [
                'label' => 'Log Aktivitas',
                'icon' => 'history',
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

<aside class="dash-sidebar" id="dash-sidebar" aria-label="Menu utama">

    {{-- BRAND --}}
    <a href="{{ route($routeDashboard) }}" class="dash-brand">
        <img src="{{ asset('images/logo-pitsi-128.png') }}" alt="Logo PT Pilar Teknotama Sinergi" class="dash-brand-logo" width="38" height="38">

        <div class="dash-brand-text">
            <div class="dash-brand-title">{{ config('app.name') }}</div>
            <div class="dash-brand-subtitle">Tumbuh bersama, kokoh berkarya.</div>
        </div>
    </a>

    {{-- MENU --}}
    @foreach ($menuSections as $sectionTitle => $menuItems)
        <div class="dash-sidebar-section">

            <div class="dash-sidebar-section-title">{{ $sectionTitle }}</div>

            <nav class="dash-sidebar-menu" aria-label="{{ $sectionTitle }}">
                @foreach ($menuItems as $item)
                    @php
                        $aktif = $item['active'] && request()->routeIs(...$item['active']);
                    @endphp

                    <a
                        href="{{ route($item['route'], $item['parameter'] ?? []) }}{{ isset($item['anchor']) ? '#'.$item['anchor'] : '' }}"
                        @class(['dash-sidebar-link', 'active' => $aktif])
                        @if ($aktif) aria-current="page" @endif
                    >
                        <x-icon :name="$item['icon']" />
                        <span>{{ $item['label'] }}</span>

                        @if (! empty($item['terkunci']))
                            <x-icon name="lock" :size="14" class="terkunci" aria-label="Terkunci" />
                        @endif

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
                <x-icon name="log-out" />
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>
