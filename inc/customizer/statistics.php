<?php
/**
 * Customizer: Statistics Section
 *
 * @package CredibleCompany
 */

// Mencegah akses langsung
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'customize_register', function( $wp_customize ) {

    // Registrasi section Statistik
    $wp_customize->add_section( 'cc_stats_section', array(
        'title'    => __( '2. Statistik', 'crediblecompany' ),
        'panel'    => 'cc_homepage_panel',
        'priority' => 20,
    ) );

    // Opsi 1: Aktifkan/Nonaktifkan Statistik
    $wp_customize->add_setting( 'cc_stats_enable', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    $wp_customize->add_control( 'cc_stats_enable', array(
        'label'    => __( 'Tampilkan Statistik Section', 'crediblecompany' ),
        'section'  => 'cc_stats_section',
        'type'     => 'checkbox',
        'priority' => 1,
    ) );

    // Opsi 2: Jumlah Statistik yang Ditampilkan
    $wp_customize->add_setting( 'cc_stats_count', array(
        'default'           => 3,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_stats_count', array(
        'label'           => __( 'Jumlah Statistik', 'crediblecompany' ),
        'section'         => 'cc_stats_section',
        'type'            => 'select',
        'choices'         => array(
            1 => '1 Statistik',
            2 => '2 Statistik',
            3 => '3 Statistik',
            4 => '4 Statistik',
            5 => '5 Statistik',
            6 => '6 Statistik',
        ),
        'active_callback' => function() {
            return get_theme_mod( 'cc_stats_enable', true );
        },
        'priority'        => 2,
    ) );

    // Default values untuk 6 stats
    $stats_defaults = array(
        array( '5.000+', 'Judul Terbit' ),
        array( '700.000+', 'Eksemplar Cetak' ),
        array( '6.500+', 'Penulis KBM' ),
        array( '500+', 'Mitra Resmi' ),
        array( '100k', 'Pengguna Aktif' ),
        array( '99%', 'Tingkat Kepuasan' ),
    );

    // Render control input angka dan label secara dinamis
    for ( $i = 1; $i <= 6; $i++ ) {
        $idx = $i - 1;

        // Callback aktif: hanya muncul jika section aktif DAN jumlah stats mencukupi
        $active_callback = function() use ( $i ) {
            $enable = get_theme_mod( 'cc_stats_enable', true );
            $count  = intval( get_theme_mod( 'cc_stats_count', 3 ) );
            return $enable && ( $count >= $i );
        };

        // Input Angka Statistik
        $wp_customize->add_setting( "cc_stat_number_{$i}", array(
            'default'           => isset( $stats_defaults[ $idx ] ) ? $stats_defaults[ $idx ][0] : '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "cc_stat_number_{$i}", array(
            'label'           => sprintf( __( 'Statistik %d — Angka', 'crediblecompany' ), $i ),
            'section'         => 'cc_stats_section',
            'type'            => 'text',
            'active_callback' => $active_callback,
            'priority'        => 10 + ( $i * 2 ),
        ) );

        // Input Label Statistik
        $wp_customize->add_setting( "cc_stat_label_{$i}", array(
            'default'           => isset( $stats_defaults[ $idx ] ) ? $stats_defaults[ $idx ][1] : '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "cc_stat_label_{$i}", array(
            'label'           => sprintf( __( 'Statistik %d — Label', 'crediblecompany' ), $i ),
            'section'         => 'cc_stats_section',
            'type'            => 'text',
            'active_callback' => $active_callback,
            'priority'        => 11 + ( $i * 2 ),
        ) );
    }

    // --- PENGATURAN WARNA STYLING STATS ---
    $style_active_callback = function() {
        return get_theme_mod( 'cc_stats_enable', true );
    };

    // Warna Angka
    $wp_customize->add_setting( 'cc_stat_number_color', array(
        'default'           => '#F59E0B',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_stat_number_color', array(
        'label'           => __( 'Warna Angka Statistik', 'crediblecompany' ),
        'section'         => 'cc_stats_section',
        'active_callback' => $style_active_callback,
        'priority'        => 30,
    ) ) );

    // Warna Label
    $wp_customize->add_setting( 'cc_stat_label_color', array(
        'default'           => '#1e293b',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_stat_label_color', array(
        'label'           => __( 'Warna Label Statistik', 'crediblecompany' ),
        'section'         => 'cc_stats_section',
        'active_callback' => $style_active_callback,
        'priority'        => 31,
    ) ) );

    // Warna Latar Belakang Section
    $wp_customize->add_setting( 'cc_stat_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_stat_bg_color', array(
        'label'           => __( 'Warna Latar Belakang Section', 'crediblecompany' ),
        'section'         => 'cc_stats_section',
        'active_callback' => $style_active_callback,
        'priority'        => 32,
    ) ) );

    // Opsi Spasi & Jarak: Jarak antar Statistik (Gap)
    $wp_customize->add_setting( 'cc_stat_gap', array(
        'default'           => 32,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_stat_gap', array(
        'label'           => __( 'Jarak Antar Statistik (Pixel)', 'crediblecompany' ),
        'section'         => 'cc_stats_section',
        'type'            => 'range',
        'input_attrs'     => array( 'min' => 10, 'max' => 100, 'step' => 2 ),
        'active_callback' => $style_active_callback,
        'priority'        => 40,
    ) );

    // Opsi Spasi & Jarak: Padding Vertikal Section
    $wp_customize->add_setting( 'cc_stat_padding', array(
        'default'           => 64,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_stat_padding', array(
        'label'           => __( 'Tinggi Spasi Atas/Bawah (Pixel)', 'crediblecompany' ),
        'section'         => 'cc_stats_section',
        'type'            => 'range',
        'input_attrs'     => array( 'min' => 20, 'max' => 150, 'step' => 2 ),
        'active_callback' => $style_active_callback,
        'priority'        => 41,
    ) );

} );

