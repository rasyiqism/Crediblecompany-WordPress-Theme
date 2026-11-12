<?php
/**
 * Modul Keamanan & Privasi (Pro Privacy)
 * Menangani Custom Login URL, Masking Error, dan Proteksi Data User.
 *
 * @package CredibleCompany
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* --------------------------------------------------------------------------
 * 1. Custom Login URL (Hide wp-login.php)
 * ---------------------------------------------------------------------- */

/**
 * Mendefinisikan slug login kustom.
 * Ganti 'masuk' dengan kata lain jika ingin lebih rahasia.
 */
function cc_get_login_slug() {
    return 'masuk';
}

/**
 * Buat nonce login yang disimpan sebagai transient (12 jam).
 * Setiap akses ke URL /masuk menghasilkan token unik yang tidak bisa ditebak.
 *
 * @return string Token nonce acak.
 */
function cc_create_login_token() {
    $token = wp_generate_password( 32, false );
    set_transient( 'cc_login_token_' . $token, 1, 12 * HOUR_IN_SECONDS );
    return $token;
}

/**
 * Handle pengalihan dari slug kustom ke wp-login.php dengan token nonce dinamis.
 */
add_action( 'init', 'cc_handle_custom_login_redirect' );
function cc_handle_custom_login_redirect() {
    // Jalankan hanya jika fitur URL login kustom diaktifkan
    if ( ! get_theme_mod( 'cc_enable_custom_login', false ) ) {
        return;
    }

    $login_slug  = cc_get_login_slug();
    $request_uri = untrailingslashit( strtok( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '/', '?' ) );
    $target_path = untrailingslashit( (string) wp_parse_url( home_url( '/' . $login_slug ), PHP_URL_PATH ) );

    // Jika user mengakses /masuk (mendukung install di root maupun subdirektori), buat token dinamis & cookie sesi singkat
    if ( $request_uri === $target_path ) {
        $token = cc_create_login_token();
        setcookie( 'cc_login_access', $token, time() + 1800, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
        wp_safe_redirect( site_url( 'wp-login.php?cc_token=' . rawurlencode( $token ) ) );
        exit;
    }
}

/**
 * Blokir akses langsung ke wp-login.php jika tidak membawa token valid (GET maupun POST).
 */
add_action( 'login_init', 'cc_protect_login_page' );
function cc_protect_login_page() {
    // Jalankan hanya jika fitur URL login kustom diaktifkan
    if ( ! get_theme_mod( 'cc_enable_custom_login', false ) ) {
        return;
    }

    // Izinkan akses jika user sudah login atau request berasal dari sistem Customizer (session check)
    $is_customize = isset( $_GET['customize-login'] ) || isset( $_GET['interim-login'] );
    if ( is_user_logged_in() || $is_customize ) {
        return;
    }

    // Cek token dinamis dari query string atau cookie HttpOnly (mendukung GET & POST form login)
    $submitted_token = '';
    if ( isset( $_GET['cc_token'] ) ) {
        $submitted_token = sanitize_text_field( wp_unslash( $_GET['cc_token'] ) );
    } elseif ( isset( $_COOKIE['cc_login_access'] ) ) {
        $submitted_token = sanitize_text_field( wp_unslash( $_COOKIE['cc_login_access'] ) );
    }

    $token_valid = false;
    if ( ! empty( $submitted_token ) ) {
        $transient_key = 'cc_login_token_' . $submitted_token;
        if ( get_transient( $transient_key ) ) {
            $token_valid = true;
            // Hapus token & cookie setelah proses POST login selesai dipicu
            if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
                delete_transient( $transient_key );
            }
        }
    }

    $is_action = isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'logout', 'lostpassword', 'rp', 'resetpass', 'confirm_admin_email' ), true );
    $is_state  = isset( $_GET['loggedout'] ) || isset( $_GET['checkemail'] ) || isset( $_GET['registration'] );

    // Jika mencoba akses langsung tanpa token valid, lempar ke 404
    if ( ! $token_valid && ! $is_action && ! $is_state ) {
        global $wp_query;
        $wp_query->set_404();
        status_header( 404 );
        // Cari file template 404 secara dinamis di folder templates/ atau root tema
        $template = locate_template( array( 'templates/404.php', '404.php' ) );
        if ( empty( $template ) && file_exists( get_template_directory() . '/templates/404.php' ) ) {
            $template = get_template_directory() . '/templates/404.php';
        }

        if ( ! empty( $template ) && file_exists( $template ) ) {
            include $template;
        } else {
            // Fallback sederhana jika file template tidak ditemukan, cegah ValueError dari include('')
            wp_die( esc_html__( 'Halaman tidak ditemukan.', 'crediblecompany' ), '404 Not Found', array( 'response' => 404 ) );
        }
        exit;
    }
}

/* --------------------------------------------------------------------------
 * 2. Login Error Masking (Keamanan Akun)
 * ---------------------------------------------------------------------- */

add_filter( 'login_errors', 'cc_mask_login_errors' );
function cc_mask_login_errors() {
    return '<strong>Gagal:</strong> Data login yang Anda masukkan tidak valid. Silakan coba lagi.';
}

/* --------------------------------------------------------------------------
 * 3. Proteksi User Enumeration (Cegah Pencarian Username)
 * ---------------------------------------------------------------------- */

add_action( 'template_redirect', 'cc_disable_user_enumeration' );
function cc_disable_user_enumeration() {
    if ( ! is_admin() && isset( $_GET['author'] ) ) {
        wp_safe_redirect( home_url(), 301 );
        exit;
    }
}

/* 
// Blokir Rest API User Endpoints (Untuk User Non-Admin, kecuali jika mengakses data dirinya sendiri)
add_filter( 'rest_prepare_user', function( $response, $user, $request ) {
    if ( ! current_user_can( 'manage_options' ) && (int) get_current_user_id() !== (int) $user->ID ) {
        return new WP_Error( 'rest_cannot_read', __( 'Anda tidak memiliki akses.', 'crediblecompany' ), array( 'status' => 403 ) );
    }
    return $response;
}, 10, 3 );
*/

/* --------------------------------------------------------------------------
 * 4. Admin White-labeling (Professional Branding)
 * ---------------------------------------------------------------------- */

// 1. Custom Login Branding (Logo & Style)
add_action( 'login_enqueue_scripts', 'cc_custom_login_branding' );
function cc_custom_login_branding() {
    $logo_url = '';
    if ( has_custom_logo() ) {
        $logo_id = get_theme_mod( 'custom_logo' );
        $logo_data = wp_get_attachment_image_src( $logo_id, 'full' );
        $logo_url = $logo_data[0];
    }
    ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo esc_url( $logo_url ); ?>);
            background-size: contain;
            width: 100%;
            height: 80px;
            margin-bottom: 20px;
        }
        body.login { background: #f8fafc; }
        .login #login_error, .login .message, .login .success { border-left-color: #d4af37; border-radius: 8px; }
        .login form { border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border: none; }
        .wp-core-ui .button-primary { background: #000; border-color: #000; transition: all 0.3s; padding: 0 20px; border-radius: 6px; }
        .wp-core-ui .button-primary:hover { background: #d4af37; border-color: #d4af37; color: #000; }
    </style>
    <?php
}

// Ganti link logo login ke Beranda
add_filter( 'login_headerurl', function() { return home_url(); } );
add_filter( 'login_headertext', function() { return get_bloginfo( 'name' ); } );



// 3. Toolbar Cleaning (Hapus Logo WP)
add_action( 'wp_before_admin_bar_render', 'cc_remove_wp_logo_admin_bar', 0 );
function cc_remove_wp_logo_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'wp-logo' );
    $wp_admin_bar->remove_menu( 'about' );
    $wp_admin_bar->remove_menu( 'wporg' );
    $wp_admin_bar->remove_menu( 'documentation' );
    $wp_admin_bar->remove_menu( 'support-forums' );
    $wp_admin_bar->remove_menu( 'feedback' );
}

// 4. Clean Admin Menu (Sembunyikan Menu Tak Perlu)
add_action( 'admin_menu', 'cc_clean_admin_menu' );
function cc_clean_admin_menu() {
    // Sembunyikan komentar jika tidak dipakai
    // remove_menu_page( 'edit-comments.php' );
    
    // Sembunyikan Dashboard asli (bisa membingungkan klien)
    // remove_menu_page( 'index.php' ); 
}

/**
 * Mengecek apakah website menggunakan HTTPS (secara langsung atau di balik reverse proxy).
 */
function cc_is_prod_env() {
    return is_ssl() || 0 === strpos( home_url(), 'https://' );
}

// Filter URL attachment media agar menggunakan HTTPS saat di produksi
add_filter( 'wp_get_attachment_url', 'cc_force_ssl_url' );
add_filter( 'wp_get_attachment_image_src', 'cc_force_ssl_image_src_url' );

function cc_force_ssl_url( $url ) {
    if ( ( is_ssl() || cc_is_prod_env() ) && ! empty( $url ) && is_string( $url ) ) {
        if ( 0 === strpos( $url, 'http://' ) ) {
            $url = 'https://' . substr( $url, 7 );
        }
    }
    return $url;
}

function cc_force_ssl_image_src_url( $image ) {
    if ( ( is_ssl() || cc_is_prod_env() ) && is_array( $image ) && ! empty( $image[0] ) ) {
        if ( 0 === strpos( $image[0], 'http://' ) ) {
            $image[0] = 'https://' . substr( $image[0], 7 );
        }
    }
    return $image;
}

/**
 * Tambahkan tag meta Content-Security-Policy (CSP) upgrade-insecure-requests
 * sebagai lapisan pertahanan tambahan di sisi browser.
 */
add_action( 'wp_head', 'cc_add_csp_upgrade_insecure', 1 );
add_action( 'admin_head', 'cc_add_csp_upgrade_insecure', 1 );
add_action( 'login_head', 'cc_add_csp_upgrade_insecure', 1 );
function cc_add_csp_upgrade_insecure() {
    if ( is_ssl() || cc_is_prod_env() ) {
        echo '<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">' . "\n";
    }
}

