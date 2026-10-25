{{--
    Tampilan saat data kosong, seragam di semua halaman.

    Contoh:
    <x-empty title="Belum ada pengajuan" message="Pengajuan baru dari website akan muncul di sini." />
    <x-empty icon="search" title="Tidak ada hasil" message="Coba ubah kata kunci pencarian." />
--}}
@props([
    'title' => 'Belum ada data',
    'message' => null,
    'icon' => 'inbox',
])

<div {{ $attributes->class(['x-empty']) }}>
    <span class="x-empty-icon"><x-icon :name="$icon" :size="22" /></span>
    <div class="x-empty-title">{{ $title }}</div>

    @if ($message)
        <div>{{ $message }}</div>
    @endif

    {{ $slot }}
</div>
