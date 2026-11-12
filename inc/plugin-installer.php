<?php
/**
 * One-Click Companion Plugin Installer (Best Practice WordPress).
 *
 * Menampilkan notice di Dashboard Admin jika plugin pendamping
 * 'credible-company-core' belum terpasang/aktif, dan menyediakan
 * instalasi 1-klik menggunakan API resmi WordPress (Plugin_Upgrader).
 *
 * @package CredibleCompany
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CC_CORE_PLUGIN_SLUG', 'credible-company-core/credible-company-core.php' );
define( 'CC_CORE_PLUGIN_ZIP', get_template_directory() . '/bundled-plugins/credible-company-core.zip' );

/**
 * Cek apakah plugin pendamping sudah terpasang di wp-content/plugins/.
 *
 * @return bool
 */
function cc_is_core_plugin_installed() {
    if ( ! function_exists( 'get_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $plugins = get_plugins();
    return isset( $plugins[ CC_CORE_PLUGIN_SLUG ] );
}

/**
 * Cek apakah plugin pendamping sedang aktif.
 *
 * @return bool
 */
function cc_is_core_plugin_active() {
    if ( defined( 'CC_CORE_ACTIVE' ) ) {
        return true;
    }
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    return is_plugin_active( CC_CORE_PLUGIN_SLUG );
}

/**
 * Tampilkan Admin Notice jika plugin pendamping belum aktif.
 */
add_action( 'admin_notices', 'cc_core_plugin_admin_notice' );
function cc_core_plugin_admin_notice() {
    if ( cc_is_core_plugin_active() ) {
        // Tampilkan pesan sukses setelah instalasi/aktivasi
        if ( isset( $_GET['cc_core_status'] ) && 'activated' === $_GET['cc_core_status'] ) {
            echo '<div class="notice notice-success is-dismissible"><p>';
            esc_html_e( 'Plugin Credible Company Core berhasil dipasang dan diaktifkan.', 'crediblecompany' );
            echo '</p></div>';
        }
        return;
    }

    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }

    // Jangan tampilkan notice di layar proses update/install itu sendiri
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( $screen && in_array( $screen->id, array( 'update', 'update-core' ), true ) ) {
        return;
    }

    $is_installed = cc_is_core_plugin_installed();
    $action_url   = wp_nonce_url(
        admin_url( 'admin-post.php?action=cc_install_core_plugin' ),
        'cc_install_core_plugin_nonce'
    );

    if ( isset( $_GET['cc_core_error'] ) ) {
        echo '<div class="notice notice-error is-dismissible"><p>';
        echo esc_html( sanitize_text_field( wp_unslash( $_GET['cc_core_error'] ) ) );
        echo '</p></div>';
    }

    echo '<div class="notice notice-warning">';
    echo '<p><strong>' . esc_html__( 'Tema Credible Company membutuhkan plugin pendamping:', 'crediblecompany' ) . '</strong> ';
    esc_html_e( 'Plugin "Credible Company Core" diperlukan untuk fitur Testimoni, Paket Jasa, Tim Marketing, SEO, Keamanan, dan Optimasi Performa.', 'crediblecompany' );
    echo '</p>';

    if ( $is_installed ) {
        echo '<p><a href="' . esc_url( $action_url ) . '" class="button button-primary">';
        esc_html_e( 'Aktifkan Credible Company Core', 'crediblecompany' );
        echo '</a></p>';
    } elseif ( defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS ) {
        echo '<p><em>' . esc_html__( 'DISALLOW_FILE_MODS aktif di wp-config.php. Silakan pasang folder credible-company-core secara manual ke wp-content/plugins/.', 'crediblecompany' ) . '</em></p>';
    } elseif ( current_user_can( 'install_plugins' ) && file_exists( CC_CORE_PLUGIN_ZIP ) ) {
        echo '<p><a href="' . esc_url( $action_url ) . '" class="button button-primary">';
        esc_html_e( 'Install & Aktifkan Credible Company Core', 'crediblecompany' );
        echo '</a></p>';
    }
    echo '</div>';
}

/**
 * Handler One-Click Install & Activate via admin-post.php.
 */
add_action( 'admin_post_cc_install_core_plugin', 'cc_handle_install_core_plugin' );
function cc_handle_install_core_plugin() {
    check_admin_referer( 'cc_install_core_plugin_nonce' );

    if ( ! current_user_can( 'activate_plugins' ) ) {
        wp_die( esc_html__( 'Anda tidak memiliki izin untuk mengaktifkan plugin.', 'crediblecompany' ), 403 );
    }

    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    if ( ! cc_is_core_plugin_installed() ) {
        if ( ! current_user_can( 'install_plugins' ) ) {
            wp_die( esc_html__( 'Anda tidak memiliki izin untuk menginstall plugin.', 'crediblecompany' ), 403 );
        }

        if ( defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS ) {
            wp_safe_redirect( add_query_arg( 'cc_core_error', rawurlencode( __( 'Instalasi dinonaktifkan oleh DISALLOW_FILE_MODS.', 'crediblecompany' ) ), admin_url() ) );
            exit;
        }

        if ( ! file_exists( CC_CORE_PLUGIN_ZIP ) ) {
            wp_safe_redirect( add_query_arg( 'cc_core_error', rawurlencode( __( 'Paket ZIP plugin pendamping tidak ditemukan di tema.', 'crediblecompany' ) ), admin_url() ) );
            exit;
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/misc.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        WP_Filesystem();

        $skin     = new Automatic_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader( $skin );
        $result   = $upgrader->install( CC_CORE_PLUGIN_ZIP );

        if ( is_wp_error( $result ) || ! $result ) {
            $err_msg = is_wp_error( $result ) ? $result->get_error_message() : __( 'Gagal mengekstrak plugin pendamping.', 'crediblecompany' );
            wp_safe_redirect( add_query_arg( 'cc_core_error', rawurlencode( $err_msg ), admin_url() ) );
            exit;
        }
    }

    $activate = activate_plugin( CC_CORE_PLUGIN_SLUG );
    if ( is_wp_error( $activate ) ) {
        wp_safe_redirect( add_query_arg( 'cc_core_error', rawurlencode( $activate->get_error_message() ), admin_url() ) );
        exit;
    }

    wp_safe_redirect( add_query_arg( 'cc_core_status', 'activated', admin_url() ) );
    exit;
}
