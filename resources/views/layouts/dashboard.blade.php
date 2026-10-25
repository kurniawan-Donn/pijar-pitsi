{{-- resources/views/layouts/dashboard.blade.php --}}
{{--
    Layout bersama halaman Admin & Super Admin.

    Section yang tersedia:
    - title          : judul tab browser (tanpa akhiran "- MAGENTA")
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

    <title>@yield('title', 'Dashboard') - MAGENTA</title>

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

    <main class="dash-main">

        {{-- TOPBAR --}}
        <header class="dash-topbar">

            <div class="dash-topbar-left">
                <h1>@yield('page_title', 'Dashboard')</h1>

                @hasSection('page_subtitle')
                    <p>@yield('page_subtitle')</p>
                @endif
            </div>

            <a href="{{ route('profil.edit') }}" class="dash-user-info" title="Profil & password">

                <x-avatar :user="$authUser" class="dash-user-avatar" />

                <div class="dash-user-text">
                    <div class="dash-user-name">
                        {{ $authUser?->name ?? 'Administrator' }}
                    </div>

                    <div class="dash-user-role">
                        {{ $roleLabel }} · Profil
                    </div>
                </div>

            </a>

        </header>

        @yield('content')

    </main>

</div>

@stack('scripts')

</body>

</html>
