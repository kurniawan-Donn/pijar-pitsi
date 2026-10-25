{{-- CSS website publik PILARA. --}}
<style>
    /* Nama variabel lama dipertahankan; nilainya dari token PILARA (partials/tema). */
    :root {
        --primary: var(--utama);
        --primary-dark: var(--utama-gelap);
        --primary-soft: var(--utama-muda);
        --navy: var(--utama-gelap);
        --accent: var(--aksen);
        --accent-dark: var(--aksen-gelap);
        --accent-soft: var(--aksen-muda);
        --success: var(--sukses);
        --success-soft: var(--sukses-muda);
        --danger: var(--bahaya);
        --danger-soft: var(--bahaya-muda);
        --text: var(--teks);
        --muted: var(--teks-sekunder);
        --line: var(--garis);
        --light: var(--latar);
        --white: #ffffff;
        --radius: 18px;
        --radius-sm: 12px;
        --shadow-sm: 0 4px 14px rgba(11, 106, 156, .06);
        --shadow: 0 18px 45px rgba(11, 106, 156, .10);
        --ease: cubic-bezier(.22, .8, .24, 1);
    }

    *,
    *::before,
    *::after {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html {
        scroll-behavior: smooth;
        scroll-padding-top: 90px;
    }

    body {
        font-family: var(--font);
        color: var(--text);
        background: var(--white);
        line-height: 1.65;
        -webkit-font-smoothing: antialiased;

        /* Animasi masuk tidak boleh menimbulkan scroll horizontal. */
        overflow-x: clip;
    }

    a {
        color: inherit;
        text-decoration: none;
    }

    img {
        max-width: 100%;
        display: block;
    }

    button,
    input,
    select,
    textarea {
        font: inherit;
        color: inherit;
    }

    :focus-visible {
        outline: 3px solid rgba(38, 136, 180, .35);
        outline-offset: 2px;
    }

    .container {
        width: min(1180px, 100% - 40px);
        margin-inline: auto;
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0 0 0 0);
        white-space: nowrap;
    }

    /* =========================================================
       TOMBOL
    ========================================================== */

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;

        min-height: 50px;
        padding: 0 24px;

        border: 2px solid transparent;
        border-radius: 999px;

        font-size: 15px;
        font-weight: 700;

        cursor: pointer;

        transition: transform .25s var(--ease), box-shadow .25s var(--ease), background .25s, color .25s, border-color .25s;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .btn:active {
        transform: translateY(0);
    }

    .btn-accent {
        background: var(--accent);
        color: var(--white);
        box-shadow: 0 10px 24px rgba(238, 118, 35, .30);
    }

    .btn-accent:hover {
        background: var(--accent-dark);
        box-shadow: 0 14px 30px rgba(238, 118, 35, .38);
    }

    .btn-primary {
        background: var(--primary);
        color: var(--white);
        box-shadow: 0 10px 24px rgba(38, 136, 180, .25);
    }

    .btn-primary:hover {
        background: var(--primary-dark);
    }

    .btn-ghost {
        background: rgba(255, 255, 255, .7);
        border-color: var(--line);
        color: var(--navy);
        backdrop-filter: blur(6px);
    }

    .btn-ghost:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .btn-outline {
        background: transparent;
        border-color: var(--line);
        color: var(--navy);
    }

    .btn-outline:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .btn-block {
        width: 100%;
    }

    .btn .arrow {
        transition: transform .25s var(--ease);
    }

    .btn:hover .arrow {
        transform: translateX(4px);
    }

    /* =========================================================
       NAVBAR
    ========================================================== */

    .navbar {
        position: fixed;
        inset: 0 0 auto;
        z-index: 100;

        padding: 18px 0;

        transition: padding .3s var(--ease), background .3s, box-shadow .3s;
    }

    .navbar.scrolled {
        padding: 10px 0;

        background: rgba(255, 255, 255, .86);
        backdrop-filter: blur(14px);
        box-shadow: 0 6px 24px rgba(11, 106, 156, .08);
    }

    .nav-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .logo {
        display: inline-flex;
        align-items: center;
        gap: 11px;

        color: var(--navy);

        font-size: 20px;
        font-weight: 800;
        letter-spacing: .5px;
    }

    .logo-mark {
        width: 40px;
        height: 40px;

        object-fit: contain;
    }

    .logo-teks {
        display: flex;
        flex-direction: column;

        line-height: 1.1;
        letter-spacing: 1.5px;
    }

    .logo-teks small {
        color: var(--muted);

        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: 0;
    }

    .nav-menu {
        display: flex;
        align-items: center;
        gap: 4px;

        list-style: none;
    }

    .nav-menu a {
        position: relative;

        display: inline-block;

        padding: 9px 14px;

        border-radius: 999px;

        color: var(--teks);

        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;

        transition: color .2s, background .2s;
    }

    .nav-menu a:hover {
        color: var(--primary);
        background: var(--primary-soft);
    }

    .nav-menu a.active {
        color: var(--primary);
    }

    .nav-menu a.active::after {
        content: "";

        position: absolute;
        left: 50%;
        bottom: 3px;

        width: 18px;
        height: 3px;

        border-radius: 3px;

        background: var(--primary);

        transform: translateX(-50%);
    }

    .nav-cta {
        min-height: 44px;
        padding: 0 20px;

        font-size: 14px;
        white-space: nowrap;
    }

    .nav-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .nav-menu-masuk {
        display: none;
    }

    .nav-toggle {
        display: none;

        width: 44px;
        height: 44px;

        border: 1px solid var(--line);
        border-radius: 12px;

        background: var(--white);

        cursor: pointer;
    }

    .nav-toggle span {
        display: block;

        width: 20px;
        height: 2px;

        margin: 4px auto;

        border-radius: 2px;

        background: var(--navy);

        transition: transform .3s var(--ease), opacity .2s;
    }

    .nav-open .nav-toggle span:nth-child(1) {
        transform: translateY(6px) rotate(45deg);
    }

    .nav-open .nav-toggle span:nth-child(2) {
        opacity: 0;
    }

    .nav-open .nav-toggle span:nth-child(3) {
        transform: translateY(-6px) rotate(-45deg);
    }

    /* =========================================================
       HERO
    ========================================================== */

    .hero {
        position: relative;
        overflow: hidden;

        padding: 150px 0 110px;

        background:
            radial-gradient(circle at 12% 18%, rgba(38, 136, 180, .12), transparent 42%),
            radial-gradient(circle at 88% 12%, rgba(238, 118, 35, .12), transparent 38%),
            linear-gradient(180deg, #f3f9fd 0%, #ffffff 100%);
    }

    .blob {
        position: absolute;

        border-radius: 50%;

        filter: blur(2px);

        opacity: .55;

        animation: float 14s ease-in-out infinite;

        pointer-events: none;
    }

    .blob-1 {
        top: 110px;
        right: -80px;

        width: 260px;
        height: 260px;

        background: radial-gradient(circle, rgba(38, 136, 180, .25), transparent 70%);
    }

    .blob-2 {
        bottom: -60px;
        left: -60px;

        width: 220px;
        height: 220px;

        background: radial-gradient(circle, rgba(238, 118, 35, .22), transparent 70%);

        animation-delay: -6s;
    }

    @keyframes float {
        0%, 100% {
            transform: translate(0, 0) scale(1);
        }

        50% {
            transform: translate(-20px, 25px) scale(1.06);
        }
    }

    .hero-grid {
        position: relative;

        display: grid;
        grid-template-columns: 1.05fr .95fr;
        align-items: center;
        gap: 60px;
    }

    .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 9px;

        padding: 7px 14px 7px 8px;

        border: 1px solid rgba(38, 136, 180, .18);
        border-radius: 999px;

        background: var(--white);
        color: var(--primary);

        font-size: 12px;
        font-weight: 800;
        letter-spacing: .6px;
        text-transform: uppercase;

        box-shadow: var(--shadow-sm);
    }

    .eyebrow .pulse {
        position: relative;

        width: 10px;
        height: 10px;

        border-radius: 50%;

        background: var(--success);
    }

    .eyebrow .pulse::after {
        content: "";

        position: absolute;
        inset: 0;

        border-radius: 50%;

        background: var(--success);

        animation: pulse 1.8s ease-out infinite;
    }

    @keyframes pulse {
        to {
            transform: scale(2.6);
            opacity: 0;
        }
    }

    .hero h1 {
        margin-top: 22px;

        color: var(--navy);

        font-size: clamp(36px, 5vw, 58px);
        font-weight: 800;
        line-height: 1.12;
        letter-spacing: -1px;
    }

    .hero h1 .highlight {
        position: relative;

        color: var(--primary);

        white-space: nowrap;
    }

    .hero h1 .highlight::after {
        content: "";

        position: absolute;
        left: 0;
        right: 0;
        bottom: 4px;
        z-index: -1;

        height: 12px;

        border-radius: 6px;

        background: rgba(238, 118, 35, .28);
    }

    .hero-text {
        max-width: 540px;

        margin-top: 20px;

        color: var(--muted);

        font-size: 17px;
    }

    .hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;

        margin-top: 32px;
    }

    .hero-points {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 22px;

        margin-top: 30px;

        list-style: none;
    }

    .hero-points li {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        color: var(--teks);

        font-size: 14px;
        font-weight: 600;
    }

    /* Lingkaran hijau dengan centang garis (tanpa karakter simbol). */
    .hero-points li::before {
        content: "";

        width: 22px;
        height: 22px;

        flex-shrink: 0;

        border-radius: 50%;

        background:
            url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23067647' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6 9 17l-5-5'/%3E%3C/svg%3E") center / 12px no-repeat,
            var(--success-soft);
    }

    .hero-visual {
        position: relative;
    }

    .hero-frame {
        position: relative;

        aspect-ratio: 4 / 3.4;

        border-radius: 28px;

        background: linear-gradient(145deg, var(--primary), var(--navy));

        box-shadow: var(--shadow);

        overflow: hidden;

        transform: rotate(1.5deg);

        transition: transform .6s var(--ease);
    }

    .hero-visual:hover .hero-frame {
        transform: rotate(0);
    }

    .hero-frame img {
        width: 100%;
        height: 100%;

        object-fit: cover;
    }

    .hero-placeholder {
        position: absolute;
        inset: 0;

        display: grid;
        place-items: center;
        align-content: center;
        gap: 18px;

        color: var(--white);
        text-align: center;
    }

    .hero-placeholder .big-mark {
        width: 110px;
        height: 110px;

        display: grid;
        place-items: center;

        border-radius: 30px;

        background: rgba(255, 255, 255, .14);

        font-size: 52px;
        font-weight: 800;

        backdrop-filter: blur(6px);
    }

    .hero-placeholder strong {
        font-size: 26px;
        letter-spacing: 3px;
    }

    .hero-placeholder span {
        color: rgba(255, 255, 255, .75);
        font-size: 14px;
    }

    .hero-frame::before {
        content: "";

        position: absolute;
        inset: 0;

        background-image:
            linear-gradient(rgba(255, 255, 255, .06) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, .06) 1px, transparent 1px);
        background-size: 34px 34px;

        pointer-events: none;
    }

    .float-card {
        position: absolute;

        display: flex;
        align-items: center;
        gap: 12px;

        padding: 13px 16px;

        border-radius: 16px;

        background: var(--white);

        box-shadow: var(--shadow);

        animation: bob 6s ease-in-out infinite;
    }

    .float-card .icon {
        width: 40px;
        height: 40px;

        display: grid;
        place-items: center;

        border-radius: 12px;

        font-size: 18px;
    }

    .float-card strong {
        display: block;

        color: var(--navy);

        font-size: 15px;
        line-height: 1.2;
    }

    .float-card small {
        color: var(--muted);
        font-size: 12px;
    }

    .float-card-1 {
        left: -34px;
        bottom: 34px;
    }

    .float-card-1 .icon {
        background: var(--success-soft);
        color: var(--success);
    }

    .float-card-2 {
        right: -22px;
        top: 30px;

        animation-delay: -3s;
    }

    .float-card-2 .icon {
        background: var(--primary-soft);
        color: var(--primary);
    }

    @keyframes bob {
        0%, 100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    /* =========================================================
       SECTION UMUM
    ========================================================== */

    .section {
        padding: 100px 0;
    }

    .section-light {
        background: var(--light);
    }

    .section-heading {
        max-width: 680px;

        margin: 0 auto 54px;

        text-align: center;
    }

    .section-label {
        display: inline-block;

        margin-bottom: 12px;
        padding: 6px 14px;

        border-radius: 999px;

        background: var(--primary-soft);
        color: var(--primary-dark);

        font-size: 12px;
        font-weight: 800;
        letter-spacing: .8px;
        text-transform: uppercase;
    }

    .section-heading h2 {
        color: var(--navy);

        font-size: clamp(28px, 3.6vw, 40px);
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -.5px;
    }

    .section-heading p {
        margin-top: 14px;

        color: var(--muted);

        font-size: 16px;
    }

    .card {
        background: var(--white);

        border: 1px solid var(--line);
        border-radius: var(--radius);

        box-shadow: var(--shadow-sm);
    }

    /* =========================================================
       ALUMNI (CAROUSEL + POPUP)
    ========================================================== */

    .alumni-carousel {
        position: relative;
    }

    .alumni-track {
        display: grid;
        grid-auto-flow: column;
        grid-auto-columns: calc((100% - 3 * 20px) / 4);
        gap: 20px;

        padding: 8px 4px 22px;

        overflow-x: auto;
        overscroll-behavior-x: contain;
        scroll-snap-type: x mandatory;
        scroll-padding-inline: 4px;
        scroll-behavior: smooth;
        scrollbar-width: thin;
    }

    .alumni-track:focus-visible {
        outline: 3px solid var(--primary-soft);
        border-radius: var(--radius);
    }

    .alumni-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;

        padding: 26px 20px 22px;

        color: var(--text);

        font-family: inherit;
        text-align: center;

        cursor: pointer;
        scroll-snap-align: start;

        transition: transform .35s var(--ease), box-shadow .35s var(--ease);
    }

    .alumni-card:hover,
    .alumni-card:focus-visible {
        transform: translateY(-6px);

        box-shadow: var(--shadow);
    }

    .alumni-card:focus-visible {
        outline: 3px solid var(--primary-soft);
    }

    .alumni-foto {
        width: 96px;
        height: 96px;
        margin-bottom: 10px;

        border: 4px solid var(--primary-soft);
        border-radius: 50%;

        object-fit: cover;
    }

    .alumni-inisial {
        display: grid;
        place-items: center;

        background: var(--primary-soft);
        color: var(--primary);

        font-size: 34px;
        font-weight: 800;
    }

    .alumni-nama {
        color: var(--navy);

        font-size: 17px;
    }

    .alumni-bintang {
        color: #f5a524;

        font-size: 16px;
        letter-spacing: 2px;
    }

    .alumni-kampus {
        margin-top: 4px;

        font-size: 14px;
        font-weight: 600;
    }

    .alumni-meta {
        color: var(--muted);

        font-size: 13px;
    }

    .alumni-project {
        margin-top: 12px;
        padding: 10px 12px;

        width: 100%;

        border-radius: var(--radius-sm);

        background: var(--light);
        color: var(--navy);

        font-size: 13.5px;
        font-weight: 700;
        line-height: 1.5;
    }

    .alumni-lihat {
        margin-top: auto;
        padding-top: 12px;

        color: var(--primary);

        font-size: 13px;
        font-weight: 700;
    }

    .alumni-geser {
        position: absolute;
        top: 42%;
        z-index: 2;

        display: grid;
        place-items: center;

        width: 44px;
        height: 44px;

        border: 1px solid var(--line);
        border-radius: 50%;

        background: var(--white);
        color: var(--navy);

        box-shadow: var(--shadow-sm);

        font-size: 26px;
        line-height: 1;

        cursor: pointer;
    }

    .alumni-geser:disabled {
        opacity: 0;
        pointer-events: none;
    }

    .alumni-geser-kiri {
        left: -22px;
    }

    .alumni-geser-kanan {
        right: -22px;
    }

    .alumni-dialog {
        width: min(560px, calc(100% - 32px));
        max-height: calc(100vh - 48px);

        margin: auto;
        padding: 30px 28px 26px;

        border: none;
        border-radius: var(--radius);

        color: var(--text);

        box-shadow: var(--shadow);
    }

    .alumni-dialog::backdrop {
        background: rgba(11, 106, 156, .55);
    }

    .alumni-tutup {
        position: absolute;
        top: 14px;
        right: 14px;

        width: 36px;
        height: 36px;

        border: none;
        border-radius: 50%;

        background: var(--light);
        color: var(--navy);

        font-size: 22px;

        cursor: pointer;
    }

    .alumni-dialog-kepala {
        display: flex;
        align-items: center;
        gap: 16px;

        margin-bottom: 18px;
    }

    .alumni-dialog-kepala .alumni-foto {
        width: 72px;
        height: 72px;
        margin: 0;
    }

    .alumni-dialog-kepala .alumni-inisial {
        font-size: 26px;
    }

    .alumni-dialog-kepala strong {
        display: block;

        color: var(--navy);

        font-size: 18px;
    }

    .alumni-dialog-kepala span {
        display: block;

        color: var(--muted);

        font-size: 13px;
    }

    .alumni-dialog h3 {
        margin-bottom: 8px;

        color: var(--navy);

        font-size: 17px;
    }

    .alumni-dialog p {
        color: var(--muted);

        font-size: 15px;
        line-height: 1.75;
    }

    .alumni-dialog blockquote {
        margin-top: 18px;
        padding: 14px 18px;

        border-left: 4px solid var(--primary);
        border-radius: 0 var(--radius-sm) var(--radius-sm) 0;

        background: var(--primary-soft);
        color: var(--text);

        font-size: 15px;
        font-style: italic;
        line-height: 1.7;
    }

    @media (max-width: 1100px) {
        .alumni-track {
            grid-auto-columns: calc((100% - 2 * 20px) / 3);
        }
    }

    @media (max-width: 800px) {
        .alumni-track {
            grid-auto-columns: calc((100% - 20px) / 2);
        }

        .alumni-geser {
            display: none;
        }
    }

    @media (max-width: 520px) {
        .alumni-track {
            grid-auto-columns: 82%;
        }
    }

    /* =========================================================
       FAQ (ACCORDION)
    ========================================================== */

    .faq-wrap {
        max-width: 820px;

        margin: 0 auto;
    }

    .faq-kategori {
        margin: 28px 0 12px;

        color: var(--primary-dark);

        font-size: 13px;
        font-weight: 800;
        letter-spacing: .6px;
        text-transform: uppercase;
    }

    .faq-kategori:first-child {
        margin-top: 0;
    }

    .faq-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .faq-item {
        border-radius: var(--radius-sm);

        transition: box-shadow .3s var(--ease), border-color .3s var(--ease);
    }

    .faq-item[open] {
        border-color: #b8dcec;

        box-shadow: var(--shadow);
    }

    .faq-item summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;

        padding: 18px 22px;

        color: var(--navy);

        font-size: 16px;
        font-weight: 700;

        cursor: pointer;
        list-style: none;
    }

    .faq-item summary::-webkit-details-marker {
        display: none;
    }

    .faq-item summary:focus-visible {
        outline: 3px solid var(--primary-soft);
        border-radius: var(--radius-sm);
    }

    .faq-ikon {
        position: relative;

        flex-shrink: 0;

        width: 28px;
        height: 28px;

        border-radius: 50%;

        background: var(--primary-soft);
    }

    .faq-ikon::before,
    .faq-ikon::after {
        content: "";

        position: absolute;
        top: 50%;
        left: 50%;

        width: 12px;
        height: 2px;

        border-radius: 2px;

        background: var(--primary);

        transform: translate(-50%, -50%);
        transition: transform .3s var(--ease);
    }

    .faq-ikon::after {
        transform: translate(-50%, -50%) rotate(90deg);
    }

    .faq-item[open] .faq-ikon::after {
        transform: translate(-50%, -50%) rotate(0deg);
    }

    .faq-jawaban {
        padding: 0 22px 20px;

        color: var(--muted);

        font-size: 15px;
        line-height: 1.75;
    }

    @media (max-width: 600px) {
        .faq-item summary {
            padding: 16px 18px;

            font-size: 15px;
        }

        .faq-jawaban {
            padding: 0 18px 18px;
        }
    }

    /* =========================================================
       HIGHLIGHT (QUICK CARD)
    ========================================================== */

    .highlights {
        position: relative;
        z-index: 2;

        margin-top: -54px;
    }

    .highlight-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .highlight-card {
        position: relative;
        overflow: hidden;

        padding: 28px;

        transition: transform .35s var(--ease), box-shadow .35s var(--ease), border-color .35s;
    }

    .highlight-card::after {
        content: "";

        position: absolute;
        left: 0;
        bottom: 0;

        width: 100%;
        height: 4px;

        background: linear-gradient(90deg, var(--primary), var(--primary-dark));

        transform: scaleX(0);
        transform-origin: left;

        transition: transform .4s var(--ease);
    }

    .highlight-card:hover {
        transform: translateY(-6px);
        border-color: transparent;
        box-shadow: var(--shadow);
    }

    .highlight-card:hover::after {
        transform: scaleX(1);
    }

    .icon-box {
        width: 54px;
        height: 54px;

        display: grid;
        place-items: center;

        border-radius: 16px;

        background: var(--primary-soft);
        color: var(--primary);

        font-size: 24px;

        transition: transform .35s var(--ease);
    }

    .highlight-card:hover .icon-box {
        transform: rotate(-8deg) scale(1.08);
    }

    .highlight-card h3 {
        margin-top: 18px;

        color: var(--navy);

        font-size: 19px;
    }

    .highlight-card p {
        margin-top: 8px;

        color: var(--muted);

        font-size: 14px;
    }

    .text-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        margin-top: 16px;

        color: var(--primary);

        font-size: 14px;
        font-weight: 700;
    }

    .text-link span {
        transition: transform .25s var(--ease);
    }

    .text-link:hover span {
        transform: translateX(4px);
    }

    /* =========================================================
       PENGUMUMAN
    ========================================================== */

    .announcement-grid {
        display: grid;
        grid-template-columns: 1.15fr 1fr;
        gap: 24px;
    }

    .announcement-main {
        position: relative;
        overflow: hidden;

        padding: 40px;

        border-radius: var(--radius);

        background: linear-gradient(140deg, var(--navy) 0%, var(--primary-dark) 100%);
        color: var(--white);
    }

    .announcement-main::after {
        content: "";

        position: absolute;
        right: -70px;
        bottom: -70px;

        width: 240px;
        height: 240px;

        border: 40px solid rgba(255, 255, 255, .05);
        border-radius: 50%;
    }

    .badge-soft {
        display: inline-block;

        padding: 6px 13px;

        border-radius: 999px;

        background: rgba(255, 255, 255, .14);

        font-size: 12px;
        font-weight: 800;
        letter-spacing: .6px;
    }

    .announcement-main h3 {
        margin-top: 20px;

        font-size: 28px;
        line-height: 1.25;
    }

    .announcement-main p {
        position: relative;
        z-index: 1;

        margin-top: 14px;

        color: rgba(255, 255, 255, .82);

        font-size: 15px;
    }

    .announcement-list {
        display: grid;
        gap: 14px;
    }

    .announcement-item {
        display: flex;
        gap: 16px;

        padding: 22px;

        transition: transform .3s var(--ease), box-shadow .3s var(--ease);
    }

    .announcement-item:hover {
        transform: translateX(6px);
        box-shadow: var(--shadow);
    }

    .announcement-item .num {
        flex-shrink: 0;

        width: 42px;
        height: 42px;

        display: grid;
        place-items: center;

        border-radius: 12px;

        background: var(--primary-soft);
        color: var(--primary-dark);

        font-weight: 800;
    }

    .announcement-item h4 {
        color: var(--navy);
        font-size: 16px;
    }

    .announcement-item p {
        margin-top: 4px;

        color: var(--muted);

        font-size: 14px;
    }

    /* =========================================================
       PERSYARATAN
    ========================================================== */

    .req-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 22px;
    }

    .req-card {
        position: relative;

        padding: 32px 28px;

        transition: transform .35s var(--ease), box-shadow .35s var(--ease);
    }

    .req-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow);
    }

    .req-card .step {
        position: absolute;
        top: 22px;
        right: 24px;

        color: #e7eef4;

        font-size: 46px;
        font-weight: 800;
        line-height: 1;

        transition: color .35s;
    }

    .req-card:hover .step {
        color: rgba(238, 118, 35, .25);
    }

    .req-card h3 {
        margin-top: 20px;

        color: var(--navy);

        font-size: 19px;
    }

    .req-card p {
        margin-top: 8px;

        color: var(--muted);

        font-size: 14px;
    }

    /* =========================================================
       ALUR (TIMELINE)
    ========================================================== */

    .timeline {
        position: relative;

        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;

        list-style: none;
    }

    .timeline::before {
        content: "";

        position: absolute;
        top: 30px;
        left: 12%;
        right: 12%;

        height: 3px;

        border-radius: 3px;

        background: linear-gradient(90deg, var(--primary), var(--primary-dark));

        transform: scaleX(var(--progress, 0));
        transform-origin: left;

        transition: transform 1.4s var(--ease);
    }

    .timeline.is-visible::before {
        --progress: 1;
    }

    .timeline li {
        position: relative;

        text-align: center;
    }

    .timeline .dot {
        position: relative;
        z-index: 1;

        width: 62px;
        height: 62px;

        margin: 0 auto 20px;

        display: grid;
        place-items: center;

        border: 4px solid var(--light);
        border-radius: 50%;

        background: var(--white);
        color: var(--primary);

        font-size: 20px;
        font-weight: 800;

        box-shadow: 0 0 0 2px var(--primary), var(--shadow-sm);

        transition: background .3s, color .3s, transform .3s var(--ease);
    }

    .timeline li:hover .dot {
        background: var(--primary);
        color: var(--white);
        transform: scale(1.1);
    }

    .timeline h3 {
        color: var(--navy);
        font-size: 18px;
    }

    .timeline p {
        max-width: 240px;

        margin: 8px auto 0;

        color: var(--muted);

        font-size: 14px;
    }

    /* =========================================================
       PENDAFTARAN
    ========================================================== */

    .register-wrap {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 28px;
        align-items: start;
    }

    .register-aside {
        position: sticky;
        top: 100px;

        padding: 30px;

        border-radius: var(--radius);

        background: linear-gradient(160deg, var(--navy), var(--primary-dark));
        color: var(--white);
    }

    .register-aside h3 {
        font-size: 22px;
        line-height: 1.3;
    }

    .register-aside > p {
        margin-top: 10px;

        color: rgba(255, 255, 255, .78);

        font-size: 14px;
    }

    .aside-list {
        display: grid;
        gap: 14px;

        margin-top: 24px;

        list-style: none;
    }

    .aside-list li {
        display: flex;
        gap: 12px;

        font-size: 14px;
    }

    .aside-list .ico {
        flex-shrink: 0;

        width: 32px;
        height: 32px;

        display: grid;
        place-items: center;

        border-radius: 10px;

        background: rgba(255, 255, 255, .12);
    }

    .aside-list small {
        display: block;

        color: rgba(255, 255, 255, .68);
        font-size: 12px;
    }

    .aside-gform {
        margin-top: 26px;
        padding-top: 22px;

        border-top: 1px solid rgba(255, 255, 255, .14);
    }

    .aside-gform p {
        margin-bottom: 12px;

        color: rgba(255, 255, 255, .78);

        font-size: 13px;
    }

    .aside-gform .btn {
        width: 100%;
        min-height: 44px;

        background: rgba(255, 255, 255, .1);
        border-color: rgba(255, 255, 255, .28);
        color: var(--white);

        font-size: 14px;
    }

    .aside-gform .btn:hover {
        background: var(--white);
        color: var(--navy);
    }

    .register-card {
        padding: 34px;
    }

    .method-tabs {
        display: inline-flex;
        gap: 4px;

        margin-bottom: 26px;
        padding: 5px;

        border-radius: 999px;

        background: var(--light);
    }

    .method-tab {
        padding: 10px 18px;

        border: 0;
        border-radius: 999px;

        background: transparent;
        color: var(--muted);

        font-size: 14px;
        font-weight: 700;

        cursor: pointer;

        transition: background .25s, color .25s, box-shadow .25s;
    }

    .method-tab[aria-selected="true"] {
        background: var(--white);
        color: var(--primary);
        box-shadow: var(--shadow-sm);
    }

    .method-panel[hidden] {
        display: none;
    }

    .method-panel {
        animation: fadeUp .45s var(--ease);
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(12px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* STEPPER */

    .stepper {
        display: flex;
        align-items: center;

        margin-bottom: 30px;

        list-style: none;
    }

    .stepper li {
        display: flex;
        align-items: center;
        gap: 10px;

        flex: 1;

        color: var(--muted);

        font-size: 13px;
        font-weight: 700;
    }

    .stepper li:not(:last-child)::after {
        content: "";

        flex: 1;

        height: 3px;

        margin: 0 12px;

        border-radius: 3px;

        background: var(--line);

        transition: background .4s;
    }

    .stepper .bullet {
        flex-shrink: 0;

        width: 34px;
        height: 34px;

        display: grid;
        place-items: center;

        border: 2px solid var(--line);
        border-radius: 50%;

        background: var(--white);

        transition: all .35s var(--ease);
    }

    .stepper li.current {
        color: var(--navy);
    }

    .stepper li.current .bullet {
        border-color: var(--primary);
        background: var(--primary);
        color: var(--white);
        box-shadow: 0 0 0 5px rgba(38, 136, 180, .14);
    }

    .stepper li.done .bullet {
        border-color: var(--success);
        background: var(--success);
        color: var(--white);
    }

    .stepper li.done::after {
        background: var(--success);
    }

    .form-step[hidden] {
        display: none;
    }

    .form-step {
        min-width: 0;

        border: 0;

        animation: fadeUp .45s var(--ease);
    }

    .form-step h3 {
        color: var(--navy);
        font-size: 20px;
    }

    .form-step > p {
        margin: 4px 0 22px;

        color: var(--muted);

        font-size: 14px;
    }

    .field-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px 20px;
    }

    .field.full {
        grid-column: 1 / -1;
    }

    .field label {
        display: block;

        margin-bottom: 7px;

        color: var(--navy);

        font-size: 14px;
        font-weight: 700;
    }

    .field label .req {
        color: var(--danger);
    }

    .field input,
    .field textarea {
        width: 100%;

        padding: 13px 15px;

        border: 1.5px solid var(--line);
        border-radius: var(--radius-sm);

        background: #fbfdfe;

        font-size: 15px;

        transition: border-color .2s, box-shadow .2s, background .2s;
    }

    .field textarea {
        min-height: 120px;
        resize: vertical;
    }

    .field input:hover,
    .field textarea:hover {
        border-color: #c7d6e3;
    }

    .field input:focus,
    .field textarea:focus {
        outline: none;
        border-color: var(--primary);
        background: var(--white);
        box-shadow: 0 0 0 4px rgba(38, 136, 180, .12);
    }

    .field.has-error input,
    .field.has-error textarea,
    .field input.is-invalid {
        border-color: var(--danger);
        background: #fffafa;
    }

    .field-hint {
        margin-top: 6px;

        color: var(--muted);

        font-size: 12px;
    }

    .field-error {
        margin-top: 6px;

        color: var(--danger);

        font-size: 13px;
        font-weight: 600;
    }

    .field-meta {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .duration-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        margin-top: 10px;
        padding: 6px 12px;

        border-radius: 999px;

        background: var(--primary-soft);
        color: var(--primary-dark);

        font-size: 13px;
        font-weight: 700;
    }

    .duration-pill[hidden] {
        display: none;
    }

    .dropzone {
        position: relative;

        display: flex;
        align-items: center;
        gap: 16px;

        padding: 22px;

        border: 2px dashed #c8d8e6;
        border-radius: var(--radius-sm);

        background: #fbfdfe;

        cursor: pointer;

        transition: border-color .2s, background .2s;
    }

    .dropzone:hover,
    .dropzone.dragover {
        border-color: var(--primary);
        background: var(--primary-soft);
    }

    .dropzone.has-file {
        border-style: solid;
        border-color: var(--success);
        background: var(--success-soft);
    }

    .dropzone input {
        position: absolute;
        inset: 0;

        opacity: 0;

        cursor: pointer;
    }

    .dropzone .ico {
        flex-shrink: 0;

        width: 50px;
        height: 50px;

        display: grid;
        place-items: center;

        border-radius: 14px;

        background: var(--white);

        font-size: 22px;

        box-shadow: var(--shadow-sm);
    }

    .dropzone strong {
        display: block;

        color: var(--navy);

        font-size: 15px;
    }

    .dropzone small {
        color: var(--muted);
        font-size: 13px;
    }

    .check-field {
        display: flex;
        align-items: flex-start;
        gap: 12px;

        padding: 16px;

        border-radius: var(--radius-sm);

        background: var(--light);

        color: var(--teks);

        font-size: 14px;

        cursor: pointer;
    }

    .check-field input {
        width: 20px;
        height: 20px;

        margin-top: 2px;

        accent-color: var(--primary);
    }

    .form-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;

        margin-top: 30px;
        padding-top: 24px;

        border-top: 1px solid var(--line);
    }

    .form-nav .step-info {
        color: var(--muted);
        font-size: 13px;
        font-weight: 600;
    }

    .form-nav .btns {
        display: flex;
        gap: 10px;

        margin-left: auto;
    }

    .btn[hidden] {
        display: none;
    }

    .btn.loading {
        pointer-events: none;
        opacity: .8;
    }

    .btn.loading::before {
        content: "";

        width: 16px;
        height: 16px;

        border: 2px solid rgba(255, 255, 255, .45);
        border-top-color: var(--white);
        border-radius: 50%;

        animation: spin .8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .alert {
        display: flex;
        gap: 12px;

        margin-bottom: 22px;
        padding: 15px 18px;

        border-radius: var(--radius-sm);

        font-size: 14px;
    }

    .alert-error {
        background: var(--danger-soft);
        color: var(--danger);
    }

    .alert-info {
        background: var(--primary-soft);
        color: var(--primary-dark);
    }

    .alert ul {
        margin-top: 4px;
        padding-left: 18px;
    }

    /* SUKSES */

    .success-state {
        padding: 20px 10px;

        text-align: center;

        animation: fadeUp .6s var(--ease);
    }

    .success-check {
        width: 92px;
        height: 92px;

        margin: 0 auto 22px;

        display: grid;
        place-items: center;

        border-radius: 50%;

        background: var(--success-soft);
    }

    .success-check svg {
        width: 46px;
        height: 46px;

        stroke: var(--success);
        stroke-width: 3;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;

        stroke-dasharray: 60;
        stroke-dashoffset: 60;

        animation: draw .8s .2s var(--ease) forwards;
    }

    @keyframes draw {
        to {
            stroke-dashoffset: 0;
        }
    }

    .success-state h3 {
        color: var(--navy);
        font-size: 26px;
    }

    .success-state p {
        max-width: 480px;

        margin: 10px auto 0;

        color: var(--muted);
    }

    .ticket {
        display: inline-flex;
        flex-direction: column;
        gap: 2px;

        margin: 24px 0;
        padding: 16px 30px;

        border: 2px dashed rgba(38, 136, 180, .35);
        border-radius: 16px;

        background: var(--primary-soft);
    }

    .ticket small {
        color: var(--muted);

        font-size: 12px;
        font-weight: 700;
        letter-spacing: .6px;
        text-transform: uppercase;
    }

    .ticket strong {
        color: var(--primary-dark);

        font-size: 28px;
        letter-spacing: 2px;
    }

    .gform-panel {
        padding: 10px 0;

        text-align: center;
    }

    .gform-panel .icon-box {
        width: 70px;
        height: 70px;

        margin: 0 auto 18px;

        font-size: 32px;
    }

    .gform-panel h3 {
        color: var(--navy);
        font-size: 22px;
    }

    .gform-panel p {
        max-width: 460px;

        margin: 10px auto 24px;

        color: var(--muted);
    }

    /* =========================================================
       CEK STATUS
    ========================================================== */

    .status-box {
        display: grid;
        grid-template-columns: 1fr 1.1fr;
        gap: 40px;
        align-items: center;

        padding: 44px;
    }

    .status-intro h2 {
        color: var(--navy);

        font-size: clamp(26px, 3vw, 34px);
        line-height: 1.25;
    }

    .status-intro p {
        margin-top: 12px;

        color: var(--muted);
    }

    .status-form {
        display: grid;
        gap: 14px;
    }

    .status-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;

        margin-top: 22px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;

        padding: 6px 12px;

        border-radius: 999px;

        font-size: 12px;
        font-weight: 800;
    }

    .status-pill::before {
        content: "";

        width: 8px;
        height: 8px;

        border-radius: 50%;

        background: currentColor;
    }

    .status-menunggu {
        background: #fff6e8;
        color: #b4630c;
    }

    .status-disetujui {
        background: var(--success-soft);
        color: var(--success);
    }

    .status-ditolak {
        background: var(--danger-soft);
        color: var(--danger);
    }

    .status-results {
        display: grid;
        gap: 14px;

        margin-top: 26px;
    }

    .status-result {
        padding: 20px 22px;

        border: 1px solid var(--line);
        border-radius: var(--radius-sm);

        background: var(--white);

        animation: fadeUp .5s var(--ease);
    }

    .status-result-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .status-result-head strong {
        color: var(--navy);
        font-size: 16px;
    }

    .status-result dl {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;

        margin-top: 14px;

        font-size: 13px;
    }

    .status-result dt {
        color: var(--muted);
        font-size: 12px;
    }

    .status-result dd {
        color: var(--navy);
        font-weight: 700;
    }

    .status-note {
        margin-top: 14px;
        padding: 12px 14px;

        border-left: 3px solid var(--primary);
        border-radius: 8px;

        background: var(--light);

        font-size: 14px;
        white-space: pre-line;
    }

    /* =========================================================
       KONTAK
    ========================================================== */

    .contact-grid {
        display: grid;
        grid-template-columns: 1.1fr 1fr;
        gap: 28px;
        align-items: start;
    }

    .about-card {
        padding: 40px;
    }

    .about-card h2 {
        color: var(--navy);

        font-size: clamp(26px, 3vw, 34px);
        line-height: 1.25;
    }

    .about-card p {
        margin-top: 14px;

        color: var(--muted);
    }

    .contact-list {
        display: grid;
        gap: 14px;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 16px;

        padding: 20px 22px;

        transition: transform .3s var(--ease), box-shadow .3s var(--ease);
    }

    .contact-item:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow);
    }

    .contact-item .icon-box {
        flex-shrink: 0;
    }

    .contact-item small {
        display: block;

        color: var(--muted);

        font-size: 12px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
    }

    .contact-item span {
        color: var(--navy);

        font-weight: 700;
        word-break: break-word;
    }

    /* =========================================================
       CTA & FOOTER
    ========================================================== */

    .cta-band {
        position: relative;
        overflow: hidden;

        padding: 56px;

        border-radius: 28px;

        background: linear-gradient(120deg, var(--primary) 0%, var(--navy) 100%);
        color: var(--white);

        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 30px;
    }

    .cta-band::before {
        content: "";

        position: absolute;
        top: -120px;
        right: 18%;

        width: 300px;
        height: 300px;

        border-radius: 50%;

        background: rgba(255, 255, 255, .06);
    }

    .cta-band h2 {
        position: relative;

        font-size: clamp(24px, 3vw, 34px);
        line-height: 1.25;
    }

    .cta-band p {
        position: relative;

        margin-top: 8px;

        color: rgba(255, 255, 255, .8);
    }

    .cta-band .btn {
        position: relative;
        flex-shrink: 0;
    }

    footer {
        padding: 60px 0 30px;

        background: var(--navy);
        color: rgba(255, 255, 255, .72);
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr;
        gap: 40px;

        padding-bottom: 36px;

        border-bottom: 1px solid rgba(255, 255, 255, .1);
    }

    .footer-grid .logo {
        color: var(--white);
    }

    /* Latar footer biru: logo diberi alas putih dan tagline dibuat terang agar terbaca. */
    .footer-grid .logo-mark {
        width: 46px;
        height: 46px;
        padding: 5px;

        border-radius: 12px;

        background: var(--white);
    }

    .footer-grid .logo-teks small {
        color: rgba(255, 255, 255, .78);
    }

    .footer-grid p {
        max-width: 340px;

        margin-top: 14px;

        font-size: 14px;
    }

    .footer-grid h4 {
        margin-bottom: 14px;

        color: var(--white);

        font-size: 15px;
    }

    .footer-links {
        display: grid;
        gap: 9px;

        list-style: none;

        font-size: 14px;
    }

    .footer-links a {
        transition: color .2s, padding .2s;
    }

    .footer-links a:hover {
        padding-left: 4px;
        color: var(--white);
    }

    .footer-bottom {
        padding-top: 24px;

        font-size: 13px;
        text-align: center;
    }

    .to-top {
        position: fixed;
        right: 22px;
        bottom: 22px;
        z-index: 90;

        width: 48px;
        height: 48px;

        display: grid;
        place-items: center;

        border: 0;
        border-radius: 50%;

        background: var(--primary);
        color: var(--white);

        font-size: 20px;

        box-shadow: 0 10px 24px rgba(238, 118, 35, .35);

        cursor: pointer;

        opacity: 0;
        transform: translateY(16px);
        pointer-events: none;

        transition: opacity .3s, transform .3s var(--ease);
    }

    .to-top.show {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    /* =========================================================
       ANIMASI SCROLL
    ========================================================== */

    .js [data-reveal] {
        opacity: 0;
        transform: translateY(28px);

        transition: opacity .8s var(--ease), transform .8s var(--ease);
        transition-delay: var(--delay, 0s);
    }

    .js [data-reveal].is-visible {
        opacity: 1;
        transform: none;
    }

    /* =========================================================
       GALERI DOKUMENTASI: 2 baris ke bawah, sisanya memanjang ke kanan
    ========================================================== */

    .galeri {
        position: relative;
    }

    .galeri-track {
        --tinggi-foto: 190px;

        display: grid;
        grid-auto-flow: column;
        grid-template-rows: repeat(2, var(--tinggi-foto));
        grid-auto-columns: calc((100% - 3 * 14px) / 4);
        gap: 14px;

        padding: 6px 4px 18px;

        overflow-x: auto;
        overscroll-behavior-x: contain;
        scroll-snap-type: x mandatory;
        scroll-padding-inline: 4px;
        scroll-behavior: smooth;
        scrollbar-width: thin;
    }

    /* Foto sedikit: tetap 2 baris, tanpa ruang kosong yang aneh. */
    .galeri-track:has(> .galeri-item:only-child) {
        grid-template-rows: var(--tinggi-foto);
    }

    .galeri-track:focus-visible {
        outline: 3px solid var(--primary-soft);
        border-radius: var(--radius);
    }

    .galeri-item {
        position: relative;

        overflow: hidden;

        padding: 0;

        border: 0;
        border-radius: var(--radius-sm);

        background: var(--light);

        font-family: inherit;
        text-align: left;

        cursor: zoom-in;
        scroll-snap-align: start;
    }

    .galeri-item img {
        width: 100%;
        height: 100%;

        display: block;

        object-fit: cover;

        transition: transform .5s var(--ease);
    }

    .galeri-overlay {
        position: absolute;
        inset: 0;

        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        gap: 4px;

        padding: 14px;

        background: linear-gradient(180deg, rgba(11, 106, 156, 0) 25%, rgba(8, 47, 73, .88) 100%);
        color: #ffffff;

        opacity: 0;

        transition: opacity .25s ease;
    }

    .galeri-overlay strong {
        font-size: 14px;
        line-height: 1.35;
    }

    .galeri-overlay small {
        display: inline-flex;
        align-items: center;
        gap: 5px;

        color: rgba(255, 255, 255, .85);
        font-size: 12px;
    }

    .galeri-overlay > span {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;

        overflow: hidden;

        color: rgba(255, 255, 255, .92);
        font-size: 12.5px;
        line-height: 1.45;
    }

    .galeri-item:hover .galeri-overlay,
    .galeri-item:focus-visible .galeri-overlay,
    .galeri-item.terbuka .galeri-overlay {
        opacity: 1;
    }

    .galeri-item:hover img,
    .galeri-item.terbuka img {
        transform: scale(1.06);
    }

    .galeri-item:focus-visible {
        outline: 3px solid var(--primary);
        outline-offset: 2px;
    }

    .galeri-geser {
        position: absolute;
        top: calc(50% - 10px);
        z-index: 2;

        width: 42px;
        height: 42px;

        display: grid;
        place-items: center;

        border: 1px solid var(--line);
        border-radius: 50%;

        background: var(--white);
        color: var(--navy);

        box-shadow: var(--shadow-sm);

        cursor: pointer;

        transform: translateY(-50%);
    }

    .galeri-geser:disabled {
        opacity: 0;
        pointer-events: none;
    }

    .galeri-geser-kiri {
        left: -16px;
    }

    .galeri-geser-kanan {
        right: -16px;
    }

    .galeri-lightbox {
        width: min(1000px, calc(100% - 24px));
        max-height: calc(100vh - 24px);

        margin: auto;
        padding: 0;

        overflow: hidden;

        border: none;
        border-radius: var(--radius);

        background: #0b1620;
        color: #ffffff;
    }

    .galeri-lightbox::backdrop {
        background: rgba(8, 20, 30, .82);
    }

    .galeri-lightbox figure {
        margin: 0;
    }

    .galeri-lightbox img {
        width: 100%;
        max-height: calc(100vh - 170px);

        display: block;

        object-fit: contain;

        background: #000000;
    }

    .galeri-lightbox figcaption {
        display: flex;
        flex-direction: column;
        gap: 3px;

        padding: 14px 18px 16px;
    }

    .galeri-lightbox figcaption strong {
        font-size: 15px;
    }

    .galeri-lightbox figcaption small {
        color: rgba(255, 255, 255, .7);
        font-size: 12.5px;
    }

    .galeri-lightbox figcaption span {
        color: rgba(255, 255, 255, .88);
        font-size: 13.5px;
        line-height: 1.55;
    }

    .galeri-tutup,
    .galeri-pindah {
        position: absolute;
        z-index: 2;

        width: 40px;
        height: 40px;

        display: grid;
        place-items: center;

        border: none;
        border-radius: 50%;

        background: rgba(0, 0, 0, .55);
        color: #ffffff;

        cursor: pointer;
    }

    .galeri-tutup {
        top: 12px;
        right: 12px;
    }

    .galeri-pindah {
        top: calc((100vh - 170px) / 2);
        max-height: none;
    }

    .galeri-pindah-kiri {
        left: 12px;
    }

    .galeri-pindah-kanan {
        right: 12px;
    }

    .galeri-pindah:disabled {
        display: none;
    }

    @media (max-width: 1000px) {
        .galeri-track {
            grid-auto-columns: calc((100% - 2 * 14px) / 3);
        }
    }

    @media (max-width: 700px) {
        .galeri-track {
            --tinggi-foto: 140px;

            grid-auto-columns: 62%;
        }

        .galeri-geser {
            display: none;
        }

        .galeri-pindah {
            top: auto;
            bottom: 90px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        html {
            scroll-behavior: auto;
        }

        *,
        *::before,
        *::after {
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: .01ms !important;
        }

        .js [data-reveal] {
            opacity: 1;
            transform: none;
        }
    }

    /* =========================================================
       RESPONSIVE
    ========================================================== */

    @media (max-width: 1080px) {
        .hero-grid,
        .register-wrap,
        .status-box,
        .contact-grid,
        .announcement-grid {
            grid-template-columns: 1fr;
        }

        .hero-visual {
            width: 100%;
            max-width: 560px;
            margin: 0 auto;
        }

        .register-aside {
            position: static;
        }

        .timeline {
            grid-template-columns: repeat(2, 1fr);
            row-gap: 40px;
        }

        .timeline::before {
            display: none;
        }
    }

    /* Navbar: menu dirapatkan, lalu dilipat menjadi tombol menu. */
    @media (max-width: 1200px) {
        .nav-menu a {
            padding: 9px 9px;
        }

        /* Tombol Login dirapatkan (jangan disembunyikan: menu lipat baru muncul di ≤ 1024px). */
        .nav-actions .btn {
            padding: 0 16px;
        }
    }

    @media (max-width: 1024px) {
        .nav-toggle {
            display: block;
        }

        .nav-menu {
            position: absolute;
            top: 100%;
            left: 20px;
            right: 20px;

            flex-direction: column;
            align-items: stretch;

            padding: 12px;

            border-radius: var(--radius);

            background: var(--white);

            box-shadow: var(--shadow);

            opacity: 0;
            transform: translateY(-10px);
            pointer-events: none;

            transition: opacity .25s, transform .3s var(--ease);
        }

        .nav-open .nav-menu {
            opacity: 1;
            transform: none;
            pointer-events: auto;
        }

        .nav-menu a {
            display: block;

            padding: 12px 16px;
        }

        .nav-menu a.active::after {
            display: none;
        }

        .nav-menu a.active {
            background: var(--primary-soft);
        }

        .nav-cta {
            display: none;
        }

        .nav-menu-masuk {
            display: block;

            margin-top: 6px;
            padding-top: 6px;

            border-top: 1px solid var(--line);
        }

        .nav-menu-masuk a {
            color: var(--primary);
            font-weight: 800;
        }

        .navbar {
            background: rgba(255, 255, 255, .9);
            backdrop-filter: blur(14px);
        }
    }

    @media (max-width: 880px) {
        .highlight-grid,
        .req-grid {
            grid-template-columns: 1fr;
        }

        .cta-band {
            flex-direction: column;
            align-items: flex-start;

            padding: 40px 30px;
        }

        .footer-grid {
            grid-template-columns: 1fr;
            gap: 28px;
        }
    }

    @media (max-width: 640px) {
        .section {
            padding: 72px 0;
        }

        .hero {
            padding: 120px 0 90px;
        }

        .hero-grid {
            gap: 44px;
        }

        .float-card {
            padding: 10px 13px;
        }

        .float-card .icon {
            width: 34px;
            height: 34px;
        }

        .float-card-1 {
            left: 8px;
            bottom: -22px;
        }

        .float-card-2 {
            right: 8px;
            top: -20px;
        }

        .field-grid,
        .timeline,
        .status-result dl {
            grid-template-columns: 1fr;
        }

        .register-card,
        .status-box,
        .about-card,
        .announcement-main {
            padding: 24px;
        }

        .stepper .label {
            display: none;
        }

        .method-tabs {
            display: flex;
        }

        .method-tab {
            flex: 1;
            padding: 10px;
        }

        .form-nav {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .form-nav .btns {
            margin-left: 0;
        }

        .form-nav .btns .btn {
            flex: 1;
        }

        .form-nav .step-info {
            text-align: center;
        }
    }
</style>
