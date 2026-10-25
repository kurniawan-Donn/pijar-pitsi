{{-- JavaScript website publik (tanpa library). --}}
<script>
(() => {
    const doc = document.documentElement;
    doc.classList.add('js');

    /* -----------------------------------------------------------
       NAVBAR: efek scroll, menu mobile, link aktif
    ----------------------------------------------------------- */
    const navbar = document.querySelector('.navbar');
    const navToggle = document.querySelector('.nav-toggle');
    const toTop = document.querySelector('.to-top');

    const onScroll = () => {
        const y = window.scrollY;
        navbar.classList.toggle('scrolled', y > 20);
        toTop?.classList.toggle('show', y > 600);
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    navToggle?.addEventListener('click', () => {
        const open = navbar.classList.toggle('nav-open');
        navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    document.querySelectorAll('.nav-menu a').forEach((link) => {
        link.addEventListener('click', () => {
            navbar.classList.remove('nav-open');
            navToggle?.setAttribute('aria-expanded', 'false');
        });
    });

    toTop?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    const navLinks = [...document.querySelectorAll('.nav-menu a[href^="#"]')];
    const sections = navLinks
        .map((link) => document.querySelector(link.getAttribute('href')))
        .filter(Boolean);

    if ('IntersectionObserver' in window) {
        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                navLinks.forEach((link) => {
                    link.classList.toggle('active', link.getAttribute('href') === '#' + entry.target.id);
                });
            });
        }, { rootMargin: '-45% 0px -50% 0px' });

        sections.forEach((section) => sectionObserver.observe(section));

        /* -------------------------------------------------------
           ANIMASI MUNCUL SAAT SCROLL
        ------------------------------------------------------- */
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        document.querySelectorAll('[data-reveal]').forEach((el) => revealObserver.observe(el));
    } else {
        document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-visible'));
    }

    /* -----------------------------------------------------------
       TAB CARA PENDAFTARAN
    ----------------------------------------------------------- */
    const tabs = [...document.querySelectorAll('.method-tab')];

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            tabs.forEach((other) => {
                const selected = other === tab;
                other.setAttribute('aria-selected', selected ? 'true' : 'false');
                document.getElementById(other.getAttribute('aria-controls')).hidden = !selected;
            });
        });
    });

    /* -----------------------------------------------------------
       FORM PENDAFTARAN MULTI-LANGKAH
    ----------------------------------------------------------- */
    const form = document.getElementById('form-pendaftaran');

    if (form) {
        const steps = [...form.querySelectorAll('.form-step')];
        const indicators = [...document.querySelectorAll('[data-step-indicator]')];
        const btnPrev = document.getElementById('btn-prev');
        const btnNext = document.getElementById('btn-next');
        const btnSubmit = document.getElementById('btn-submit');
        const stepInfo = document.getElementById('step-info');
        let current = Number(form.dataset.startStep || 1);

        const showStep = (number, focus = false) => {
            current = number;

            steps.forEach((step) => {
                step.hidden = Number(step.dataset.step) !== number;
            });

            indicators.forEach((indicator) => {
                const n = Number(indicator.dataset.stepIndicator);
                indicator.classList.toggle('current', n === number);
                indicator.classList.toggle('done', n < number);
            });

            btnPrev.hidden = number === 1;
            btnNext.hidden = number === steps.length;
            btnSubmit.hidden = number !== steps.length;
            stepInfo.textContent = `Langkah ${number} dari ${steps.length}`;

            if (focus) {
                document.getElementById('daftar').scrollIntoView({ behavior: 'smooth', block: 'start' });
                steps[number - 1].querySelector('input, textarea')?.focus({ preventScroll: true });
            }
        };

        const markField = (input) => {
            const field = input.closest('.field');
            let error = field?.querySelector('.field-error.client');

            if (input.checkValidity()) {
                input.classList.remove('is-invalid');
                error?.remove();
                return true;
            }

            input.classList.add('is-invalid');

            if (field && !error) {
                error = document.createElement('div');
                error.className = 'field-error client';
                field.appendChild(error);
            }

            if (error) {
                error.textContent = pesanValidasi(input);
            }

            return false;
        };

        const pesanValidasi = (input) => {
            const v = input.validity;
            const label = input.closest('.field')?.querySelector('label')?.childNodes[0]?.textContent.trim() || 'Kolom ini';

            if (v.valueMissing) {
                return input.type === 'checkbox'
                    ? 'Centang pernyataan ini untuk melanjutkan.'
                    : input.type === 'file' ? 'Surat pengantar wajib diunggah.' : `${label} wajib diisi.`;
            }
            if (v.typeMismatch) {
                return `Format ${label.toLowerCase()} tidak valid.`;
            }
            if (v.patternMismatch) {
                return `Format ${label.toLowerCase()} tidak sesuai.`;
            }
            if (v.rangeUnderflow) {
                return input.id === 'tanggal_selesai'
                    ? 'Tanggal selesai harus setelah tanggal mulai.'
                    : 'Tanggal tidak boleh sebelum hari ini.';
            }
            if (v.customError) {
                return input.validationMessage;
            }

            return `${label} belum valid.`;
        };

        const validateStep = (number) => {
            const inputs = [...steps[number - 1].querySelectorAll('input, textarea')];
            const results = inputs.map(markField);
            const firstInvalid = inputs[results.indexOf(false)];

            firstInvalid?.focus();

            return results.every(Boolean);
        };

        form.addEventListener('input', (event) => {
            if (event.target.classList.contains('is-invalid')) {
                markField(event.target);
            }
        });

        btnNext.addEventListener('click', () => {
            if (validateStep(current)) {
                showStep(current + 1, true);
            }
        });

        btnPrev.addEventListener('click', () => showStep(current - 1, true));

        // Enter di input tidak langsung mengirim form sebelum langkah terakhir.
        form.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && event.target.tagName === 'INPUT' && current < steps.length) {
                event.preventDefault();
                btnNext.click();
            }
        });

        form.addEventListener('submit', (event) => {
            for (let n = 1; n <= steps.length; n++) {
                if (!validateStep(n)) {
                    event.preventDefault();
                    showStep(n, true);
                    validateStep(n);
                    return;
                }
            }

            btnSubmit.classList.add('loading');
            btnSubmit.textContent = 'Mengirim...';
        });

        showStep(current);

        /* -------- Durasi magang -------- */
        const mulai = document.getElementById('tanggal_mulai');
        const selesai = document.getElementById('tanggal_selesai');
        const durasi = document.getElementById('durasi');

        const updateDurasi = () => {
            if (mulai.value) {
                const minSelesai = new Date(mulai.value);
                minSelesai.setDate(minSelesai.getDate() + 1);
                selesai.min = minSelesai.toISOString().slice(0, 10);
            }

            if (!mulai.value || !selesai.value) {
                durasi.hidden = true;
                return;
            }

            const hari = Math.round((new Date(selesai.value) - new Date(mulai.value)) / 86400000) + 1;

            if (hari < 2) {
                durasi.hidden = true;
                return;
            }

            const bulan = Math.floor(hari / 30);
            const sisa = hari % 30;
            const teks = bulan ? `${bulan} bulan${sisa ? ' ' + sisa + ' hari' : ''}` : `${hari} hari`;

            durasi.querySelector('span').textContent = `Durasi magang ± ${teks}`;
            durasi.hidden = false;
        };

        mulai.addEventListener('change', updateDurasi);
        selesai.addEventListener('change', updateDurasi);
        updateDurasi();

        /* -------- Penghitung karakter -------- */
        const keterangan = document.getElementById('keterangan');
        const counter = document.getElementById('keterangan-counter');
        const updateCounter = () => {
            counter.textContent = `${keterangan.value.length}/1000`;
        };

        keterangan.addEventListener('input', updateCounter);
        updateCounter();

        /* -------- Dropzone surat pengantar -------- */
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('dokumen');
        const dzTitle = document.getElementById('dropzone-title');
        const dzSub = document.getElementById('dropzone-sub');
        const maxBytes = 2 * 1024 * 1024;

        const updateFile = () => {
            const file = fileInput.files[0];
            fileInput.setCustomValidity('');

            if (!file) {
                dropzone.classList.remove('has-file');
                dzTitle.textContent = 'Klik atau seret file ke sini';
                dzSub.textContent = 'PDF, JPG, atau PNG · maksimal 2 MB';
                return;
            }

            if (!/\.(pdf|jpe?g|png)$/i.test(file.name)) {
                fileInput.setCustomValidity('Surat pengantar harus berformat PDF, JPG, atau PNG.');
            } else if (file.size > maxBytes) {
                fileInput.setCustomValidity('Ukuran surat pengantar maksimal 2 MB.');
            }

            dropzone.classList.toggle('has-file', fileInput.checkValidity());
            dzTitle.textContent = file.name;
            dzSub.textContent = `${(file.size / 1024 / 1024).toFixed(2)} MB · klik untuk mengganti`;
            markField(fileInput);
        };

        fileInput.addEventListener('change', updateFile);

        ['dragenter', 'dragover'].forEach((type) => dropzone.addEventListener(type, () => dropzone.classList.add('dragover')));
        ['dragleave', 'drop'].forEach((type) => dropzone.addEventListener(type, () => dropzone.classList.remove('dragover')));
    }

    /* -----------------------------------------------------------
       SETELAH REDIRECT: ARAHKAN KE KARTU HASIL
       (lompatan anchor bawaan browser meleset karena font
       web baru selesai dimuat)
    ----------------------------------------------------------- */
    const hasilForm = document.querySelector(
        '.success-state, #daftar .alert-error, #status .status-results, #status .alert, #status .field-error'
    );

    if (hasilForm) {
        const card = hasilForm.closest('.card') || hasilForm;
        const scrollKeHasil = () => window.scrollTo({
            top: card.getBoundingClientRect().top + window.scrollY - 100,
            behavior: 'instant',
        });

        (document.fonts ? document.fonts.ready : Promise.resolve())
            .then(() => requestAnimationFrame(scrollKeHasil));
    }

    /* -----------------------------------------------------------
       ALUMNI: CAROUSEL & POPUP
    ----------------------------------------------------------- */
    const alumniTrack = document.querySelector('.alumni-track');

    if (alumniTrack) {
        const tombolGeser = [...document.querySelectorAll('.alumni-geser')];
        const aturTombol = () => {
            // Toleransi beberapa piksel karena padding jalur & pembulatan snap.
            const maks = alumniTrack.scrollWidth - alumniTrack.clientWidth - 8;
            tombolGeser.forEach((tombol) => {
                tombol.disabled = tombol.dataset.arah === '-1' ? alumniTrack.scrollLeft <= 8 : alumniTrack.scrollLeft >= maks;
            });
        };

        tombolGeser.forEach((tombol) => tombol.addEventListener('click', () => {
            const kartu = alumniTrack.querySelector('.alumni-card');
            const langkah = kartu ? kartu.offsetWidth + 20 : alumniTrack.clientWidth;
            const maks = alumniTrack.scrollWidth - alumniTrack.clientWidth;
            const tujuan = Math.min(maks, Math.max(0, alumniTrack.scrollLeft + langkah * Number(tombol.dataset.arah)));
            alumniTrack.scrollTo({ left: tujuan, behavior: 'smooth' });
        }));

        alumniTrack.addEventListener('scroll', aturTombol, { passive: true });
        window.addEventListener('resize', aturTombol);
        aturTombol();
    }

    document.querySelectorAll('[data-dialog]').forEach((kartu) => {
        const dialog = document.getElementById(kartu.dataset.dialog);

        if (!dialog || typeof dialog.showModal !== 'function') {
            return;
        }

        kartu.addEventListener('click', () => dialog.showModal());
        dialog.querySelector('.alumni-tutup')?.addEventListener('click', () => dialog.close());

        // Klik di luar kotak popup (backdrop) menutup popup.
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                dialog.close();
            }
        });
    });

    /* -----------------------------------------------------------
       GALERI DOKUMENTASI: geser, keterangan saat disentuh, lightbox
    ----------------------------------------------------------- */
    const galeriTrack = document.querySelector('.galeri-track');

    if (galeriTrack) {
        const itemGaleri = [...galeriTrack.querySelectorAll('.galeri-item')];
        const tombolGaleri = [...document.querySelectorAll('.galeri-geser')];
        const lightbox = document.querySelector('.galeri-lightbox');
        const layarSentuh = window.matchMedia('(hover: none)').matches;
        let posisiLightbox = 0;

        const aturTombolGaleri = () => {
            const maks = galeriTrack.scrollWidth - galeriTrack.clientWidth - 8;
            tombolGaleri.forEach((tombol) => {
                tombol.disabled = tombol.dataset.arah === '-1' ? galeriTrack.scrollLeft <= 8 : galeriTrack.scrollLeft >= maks;
            });
        };

        tombolGaleri.forEach((tombol) => tombol.addEventListener('click', () => {
            const kolom = itemGaleri[0] ? itemGaleri[0].offsetWidth + 14 : galeriTrack.clientWidth;
            const maks = galeriTrack.scrollWidth - galeriTrack.clientWidth;
            const tujuan = Math.min(maks, Math.max(0, galeriTrack.scrollLeft + kolom * 2 * Number(tombol.dataset.arah)));
            galeriTrack.scrollTo({ left: tujuan, behavior: 'smooth' });
        }));

        galeriTrack.addEventListener('scroll', aturTombolGaleri, { passive: true });
        window.addEventListener('resize', aturTombolGaleri);
        aturTombolGaleri();

        const tampilkan = (index) => {
            posisiLightbox = (index + itemGaleri.length) % itemGaleri.length;
            const item = itemGaleri[posisiLightbox].dataset;
            const gambar = lightbox.querySelector('img');

            gambar.src = item.foto;
            gambar.alt = item.judul;
            lightbox.querySelector('figcaption strong').textContent = item.judul;
            lightbox.querySelector('figcaption small').textContent = item.tanggal || '';
            lightbox.querySelector('figcaption span').textContent = item.keterangan || '';
            lightbox.querySelectorAll('.galeri-pindah').forEach((tombol) => {
                tombol.disabled = itemGaleri.length < 2;
            });
        };

        itemGaleri.forEach((item, index) => item.addEventListener('click', () => {
            // Di layar sentuh: sentuhan pertama menampilkan keterangan, kedua memperbesar.
            if (layarSentuh && !item.classList.contains('terbuka')) {
                itemGaleri.forEach((lain) => lain.classList.toggle('terbuka', lain === item));
                return;
            }

            if (!lightbox || typeof lightbox.showModal !== 'function') {
                window.open(item.dataset.foto, '_blank', 'noopener');
                return;
            }

            tampilkan(index);
            lightbox.showModal();
        }));

        if (lightbox) {
            lightbox.querySelector('.galeri-tutup').addEventListener('click', () => lightbox.close());
            lightbox.querySelectorAll('.galeri-pindah').forEach((tombol) => tombol.addEventListener('click', () => {
                tampilkan(posisiLightbox + Number(tombol.dataset.arah));
            }));
            lightbox.addEventListener('click', (event) => {
                if (event.target === lightbox) {
                    lightbox.close();
                }
            });
            lightbox.addEventListener('keydown', (event) => {
                if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
                    event.preventDefault();
                    tampilkan(posisiLightbox + (event.key === 'ArrowRight' ? 1 : -1));
                }
            });
            lightbox.addEventListener('close', () => itemGaleri[posisiLightbox]?.focus({ preventScroll: true }));
        }
    }

    /* -----------------------------------------------------------
       TOMBOL LOADING FORM CEK STATUS
    ----------------------------------------------------------- */
    document.getElementById('form-status')?.addEventListener('submit', (event) => {
        const button = event.target.querySelector('button[type="submit"]');
        button.classList.add('loading');
        button.textContent = 'Mengecek...';
    });
})();
</script>
