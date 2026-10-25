{{--
    Skrip umum dashboard.

    1. Pratinjau upload gambar sebelum disimpan:
       <input type="file" data-pratinjau="#foto-alumni" accept="image/jpeg,image/png">
       Elemen tujuan boleh <img> atau placeholder (mis. inisial avatar); placeholder diganti <img>.
       Batas ukuran dibaca dari data-maks-mb (bawaan 2).

    2. Penghitung karakter: <textarea maxlength="500" data-hitung>.
--}}
<script>
    (function () {
        var JENIS_GAMBAR = ['image/jpeg', 'image/png'];

        function pesanUntuk(input) {
            var pesan = input.parentElement.querySelector('.x-pratinjau-pesan');

            if (! pesan) {
                pesan = document.createElement('div');
                pesan.className = 'x-pratinjau-pesan';
                pesan.setAttribute('aria-live', 'polite');
                input.insertAdjacentElement('afterend', pesan);
            }

            return pesan;
        }

        function tampilkanPratinjau(input) {
            var tujuan = document.querySelector(input.dataset.pratinjau);
            var file = input.files && input.files[0];
            var pesan = pesanUntuk(input);
            var maksMb = parseFloat(input.dataset.maksMb || '2');

            pesan.className = 'x-pratinjau-pesan';
            pesan.textContent = '';

            if (! file || ! tujuan) {
                return;
            }

            if (JENIS_GAMBAR.indexOf(file.type) === -1) {
                pesan.textContent = 'File harus berupa gambar JPG atau PNG.';
                return;
            }

            if (file.size > maksMb * 1024 * 1024) {
                pesan.textContent = 'Ukuran file ' + (file.size / 1024 / 1024).toFixed(1) + ' MB, maksimal ' + maksMb + ' MB.';
                return;
            }

            var gambar = tujuan;

            if (tujuan.tagName !== 'IMG') {
                gambar = document.createElement('img');
                gambar.id = tujuan.id;
                gambar.className = tujuan.className.replace(/\b(x-avatar-inisial|foto-kosong)\b/g, '').trim();
                gambar.setAttribute('style', tujuan.getAttribute('style') || '');
                tujuan.replaceWith(gambar);
            }

            if (gambar.dataset.urlPratinjau) {
                URL.revokeObjectURL(gambar.dataset.urlPratinjau);
            }

            gambar.dataset.urlPratinjau = URL.createObjectURL(file);
            gambar.src = gambar.dataset.urlPratinjau;
            gambar.alt = 'Pratinjau foto';
            gambar.hidden = false;

            pesan.className = 'x-pratinjau-pesan ok';
            pesan.textContent = 'Pratinjau, belum tersimpan. Klik tombol simpan/unggah untuk menyimpan.';
        }

        function perbaruiHitungan(area) {
            var info = area.nextElementSibling;

            if (! info || ! info.classList.contains('x-hitung-karakter')) {
                info = document.createElement('div');
                info.className = 'x-hitung-karakter';
                info.setAttribute('aria-live', 'polite');
                area.insertAdjacentElement('afterend', info);
            }

            var maks = parseInt(area.getAttribute('maxlength'), 10);
            var jumlah = area.value.length;

            info.textContent = jumlah + ' / ' + maks + ' karakter';
            info.classList.toggle('hampir', jumlah >= maks * 0.9);
        }

        document.addEventListener('change', function (event) {
            if (event.target.matches('input[type="file"][data-pratinjau]')) {
                tampilkanPratinjau(event.target);
            }
        });

        document.addEventListener('input', function (event) {
            if (event.target.matches('textarea[maxlength][data-hitung]')) {
                perbaruiHitungan(event.target);
            }
        });

        document.querySelectorAll('textarea[maxlength][data-hitung]').forEach(perbaruiHitungan);
    })();
</script>
