<?php
/**
 * Graceful Fallback Stubs.
 *
 * Menyediakan fungsi pengganti yang aman apabila plugin pendamping
 * 'credible-company-core' belum diaktifkan, sehingga template tema
 * tetap berjalan tanpa fatal error.
 *
 * @package CredibleCompany
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cc_get_post_views' ) ) {
    function cc_get_post_views( $post_id ) {
        $count = get_post_meta( $post_id, 'cc_post_views', true );
        return ( '' === $count ) ? 0 : intval( $count );
    }
}

if ( ! function_exists( 'cc_set_post_views' ) ) {
    function cc_set_post_views( $post_id ) {
        // Dikelola oleh plugin credible-company-core.
    }
}

if ( ! function_exists( 'cc_get_total_site_views' ) ) {
    function cc_get_total_site_views() {
        return intval( get_option( 'cc_total_site_views', 0 ) );
    }
}

if ( ! function_exists( 'cc_get_views_today' ) ) {
    function cc_get_views_today() {
        return intval( get_option( 'cc_views_today_' . wp_date( 'Y-m-d' ), 0 ) );
    }
}

if ( ! function_exists( 'cc_dynamic_wa_url' ) ) {
    function cc_dynamic_wa_url( $original_url ) {
        $fallback = get_theme_mod( 'cc_marketing_fallback_name', 'Admin' );
        return str_replace( array( '{Nama Marketing}', '{nama marketing}' ), $fallback, $original_url );
    }
}

if ( ! function_exists( 'cc_dynamic_text' ) ) {
    function cc_dynamic_text( $text ) {
        $fallback     = get_theme_mod( 'cc_marketing_fallback_name', 'Admin' );
        $placeholders = array( ' {Nama Marketing}', ' {nama marketing}', '{Nama Marketing}', '{nama marketing}' );
        return str_replace( $placeholders, $fallback, $text );
    }
}
