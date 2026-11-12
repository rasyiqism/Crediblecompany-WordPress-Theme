<?php
/**
 * Header Customizer (Entry Point Utama)
 * Komentar di kode menggunakan bahasa Indonesia.
 *
 * @package CredibleCompany
 */

// Mencegah akses langsung
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section( 'cc_header_section', array(
        'title'    => __( 'Header & Navigasi', 'crediblecompany' ),
        'panel'    => 'cc_global_panel',
        'priority' => 10,
    ) );

    // Pilihan Gaya Header
    $wp_customize->add_setting( 'cc_header_style', array(
        'default'           => 'classic',
        'sanitize_callback' => 'sanitize_key',
    ) );
    $wp_customize->add_control( 'cc_header_style', array(
        'label'    => __( 'Gaya Tampilan Header', 'crediblecompany' ),
        'section'  => 'cc_header_section',
        'type'     => 'radio',
        'choices'  => array(
            'classic'  => __( 'Klasik (Default)', 'crediblecompany' ),
            'centered' => __( 'Logo Terpusat & Stacked Menu', 'crediblecompany' ),
            'glass'    => __( 'Glassmorphism Floating', 'crediblecompany' ),
        ),
        'priority' => 1,
    ) );

    // Sticky Header Toggle
    $wp_customize->add_setting( 'cc_header_sticky', array(
        'default'           => true,
        'sanitize_callback' => 'cc_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'cc_header_sticky', array(
        'label'    => __( 'Aktifkan Sticky Header (Melayang saat di-scroll)', 'crediblecompany' ),
        'section'  => 'cc_header_section',
        'type'     => 'checkbox',
        'priority' => 2,
    ) );

    // Lebar Maksimal Logo
    $wp_customize->add_setting( 'cc_header_logo_width', array(
        'default'           => 150,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_header_logo_width', array(
        'label'       => __( 'Lebar Maksimal Logo (px)', 'crediblecompany' ),
        'section'     => 'cc_header_section',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 50,
            'max'  => 350,
            'step' => 5,
        ),
        'priority'    => 3,
    ) );

    // Header Search URL
    $wp_customize->add_setting( 'header_search_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'header_search_url', array(
        'label'       => __( 'URL Ikon Pencarian', 'crediblecompany' ),
        'description' => __( 'Biarkan kosong untuk menggunakan fitur pencarian bawaan (overlay/popup default dari tema). Isi URL untuk mengarahkan ikon search menyeberang ke halaman/link lain.', 'crediblecompany' ),
        'section'     => 'cc_header_section',
        'type'        => 'url',
        'priority'    => 100,
    ) );

    // Header Account URL
    $wp_customize->add_setting( 'header_account_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'header_account_url', array(
        'label'       => __( 'URL Akun/Profil Pengguna', 'crediblecompany' ),
        'description' => __( 'Tautan untuk halaman login/register atau laman dashboard pengguna/client.', 'crediblecompany' ),
        'section'     => 'cc_header_section',
        'type'        => 'url',
        'priority'    => 101,
    ) );

    // Muat konfigurasi modular untuk tipe-tipe header secara dinamis
    require_once __DIR__ . '/header/classic.php';
    require_once __DIR__ . '/header/centered.php';
    require_once __DIR__ . '/header/glass.php';

} );
