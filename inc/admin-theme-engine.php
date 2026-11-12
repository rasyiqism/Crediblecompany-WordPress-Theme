<?php
/**
 * Engine Kustomisasi Halaman Login & Dashboard Admin (WP Admin) secara otomatis.
 * Komentar di kode menggunakan bahasa Indonesia.
 *
 * @package CredibleCompany
 */

// Mencegah akses langsung
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Cek apakah fitur tema admin kustom diaktifkan oleh user
if ( ! get_theme_mod( 'cc_enable_admin_theme', false ) ) {
    return;
}

if ( ! function_exists( 'cc_get_admin_primary_color' ) ) {
    /**
     * Mendapatkan warna primer tema secara dinamis dari Customizer.
     * Digunakan untuk aksen tombol login, menu aktif, hover sidebar, dll.
     *
     * @return string Kode warna Hex.
     */
    function cc_get_admin_primary_color() {
        // Cek jika pengguna mengatur warna aksen admin kustom secara khusus
        $custom_admin_primary = get_theme_mod( 'cc_admin_primary_color' );
        if ( ! empty( $custom_admin_primary ) ) {
            return $custom_admin_primary;
        }

        $header_style = get_theme_mod( 'cc_header_style', 'classic' );
        $primary      = '#c01314'; // Fallback default (merah brand)

        if ( 'classic' === $header_style ) {
            $primary = get_theme_mod( 'cc_header_classic_bg_color', '#c01314' );
        } elseif ( 'centered' === $header_style ) {
            $primary = get_theme_mod( 'cc_header_centered_bg_color', '#c01314' );
        } else {
            $primary = get_theme_mod( 'cc_header_glass_bg_color', '#ffffff' );
        }

        // Jika warna terlalu terang/putih, gunakan fallback merah agar tombol tetap kontras dan terbaca
        if ( in_array( strtolower( $primary ), array( '#ffffff', '#fff', '#f8fafc', '#f1f5f9' ), true ) ) {
            $primary = '#c01314';
        }

        return $primary;
    }
}

if ( ! function_exists( 'cc_get_admin_dark_color' ) ) {
    /**
     * Mendapatkan warna latar belakang gelap tema dari Customizer (warna footer).
     * Digunakan untuk latar belakang sidebar admin dan admin bar.
     *
     * @return string Kode warna Hex.
     */
    function cc_get_admin_dark_color() {
        // Cek jika pengguna mengatur warna latar admin kustom secara khusus
        $custom_admin_dark = get_theme_mod( 'cc_admin_dark_color' );
        if ( ! empty( $custom_admin_dark ) ) {
            return $custom_admin_dark;
        }

        return get_theme_mod( 'cc_footer_bg_color', '#c01314' );
    }
}


if ( ! function_exists( 'cc_admin_color_darken' ) ) {
    /**
     * Helper untuk menghitung warna yang lebih gelap (darken) untuk hover state tombol.
     *
     * @param string $hex Warna hex asal.
     * @param int    $percent Persentase penggelapan (1-100).
     * @return string Warna hex yang lebih gelap.
     */
    function cc_admin_color_darken( $hex, $percent ) {
        $rgb = function_exists( 'cc_hex_to_rgb' ) ? cc_hex_to_rgb( $hex ) : false;
        if ( false === $rgb ) {
            return $hex;
        }

        $factor = 1 - ( $percent / 100 );
        $r = max( 0, min( 255, (int) round( $rgb[0] * $factor ) ) );
        $g = max( 0, min( 255, (int) round( $rgb[1] * $factor ) ) );
        $b = max( 0, min( 255, (int) round( $rgb[2] * $factor ) ) );

        return sprintf( '#%02x%02x%02x', $r, $g, $b );
    }
}


/* --------------------------------------------------------------------------
 * 1. Kustomisasi Halaman Login WordPress
 * ---------------------------------------------------------------------- */

// Ganti tautan URL logo halaman login ke homepage situs
add_filter( 'login_headerurl', function() {
    return home_url();
} );

// Ganti alt text/title logo halaman login ke nama situs
add_filter( 'login_headertext', function() {
    return get_bloginfo( 'name' );
} );

// Suntikkan CSS kustom untuk halaman login agar tampil modern & premium
add_action( 'login_enqueue_scripts', function() {
    $primary_color = cc_get_admin_primary_color();
    $hover_color   = cc_admin_color_darken( $primary_color, 15 );
    $logo_id       = get_theme_mod( 'custom_logo' );
    $logo_url      = '';

    if ( $logo_id ) {
        $logo_data = wp_get_attachment_image_src( $logo_id, 'full' );
        if ( $logo_data ) {
            $logo_url = $logo_data[0];
        }
    }
    ?>
    <style type="text/css">
        body.login {
            background-color: #f8fafc !important;
            background-image: radial-gradient(circle at 10% 20%, rgba(244, 246, 249, 1) 0%, rgba(230, 235, 243, 0.7) 90.1%) !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif !important;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        #login {
            padding: 40px 0 0 0 !important;
            margin: auto !important;
            width: 360px !important;
        }
        .login h1 a {
            <?php if ( $logo_url ) : ?>
                background-image: url('<?php echo esc_url( $logo_url ); ?>') !important;
                background-size: contain !important;
                background-position: center !important;
                width: 100% !important;
                height: 70px !important;
                margin-bottom: 30px !important;
            <?php else : ?>
                background-image: none !important;
                text-indent: 0 !important;
                width: auto !important;
                height: auto !important;
                font-size: 28px !important;
                font-weight: 800 !important;
                color: #0f172a !important;
                text-decoration: none !important;
                text-align: center !important;
                display: block !important;
                letter-spacing: -0.5px !important;
                margin-bottom: 25px !important;
            <?php endif; ?>
        }
        .login form {
            background: #ffffff !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
            border-radius: 16px !important;
            padding: 30px 25px !important;
        }
        .login label {
            color: #475569 !important;
            font-weight: 500 !important;
            font-size: 13px !important;
        }
        .login input[type="text"],
        .login input[type="password"] {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            font-size: 15px !important;
            color: #1e293b !important;
            transition: all 0.2s ease-in-out !important;
            box-shadow: none !important;
        }
        .login input[type="text"]:focus,
        .login input[type="password"]:focus {
            border-color: <?php echo esc_attr( $primary_color ); ?> !important;
            box-shadow: 0 0 0 3px <?php echo esc_attr( cc_hex_to_rgba( $primary_color, 0.15 ) ); ?> !important;
            background: #ffffff !important;
        }
        .login input[type="checkbox"] {
            border: 1px solid #cbd5e1 !important;
            border-radius: 4px !important;
        }
        .login input[type="checkbox"]:checked {
            background: <?php echo esc_attr( $primary_color ); ?> !important;
            border-color: <?php echo esc_attr( $primary_color ); ?> !important;
        }
        .login input[type="checkbox"]:focus {
            box-shadow: 0 0 0 2px <?php echo esc_attr( cc_hex_to_rgba( $primary_color, 0.15 ) ); ?> !important;
        }
        .wp-core-ui .button-primary {
            background: <?php echo esc_attr( $primary_color ); ?> !important;
            border-color: <?php echo esc_attr( $primary_color ); ?> !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            height: auto !important;
            padding: 8px 20px !important;
            line-height: 1.5 !important;
            transition: all 0.2s ease-in-out !important;
            width: 100% !important;
            margin-top: 10px !important;
            float: none !important;
            text-shadow: none !important;
        }
        .wp-core-ui .button-primary:hover,
        .wp-core-ui .button-primary:focus {
            background: <?php echo esc_attr( $hover_color ); ?> !important;
            border-color: <?php echo esc_attr( $hover_color ); ?> !important;
            box-shadow: 0 6px 8px -1px rgba(0, 0, 0, 0.12), 0 0 0 3px <?php echo esc_attr( cc_hex_to_rgba( $primary_color, 0.25 ) ); ?> !important;
        }
        .login #nav,
        .login #backtoblog {
            text-align: center !important;
            padding: 0 !important;
            margin: 15px 0 0 0 !important;
        }
        .login #nav a,
        .login #backtoblog a {
            color: #64748b !important;
            font-size: 13px !important;
            transition: color 0.2s !important;
        }
        .login #nav a:hover,
        .login #backtoblog a:hover {
            color: <?php echo esc_attr( $primary_color ); ?> !important;
        }
        .privacy-policy-page-link {
            text-align: center !important;
            margin-top: 25px !important;
        }
        .privacy-policy-page-link a {
            color: #94a3b8 !important;
            font-size: 12px !important;
            text-decoration: none !important;
        }
        .privacy-policy-page-link a:hover {
            color: <?php echo esc_attr( $primary_color ); ?> !important;
        }
    </style>
    <?php
} );

/* --------------------------------------------------------------------------
 * 2. Kustomisasi Dashboard Admin (WP Admin)
 * ---------------------------------------------------------------------- */

// Suntikkan CSS kustom untuk mengubah warna dashboard admin
add_action( 'admin_head', function() {
    $primary_color = cc_get_admin_primary_color();
    $dark_bg       = cc_get_admin_dark_color();
    
    // Hitung variasi warna untuk menu aktif, hover, dan submenu
    $hover_bg      = cc_admin_color_darken( $dark_bg, 10 );
    $active_border = $primary_color;
    $submenu_bg    = cc_admin_color_darken( $dark_bg, 15 );
    ?>
    <style type="text/css">
        /* Reset box-shadow untuk elemen admin */
        #wpadminbar,
        html #adminmenuback,
        html #adminmenuwrap {
            box-shadow: 0 0px 0px rgba(56, 56, 58, 0) !important;
        }

        /* Background dan styling untuk body */
        html body {
            background: #f5f5f5db !important;
        }

        /* Styling untuk admin bar secara konsisten */
        #wpadminbar,
        #wpadminbar .ab-top-menu,
        #wpadminbar .ab-top-menu > li,
        #wpadminbar .ab-top-menu > li > .ab-item,
        #wpadminbar .ab-top-secondary,
        #wpadminbar .ab-top-secondary > li,
        #wpadminbar .ab-top-secondary > li > .ab-item,
        #wpadminbar .quicklinks,
        #wpadminbar .quicklinks > ul > li > a {
            background-color: <?php echo esc_attr( $dark_bg ); ?> !important;
            color: #ffffff !important;
        }

        #wpadminbar .ab-item {
            font-weight: 700 !important;
            color: #ffffff !important;
        }

        /* Ikon dashicons di admin bar */
        #wpadminbar .ab-icon,
        #wpadminbar .ab-icon::before,
        #wpadminbar .ab-item::before,
        #wpadminbar #wp-admin-bar-my-account .user-name {
            color: #ffffff !important;
        }

        /* Menjaga avatar agar tidak ter-override background-nya */
        #wpadminbar .avatar {
            background: transparent !important;
        }

        /* Hover state untuk item di admin bar */
        #wpadminbar .ab-top-menu > li:hover > .ab-item,
        #wpadminbar .ab-top-menu > li.hover > .ab-item,
        #wpadminbar .ab-top-secondary > li:hover > .ab-item,
        #wpadminbar .ab-top-secondary > li.hover > .ab-item,
        #wpadminbar .quicklinks > ul > li:hover > a {
            background-color: <?php echo esc_attr( $primary_color ); ?> !important;
            color: <?php echo esc_attr( $dark_bg ); ?> !important;
        }

        #wpadminbar .ab-top-menu > li:hover .ab-icon::before,
        #wpadminbar .ab-top-menu > li:hover .ab-item::before,
        #wpadminbar .ab-top-secondary > li:hover .ab-icon::before,
        #wpadminbar .ab-top-secondary > li:hover .ab-item::before {
            color: <?php echo esc_attr( $dark_bg ); ?> !important;
        }

        /* Submenu dropdown di admin bar */
        #wpadminbar .menupop .ab-sub-wrapper,
        #wpadminbar .menupop .ab-sub-wrapper * {
            background-color: <?php echo esc_attr( $submenu_bg ); ?> !important;
            color: #ffffff !important;
        }

        #wpadminbar .menupop .ab-sub-wrapper a:hover,
        #wpadminbar .menupop .ab-sub-wrapper a:hover * {
            background-color: <?php echo esc_attr( $primary_color ); ?> !important;
            color: <?php echo esc_attr( $dark_bg ); ?> !important;
        }


        /* Admin menu colors */
        html #adminmenu,
        html #adminmenuback,
        html #adminmenuwrap {
            background-color: <?php echo esc_attr( $dark_bg ); ?> !important;
        }
        #adminmenu .wp-submenu,
        #adminmenu .wp-has-current-submenu .wp-submenu,
        #adminmenu a.wp-has-current-submenu:focus+.wp-submenu,
        .folded #adminmenu a.wp-has-current-submenu:focus+.wp-submenu,
        .no-js li.wp-has-current-submenu:hover .wp-submenu {
            background-color: <?php echo esc_attr( $dark_bg ); ?> !important;
            color: #ffffff !important;
        }

        /* Menu colors on hover and active states */
        #adminmenu li.current a.menu-top,
        #adminmenu li.menu-top:hover,
        #adminmenu li.opensub>a.menu-top,
        #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu,
        #adminmenu li>a.menu-top:focus,
        .folded #adminmenu li.current.menu-top,
        .folded #adminmenu li.wp-has-current-submenu {
            background: <?php echo esc_attr( $primary_color ); ?> !important;
            color: <?php echo esc_attr( $dark_bg ); ?> !important;
        }

        /* Submenu links */
        #adminmenu .wp-submenu a {
            color: #ffffff !important;
        }
        #adminmenu .wp-submenu a:focus,
        #adminmenu .wp-submenu a:hover {
            color: <?php echo esc_attr( $primary_color ); ?> !important;
        }

        /* Admin menu links */
        #adminmenu a {
            color: #ffffff !important;
        }
        #adminmenu a:hover {
            color: <?php echo esc_attr( $dark_bg ); ?> !important;
        }
        /* Ikon menu default: putih */
        #adminmenu .wp-menu-image:before {
            color: #ffffff !important;
        }

        /* Ikon menu saat aktif (current), sub-menu aktif (wp-has-current-submenu), atau dihover */
        #adminmenu li.current .wp-menu-image:before,
        #adminmenu li.wp-has-current-submenu .wp-menu-image:before,
        #adminmenu li.wp-menu-open .wp-menu-image:before,
        #adminmenu li.menu-top:hover .wp-menu-image:before,
        #adminmenu li.opensub > a.menu-top .wp-menu-image:before,
        #adminmenu li > a.menu-top:focus .wp-menu-image:before,
        #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu .wp-menu-image:before,
        #adminmenu li.current a.menu-top .wp-menu-image:before {
            color: <?php echo esc_attr( $dark_bg ); ?> !important;
            background-color: currentColor !important; /* Menangani ikon yang menggunakan SVG mask */
        }

        /* Menangani ikon gambar kustom atau SVG inline dari plugin agar tetap kontras (gelap) saat menu aktif/hover */
        #adminmenu li.current .wp-menu-image img,
        #adminmenu li.wp-has-current-submenu .wp-menu-image img,
        #adminmenu li.wp-menu-open .wp-menu-image img,
        #adminmenu li.menu-top:hover .wp-menu-image img,
        #adminmenu li.opensub > a.menu-top .wp-menu-image img,
        #adminmenu li > a.menu-top:focus .wp-menu-image img {
            filter: brightness(0) !important;
        }

        #adminmenu li.current .wp-menu-image svg,
        #adminmenu li.wp-has-current-submenu .wp-menu-image svg,
        #adminmenu li.wp-menu-open .wp-menu-image svg,
        #adminmenu li.menu-top:hover .wp-menu-image svg,
        #adminmenu li.opensub > a.menu-top .wp-menu-image svg,
        #adminmenu li > a.menu-top:focus .wp-menu-image svg,
        #adminmenu li.current .wp-menu-image svg path,
        #adminmenu li.wp-has-current-submenu .wp-menu-image svg path,
        #adminmenu li.wp-menu-open .wp-menu-image svg path,
        #adminmenu li.menu-top:hover .wp-menu-image svg path,
        #adminmenu li.opensub > a.menu-top .wp-menu-image svg path,
        #adminmenu li > a.menu-top:focus .wp-menu-image svg path {
            fill: <?php echo esc_attr( $dark_bg ); ?> !important;
        }



        /* General links */
        a {
            color: <?php echo esc_attr( $dark_bg ); ?>;
        }
        a:hover {
            color: <?php echo esc_attr( $primary_color ); ?> !important;
        }
        body #adminmenu a:hover {
            color: <?php echo esc_attr( $dark_bg ); ?> !important;
        }

        /* Search box styling */
        .search-box {
            display: flex !important;
            justify-content: center !important;
            width: 100% !important;
            margin-bottom: 20px !important;
        }
        #post-search-input {
            flex: auto !important;
            border: 1px solid #ccc !important;
            border-radius: 10px !important;
            font-size: 16px !important;
            background-color: #ffffff !important;
            text-align: center !important;
            padding: 5px !important;
            box-shadow: inset 4px 4px 8px #d9d9d9, inset -4px -4px 8px #ffffff !important;
        }
        #search-submit {
            padding: 10px !important;
            border: none !important;
            background-color: #f0f0f0 !important;
            color: <?php echo esc_attr( $dark_bg ); ?> !important;
            border-radius: 10px !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            box-shadow: 4px 4px 8px #d9d9d9, -4px -4px 8px #ffffff !important;
        }
        #search-submit:hover {
            background-color: #e0e0e0 !important;
            box-shadow: none !important;
        }

        /* Submenu and list styling */
        ul.subsubsub {
            display: flex !important;
            overflow-x: auto !important;
            white-space: nowrap !important;
        }
        ul.subsubsub li {
            flex-shrink: 0 !important;
        }
        .subsubsub {
            list-style: none !important;
            margin: 5px 0 !important;
            padding: 0 !important;
            font-size: 13px !important;
            float: none !important;
            font-weight: 600 !important;
            color: <?php echo esc_attr( $dark_bg ); ?> !important;
        }
        @media (min-width: 768px) {
            ul.subsubsub {
                justify-content: center !important;
            }
        }

        /* Grid mode styling */
        .mode-grid .attachments-browser .media-toolbar-primary {
            display: flex !important;
            align-items: center !important;
            column-gap: .5rem !important;
        }

        /* Form styling */
        #your-profile {
            background-color: #f0f0f0 !important;
            padding: 20px !important;
            border-radius: 15px !important;
            box-shadow: 8px 8px 16px #d0d0d0, -8px -8px 16px #ffffff !important;
        }
        #your-profile input[type="text"],
        #your-profile input[type="email"],
        #your-profile input[type="url"],
        #your-profile input[type="password"],
        #your-profile select,
        #your-profile textarea {
            background-color: #f0f0f0 !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 10px !important;
            margin-bottom: 10px !important;
            box-shadow: inset 4px 4px 8px #d0d0d0, inset -4px -4px 8px #ffffff !important;
        }
        #your-profile button {
            background-color: #f0f0f0 !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 10px 20px !important;
            margin-bottom: 10px !important;
            box-shadow: 4px 4px 8px #d0d0d0, -4px -4px 8px #ffffff !important;
        }
        #your-profile input[type="checkbox"] {
            width: 20px !important;
            height: 20px !important;
            margin-right: 10px !important;
        }
        #your-profile label {
            display: inline-block !important;
            margin-bottom: 10px !important;
        }
        #your-profile a {
            color: <?php echo esc_attr( $dark_bg ); ?> !important;
            text-decoration: none !important;
        }
        #your-profile a:hover {
            text-decoration: underline !important;
        }

        /* Postbox and widget styling */
        .postbox .hndle,
        .stuffbox .hndle {
            background-color: #ffffff !important;
        }
        .rb-meta-wrapper {
            border-top: 5px solid <?php echo esc_attr( $dark_bg ); ?> !important;
        }
        .rb-tab-title.is-active {
            background-color: <?php echo esc_attr( $primary_color ); ?> !important;
        }
        .rb-group-trigger,
        .rb-button {
            background-color: <?php echo esc_attr( $dark_bg ); ?> !important;
        }

        /* Google dashboard widget */
        #google_dashboard_widget .googlesitekit-wp-dashboard-stats * {
            box-sizing: border-box !important;
            color: <?php echo esc_attr( $dark_bg ); ?> !important;
        }

        /* Editor styling */
        .editor-styles-wrapper .editor-block-list__layout a,
        .block-editor-rich-text__editable a {
            font-weight: 700 !important;
            font-style: normal !important;
            color: <?php echo esc_attr( $dark_bg ); ?> !important;
        }

        /* Hide specific profile sections */
        tbody tr.user-rich-editing-wrap,
        tbody tr.user-syntax-highlighting-wrap,
        tbody tr.user-admin-color-wrap,
        tbody tr.user-admin-color-wrap + tr,
        tr.show-admin-bar.user-admin-bar-front-wrap {
            display: none !important;
        }

        /* Responsive adjustments */
        @media screen and (max-width: 768px) {
            .alignleft {
                display: grid !important;
                justify-content: center !important;
                align-items: center !important;
                width: -webkit-fill-available !important;
            }
            body:not(.plugins-php) .row-actions {
                display: flex !important;
                flex-wrap: wrap !important;
                gap: 0 !important;
                color: transparent !important;
            }
            .wrap .add-new-h2,
            .wrap .add-new-h2:active,
            .wrap .page-title-action,
            .wrap .page-title-action:active {
                padding: 0px 5px !important;
                font-size: 14px !important;
                white-space: nowrap !important;
            }
        }
        @media screen and (max-width: 480px) {
            .tablenav.bottom .displaying-num {
                text-align: center !important;
            }
        }
    </style>
    <?php
} );

// Mengubah teks footer kiri admin WordPress
add_filter( 'admin_footer_text', function() {
    return 'Theme & Plugin Developed by <a href="https://crediblemark.com/" target="_blank" rel="noopener" style="font-weight: 600;">Rasyiqi - KBM x Crediblemark</a>';
} );



// Menyembunyikan versi WordPress di footer kanan admin
add_filter( 'update_footer', '__return_empty_string', 11 );
