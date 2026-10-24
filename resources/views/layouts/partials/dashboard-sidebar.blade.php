{{--
    Sidebar dashboard. Menu ditampilkan sesuai role pengguna yang login.
    Item "roles" kosong berarti tampil untuk semua role.
--}}
@php
    $isSuperAdmin = auth()->user()?->role === 'super_admin';

    $menuSections = [
        'Utama' => [
            [
                'label' => 'Dashboard',
                'icon' => '⌂',
                'route' => $isSuperAdmin ? 'superadmin.dashboard' : 'admin.dashboard',
                'active' => ['superadmin.dashboard', 'admin.dashboard'],
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
                'label' => 'Kelola Pengguna',
                'icon' => '♟',
                'route' => 'superadmin.pengguna.index',
                'active' => ['superadmin.pengguna.*'],
                'roles' => ['super_admin'],
            ],
        ],
    ];
@endphp

<aside class="dash-sidebar">

    {{-- BRAND --}}
    <a href="{{ route($isSuperAdmin ? 'superadmin.dashboard' : 'admin.dashboard') }}" class="dash-brand">

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
                    @continue(! empty($item['roles']) && ! in_array(auth()->user()?->role, $item['roles'], true))

                    <a
                        href="{{ route($item['route']) }}"
                        class="dash-sidebar-link {{ request()->routeIs(...$item['active']) ? 'active' : '' }}"
                    >
                        <span class="dash-sidebar-icon">{{ $item['icon'] }}</span>
                        <span>{{ $item['label'] }}</span>
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
