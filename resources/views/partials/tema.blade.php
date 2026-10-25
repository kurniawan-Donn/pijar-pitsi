{{--
    Token warna PILARA (satu sumber untuk dashboard, login, dan website publik).
    Pakai var(--nama) di CSS; jangan menulis ulang kode warna di halaman.
    PDF (dompdf) tidak mendukung var(), jadi PDF memakai nilai yang sama secara langsung.
--}}
<style>
    :root {
        /* Palet utama */
        --latar: #f7f9fb;
        --permukaan: #ffffff;
        --utama: #2688b4;
        --utama-gelap: #0b6a9c;
        --utama-muda: #e8f3f9;
        --aksen: #ee7623;
        --aksen-gelap: #d4621a;
        --aksen-muda: #fdf0e7;
        --teks: #1f2937;
        --teks-sekunder: #6b7280;

        /* Turunan netral */
        --teks-redup: #9ca3af;
        --garis: #e5e7eb;
        --garis-kuat: #d1d5db;
        --permukaan-redup: #f9fafb;

        /* Status */
        --sukses: #067647;
        --sukses-muda: #ecfdf3;
        --peringatan: #b54708;
        --peringatan-muda: #fffaeb;
        --bahaya: #b42318;
        --bahaya-muda: #fef3f2;
        --info: #0b6a9c;
        --info-muda: #e8f3f9;

        /* Bentuk */
        --radius: 12px;
        --radius-kecil: 8px;
        --bayangan: 0 1px 3px rgba(17, 24, 39, .06), 0 1px 2px rgba(17, 24, 39, .04);
        --fokus: 0 0 0 3px rgba(38, 136, 180, .18);
        --font: "Plus Jakarta Sans", system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
    }
</style>
