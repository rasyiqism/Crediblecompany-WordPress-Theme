<?php
/**
 * Customizer: CTA Section
 *
 * @package CredibleCompany
 */

add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section( 'cc_cta_section', array(
        'title'    => __( '11. CTA & Kontak', 'crediblecompany' ),
        'panel'    => 'cc_homepage_panel',
        'priority' => 110,
    ) );

    // Pilihan Layout CTA
    $wp_customize->add_setting( 'cc_cta_layout', array(
        'default'           => 'centered',
        'sanitize_callback' => 'sanitize_key',
    ) );
    $wp_customize->add_control( 'cc_cta_layout', array(
        'label'   => __( 'Pilih Layout CTA', 'crediblecompany' ),
        'section' => 'cc_cta_section',
        'type'    => 'select',
        'choices' => array(
            'centered'    => __( 'Centered (Teks di Tengah)', 'crediblecompany' ),
            'split'       => __( 'Split (Kiri-Kanan)', 'crediblecompany' ),
            'dark-banner' => __( 'Dark Banner (Tengah, Latar Gelap)', 'crediblecompany' ),
            'red-banner'  => __( 'Red Banner (Split, Latar Merah)', 'crediblecompany' ),
        ),
    ) );

    // Judul CTA
    $wp_customize->add_setting( 'cc_cta_title', array(
        'default'           => 'Konsultasi Gratis',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_cta_title', array(
        'label'   => __( 'Judul CTA', 'crediblecompany' ),
        'section' => 'cc_cta_section',
        'type'    => 'text',
    ) );

    // Deskripsi CTA
    $wp_customize->add_setting( 'cc_cta_desc', array(
        'default'           => 'Untuk mendapatkan Harga Promo dan Diskon bulan ini',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_cta_desc', array(
        'label'   => __( 'Deskripsi CTA', 'crediblecompany' ),
        'section' => 'cc_cta_section',
        'type'    => 'text',
    ) );

    // Teks Tombol CTA
    $wp_customize->add_setting( 'cc_cta_button_text', array(
        'default'           => 'Hubungi Kantor Pusat',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_cta_button_text', array(
        'label'   => __( 'Teks Tombol', 'crediblecompany' ),
        'section' => 'cc_cta_section',
        'type'    => 'text',
    ) );

    // URL Tombol CTA (WhatsApp / link)
    $wp_customize->add_setting( 'cc_cta_button_url', array(
        'default'           => 'https://wa.me/6281357517526?text=Halo+{Nama+Marketing},+saya+tertarik+dengan+layanan+Anda+....+Note:+Transaksi+HANYA+di+rekening+atas+nama:-+Sutrisno+(BSI+-+7145671967),+-+Ahmad+Fathur+Rozaq+(BSI+-+7187174923).',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'cc_cta_button_url', array(
        'label'   => __( 'URL Tombol (misal WhatsApp)', 'crediblecompany' ),
        'section' => 'cc_cta_section',
        'type'    => 'url',
    ) );

    // Pengaturan Warna Section CTA
    $wp_customize->add_setting( 'cc_cta_custom_bg_color', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_cta_custom_bg_color', array(
        'label'       => __( 'Warna Latar Belakang Kustom', 'crediblecompany' ),
        'description' => __( 'Kosongkan jika ingin menggunakan warna bawaan dari Layout CTA terpilih.', 'crediblecompany' ),
        'section'     => 'cc_cta_section',
    ) ) );

    $wp_customize->add_setting( 'cc_cta_custom_text_color', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_cta_custom_text_color', array(
        'label'       => __( 'Warna Font Kustom', 'crediblecompany' ),
        'description' => __( 'Kosongkan jika ingin menggunakan warna bawaan dari Layout CTA terpilih.', 'crediblecompany' ),
        'section'     => 'cc_cta_section',
    ) ) );

    // Warna Latar Tombol CTA
    $wp_customize->add_setting( 'cc_cta_btn_bg', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_cta_btn_bg', array(
        'label'       => __( 'Warna Latar Tombol CTA', 'crediblecompany' ),
        'description' => __( 'Kosongkan jika ingin menggunakan warna bawaan dari Layout CTA terpilih.', 'crediblecompany' ),
        'section'     => 'cc_cta_section',
    ) ) );

    // Warna Teks Tombol CTA
    $wp_customize->add_setting( 'cc_cta_btn_text', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_cta_btn_text', array(
        'label'       => __( 'Warna Teks Tombol CTA', 'crediblecompany' ),
        'description' => __( 'Kosongkan jika ingin menggunakan warna bawaan dari Layout CTA terpilih.', 'crediblecompany' ),
        'section'     => 'cc_cta_section',
    ) ) );

    // Warna Hover Latar Tombol CTA
    $wp_customize->add_setting( 'cc_cta_btn_hover_bg', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_cta_btn_hover_bg', array(
        'label'       => __( 'Warna Hover Latar Tombol CTA', 'crediblecompany' ),
        'description' => __( 'Kosongkan jika ingin menggunakan warna bawaan dari Layout CTA terpilih.', 'crediblecompany' ),
        'section'     => 'cc_cta_section',
    ) ) );

    // Warna Hover Teks Tombol CTA
    $wp_customize->add_setting( 'cc_cta_btn_hover_text', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_cta_btn_hover_text', array(
        'label'       => __( 'Warna Hover Teks Tombol CTA', 'crediblecompany' ),
        'description' => __( 'Kosongkan jika ingin menggunakan warna bawaan dari Layout CTA terpilih.', 'crediblecompany' ),
        'section'     => 'cc_cta_section',
    ) ) );

} );

