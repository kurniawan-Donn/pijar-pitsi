{{--
    Tombol. Bila "href" diisi, dirender sebagai tautan.

    Contoh:
    <x-button>Simpan</x-button>
    <x-button variant="secondary" :href="route('superadmin.pengguna.index')">Kembali</x-button>
    <x-button variant="danger" onclick="return confirm('Hapus data ini?')">Hapus</x-button>
--}}
@props([
    'variant' => 'primary',
    'type' => 'submit',
    'href' => null,
])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class(['x-btn', 'x-btn-' . $variant]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class(['x-btn', 'x-btn-' . $variant]) }}>
        {{ $slot }}
    </button>
@endif
