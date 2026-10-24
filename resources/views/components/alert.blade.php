{{--
    Alert pesan.

    Contoh:
    <x-alert type="success" :message="session('success')" />
    <x-alert type="error">Isi bebas di slot</x-alert>
    <x-alert type="error" :messages="$errors->all()" title="Periksa kembali isian form:" />
--}}
@props([
    'type' => 'info',
    'message' => null,
    'messages' => [],
    'title' => null,
])

@php
    $icons = [
        'success' => '✓',
        'error' => '!',
        'warning' => '!',
        'info' => 'i',
    ];
@endphp

@if ($message || count($messages) || $slot->isNotEmpty())
    <div {{ $attributes->class(['x-alert', 'x-alert-' . $type]) }} role="alert">

        <span class="x-alert-icon">{{ $icons[$type] ?? 'i' }}</span>

        <div>
            @if ($title)
                <strong>{{ $title }}</strong>
            @endif

            @if ($message)
                <div>{{ $message }}</div>
            @endif

            @if (count($messages))
                <ul>
                    @foreach ($messages as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            @endif

            {{ $slot }}
        </div>

    </div>
@endif
