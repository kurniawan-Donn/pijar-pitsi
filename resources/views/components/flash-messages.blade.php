{{--
    Menampilkan pesan flash session (success / error)
    dan ringkasan error validasi bila ada.
--}}
@props([
    'validation' => true,
])

<x-alert type="success" :message="session('success')" />

<x-alert type="error" :message="session('error')" />

@if ($validation && $errors->any())
    <x-alert
        type="error"
        title="Periksa kembali data yang diisi:"
        :messages="$errors->all()"
    />
@endif
