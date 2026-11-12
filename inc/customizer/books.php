<?php
/**
 * Customizer: Books Section
 *
 * @package CredibleCompany
 */

add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section( 'cc_books_section', array(
        'title'    => __( '8. Katalog Buku', 'crediblecompany' ),
        'panel'    => 'cc_homepage_panel',
        'priority' => 80,
    ) );

    // Judul Buku Terbitan
    $wp_customize->add_setting( 'cc_books_title', array(
        'default'           => 'Buku Terbitan KBM',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_books_title', array(
        'label'   => __( 'Judul Section', 'crediblecompany' ),
        'section' => 'cc_books_section',
        'type'    => 'text',
    ) );

    // Tampilkan / Sembunyikan Seksi Buku
    $wp_customize->add_setting( 'cc_books_enable', array(
        'default'           => true,
        'sanitize_callback' => 'cc_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'cc_books_enable', array(
        'label'   => __( 'Tampilkan Seksi Buku', 'crediblecompany' ),
        'section' => 'cc_books_section',
        'type'    => 'checkbox',
    ) );

    // Batasi Jumlah Buku
    $wp_customize->add_setting( 'cc_books_limit', array(
        'default'           => 5,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_books_limit', array(
        'label'       => __( 'Jumlah Buku yang Ditampilkan', 'crediblecompany' ),
        'description' => __( 'Tentukan jumlah item buku terbitan yang dimuat di beranda.', 'crediblecompany' ),
        'section'     => 'cc_books_section',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 1,
            'max'  => 20,
            'step' => 1,
        ),
    ) );

    // Pengaturan Warna Section Books
    $wp_customize->add_setting( 'cc_books_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_books_bg_color', array(
        'label'   => __( 'Warna Latar Belakang Section', 'crediblecompany' ),
        'section' => 'cc_books_section',
    ) ) );

    $wp_customize->add_setting( 'cc_books_title_color', array(
        'default'           => '#0f172a',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_books_title_color', array(
        'label'   => __( 'Warna Font Judul/Teks', 'crediblecompany' ),
        'section' => 'cc_books_section',
    ) ) );

    // Kustomisasi Warna Tombol
    $wp_customize->add_setting( 'cc_books_btn_bg_color', array(
        'default'           => 'transparent',
        'sanitize_callback' => 'sanitize_text_field', // menggunakan sanitize_text_field karena 'transparent' bukan hex
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_books_btn_bg_color', array(
        'label'   => __( 'Warna Latar Tombol', 'crediblecompany' ),
        'section' => 'cc_books_section',
    ) ) );

    $wp_customize->add_setting( 'cc_books_btn_text_color', array(
        'default'           => '#0f172a',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_books_btn_text_color', array(
        'label'   => __( 'Warna Teks/Border Tombol', 'crediblecompany' ),
        'section' => 'cc_books_section',
    ) ) );

    $wp_customize->add_setting( 'cc_books_btn_hover_bg_color', array(
        'default'           => '#0f172a',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_books_btn_hover_bg_color', array(
        'label'   => __( 'Warna Hover Latar Tombol', 'crediblecompany' ),
        'section' => 'cc_books_section',
    ) ) );

    $wp_customize->add_setting( 'cc_books_btn_hover_text_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_books_btn_hover_text_color', array(
        'label'   => __( 'Warna Hover Teks Tombol', 'crediblecompany' ),
        'section' => 'cc_books_section',
    ) ) );

    // Kustomisasi Spasi & Geometri (Slider)
    $wp_customize->add_setting( 'cc_books_padding_desktop', array(
        'default'           => 64,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_books_padding_desktop', array(
        'label'       => __( 'Padding Vertikal Seksi (Desktop px)', 'crediblecompany' ),
        'section'     => 'cc_books_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 200, 'step' => 2 ),
    ) );

    $wp_customize->add_setting( 'cc_books_padding_mobile', array(
        'default'           => 40,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_books_padding_mobile', array(
        'label'       => __( 'Padding Vertikal Seksi (Mobile px)', 'crediblecompany' ),
        'section'     => 'cc_books_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 150, 'step' => 2 ),
    ) );

    $wp_customize->add_setting( 'cc_books_gap_desktop', array(
        'default'           => 24,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_books_gap_desktop', array(
        'label'       => __( 'Jarak Antar Buku/Gap Grid (Desktop px)', 'crediblecompany' ),
        'section'     => 'cc_books_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 2 ),
    ) );

    $wp_customize->add_setting( 'cc_books_gap_mobile', array(
        'default'           => 12,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_books_gap_mobile', array(
        'label'       => __( 'Jarak Antar Buku/Gap Grid (Mobile px)', 'crediblecompany' ),
        'section'     => 'cc_books_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 80, 'step' => 2 ),
    ) );

    // Opsi: Aktifkan Horizontal Scroll di Mobile
    $wp_customize->add_setting( 'cc_mobile_scroll_books', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    $wp_customize->add_control( 'cc_mobile_scroll_books', array(
        'label'   => __( 'Aktifkan Horizontal Scroll di Mobile', 'crediblecompany' ),
        'section' => 'cc_books_section',
        'type'    => 'checkbox',
    ) );

} );

