<?php
/**
 * Customizer Loader
 *
 * @package CredibleCompany
 */

// 1. Daftarkan Panel Utama
add_action( 'customize_register', function ( $wp_customize ) {
    // Panel 1: Konfigurasi Global
    $wp_customize->add_panel( 'cc_global_panel', array(
        'title'    => __( 'Konfigurasi Global', 'crediblecompany' ),
        'priority' => 25,
    ) );

    // Panel 2: Tata Letak Homepage
    $wp_customize->add_panel( 'cc_homepage_panel', array(
        'title'    => __( 'Tata Letak Beranda (Sections)', 'crediblecompany' ),
        'priority' => 30,
    ) );
} );

// 2. Load Modular Sections (Setiap file memiliki add_action mandiri)
// Note: Pastikan file-file ini didaftarkan di luar block action panel utama
$_cc_customizer_dir = get_template_directory() . '/inc/customizer/';
require_once $_cc_customizer_dir . 'header.php';
require_once $_cc_customizer_dir . 'hero.php';
require_once $_cc_customizer_dir . 'statistics.php';
require_once $_cc_customizer_dir . 'about.php';
require_once $_cc_customizer_dir . 'features.php';
require_once $_cc_customizer_dir . 'cta.php';
require_once $_cc_customizer_dir . 'social.php';
require_once $_cc_customizer_dir . 'pricing.php';
require_once $_cc_customizer_dir . 'mitra.php';
require_once $_cc_customizer_dir . 'testimonials.php';
require_once $_cc_customizer_dir . 'books.php';
require_once $_cc_customizer_dir . 'faq.php';
require_once $_cc_customizer_dir . 'blog.php';
require_once $_cc_customizer_dir . 'marketing.php';
require_once $_cc_customizer_dir . 'footer.php';
require_once $_cc_customizer_dir . 'seo.php';
require_once $_cc_customizer_dir . 'mobile-layout.php';
require_once $_cc_customizer_dir . 'admin-theme.php';
require_once $_cc_customizer_dir . 'sections-dynamic-css.php';
unset( $_cc_customizer_dir );

/**
 * Sanitasi checkbox (Global)
 */
if ( ! function_exists( 'cc_sanitize_checkbox' ) ) {
    function cc_sanitize_checkbox( $checked ) {
        return ( ( isset( $checked ) && true == $checked ) ? true : false );
    }
}

/**
 * Sanitasi data JSON FAQ secara aman (Global)
 */
if ( ! function_exists( 'cc_sanitize_faq_json' ) ) {
    function cc_sanitize_faq_json( $value ) {
        if ( empty( $value ) ) {
            return '';
        }

        // Dekode string JSON
        $data = json_decode( $value, true );
        if ( ! is_array( $data ) ) {
            return '';
        }

        $clean_data = array();
        foreach ( $data as $item ) {
            $q = isset( $item['q'] ) ? sanitize_text_field( $item['q'] ) : '';
            $a = isset( $item['a'] ) ? wp_kses_post( $item['a'] ) : '';
            if ( $q !== '' || $a !== '' ) {
                $clean_data[] = array( 'q' => $q, 'a' => $a );
            }
        }

        return json_encode( $clean_data );
    }
}

/**
 * Generator Registrasi Kontrol Customizer Header untuk DRY (Global)
 */
if ( ! function_exists( 'cc_register_header_settings' ) ) {
    function cc_register_header_settings( $wp_customize, $prefix, $label_prefix, $active_callback ) {
        // Warna Latar Belakang Header
        $wp_customize->add_setting( "cc_header_{$prefix}_bg_color", array(
            'default'           => '#c01314',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "cc_header_{$prefix}_bg_color", array(
            'label'           => sprintf( __( '%s: Warna Latar Belakang Header', 'crediblecompany' ), $label_prefix ),
            'section'         => 'cc_header_section',
            'active_callback' => $active_callback,
        ) ) );

        // Warna Teks / Ikon Header
        $wp_customize->add_setting( "cc_header_{$prefix}_text_color", array(
            'default'           => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "cc_header_{$prefix}_text_color", array(
            'label'           => sprintf( __( '%s: Warna Teks & Ikon Header', 'crediblecompany' ), $label_prefix ),
            'section'         => 'cc_header_section',
            'active_callback' => $active_callback,
        ) ) );

        // Warna Teks Hover Header
        $wp_customize->add_setting( "cc_header_{$prefix}_text_hover_color", array(
            'default'           => '#ffcccc',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "cc_header_{$prefix}_text_hover_color", array(
            'label'           => sprintf( __( '%s: Warna Teks & Ikon saat Hover', 'crediblecompany' ), $label_prefix ),
            'section'         => 'cc_header_section',
            'active_callback' => $active_callback,
        ) ) );

        // Warna Latar Belakang Header Mobile (Opsional)
        $wp_customize->add_setting( "cc_header_{$prefix}_mobile_bg_color", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "cc_header_{$prefix}_mobile_bg_color", array(
            'label'           => sprintf( __( '%s: Warna Latar Belakang Header Mobile (Opsional)', 'crediblecompany' ), $label_prefix ),
            'description'     => __( 'Kosongkan untuk mengikuti warna header utama.', 'crediblecompany' ),
            'section'         => 'cc_header_section',
            'active_callback' => $active_callback,
        ) ) );

        // Warna Teks / Ikon Header Mobile (Opsional)
        $wp_customize->add_setting( "cc_header_{$prefix}_mobile_text_color", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "cc_header_{$prefix}_mobile_text_color", array(
            'label'           => sprintf( __( '%s: Warna Teks & Ikon Header Mobile (Opsional)', 'crediblecompany' ), $label_prefix ),
            'description'     => __( 'Kosongkan untuk mengikuti warna header utama.', 'crediblecompany' ),
            'section'         => 'cc_header_section',
            'active_callback' => $active_callback,
        ) ) );

        // Warna Teks Hover Header Mobile (Opsional)
        $wp_customize->add_setting( "cc_header_{$prefix}_mobile_text_hover_color", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "cc_header_{$prefix}_mobile_text_hover_color", array(
            'label'           => sprintf( __( '%s: Warna Teks Hover Header Mobile (Opsional)', 'crediblecompany' ), $label_prefix ),
            'description'     => __( 'Kosongkan untuk mengikuti warna header utama.', 'crediblecompany' ),
            'section'         => 'cc_header_section',
            'active_callback' => $active_callback,
        ) ) );

        // Padding Vertikal Header (Tinggi Navbar)
        $wp_customize->add_setting( "cc_header_{$prefix}_padding", array(
            'default'           => 12,
            'sanitize_callback' => 'absint',
        ) );
        $wp_customize->add_control( "cc_header_{$prefix}_padding", array(
            'label'           => sprintf( __( '%s: Padding Vertikal Header (px)', 'crediblecompany' ), $label_prefix ),
            'section'         => 'cc_header_section',
            'type'            => 'range',
            'input_attrs'     => array(
                'min'  => 5,
                'max'  => 50,
                'step' => 1,
            ),
            'active_callback' => $active_callback,
        ) );

        // Ukuran Font Menu Navigasi
        $wp_customize->add_setting( "cc_header_{$prefix}_menu_font_size", array(
            'default'           => 14,
            'sanitize_callback' => 'absint',
        ) );
        $wp_customize->add_control( "cc_header_{$prefix}_menu_font_size", array(
            'label'           => sprintf( __( '%s: Ukuran Font Menu Navigasi (px)', 'crediblecompany' ), $label_prefix ),
            'section'         => 'cc_header_section',
            'type'            => 'range',
            'input_attrs'     => array(
                'min'  => 12,
                'max'  => 24,
                'step' => 1,
            ),
            'active_callback' => $active_callback,
        ) );

        // Toggle Border Bawah
        $wp_customize->add_setting( "cc_header_{$prefix}_border_enable", array(
            'default'           => false,
            'sanitize_callback' => 'cc_sanitize_checkbox',
        ) );
        $wp_customize->add_control( "cc_header_{$prefix}_border_enable", array(
            'label'           => sprintf( __( '%s: Aktifkan Border Bawah Header', 'crediblecompany' ), $label_prefix ),
            'section'         => 'cc_header_section',
            'type'            => 'checkbox',
            'active_callback' => $active_callback,
        ) );

        // Warna Border Bawah
        $wp_customize->add_setting( "cc_header_{$prefix}_border_color", array(
            'default'           => 'rgba(255, 255, 255, 0.15)',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "cc_header_{$prefix}_border_color", array(
            'label'           => sprintf( __( '%s: Warna Border Bawah', 'crediblecompany' ), $label_prefix ),
            'description'     => __( 'Mendukung Hex atau RGBA.', 'crediblecompany' ),
            'section'         => 'cc_header_section',
            'type'            => 'text',
            'active_callback' => $active_callback,
        ) );
    }
}

