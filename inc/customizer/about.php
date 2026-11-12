<?php
/**
 * Customizer: About Section (Tentang Kami)
 *
 * @package CredibleCompany
 */

// Mencegah akses langsung
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'customize_register', function( $wp_customize ) {

    // Daftarkan seksi About
    $wp_customize->add_section( 'cc_about_section', array(
        'title'    => __( '3. Tentang Kami (About)', 'crediblecompany' ),
        'panel'    => 'cc_homepage_panel',
        'priority' => 30,
    ) );

    $wp_customize->add_setting( 'cc_about_desc', array(
        'default'           => 'Penerbit Buku KBM Indonesia telah terbukti menjadi mitra penerbitan buku ber ISBN yang terbaik di Indonesia. Dengan modal pelayanan dedikasi secara ramah, fast respon dalam melayani para penulis di seluruh Indonesia. Di sini, anda akan mendapatkan gratis ongkos kirim buku ke seluruh Indonesia, terindeks di google schoolar, buku diupload di google playbook dan royalti penjualan buku sebanyak 25% yang mana laporan penjualan disampaikan melalui email setiap bulannya dan bahkan ada fasilitas khusus yang mampu mengeluarkan ISBN dalam 1-2 hari.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'cc_about_desc', array(
        'label'       => __( 'Paragraf Tentang Kami', 'crediblecompany' ),
        'description' => __( 'Teks deskripsi penjelasan mengenai profil perusahaan Anda.', 'crediblecompany' ),
        'section'     => 'cc_about_section',
        'type'        => 'textarea',
    ) );

    // 2. Gambar Tentang Kami
    $wp_customize->add_setting( 'cc_about_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'cc_about_image', array(
        'label'       => __( 'Gambar Kantor / Tim', 'crediblecompany' ),
        'description' => __( 'Unggah gambar representasi kantor atau tim di sini.', 'crediblecompany' ),
        'section'     => 'cc_about_section',
    ) ) );

    // 3. Warna Latar Belakang Seksi About
    $wp_customize->add_setting( 'cc_about_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_about_bg_color', array(
        'label'   => __( 'Warna Latar Belakang Section', 'crediblecompany' ),
        'section' => 'cc_about_section',
    ) ) );

    // 4. Warna Latar Belakang Box/Kotak Detail About
    $wp_customize->add_setting( 'cc_about_block_bg_color', array(
        'default'           => '#f1f5f9',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_about_block_bg_color', array(
        'label'   => __( 'Warna Latar Belakang Kotak Detail', 'crediblecompany' ),
        'section' => 'cc_about_section',
    ) ) );

    // 5. Warna Teks Deskripsi
    $wp_customize->add_setting( 'cc_about_text_color', array(
        'default'           => '#334155',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_about_text_color', array(
        'label'   => __( 'Warna Font Teks', 'crediblecompany' ),
        'section' => 'cc_about_section',
    ) ) );

    // 6a. [Desktop] Padding Horizontal Teks (Kanan/Kiri)
    $wp_customize->add_setting( 'cc_about_text_padding_lr_desktop', array(
        'default'           => 0,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_about_text_padding_lr_desktop', array(
        'label'       => __( 'Desktop: Spasi Kanan/Kiri Teks (Pixel)', 'crediblecompany' ),
        'description' => __( 'Tambahan spasi horizontal teks About di desktop (bawaan kontainer sudah memiliki 2rem).', 'crediblecompany' ),
        'section'     => 'cc_about_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 80, 'step' => 2 ),
    ) );

    // 6b. [Mobile] Padding Horizontal Teks (Kanan/Kiri)
    $wp_customize->add_setting( 'cc_about_text_padding_lr_mobile', array(
        'default'           => 16,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_about_text_padding_lr_mobile', array(
        'label'       => __( 'Mobile: Spasi Kanan/Kiri Teks (Pixel)', 'crediblecompany' ),
        'description' => __( 'Spasi horizontal teks About di tampilan layar mobile (ketika kontainer disembunyikan).', 'crediblecompany' ),
        'section'     => 'cc_about_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 80, 'step' => 2 ),
    ) );

    // 7a. [Desktop] Spasi Padding Atas/Bawah Section (Tinggi Section)
    $wp_customize->add_setting( 'cc_about_padding_desktop', array(
        'default'           => 64,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_about_padding_desktop', array(
        'label'       => __( 'Desktop: Tinggi Spasi Section (Pixel)', 'crediblecompany' ),
        'description' => __( 'Mengatur padding vertikal atas dan bawah About section di layar desktop.', 'crediblecompany' ),
        'section'     => 'cc_about_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 20, 'max' => 150, 'step' => 2 ),
    ) );

    // 7b. [Mobile] Spasi Padding Atas/Bawah Section (Tinggi Section)
    $wp_customize->add_setting( 'cc_about_padding_mobile', array(
        'default'           => 40,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_about_padding_mobile', array(
        'label'       => __( 'Mobile: Tinggi Spasi Section (Pixel)', 'crediblecompany' ),
        'description' => __( 'Mengatur padding vertikal atas dan bawah About section di layar mobile.', 'crediblecompany' ),
        'section'     => 'cc_about_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 10, 'max' => 120, 'step' => 2 ),
    ) );

} );
