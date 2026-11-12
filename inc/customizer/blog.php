<?php
/**
 * Customizer: Blog Settings
 *
 * @package CredibleCompany
 */

add_action( 'customize_register', function ( $wp_customize ) {
    
    // Section 1 (Panel Beranda): Section Blog di Halaman Depan
    $wp_customize->add_section( 'cc_blog_section', array(
        'title'    => __( '9. Section Blog Beranda', 'crediblecompany' ),
        'panel'    => 'cc_homepage_panel',
        'priority' => 90,
    ) );

    // Section 2 (Panel Global): Halaman Arsip Blog (/blog/) & Komentar
    $wp_customize->add_section( 'cc_blog_archive_section', array(
        'title'    => __( 'Halaman Blog & Komentar', 'crediblecompany' ),
        'panel'    => 'cc_global_panel',
        'priority' => 50,
    ) );

    // 0. Tampilkan / Sembunyikan Blog di Homepage
    $wp_customize->add_setting( 'cc_show_blog', array(
        'default'           => true,
        'sanitize_callback' => 'cc_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'cc_show_blog', array(
        'label'    => __( 'Tampilkan Blog di Halaman Depan', 'crediblecompany' ),
        'section'  => 'cc_blog_section',
        'type'     => 'checkbox',
    ) );

    // 1. Warna Banner Hero Halaman Arsip Blog (Gradient Start)
    $wp_customize->add_setting( 'cc_blog_hero_bg_start', array(
        'default'           => '#6366f1',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_blog_hero_bg_start', array(
        'label'    => __( 'Warna Banner Blog (Awal)', 'crediblecompany' ),
        'section'  => 'cc_blog_archive_section',
    ) ) );

    // Warna Banner Hero Halaman Arsip Blog (Gradient End)
    $wp_customize->add_setting( 'cc_blog_hero_bg_end', array(
        'default'           => '#a855f7',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_blog_hero_bg_end', array(
        'label'    => __( 'Warna Banner Blog (Akhir)', 'crediblecompany' ),
        'section'  => 'cc_blog_archive_section',
    ) ) );

    // 2. Judul Halaman Blog
    $wp_customize->add_setting( 'cc_blog_title', array(
        'default'           => __( 'Blog & Berita', 'crediblecompany' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_blog_title', array(
        'label'    => __( 'Judul Halaman Blog', 'crediblecompany' ),
        'section'  => 'cc_blog_archive_section',
        'type'     => 'text',
    ) );

    // 3. Sub Judul Halaman Blog
    $wp_customize->add_setting( 'cc_blog_subtitle', array(
        'default'           => __( 'Temukan wawasan dan cerita menarik terbaru dari kami.', 'crediblecompany' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_blog_subtitle', array(
        'label'    => __( 'Sub Judul Halaman Blog', 'crediblecompany' ),
        'section'  => 'cc_blog_archive_section',
        'type'     => 'textarea',
    ) );

    // Pengaturan Warna Section Blog Homepage
    $wp_customize->add_setting( 'cc_blog_section_bg_color', array(
        'default'           => '#f8fafc',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_blog_section_bg_color', array(
        'label'   => __( 'Warna Latar Belakang Section (Homepage)', 'crediblecompany' ),
        'section' => 'cc_blog_section',
    ) ) );

    $wp_customize->add_setting( 'cc_blog_section_title_color', array(
        'default'           => '#0f172a',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_blog_section_title_color', array(
        'label'   => __( 'Warna Font Judul Section (Homepage)', 'crediblecompany' ),
        'section' => 'cc_blog_section',
    ) ) );

} );

