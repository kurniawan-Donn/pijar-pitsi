{{--
    Kartu statistik.

    Contoh:
    <x-stat-card label="Peserta Aktif" :value="$jumlahAktif" icon="users" hint="Periode berjalan" />

    "icon" adalah nama ikon <x-icon>.
--}}
@props([
    'label',
    'value',
    'icon' => null,
    'hint' => null,
])

<div {{ $attributes->class(['x-stat-card']) }}>

    @if ($icon)
        <div class="x-stat-icon"><x-icon :name="$icon" :size="20" /></div>
    @endif

    <div>
        <div class="x-stat-label">{{ $label }}</div>
        <div class="x-stat-value">{{ $value }}</div>

        @if ($hint)
            <div class="x-stat-hint">{{ $hint }}</div>
        @endif
    </div>

</div>
