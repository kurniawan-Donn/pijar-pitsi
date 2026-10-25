{{--
    Logo PITSI untuk kop PDF (dompdf) sebagai data URI.
    Contoh: @include('partials.logo-pdf', ['tinggi' => 40])
--}}
@php
    $berkasLogo = public_path('images/logo-pitsi-256.png');
@endphp

@if (is_file($berkasLogo))
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($berkasLogo)) }}" alt="Logo PT Pilar Teknotama Sinergi" style="height: {{ $tinggi ?? 40 }}px;">
@endif
