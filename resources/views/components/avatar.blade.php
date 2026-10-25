{{--
    Foto profil bulat, atau inisial nama bila belum ada foto.

    Contoh:
    <x-avatar :user="auth()->user()" />
    <x-avatar :user="$peserta->user" :nama="$peserta->nama" :ukuran="32" />
--}}
@props([
    'user' => null,
    'nama' => null,
    'ukuran' => 40,
])

@php
    $nama = $nama ?? $user?->name ?? '';
    $url = $user?->urlFoto();
    $inisial = mb_strtoupper(mb_substr(trim($nama) ?: 'A', 0, 1));
@endphp

@if ($url)
    <img
        src="{{ $url }}"
        alt="Foto {{ $nama }}"
        width="{{ $ukuran }}"
        height="{{ $ukuran }}"
        loading="lazy"
        {{ $attributes->class(['x-avatar'])->merge(['style' => "--avatar: {$ukuran}px"]) }}
    >
@else
    <span {{ $attributes->class(['x-avatar', 'x-avatar-inisial'])->merge(['style' => "--avatar: {$ukuran}px"]) }} aria-hidden="true">{{ $inisial }}</span>
@endif
