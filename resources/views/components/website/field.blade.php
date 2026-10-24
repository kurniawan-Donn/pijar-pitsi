{{--
    Field form website publik (label + input + hint + error).

    Contoh:
    <x-website.field name="nama" label="Nama Lengkap" required autocomplete="name" />
    <x-website.field name="keterangan" type="textarea" label="Motivasi" maxlength="1000" />
--}}
@props([
    'name',
    'label',
    'type' => 'text',
    'hint' => null,
    'required' => false,
    'full' => false,
])

@php
    $hasError = $errors->has($name);
@endphp

<div @class(['field', 'full' => $full, 'has-error' => $hasError])>

    <label for="{{ $name }}">
        {{ $label }}
        @if ($required)
            <span class="req" aria-hidden="true">*</span>
        @endif
    </label>

    @if ($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            @required($required)
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
            {{ $attributes }}
        >{{ old($name) }}</textarea>
    @else
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name) }}"
            @required($required)
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
            {{ $attributes }}
        >
    @endif

    {{ $slot }}

    @if ($hint)
        <div class="field-hint">{{ $hint }}</div>
    @endif

    @error($name)
        <div class="field-error" id="{{ $name }}-error">{{ $message }}</div>
    @enderror

</div>
