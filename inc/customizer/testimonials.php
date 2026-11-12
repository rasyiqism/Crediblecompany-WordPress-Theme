<?php
/**
 * Customizer: Testimonials Section
 *
 * @package CredibleCompany
 */

add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section( 'cc_testimonials_section', array(
        'title'    => __( '6. Testimoni', 'crediblecompany' ),
        'panel'    => 'cc_homepage_panel',
        'priority' => 60,
    ) );

    // Judul Testimoni
    $wp_customize->add_setting( 'cc_testimonials_title', array(
        'default'           => 'Testimoni Mitra Kami',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_testimonials_title', array(
        'label'   => __( 'Judul Section', 'crediblecompany' ),
        'section' => 'cc_testimonials_section',
        'type'    => 'text',
    ) );

    // Tampilkan / Sembunyikan Seksi Testimoni
    $wp_customize->add_setting( 'cc_testimonials_enable', array(
        'default'           => true,
        'sanitize_callback' => 'cc_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'cc_testimonials_enable', array(
        'label'   => __( 'Tampilkan Seksi Testimoni', 'crediblecompany' ),
        'section' => 'cc_testimonials_section',
        'type'    => 'checkbox',
    ) );

    // Batasi Jumlah Testimoni
    $wp_customize->add_setting( 'cc_testimonials_limit', array(
        'default'           => 6,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_testimonials_limit', array(
        'label'       => __( 'Jumlah Testimoni yang Ditampilkan', 'crediblecompany' ),
        'description' => __( 'Tentukan jumlah testimoni yang dimuat di beranda.', 'crediblecompany' ),
        'section'     => 'cc_testimonials_section',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 1,
            'max'  => 30,
            'step' => 1,
        ),
    ) );

    // Pengaturan Warna Section Testimoni
    $wp_customize->add_setting( 'cc_testimonials_bg_color', array(
        'default'           => '#f8fafc',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_testimonials_bg_color', array(
        'label'   => __( 'Warna Latar Belakang Section', 'crediblecompany' ),
        'section' => 'cc_testimonials_section',
    ) ) );

    $wp_customize->add_setting( 'cc_testimonials_card_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_testimonials_card_bg_color', array(
        'label'   => __( 'Warna Latar Belakang Card', 'crediblecompany' ),
        'section' => 'cc_testimonials_section',
    ) ) );

    $wp_customize->add_setting( 'cc_testimonials_text_color', array(
        'default'           => '#0f172a',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_testimonials_text_color', array(
        'label'   => __( 'Warna Font Teks/Nama', 'crediblecompany' ),
        'section' => 'cc_testimonials_section',
    ) ) );

} );

