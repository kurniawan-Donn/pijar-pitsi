{{-- CSS bersama layout dashboard (sidebar, topbar, dan komponen). Warna dari partials/tema. --}}
@include('partials.tema')

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: var(--font);
        background: var(--latar);
        color: var(--teks);
        font-size: 14px;
        line-height: 1.5;
        -webkit-font-smoothing: antialiased;
    }

    a {
        color: var(--utama-gelap);
        text-decoration: none;
    }

    button {
        font-family: inherit;
    }

    .x-icon {
        flex-shrink: 0;
        vertical-align: middle;
    }

    :focus-visible {
        outline: 2px solid var(--utama);
        outline-offset: 2px;
    }

    /* =========================================================
       SIDEBAR (terang; ikon & teks abu netral, menu aktif berwarna utama)
    ========================================================== */

    .dash-sidebar {
        position: fixed;
        top: 0;
        left: 0;

        width: 264px;
        height: 100vh;

        overflow-y: auto;

        background: var(--permukaan);
        border-right: 1px solid var(--garis);

        padding: 20px 14px;

        display: flex;
        flex-direction: column;

        z-index: 1000;
    }

    .dash-brand {
        display: flex;
        align-items: center;
        gap: 11px;

        padding: 2px 10px 22px;
    }

    .dash-brand-logo {
        width: 38px;
        height: 38px;

        object-fit: contain;
        flex-shrink: 0;
    }

    .dash-brand-text {
        min-width: 0;
    }

    .dash-brand-title {
        color: var(--utama-gelap);

        font-size: 18px;
        font-weight: 800;
        letter-spacing: 1.5px;
        line-height: 1.2;
    }

    .dash-brand-subtitle {
        margin-top: 2px;

        color: var(--teks-sekunder);

        font-size: 11px;
        line-height: 1.35;
    }

    .dash-sidebar-section + .dash-sidebar-section {
        margin-top: 18px;
    }

    .dash-sidebar-section-title {
        padding: 0 12px;
        margin-bottom: 6px;

        color: var(--teks-redup);

        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .8px;
        text-transform: uppercase;
    }

    .dash-sidebar-menu {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .dash-sidebar-link,
    .dash-logout-button {
        display: flex;
        align-items: center;
        gap: 11px;

        min-height: 40px;
        width: 100%;

        padding: 0 12px;

        border: 0;
        border-radius: var(--radius-kecil);

        background: transparent;
        color: var(--teks-sekunder);

        font-size: 13.5px;
        font-weight: 600;
        text-align: left;

        cursor: pointer;

        transition: background .15s ease, color .15s ease;
    }

    .dash-sidebar-link:hover,
    .dash-logout-button:hover {
        background: var(--permukaan-redup);
        color: var(--teks);
    }

    .dash-sidebar-link.active {
        background: var(--utama-muda);
        color: var(--utama-gelap);
    }

    .dash-sidebar-link.active .x-icon {
        color: var(--utama);
    }

    .dash-sidebar-link .terkunci {
        margin-left: 2px;

        color: var(--teks-redup);
    }

    .dash-sidebar-badge {
        margin-left: auto;

        min-width: 20px;
        height: 20px;

        padding: 0 6px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        border-radius: 999px;

        background: var(--aksen);
        color: #ffffff;

        font-size: 11px;
        font-weight: 700;
    }

    .dash-sidebar-footer {
        margin-top: auto;
        padding-top: 14px;

        border-top: 1px solid var(--garis);
    }

    .dash-overlay {
        display: none;
    }

    /* =========================================================
       MAIN & TOPBAR
    ========================================================== */

    .dash-main {
        margin-left: 264px;
        min-height: 100vh;
    }

    .dash-topbar {
        position: sticky;
        top: 0;
        z-index: 900;

        min-height: 68px;

        padding: 12px 32px;

        background: var(--permukaan);

        border-bottom: 1px solid var(--garis);

        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .dash-menu-toggle {
        display: none;

        width: 40px;
        height: 40px;

        flex-shrink: 0;
        align-items: center;
        justify-content: center;

        border: 1px solid var(--garis);
        border-radius: var(--radius-kecil);

        background: var(--permukaan);
        color: var(--teks);

        cursor: pointer;
    }

    .dash-topbar-left {
        flex: 1;
        min-width: 0;
    }

    .dash-topbar-left h1 {
        color: var(--teks);

        font-size: 19px;
        font-weight: 700;
        line-height: 1.3;
    }

    .dash-topbar-left p {
        margin-top: 2px;

        color: var(--teks-sekunder);
        font-size: 12.5px;
    }

    .dash-user-info {
        display: flex;
        align-items: center;
        gap: 10px;

        padding: 4px 6px;

        border-radius: var(--radius-kecil);
    }

    .dash-user-info:hover {
        background: var(--permukaan-redup);
    }

    .dash-user-text {
        text-align: right;
    }

    .dash-user-name {
        color: var(--teks);

        font-size: 13px;
        font-weight: 700;
    }

    .dash-user-role {
        margin-top: 1px;

        color: var(--teks-sekunder);
        font-size: 11.5px;
    }

    /* =========================================================
       ISI HALAMAN: jarak, panel, judul, tabel, data kosong
       (dipakai lintas halaman agar seragam)
    ========================================================== */

    .content {
        padding: 28px 32px 40px;
    }

    .panel {
        background: var(--permukaan);

        border: 1px solid var(--garis);
        border-radius: var(--radius);

        box-shadow: var(--bayangan);
    }

    .panel h3,
    .section-title {
        color: var(--teks);
    }

    .table-scroll,
    .table-wrap,
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .x-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;

        padding: 36px 20px;

        color: var(--teks-sekunder);
        font-size: 13px;
        text-align: center;
    }

    .x-empty-icon {
        width: 48px;
        height: 48px;

        display: grid;
        place-items: center;

        border-radius: 50%;

        background: var(--permukaan-redup);
        color: var(--teks-redup);
    }

    .x-empty-title {
        color: var(--teks);
        font-size: 14px;
        font-weight: 700;
    }

    /* =========================================================
       KOMPONEN: ALERT
    ========================================================== */

    .x-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;

        margin-bottom: 20px;
        padding: 12px 14px;

        border: 1px solid;
        border-radius: var(--radius-kecil);

        font-size: 13.5px;
        line-height: 1.6;
    }

    .x-alert-icon {
        flex-shrink: 0;
        margin-top: 2px;
    }

    .x-alert ul {
        margin-top: 4px;
        padding-left: 18px;
    }

    .x-alert-success {
        background: var(--sukses-muda);
        border-color: #abefc6;
        color: var(--sukses);
    }

    .x-alert-error {
        background: var(--bahaya-muda);
        border-color: #fecdca;
        color: var(--bahaya);
    }

    .x-alert-warning {
        background: var(--peringatan-muda);
        border-color: #fedf89;
        color: var(--peringatan);
    }

    .x-alert-info {
        background: var(--info-muda);
        border-color: #bfdcec;
        color: var(--info);
    }

    /* =========================================================
       KOMPONEN: KARTU STATISTIK
    ========================================================== */

    .x-stat-card {
        display: flex;
        align-items: center;
        gap: 14px;

        padding: 18px;

        background: var(--permukaan);

        border: 1px solid var(--garis);
        border-radius: var(--radius);

        box-shadow: var(--bayangan);
    }

    .x-stat-icon {
        width: 42px;
        height: 42px;

        flex-shrink: 0;

        border-radius: 10px;

        background: var(--utama-muda);
        color: var(--utama);

        display: flex;
        align-items: center;
        justify-content: center;
    }

    .x-stat-label {
        color: var(--teks-sekunder);
        font-size: 12px;
        font-weight: 600;
    }

    .x-stat-value {
        margin-top: 2px;

        color: var(--teks);
        font-size: 22px;
        font-weight: 800;
    }

    .x-stat-hint {
        margin-top: 2px;

        color: var(--teks-redup);
        font-size: 11px;
    }

    /* =========================================================
       KOMPONEN: TOMBOL
    ========================================================== */

    .x-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;

        min-width: 110px;
        height: 38px;
        padding: 0 16px;

        border: 1px solid transparent;
        border-radius: var(--radius-kecil);

        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;

        cursor: pointer;

        transition: background .15s ease, border-color .15s ease;
    }

    .x-btn:disabled,
    .x-btn[aria-disabled="true"] {
        opacity: .5;
        cursor: not-allowed;
    }

    .x-btn-primary {
        background: var(--utama);
        border-color: var(--utama);
        color: #ffffff;
    }

    .x-btn-primary:hover {
        background: var(--utama-gelap);
        border-color: var(--utama-gelap);
    }

    .x-btn-secondary {
        background: var(--permukaan);
        border-color: var(--garis-kuat);
        color: var(--teks);
    }

    .x-btn-secondary:hover {
        background: var(--permukaan-redup);
    }

    .x-btn-danger {
        background: var(--permukaan);
        border-color: #fecdca;
        color: var(--bahaya);
    }

    .x-btn-danger:hover {
        background: var(--bahaya-muda);
    }

    .x-btn-accent {
        background: var(--aksen);
        border-color: var(--aksen);
        color: #ffffff;
    }

    /* =========================================================
       KOMPONEN: INPUT FORM
    ========================================================== */

    .x-form-group {
        margin-bottom: 16px;
    }

    .x-form-label {
        display: block;

        margin-bottom: 6px;

        color: var(--teks);
        font-size: 12.5px;
        font-weight: 600;
    }

    .x-form-required {
        color: var(--bahaya);
    }

    .x-form-control {
        width: 100%;
        height: 40px;

        padding: 0 12px;

        border: 1px solid var(--garis-kuat);
        border-radius: var(--radius-kecil);

        background: var(--permukaan);
        color: var(--teks);

        font-family: inherit;
        font-size: 13.5px;

        outline: none;

        transition: border-color .15s ease, box-shadow .15s ease;
    }

    input[type="file"].x-form-control {
        height: auto;
        padding: 7px 10px;
    }

    textarea.x-form-control {
        height: auto;
        padding: 10px 12px;
        line-height: 1.6;
    }

    .x-form-control:focus {
        border-color: var(--utama);
        box-shadow: var(--fokus);
    }

    .x-form-control:disabled {
        background: var(--permukaan-redup);
        color: var(--teks-sekunder);
    }

    .x-form-control.is-invalid {
        border-color: var(--bahaya);
    }

    .x-form-hint {
        margin-top: 5px;

        color: var(--teks-sekunder);
        font-size: 11.5px;
        line-height: 1.5;
    }

    .x-form-error {
        margin-top: 5px;

        color: var(--bahaya);
        font-size: 12px;
        font-weight: 600;
    }

    .x-pratinjau-pesan {
        margin-top: 6px;

        color: var(--peringatan);
        font-size: 11.5px;
        font-weight: 600;
    }

    .x-pratinjau-pesan.ok {
        color: var(--sukses);
    }

    .x-hitung-karakter {
        margin-top: 4px;

        color: var(--teks-sekunder);
        font-size: 11px;
        text-align: right;
    }

    .x-hitung-karakter.hampir {
        color: var(--peringatan);
        font-weight: 700;
    }

    /* =========================================================
       KOMPONEN: AVATAR (foto profil / inisial)
    ========================================================== */

    .x-avatar {
        width: var(--avatar, 40px);
        height: var(--avatar, 40px);

        flex-shrink: 0;

        border-radius: 50%;

        object-fit: cover;
    }

    .x-avatar-inisial {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        background: var(--utama-muda);
        color: var(--utama-gelap);

        font-size: calc(var(--avatar, 40px) * .4);
        font-weight: 700;
        line-height: 1;
    }

    /* =========================================================
       KOMPONEN: BADGE STATUS
       Nada: menunggu/review (kuning), diterima/disetujui/aktif (hijau),
       ditolak (merah), selesai (biru), lainnya (abu).
    ========================================================== */

    .x-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        padding: 3px 10px;

        border-radius: 999px;

        background: var(--permukaan-redup);
        color: var(--teks-sekunder);
        box-shadow: inset 0 0 0 1px var(--garis);

        font-size: 11.5px;
        font-weight: 600;
        line-height: 1.5;
        white-space: nowrap;
    }

    .x-badge::before {
        content: "";

        width: 6px;
        height: 6px;

        border-radius: 50%;

        background: currentColor;
    }

    .x-badge-menunggu,
    .x-badge-diproses,
    .x-badge-review,
    .x-badge-diajukan,
    .x-badge-perlu_revisi {
        background: var(--peringatan-muda);
        color: var(--peringatan);
        box-shadow: inset 0 0 0 1px #fedf89;
    }

    .x-badge-disetujui,
    .x-badge-diterima,
    .x-badge-aktif,
    .x-badge-terbit {
        background: var(--sukses-muda);
        color: var(--sukses);
        box-shadow: inset 0 0 0 1px #abefc6;
    }

    .x-badge-ditolak,
    .x-badge-nonaktif,
    .x-badge-dibatalkan {
        background: var(--bahaya-muda);
        color: var(--bahaya);
        box-shadow: inset 0 0 0 1px #fecdca;
    }

    .x-badge-selesai {
        background: var(--info-muda);
        color: var(--info);
        box-shadow: inset 0 0 0 1px #bfdcec;
    }

    /* =========================================================
       KOMPONEN: PAGINATION
    ========================================================== */

    .x-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;

        padding: 14px 20px;

        border-top: 1px solid var(--garis);
    }

    .x-pagination-info {
        color: var(--teks-sekunder);
        font-size: 12.5px;
    }

    .x-pagination-links {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 4px;
    }

    .x-pagination-links a,
    .x-pagination-links span {
        min-width: 32px;
        height: 32px;

        padding: 0 8px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        border: 1px solid var(--garis);
        border-radius: var(--radius-kecil);

        background: var(--permukaan);
        color: var(--teks-sekunder);

        font-size: 12.5px;
        font-weight: 600;
    }

    .x-pagination-links span[aria-current="page"] {
        background: var(--utama);
        border-color: var(--utama);
        color: #ffffff;
    }

    .x-pagination-links span[aria-disabled="true"] {
        opacity: .45;
    }

    .x-pagination-links a:hover {
        background: var(--permukaan-redup);
    }

    /* =========================================================
       RESPONSIVE: di layar kecil sidebar menjadi panel geser
    ========================================================== */

    @media (max-width: 960px) {

        .dash-sidebar {
            transform: translateX(-100%);
            transition: transform .2s ease;

            box-shadow: 0 10px 40px rgba(17, 24, 39, .18);
        }

        body.menu-terbuka .dash-sidebar {
            transform: none;
        }

        body.menu-terbuka .dash-overlay {
            display: block;

            position: fixed;
            inset: 0;
            z-index: 999;

            background: rgba(17, 24, 39, .35);
        }

        body.menu-terbuka {
            overflow: hidden;
        }

        .dash-main {
            margin-left: 0;
        }

        .dash-menu-toggle {
            display: inline-flex;
        }

        .dash-topbar {
            padding: 10px 16px;
        }

        .content {
            padding: 20px 16px 32px !important;
        }
    }

    @media (max-width: 600px) {

        .dash-topbar-left h1 {
            font-size: 16px;
        }

        .dash-topbar-left p,
        .dash-user-text {
            display: none;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        * {
            transition: none !important;
        }
    }
</style>
