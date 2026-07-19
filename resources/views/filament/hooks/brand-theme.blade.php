<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --gudang-maroon-900: #112E81;
        --gudang-maroon-800: #112E81;
        --gudang-maroon-700: #112E81;
        --gudang-maroon-100: #112E81;
    }

    .fi-topbar {
        background-image: linear-gradient(
            to right,
            var(--gudang-maroon-900) 0,
            var(--gudang-maroon-900) 20rem,
            #ffffff 20rem,
            #ffffff 100%
        );
    }

    .fi-topbar-start {
        color: #ffffff;
    }

    .fi-topbar-start a,
    .fi-topbar-start .fi-logo,
    .fi-topbar-start .fi-logo * {
        color: #ffffff !important;
        fill: currentColor;
    }

    .fi-topbar .fi-icon-btn,
    .fi-topbar .fi-topbar-open-sidebar-btn,
    .fi-topbar .fi-topbar-close-sidebar-btn {
        color: #ffffff;
    }

    .fi-sidebar,
    .fi-sidebar-header-ctn,
    .fi-sidebar-header,
    .fi-sidebar-nav,
    .fi-sidebar-footer {
        background: var(--gudang-maroon-900) !important;
    }

    .fi-sidebar-header-logo-ctn,
    .fi-sidebar-header-logo-ctn a,
    .fi-sidebar-header-logo-ctn .fi-logo,
    .fi-sidebar-header-logo-ctn .fi-logo * {
        color: #ffffff !important;
        fill: currentColor;
    }

    .fi-sidebar-group-label,
    .fi-sidebar-item-label,
    .fi-sidebar-item-btn,
    .fi-sidebar-group-btn,
    .fi-sidebar-group-collapse-btn,
    .fi-sidebar-group-dropdown-trigger-btn,
    .fi-sidebar-item-icon,
    .fi-sidebar-group svg,
    .fi-sidebar-item svg,
    .fi-sidebar-open-collapse-sidebar-btn,
    .fi-sidebar-close-collapse-sidebar-btn {
        color: #ffffff !important;
        stroke: currentColor;
    }

    .fi-sidebar-group-btn,
    .fi-sidebar-item-btn,
    .fi-sidebar-group-dropdown-trigger-btn {
        border-radius: 0.9rem;
    }

    .fi-sidebar-item-btn:hover,
    .fi-sidebar-group-btn:hover,
    .fi-sidebar-group-dropdown-trigger-btn:hover,
    .fi-sidebar-item.fi-active .fi-sidebar-item-btn,
    .fi-sidebar-item.fi-sidebar-item-has-active-child-items .fi-sidebar-item-btn {
        background: rgba(255, 255, 255, 0.12) !important;
    }

    .fi-sidebar-group {
        border-color: rgba(255, 255, 255, 0.08);
    }

    .fi-sidebar-item-grouped-border,
    .fi-sidebar-item-grouped-border-part,
    .fi-sidebar-item-grouped-border-part-not-first,
    .fi-sidebar-item-grouped-border-part-not-last {
        background: rgba(255, 255, 255, 0.25) !important;
    }

    .fi-sidebar .fi-badge {
        background: rgba(255, 255, 255, 0.14) !important;
        color: #ffffff !important;
    }

    .fi-sidebar .fi-sidebar-item.fi-active .fi-badge,
    .fi-sidebar .fi-sidebar-item.fi-sidebar-item-has-active-child-items .fi-badge {
        background: rgba(255, 255, 255, 0.2) !important;
    }

    @media (max-width: 1024px) {
        .fi-topbar {
            background: #ffffff;
        }

        .fi-topbar-start,
        .fi-topbar .fi-icon-btn,
        .fi-topbar .fi-topbar-open-sidebar-btn,
        .fi-topbar .fi-topbar-close-sidebar-btn {
            color: inherit;
        }
    }

    /* ==========================================================================
       Clean & Simple Login Page Styling (Blue Background, White Card)
       ========================================================================== */
    
    body,
    .fi-simple-layout,
    .fi-simple-main,
    .fi-simple-main-ctn,
    input,
    button,
    select,
    textarea {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    }

    /* Page background - same brand blue as sidebar */
    .fi-simple-layout {
        background: #112E81 !important;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .fi-simple-main-ctn {
        width: 100%;
        max-width: 28rem;
    }

    /* Card background - solid white */
    .fi-simple-main {
        background: #ffffff !important;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.2) !important;
        border-radius: 16px !important;
        padding: 2.25rem !important;
    }

    /* Align brand logo and headers */
    .fi-simple-header {
        text-align: center !important;
        margin-bottom: 1.5rem !important;
    }

    /* Money Tracker logo and heading - dark slate */
    .fi-simple-header a,
    .fi-simple-header .fi-logo,
    .fi-simple-header-title {
        color: #1e293b !important;
        font-weight: 700 !important;
    }

    .fi-simple-header a,
    .fi-simple-header .fi-logo {
        font-size: 1.5rem !important;
        letter-spacing: -0.02em !important;
    }

    .fi-simple-header-title {
        font-size: 1.15rem !important;
        margin-top: 0.5rem !important;
        opacity: 0.9 !important;
    }

    /* Form labels, texts, and links - dark slate */
    .fi-simple-main-ctn label,
    .fi-simple-main-ctn span:not(.fi-btn-label),
    .fi-simple-main-ctn a,
    .fi-simple-main-ctn .fi-link {
        color: #1e293b !important;
        font-weight: 500 !important;
        font-size: 0.875rem !important;
    }

    /* Links - brand blue with hover */
    .fi-simple-main-ctn a,
    .fi-simple-main-ctn .fi-link {
        color: #112E81 !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        transition: color 0.2s ease !important;
    }

    .fi-simple-main-ctn a:hover,
    .fi-simple-main-ctn .fi-link:hover {
        color: #1d4ed8 !important;
    }

    /* Required field asterisk */
    .fi-simple-main-ctn label span[class*="text-danger"] {
        color: #ef4444 !important;
    }

    /* Clean input field styling (white bg, grey border, dark text) */
    .fi-simple-main-ctn .fi-input-wrp {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        box-shadow: none !important;
        transition: all 0.2s ease !important;
    }

    .fi-simple-main-ctn .fi-input-wrp input {
        color: #0f172a !important;
        font-size: 0.95rem !important;
    }

    .fi-simple-main-ctn .fi-input-wrp:focus-within {
        border-color: #112E81 !important;
        box-shadow: 0 0 0 3px rgba(17, 46, 129, 0.15) !important;
    }

    /* Checkbox styling */
    .fi-simple-main-ctn input[type="checkbox"] {
        border-color: #cbd5e1 !important;
        border-radius: 4px !important;
        cursor: pointer !important;
    }

    .fi-simple-main-ctn input[type="checkbox"]:checked {
        background-color: #112E81 !important;
        border-color: #112E81 !important;
    }

    /* Button - solid brand blue with white text */
    .fi-simple-main-ctn button[type="submit"] {
        background: #112E81 !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 8px !important;
        font-weight: 700 !important;
        font-size: 0.95rem !important;
        padding: 0.75rem 1.5rem !important;
        box-shadow: 0 4px 10px rgba(17, 46, 129, 0.2) !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
        width: 100% !important;
    }

    .fi-simple-main-ctn button[type="submit"] span,
    .fi-simple-main-ctn button[type="submit"] .fi-btn-label {
        color: #ffffff !important;
    }

    .fi-simple-main-ctn button[type="submit"]:hover {
        background: #0d2568 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 14px rgba(17, 46, 129, 0.3) !important;
    }

    .fi-simple-main-ctn button[type="submit"]:active {
        transform: translateY(0) !important;
    }

    /* Clean error messages */
    .fi-fo-field-wrp-error-message,
    .text-danger-600,
    .text-sm.text-red-600 {
        color: #ef4444 !important;
    }

    /* Login/Register page logo override (swap to black logo and clean size) */
    .fi-simple-layout .fi-logo {
        content: url('{{ asset('images/logo1.png') }}') !important;
        height: 2.2rem !important;
    }
</style>
