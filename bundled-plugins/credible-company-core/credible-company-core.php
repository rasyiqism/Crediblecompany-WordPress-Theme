<?php
/**
 * Plugin Name:       Credible Company Core
 * Plugin URI:        https://crediblemark.com/
 * Description:       Plugin pendamping resmi untuk tema Credible Company. Mengelola Custom Post Types (Testimoni, Paket Jasa, Marketing), Dynamic WhatsApp, Post Views, Submit Testimoni, serta modul SEO, Keamanan, dan Performa.
 * Version:           1.0.2
 * Author:            Credible Company
 * Text Domain:       crediblecompany
 * Requires at least: 6.0
 * Requires PHP:      7.4
 *
 * @package CredibleCompanyCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CC_CORE_ACTIVE', true );
define( 'CC_CORE_VERSION', '1.0.2' );
define( 'CC_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'CC_CORE_URL', plugin_dir_url( __FILE__ ) );

/* --------------------------------------------------------------------------
 * 1. Fallback Helpers (agar plugin tetap aman jika dipakai tanpa tema CredibleCompany)
 * ---------------------------------------------------------------------- */
if ( ! function_exists( 'cc_get' ) ) {
    function cc_get( $key, $default = '' ) {
        $value = get_theme_mod( 'cc_' . $key, '' );
        if ( '' === $value || false === $value || null === $value ) {
            $value = $default;
        }
        if ( is_ssl() && is_string( $value ) && ! empty( $value ) ) {
            $value = str_replace( 'http://', 'https://', $value );
        }
        return $value;
    }
}

if ( ! function_exists( 'cc_get_default_wa_message' ) ) {
    function cc_get_default_wa_message() {
        return 'Halo {Nama Marketing}, saya tertarik dengan layanan Anda.';
    }
}

/* --------------------------------------------------------------------------
 * 2. Muat Modul Inti Plugin
 * ---------------------------------------------------------------------- */
require_once CC_CORE_DIR . 'includes/cpts/cpt-testimoni.php';
require_once CC_CORE_DIR . 'includes/cpts/cpt-paket-jasa.php';
require_once CC_CORE_DIR . 'includes/cpts/cpt-marketing.php';
require_once CC_CORE_DIR . 'includes/cpts/seed-paket-jasa.php';

require_once CC_CORE_DIR . 'includes/features/post-views.php';
require_once CC_CORE_DIR . 'includes/features/dynamic-wa.php';
require_once CC_CORE_DIR . 'includes/features/submit-testimoni.php';

require_once CC_CORE_DIR . 'includes/optimizers/performance-optimizer.php';
require_once CC_CORE_DIR . 'includes/optimizers/security-optimizer.php';
require_once CC_CORE_DIR . 'includes/optimizers/seo-optimizer.php';

/* --------------------------------------------------------------------------
 * 3. Activation & Deactivation Hooks
 * ---------------------------------------------------------------------- */
register_activation_hook( __FILE__, function () {
    // Pastikan CPT & rewrite rules terdaftar sebelum flush
    if ( function_exists( 'cc_register_cpt_marketing' ) ) {
        cc_register_cpt_marketing();
    }
    if ( function_exists( 'cc_add_sitemap_endpoint' ) ) {
        cc_add_sitemap_endpoint();
    }
    if ( function_exists( 'cc_seed_paket_jasa' ) ) {
        cc_seed_paket_jasa();
    }
    if ( function_exists( 'cc_schedule_weekly_db_cleanup' ) ) {
        cc_schedule_weekly_db_cleanup();
    }
    if ( function_exists( 'cc_schedule_daily_views_cleanup' ) ) {
        cc_schedule_daily_views_cleanup();
    }
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function () {
    wp_clear_scheduled_hook( 'cc_weekly_db_cleanup' );
    wp_clear_scheduled_hook( 'cc_daily_views_cleanup' );
    flush_rewrite_rules();
} );
