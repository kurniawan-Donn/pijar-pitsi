{{--
    Galeri dokumentasi kegiatan: foto kecil tersusun 2 baris ke bawah, sisanya memanjang
    ke kanan dan bisa digeser. Keterangan muncul di atas foto saat kursor diarahkan
    (di HP saat disentuh); klik foto untuk memperbesar (lightbox).
    $dokumentasis: Dokumentasi::tampil()->berurutan().
--}}
<section id="dokumentasi" class="section">
    <div class="container">

        <div class="section-heading" data-reveal>
            <span class="section-label">Dokumentasi</span>
            <h2>Kegiatan Magang</h2>
            <p>Momen kegiatan peserta magang bersama tim kami.</p>
        </div>

        <div class="galeri" data-reveal>
            <button type="button" class="galeri-geser galeri-geser-kiri" data-arah="-1" aria-label="Foto sebelumnya">
                <x-icon name="chevron-left" :size="20" />
            </button>

            <div class="galeri-track" tabindex="0" aria-label="Galeri dokumentasi, geser untuk melihat lainnya">
                @foreach ($dokumentasis as $dokumentasi)
                    <button
                        type="button"
                        class="galeri-item"
                        data-index="{{ $loop->index }}"
                        data-foto="{{ $dokumentasi->urlFoto() }}"
                        data-judul="{{ $dokumentasi->judul }}"
                        data-tanggal="{{ $dokumentasi->labelTanggal() }}"
                        data-keterangan="{{ $dokumentasi->keterangan }}"
                        aria-label="Perbesar foto: {{ $dokumentasi->judul }}"
                    >
                        <img src="{{ $dokumentasi->urlThumbnail() }}" alt="{{ $dokumentasi->judul }}" loading="lazy">

                        <span class="galeri-overlay">
                            <strong>{{ $dokumentasi->judul }}</strong>
                            @if ($dokumentasi->labelTanggal())
                                <small><x-icon name="calendar" :size="13" /> {{ $dokumentasi->labelTanggal() }}</small>
                            @endif
                            @if ($dokumentasi->keterangan)
                                <span>{{ $dokumentasi->keterangan }}</span>
                            @endif
                        </span>
                    </button>
                @endforeach
            </div>

            <button type="button" class="galeri-geser galeri-geser-kanan" data-arah="1" aria-label="Foto berikutnya">
                <x-icon name="chevron-right" :size="20" />
            </button>
        </div>

        {{-- LIGHTBOX --}}
        <dialog class="galeri-lightbox" aria-labelledby="galeri-lightbox-judul">
            <button type="button" class="galeri-tutup" aria-label="Tutup"><x-icon name="x" :size="20" /></button>

            <figure>
                <img src="" alt="">
                <figcaption>
                    <strong id="galeri-lightbox-judul"></strong>
                    <small></small>
                    <span></span>
                </figcaption>
            </figure>

            <button type="button" class="galeri-pindah galeri-pindah-kiri" data-arah="-1" aria-label="Foto sebelumnya"><x-icon name="chevron-left" :size="22" /></button>
            <button type="button" class="galeri-pindah galeri-pindah-kanan" data-arah="1" aria-label="Foto berikutnya"><x-icon name="chevron-right" :size="22" /></button>
        </dialog>

    </div>
</section>
