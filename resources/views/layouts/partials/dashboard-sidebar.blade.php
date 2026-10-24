{{--
    Sidebar dashboard. Menu ditampilkan sesuai role pengguna yang login.
    Item tanpa "can" tampil untuk semua pengguna login; "can" berisi nama Gate
    (lihat AppServiceProvider::configureGates()).
--}}
@php
    $routeDashboard = auth()->user()?->routeDashboard() ?? 'admin.dashboard';

    // Peserta magang punya menu sendiri (bertambah di tahap biodata & logbook).
    $menuSections = auth()->user()?->isPeserta() ? [
        'Magang Saya' => [
            [
                'label' => 'Dashboard',
                'icon' => '⌂',
                'route' => 'peserta.dashboard',
                'active' => ['peserta.dashboard'],
            ],
            [
                'label' => 'Biodata',
                'icon' => '☷',
                'route' => 'peserta.biodata.edit',
                'active' => ['peserta.biodata.*'],
            ],
            [
                'label' => 'Profil & Password',
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
                'label' => 'Pengajuan Magang',
                'icon' => '✉',
                'route' => 'admin.pengajuan.index',
                'active' => ['admin.pengajuan.*'],
                'badge' => \App\Models\PengajuanMagang::where('status', \App\Models\PengajuanMagang::STATUS_MENUNGGU)->count(),
            ],
            [
                'label' => 'Laporan',
                'icon' => '▥',
                'route' => 'superadmin.laporan.index',
                'active' => ['superadmin.laporan.*'],
            ],
        ],
        'Sistem' => [
            [
                'label' => 'Profil Perusahaan',
                'icon' => '▤',
                'route' => 'superadmin.perusahaan.index',
                'active' => ['superadmin.perusahaan.*'],
            ],
            [
                'label' => 'Kelola Banner',
                'icon' => '▣',
                'route' => 'superadmin.banners.index',
                'active' => ['superadmin.banners.*'],
            ],
            [
                'label' => 'Kelola Konten',
                'icon' => '⚙',
                'route' => 'superadmin.pengaturan.index',
                'active' => ['superadmin.pengaturan.*'],
            ],
            [
                'label' => 'Data Peserta',
                'icon' => '♙',
                'route' => 'superadmin.peserta-magang.index',
                'active' => ['superadmin.peserta-magang.*'],
            ],
            [
                'label' => 'Pengaturan Magang',
                'icon' => '⚑',
                'route' => 'superadmin.pengaturan-magang.index',
                'active' => ['superadmin.pengaturan-magang.*'],
            ],
            [
                'label' => 'Divisi',
                'icon' => '◫',
                'route' => 'superadmin.divisi.index',
                'active' => ['superadmin.divisi.*'],
            ],
            [
                'label' => 'Aspek Penilaian',
                'icon' => '★',
                'route' => 'superadmin.aspek-penilaian.index',
                'active' => ['superadmin.aspek-penilaian.*'],
                'can' => 'kelola-aspek-penilaian',
            ],
            [
                'label' => 'Log Aktivitas',
                'icon' => '☰',
                'route' => 'superadmin.log-aktivitas.index',
                'active' => ['superadmin.log-aktivitas.*'],
                'can' => 'lihat-log-aktivitas',
            ],
            [
                'label' => 'Kelola Pengguna',
                'icon' => '♟',
                'route' => 'superadmin.pengguna.index',
                'active' => ['superadmin.pengguna.*'],
                'can' => 'kelola-pengguna',
            ],
        ],
    ];
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
                    @continue(! empty($item['can']) && auth()->user()?->cannot($item['can']))

                    <a
                        href="{{ route($item['route']) }}"
                        class="dash-sidebar-link {{ request()->routeIs(...$item['active']) ? 'active' : '' }}"
                    >
                        <span class="dash-sidebar-icon">{{ $item['icon'] }}</span>
                        <span>{{ $item['label'] }}</span>

                        @if (! empty($item['badge']))
                            <span class="dash-sidebar-badge" title="Pengajuan menunggu diproses">{{ $item['badge'] }}</span>
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
