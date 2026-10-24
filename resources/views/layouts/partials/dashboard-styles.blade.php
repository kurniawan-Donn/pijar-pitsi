{{-- CSS bersama layout dashboard (sidebar, topbar, dan komponen). --}}
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        background: #f4f7fb;
        color: #1f2937;
    }

    a {
        text-decoration: none;
    }

    button {
        font-family: inherit;
    }

    /* =========================================================
       SIDEBAR
    ========================================================== */

    .dash-sidebar {
        position: fixed;
        top: 0;
        left: 0;

        width: 275px;
        height: 100vh;

        overflow-y: auto;

        background: #12345b;
        color: #ffffff;

        padding: 24px 18px;

        display: flex;
        flex-direction: column;

        z-index: 1000;
    }

    .dash-brand {
        display: flex;
        align-items: center;
        gap: 12px;

        padding: 4px 8px 28px;

        color: #ffffff;
    }

    .dash-brand-logo {
        width: 42px;
        height: 42px;

        border-radius: 10px;

        background: #ffffff;
        color: #12345b;

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 18px;
        font-weight: 800;
    }

    .dash-brand-text {
        min-width: 0;
    }

    .dash-brand-title {
        font-size: 19px;
        font-weight: 800;
        letter-spacing: .5px;
        line-height: 1.2;
    }

    .dash-brand-subtitle {
        margin-top: 4px;

        color: #b8cbe0;

        font-size: 11px;
        line-height: 1.4;
    }

    .dash-sidebar-section {
        margin-top: 8px;
    }

    .dash-sidebar-section + .dash-sidebar-section {
        margin-top: 22px;
    }

    .dash-sidebar-section-title {
        padding: 0 12px;
        margin-bottom: 8px;

        color: #8eaccb;

        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .dash-sidebar-menu {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .dash-sidebar-link {
        display: flex;
        align-items: center;
        gap: 12px;

        min-height: 46px;

        padding: 0 13px;

        border-radius: 9px;

        color: #d9e6f4;

        font-size: 14px;
        font-weight: 600;

        transition: .2s ease;
    }

    .dash-sidebar-link:hover {
        background: rgba(255, 255, 255, .08);
        color: #ffffff;
    }

    .dash-sidebar-link.active {
        background: #1779c9;
        color: #ffffff;

        box-shadow: 0 5px 14px rgba(23, 121, 201, .22);
    }

    .dash-sidebar-icon {
        width: 22px;
        text-align: center;

        font-size: 17px;

        flex-shrink: 0;
    }

    .dash-sidebar-footer {
        margin-top: auto;
        padding-top: 18px;

        border-top: 1px solid rgba(255, 255, 255, .10);
    }

    .dash-logout-button {
        width: 100%;

        display: flex;
        align-items: center;
        gap: 12px;

        min-height: 46px;

        padding: 0 13px;

        border: 0;
        border-radius: 9px;

        background: transparent;
        color: #d9e6f4;

        font-size: 14px;
        font-weight: 600;

        cursor: pointer;

        transition: .2s ease;
    }

    .dash-logout-button:hover {
        background: rgba(255, 255, 255, .08);
        color: #ffffff;
    }

    /* =========================================================
       MAIN & TOPBAR
    ========================================================== */

    .dash-main {
        margin-left: 275px;
        min-height: 100vh;
    }

    .dash-topbar {
        min-height: 76px;

        padding: 0 32px;

        background: #ffffff;

        border-bottom: 1px solid #e5eaf0;

        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .dash-topbar-left h1 {
        font-size: 21px;
        font-weight: 750;
        color: #17324d;
    }

    .dash-topbar-left p {
        margin-top: 4px;

        font-size: 12px;
        color: #7b8794;
    }

    .dash-user-info {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .dash-user-avatar {
        width: 40px;
        height: 40px;

        border-radius: 50%;

        background: #eaf3fb;
        color: #1779c9;

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 15px;
        font-weight: 800;
    }

    .dash-user-text {
        text-align: right;
    }

    .dash-user-name {
        font-size: 13px;
        font-weight: 700;
        color: #23384d;
    }

    .dash-user-role {
        margin-top: 3px;

        font-size: 11px;
        color: #7b8794;
    }

    /* =========================================================
       KOMPONEN: ALERT
    ========================================================== */

    .x-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;

        margin-bottom: 20px;
        padding: 14px 16px;

        border: 1px solid;
        border-radius: 10px;

        font-size: 14px;
        line-height: 1.6;
    }

    .x-alert-icon {
        flex-shrink: 0;
        font-weight: 800;
    }

    .x-alert ul {
        margin-top: 4px;
        padding-left: 18px;
    }

    .x-alert-success {
        background: #ecfdf3;
        border-color: #abefc6;
        color: #067647;
    }

    .x-alert-error {
        background: #fef3f2;
        border-color: #fecdca;
        color: #b42318;
    }

    .x-alert-warning {
        background: #fff8f0;
        border-color: #fbd7ae;
        color: #a85a12;
    }

    .x-alert-info {
        background: #eaf3fb;
        border-color: #c4dcf1;
        color: #175c95;
    }

    /* =========================================================
       KOMPONEN: KARTU STATISTIK
    ========================================================== */

    .x-stat-card {
        display: flex;
        align-items: center;
        gap: 15px;

        padding: 20px;

        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .x-stat-icon {
        width: 46px;
        height: 46px;

        flex-shrink: 0;

        border-radius: 11px;

        background: #eaf3fb;
        color: #1779c9;

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 20px;
    }

    .x-stat-label {
        color: #7b8794;
        font-size: 12px;
        font-weight: 600;
    }

    .x-stat-value {
        margin-top: 4px;

        color: #17324d;
        font-size: 24px;
        font-weight: 800;
    }

    .x-stat-hint {
        margin-top: 3px;

        color: #8a96a3;
        font-size: 11px;
    }

    /* =========================================================
       KOMPONEN: TOMBOL
    ========================================================== */

    .x-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;

        min-width: 120px;
        height: 40px;
        padding: 0 16px;

        border: 1px solid transparent;
        border-radius: 7px;

        font-family: inherit;
        font-size: 12px;
        font-weight: 700;

        cursor: pointer;

        transition: .2s ease;
    }

    .x-btn-primary {
        background: #1779c9;
        border-color: #1779c9;
        color: #ffffff;
    }

    .x-btn-primary:hover {
        background: #1269b1;
        border-color: #1269b1;
    }

    .x-btn-secondary {
        background: #ffffff;
        border-color: #d7e0eb;
        color: #60758d;
    }

    .x-btn-secondary:hover {
        background: #f4f7fb;
    }

    .x-btn-danger {
        background: #d92d20;
        border-color: #d92d20;
        color: #ffffff;
    }

    .x-btn-danger:hover {
        background: #b42318;
        border-color: #b42318;
    }

    /* =========================================================
       KOMPONEN: INPUT FORM
    ========================================================== */

    .x-form-group {
        margin-bottom: 18px;
    }

    .x-form-label {
        display: block;

        margin-bottom: 7px;

        color: #174a7e;
        font-size: 12px;
        font-weight: 700;
    }

    .x-form-required {
        color: #e05252;
    }

    .x-form-control {
        width: 100%;
        height: 42px;

        padding: 0 12px;

        border: 1px solid #d7e0eb;
        border-radius: 7px;

        background: #ffffff;
        color: #243f5c;

        font-family: inherit;
        font-size: 12px;

        outline: none;

        transition: .2s ease;
    }

    textarea.x-form-control {
        height: auto;
        padding: 10px 12px;
        line-height: 1.6;
    }

    .x-form-control:focus {
        border-color: #1779c9;
        box-shadow: 0 0 0 3px rgba(23, 121, 201, .08);
    }

    .x-form-control.is-invalid {
        border-color: #e05252;
    }

    .x-form-hint {
        margin-top: 6px;

        color: #879bb1;
        font-size: 10px;
        line-height: 1.5;
    }

    .x-form-error {
        margin-top: 6px;

        color: #e05252;
        font-size: 11px;
        font-weight: 600;
    }

    /* =========================================================
       RESPONSIVE
    ========================================================== */

    @media (max-width: 800px) {

        .dash-sidebar {
            position: static;

            width: 100%;
            height: auto;

            padding: 18px;
        }

        .dash-sidebar-footer {
            margin-top: 20px;
        }

        .dash-main {
            margin-left: 0;
        }

        .dash-topbar {
            padding: 18px 20px;
        }
    }

    @media (max-width: 600px) {

        .dash-topbar {
            align-items: flex-start;
            flex-direction: column;
        }

        .dash-user-info {
            width: 100%;
            justify-content: flex-start;
        }

        .dash-user-text {
            text-align: left;
        }
    }
</style>
