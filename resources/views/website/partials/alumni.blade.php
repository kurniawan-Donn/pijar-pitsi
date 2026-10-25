{{--
    Bagian alumni: kartu yang bisa digeser (carousel) dan popup detail.
    $alumnis: Alumni::publik()->berurutan() beserta pesertaMagang.feedback.
--}}
@php
    $bintang = fn (?int $nilai) => $nilai ? str_repeat('★', $nilai).str_repeat('☆', 5 - $nilai) : null;
@endphp

<section id="alumni" class="section section-light">
    <div class="container">

        <div class="section-heading" data-reveal>
            <span class="section-label">Alumni</span>
            <h2>Cerita Alumni Magang</h2>
            <p>Project yang dikerjakan dan kesan para alumni selama magang bersama kami.</p>
        </div>

        <div class="alumni-carousel" data-reveal>
            <button type="button" class="alumni-geser alumni-geser-kiri" data-arah="-1" aria-label="Alumni sebelumnya">‹</button>

            <div class="alumni-track" tabindex="0" aria-label="Daftar alumni, geser untuk melihat lainnya">
                @foreach ($alumnis as $alumni)
                    @php
                        $rating = $alumni->nilaiRating();
                    @endphp

                    <button type="button" class="card alumni-card" data-dialog="alumni-dialog-{{ $alumni->id }}" aria-haspopup="dialog">
                        @if ($alumni->urlFoto())
                            <img src="{{ $alumni->urlFoto() }}" alt="Foto {{ $alumni->nama }}" class="alumni-foto" loading="lazy" width="96" height="96">
                        @else
                            <span class="alumni-foto alumni-inisial" aria-hidden="true">{{ mb_strtoupper(mb_substr($alumni->nama, 0, 1)) }}</span>
                        @endif

                        <strong class="alumni-nama">{{ $alumni->nama }}</strong>

                        @if ($rating)
                            <span class="alumni-bintang" aria-label="Rating {{ $rating }} dari 5">{{ $bintang($rating) }}</span>
                        @endif

                        <span class="alumni-kampus">{{ $alumni->universitas }}</span>
                        @if ($alumni->program_studi)
                            <span class="alumni-meta">{{ $alumni->program_studi }}</span>
                        @endif
                        <span class="alumni-meta">{{ collect([$alumni->divisi, $alumni->periode])->filter()->implode(' · ') }}</span>

                        <span class="alumni-project">{{ $alumni->judul_project }}</span>
                        <span class="alumni-lihat">Lihat cerita →</span>
                    </button>
                @endforeach
            </div>

            <button type="button" class="alumni-geser alumni-geser-kanan" data-arah="1" aria-label="Alumni berikutnya">›</button>
        </div>

        {{-- POPUP DETAIL --}}
        @foreach ($alumnis as $alumni)
            <dialog class="alumni-dialog" id="alumni-dialog-{{ $alumni->id }}" aria-labelledby="alumni-judul-{{ $alumni->id }}">
                <button type="button" class="alumni-tutup" aria-label="Tutup">×</button>

                <div class="alumni-dialog-kepala">
                    @if ($alumni->urlFoto())
                        <img src="{{ $alumni->urlFoto() }}" alt="" class="alumni-foto" width="72" height="72">
                    @else
                        <span class="alumni-foto alumni-inisial" aria-hidden="true">{{ mb_strtoupper(mb_substr($alumni->nama, 0, 1)) }}</span>
                    @endif
                    <div>
                        <strong>{{ $alumni->nama }}</strong>
                        <span>{{ collect([$alumni->universitas, $alumni->program_studi])->filter()->implode(' · ') }}</span>
                        <span>{{ collect([$alumni->divisi, $alumni->periode])->filter()->implode(' · ') }}</span>
                        @if ($alumni->nilaiRating())
                            <span class="alumni-bintang">{{ $bintang($alumni->nilaiRating()) }}</span>
                        @endif
                    </div>
                </div>

                <h3 id="alumni-judul-{{ $alumni->id }}">{{ $alumni->judul_project }}</h3>
                <p>{!! nl2br(e($alumni->deskripsi_project)) !!}</p>

                @if ($alumni->testimoni)
                    <blockquote>“{{ $alumni->testimoni }}”</blockquote>
                @endif
            </dialog>
        @endforeach

    </div>
</section>
