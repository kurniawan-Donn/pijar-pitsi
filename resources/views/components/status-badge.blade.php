{{--
    Badge status pengajuan / peserta.

    Contoh:
    <x-status-badge :status="$pengajuan->status" :label="$pengajuan->statusLabel()" />
--}}
@props([
    'status',
    'label' => null,
])

<span {{ $attributes->class(['x-badge', 'x-badge-' . $status]) }}>
    {{ $label ?? ucfirst($status) }}
</span>
