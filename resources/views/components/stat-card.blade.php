{{--
    Kartu statistik.

    Contoh:
    <x-stat-card label="Peserta Aktif" :value="$jumlahAktif" icon="♙" hint="Periode berjalan" />
--}}
@props([
    'label',
    'value',
    'icon' => null,
    'hint' => null,
])

<div {{ $attributes->class(['x-stat-card']) }}>

    @if ($icon)
        <div class="x-stat-icon">{{ $icon }}</div>
    @endif

    <div>
        <div class="x-stat-label">{{ $label }}</div>
        <div class="x-stat-value">{{ $value }}</div>

        @if ($hint)
            <div class="x-stat-hint">{{ $hint }}</div>
        @endif
    </div>

</div>
