{{-- resources/views/layouts/dashboard.blade.php --}}
{{--
    Layout bersama halaman Admin, Super Admin, dan Peserta.

    Section yang tersedia:
    - title          : judul tab browser (tanpa akhiran nama aplikasi)
    - page_title     : judul di topbar
    - page_subtitle  : keterangan di bawah judul topbar
    - content        : isi halaman

    Stack yang tersedia:
    - styles  : CSS khusus halaman
    - scripts : JavaScript khusus halaman
--}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="var(--utama-gelap)">

    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-pitsi-128.png') }}">

    @include('partials.font')
    @include('layouts.partials.dashboard-styles')

    @stack('styles')
</head>

<body>

@php
    $authUser = auth()->user();
    $roleLabel = $authUser?->labelRole() ?? 'Admin';
@endphp

<div class="dash-layout">

    @include('layouts.partials.dashboard-sidebar')

    <div class="dash-overlay" data-tutup-menu></div>

    <main class="dash-main">

        {{-- TOPBAR --}}
        <header class="dash-topbar">

            <button type="button" class="dash-menu-toggle" aria-label="Buka menu" aria-controls="dash-sidebar" aria-expanded="false" data-buka-menu>
                <x-icon name="menu" :size="20" />
            </button>

            <div class="dash-topbar-left">
                <h1>@yield('page_title', 'Dashboard')</h1>

                @hasSection('page_subtitle')
                    <p>@yield('page_subtitle')</p>
                @endif
            </div>

            <a href="{{ route('profil.edit') }}" class="dash-user-info" title="Profil & password">

                <x-avatar :user="$authUser" :ukuran="36" class="dash-user-avatar" />

                <div class="dash-user-text">
                    <div class="dash-user-name">
                        {{ $authUser?->name ?? 'Administrator' }}
                    </div>

                    <div class="dash-user-role">
                        {{ $roleLabel }}
                    </div>
                </div>

            </a>

        </header>

        @yield('content')

    </main>

</div>

@include('layouts.partials.dashboard-scripts')

@stack('scripts')

</body>

</html>
