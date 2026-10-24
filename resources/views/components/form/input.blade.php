{{--
    Input form beserta label dan pesan error validasi.

    Contoh:
    <x-form.input name="email" type="email" label="Email" required />
    <x-form.input name="nama" label="Nama" :value="$peserta->nama" hint="Sesuai KTM" />
    <x-form.input name="keterangan" type="textarea" label="Keterangan" rows="4" />
--}}
@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'hint' => null,
    'required' => false,
])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
    // Password tidak pernah diisi ulang.
    $currentValue = $type === 'password' ? null : old($name, $value);
@endphp

<div class="x-form-group">

    @if ($label)
        <label for="{{ $id }}" class="x-form-label">
            {{ $label }}
            @if ($required)
                <span class="x-form-required">*</span>
            @endif
        </label>
    @endif

    @if ($type === 'textarea')
        <textarea
            id="{{ $id }}"
            name="{{ $name }}"
            @required($required)
            {{ $attributes->except('id')->class(['x-form-control', 'is-invalid' => $hasError]) }}
        >{{ $currentValue }}</textarea>
    @else
        <input
            type="{{ $type }}"
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ $currentValue }}"
            @required($required)
            {{ $attributes->except('id')->class(['x-form-control', 'is-invalid' => $hasError]) }}
        >
    @endif

    @if ($hint)
        <div class="x-form-hint">{{ $hint }}</div>
    @endif

    @error($name)
        <div class="x-form-error">{{ $message }}</div>
    @enderror

</div>
