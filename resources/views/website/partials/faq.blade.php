{{-- Bagian FAQ (accordion). $faqs: FAQ yang tampil, sudah berurutan. --}}
@php
    // FAQ tanpa kategori dikumpulkan di grup tanpa judul.
    $grupFaq = $faqs->groupBy(fn ($faq) => $faq->kategori ?? '');
@endphp

<section id="faq" class="section section-light">
    <div class="container">

        <div class="section-heading" data-reveal>
            <span class="section-label">FAQ</span>
            <h2>Pertanyaan yang Sering Diajukan</h2>
            <p>Belum menemukan jawaban? Hubungi kami lewat kontak di bagian bawah halaman.</p>
        </div>

        <div class="faq-wrap" data-reveal>
            @foreach ($grupFaq as $kategori => $daftar)
                @if ($kategori !== '' && $grupFaq->count() > 1)
                    <h3 class="faq-kategori">{{ $kategori }}</h3>
                @endif

                <div class="faq-list">
                    @foreach ($daftar as $faq)
                        <details class="card faq-item">
                            <summary>
                                <span>{{ $faq->pertanyaan }}</span>
                                <span class="faq-ikon" aria-hidden="true"></span>
                            </summary>
                            <div class="faq-jawaban">{!! nl2br(e($faq->jawaban)) !!}</div>
                        </details>
                    @endforeach
                </div>
            @endforeach
        </div>

    </div>
</section>
