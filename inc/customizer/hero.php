<?php
/**
 * Customizer: Hero Section (Entry Point Utama)
 *
 * @package CredibleCompany
 */

// Mencegah akses langsung
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'customize_register', function( $wp_customize ) {

    // Daftarkan Section Hero
    $wp_customize->add_section( 'cc_hero_section', array(
        'title'    => __( '1. Hero', 'crediblecompany' ),
        'panel'    => 'cc_homepage_panel',
        'priority' => 10,
    ) );

    // --- PILIHAN LAYOUT / VARIANT HERO ---
    $wp_customize->add_setting( 'cc_hero_variant', array(
        'default'           => 'default',
        'sanitize_callback' => 'sanitize_key',
    ) );
    $wp_customize->add_control( 'cc_hero_variant', array(
        'label'       => __( 'Layout Hero', 'crediblecompany' ),
        'description' => __( 'Pilih tampilan hero yang digunakan di halaman depan.', 'crediblecompany' ),
        'section'     => 'cc_hero_section',
        'type'        => 'select',
        'choices'     => array(
            'default' => 'Default — Teks kiri, gambar kanan dengan shapes',
            'v2'      => 'Centered — Teks tengah, gradient latar, tanpa gambar',
            'v3'      => 'Jasper — Centered, promo badge, & grid grafis melayang',
        ),
        'priority'    => 1, // Paling atas
    ) );

    // --- JUDUL HERO (GLOBAL) ---
    $wp_customize->add_setting( 'cc_hero_title', array(
        'default'           => 'Penerbit Buku Profesional',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'cc_hero_title', array(
        'label'   => __( 'Judul Hero', 'crediblecompany' ),
        'section' => 'cc_hero_section',
        'type'    => 'text',
    ) );

    // --- DESKRIPSI HERO (GLOBAL) ---
    $wp_customize->add_setting( 'cc_hero_desc', array(
        'default'           => 'Penerbit Buku KBM Indonesia mempersembahkan lebih dari 5.000 judul buku. Yuk, wujudkan mimpimu menjadi penulis — jadikan naskahmu memiliki ISBN.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'cc_hero_desc', array(
        'label'   => __( 'Deskripsi Hero', 'crediblecompany' ),
        'section' => 'cc_hero_section',
        'type'    => 'textarea',
    ) );

    // --- KETEBALAN FONT JUDUL HERO (GLOBAL) ---
    $wp_customize->add_setting( 'cc_hero_title_weight', array(
        'default'           => '900',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_hero_title_weight', array(
        'label'   => __( 'Ketebalan Font Judul Hero (Global)', 'crediblecompany' ),
        'section' => 'cc_hero_section',
        'type'    => 'select',
        'choices' => array(
            '400' => 'Normal (400)',
            '600' => 'Semi-Bold (600)',
            '700' => 'Bold (700)',
            '800' => 'Extra-Bold (800)',
            '900' => 'Black (900)',
        )
    ) );

    // --- LOAD MODULAR HERO SETTINGS PER VARIANT ---
    // Menggunakan __DIR__ untuk merujuk ke direktori yang sama
    require_once __DIR__ . '/hero/v1.php';
    require_once __DIR__ . '/hero/v2.php';
    require_once __DIR__ . '/hero/v3.php';

} );

// --- LOAD CALLBACK CSS DINAMIS ---
require_once __DIR__ . '/hero/dynamic-css.php';
