<?php
/**
 * Customizer: Mitra & Proses
 *
 * @package CredibleCompany
 */

add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section( 'cc_mitra_section', array(
        'title'    => __( '7. Mitra & Proses', 'crediblecompany' ),
        'panel'    => 'cc_homepage_panel',
        'priority' => 70,
    ) );

    $wp_customize->add_setting( 'cc_mitra_proses_tagline', array(
        'default'           => 'Tulis Karya Anda → Publikasikan → Bangun Pembaca → Dapatkan Koin → Tarik Pendapatan',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_mitra_proses_tagline', array(
        'label'   => __( 'Tagline Proses Kerja', 'crediblecompany' ),
        'section' => 'cc_mitra_section',
        'type'    => 'text',
    ) );

    // Fallback Teks Mitra Resmi (dipisahkan koma)
    $wp_customize->add_setting( 'cc_mitra_names', array(
        'default'           => 'Penerbit KBM, Komunitas Menulis Indonesia, KBM Book Store, Asosiasi Penulis Digital',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_mitra_names', array(
        'label'       => __( 'Fallback Teks Mitra Resmi (pisahkan koma)', 'crediblecompany' ),
        'description' => __( 'Teks yang tampil jika gambar logo Mitra Resmi di bawah ini kosong.', 'crediblecompany' ),
        'section'     => 'cc_mitra_section',
        'type'        => 'text',
    ) );

    // Daftar 6 slot logo Mitra Resmi
    for ( $i = 1; $i <= 6; $i++ ) {
        $wp_customize->add_setting( "cc_mitra_logo_{$i}", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, "cc_mitra_logo_{$i}", array(
            'label'       => sprintf( __( 'Logo Mitra Resmi %d', 'crediblecompany' ), $i ),
            'description' => __( 'Upload gambar logo (PNG dengan background transparan disarankan).', 'crediblecompany' ),
            'section'     => 'cc_mitra_section',
        ) ) );
    }

    // Daftar Mitra Pembayaran / Pengiriman (dipisahkan koma)
    $wp_customize->add_setting( 'cc_mitra_payment', array(
        'default'           => 'Lorem, Ipsum, Dolor, Sit, Amet, Consectetur',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_mitra_payment', array(
        'label'       => __( 'Fallback Teks Mitra Pembayaran & Pengiriman (pisahkan koma)', 'crediblecompany' ),
        'description' => __( 'Teks yang tampil jika gambar logo pembayaran di bawah ini kosong.', 'crediblecompany' ),
        'section'     => 'cc_mitra_section',
        'type'        => 'text',
    ) );

    // Daftar 8 slot logo Mitra Pembayaran / Pengiriman
    for ( $i = 1; $i <= 8; $i++ ) {
        $wp_customize->add_setting( "cc_mitra_payment_logo_{$i}", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, "cc_mitra_payment_logo_{$i}", array(
            'label'       => sprintf( __( 'Logo Pembayaran/Pengiriman %d', 'crediblecompany' ), $i ),
            'description' => __( 'Upload logo (disarankan tinggi 30px-40px, latar transparan).', 'crediblecompany' ),
            'section'     => 'cc_mitra_section',
        ) ) );
    }

    // Pengaturan Warna Section Mitra & Partners
    $wp_customize->add_setting( 'cc_mitra_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_mitra_bg_color', array(
        'label'   => __( 'Warna Latar Belakang Section Mitra Resmi', 'crediblecompany' ),
        'section' => 'cc_mitra_section',
    ) ) );

    $wp_customize->add_setting( 'cc_mitra_partners_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_mitra_partners_bg_color', array(
        'label'   => __( 'Warna Latar Belakang Section Pembayaran/Pengiriman', 'crediblecompany' ),
        'section' => 'cc_mitra_section',
    ) ) );

    // Teks Label Mitra Pembayaran/Pengiriman
    $wp_customize->add_setting( 'cc_mitra_payment_label', array(
        'default'           => 'Pembayaran dan Pengiriman Didukung oleh Mitra Tepercaya Kami',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_mitra_payment_label', array(
        'label'   => __( 'Label Mitra Pembayaran & Pengiriman', 'crediblecompany' ),
        'section' => 'cc_mitra_section',
        'type'    => 'text',
    ) );

    // Opsi Logo Grayscale
    $wp_customize->add_setting( 'cc_mitra_payment_grayscale', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    $wp_customize->add_control( 'cc_mitra_payment_grayscale', array(
        'label'   => __( 'Aktifkan Efek Hitam-Putih (Grayscale) Logo', 'crediblecompany' ),
        'section' => 'cc_mitra_section',
        'type'    => 'checkbox',
    ) );

    // Opsi Horizontal Scroll Mobile (Pembayaran)
    $wp_customize->add_setting( 'cc_mobile_scroll_partners', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    $wp_customize->add_control( 'cc_mobile_scroll_partners', array(
        'label'   => __( 'Aktifkan Horizontal Scroll di Mobile (Pembayaran)', 'crediblecompany' ),
        'section' => 'cc_mitra_section',
        'type'    => 'checkbox',
    ) );

    // Kecepatan Scroll Marquee (Detik)
    $wp_customize->add_setting( 'cc_mitra_marquee_speed', array(
        'default'           => 20,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_mitra_marquee_speed', array(
        'label'       => __( 'Kecepatan Scroll Logo Mitra Resmi (Detik)', 'crediblecompany' ),
        'description' => __( 'Mengatur waktu durasi (dalam detik) satu putaran animasi scroll logo. Semakin kecil nilainya, semakin cepat putarannya.', 'crediblecompany' ),
        'section'     => 'cc_mitra_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 5, 'max' => 60, 'step' => 1 ),
    ) );

    // Warna Teks Tagline Proses
    $wp_customize->add_setting( 'cc_mitra_proses_tagline_color', array(
        'default'           => '#c01314',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_mitra_proses_tagline_color', array(
        'label'   => __( 'Warna Teks Tagline Proses', 'crediblecompany' ),
        'section' => 'cc_mitra_section',
    ) ) );

    // Warna Teks Label Mitra Pembayaran
    $wp_customize->add_setting( 'cc_mitra_payment_label_color', array(
        'default'           => '#64748b',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_mitra_payment_label_color', array(
        'label'   => __( 'Warna Teks Label Mitra Pembayaran', 'crediblecompany' ),
        'section' => 'cc_mitra_section',
    ) ) );

    // Padding Vertikal Mitra Resmi - Desktop
    $wp_customize->add_setting( 'cc_mitra_resmi_padding_desktop', array(
        'default'           => 48,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_mitra_resmi_padding_desktop', array(
        'label'   => __( 'Desktop: Tinggi Spasi Seksi Mitra Resmi (px)', 'crediblecompany' ),
        'section' => 'cc_mitra_section',
        'type'    => 'range',
        'input_attrs' => array( 'min' => 10, 'max' => 120, 'step' => 2 ),
    ) );

    // Padding Vertikal Mitra Resmi - Mobile
    $wp_customize->add_setting( 'cc_mitra_resmi_padding_mobile', array(
        'default'           => 32,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_mitra_resmi_padding_mobile', array(
        'label'   => __( 'Mobile: Tinggi Spasi Seksi Mitra Resmi (px)', 'crediblecompany' ),
        'section' => 'cc_mitra_section',
        'type'    => 'range',
        'input_attrs' => array( 'min' => 5, 'max' => 80, 'step' => 2 ),
    ) );

    // Padding Vertikal Mitra Pembayaran - Desktop
    $wp_customize->add_setting( 'cc_mitra_partners_padding_desktop', array(
        'default'           => 32,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_mitra_partners_padding_desktop', array(
        'label'   => __( 'Desktop: Tinggi Spasi Seksi Pembayaran (px)', 'crediblecompany' ),
        'section' => 'cc_mitra_section',
        'type'    => 'range',
        'input_attrs' => array( 'min' => 10, 'max' => 100, 'step' => 2 ),
    ) );

    // Padding Vertikal Mitra Pembayaran - Mobile
    $wp_customize->add_setting( 'cc_mitra_partners_padding_mobile', array(
        'default'           => 24,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_mitra_partners_padding_mobile', array(
        'label'   => __( 'Mobile: Tinggi Spasi Seksi Pembayaran (px)', 'crediblecompany' ),
        'section' => 'cc_mitra_section',
        'type'    => 'range',
        'input_attrs' => array( 'min' => 5, 'max' => 80, 'step' => 2 ),
    ) );

} );


