<?php
/**
 * Customizer: Footer Section
 *
 * @package CredibleCompany
 */

add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section( 'cc_footer_section', array(
        'title'    => __( 'Footer', 'crediblecompany' ),
        'panel'    => 'cc_global_panel',
        'priority' => 20,
    ) );

    /* --- Alamat Kantor --- */
    $wp_customize->add_setting( 'cc_footer_address', array(
        'default'           => 'Kantor : Paingan, (Halaman Masjid Ash Sholihin), Maguwoharjo, Depok, Sleman, Daerah Istimewa Yogyakarta.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'cc_footer_address', array(
        'label'       => __( 'Alamat Kantor', 'crediblecompany' ),
        'description' => __( 'Masukkan alamat lengkap kantor (mendukung baris baru).', 'crediblecompany' ),
        'section'     => 'cc_footer_section',
        'type'        => 'textarea',
    ) );

    /* --- Teks Hak Cipta --- */
    $wp_customize->add_setting( 'cc_footer_copyright', array(
        'default'           => 'Credible Company.',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_footer_copyright', array(
        'label'   => __( 'Teks Hak Cipta', 'crediblecompany' ),
        'section' => 'cc_footer_section',
        'type'    => 'text',
    ) );

    // Pengaturan Warna Section Footer
    $wp_customize->add_setting( 'cc_footer_bg_color', array(
        'default'           => '#c01314',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_footer_bg_color', array(
        'label'   => __( 'Warna Latar Belakang Footer', 'crediblecompany' ),
        'section' => 'cc_footer_section',
    ) ) );

    $wp_customize->add_setting( 'cc_footer_text_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_footer_text_color', array(
        'label'   => __( 'Warna Font Teks Footer', 'crediblecompany' ),
        'section' => 'cc_footer_section',
    ) ) );

    // Kustomisasi Warna Latar Box Logo & Baris Alamat
    $wp_customize->add_setting( 'cc_footer_logo_bg', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_footer_logo_bg', array(
        'label'   => __( 'Warna Latar Box Logo', 'crediblecompany' ),
        'section' => 'cc_footer_section',
    ) ) );

    $wp_customize->add_setting( 'cc_footer_middle_bg', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_footer_middle_bg', array(
        'label'   => __( 'Warna Latar Baris Alamat (Middle)', 'crediblecompany' ),
        'section' => 'cc_footer_section',
    ) ) );

    $wp_customize->add_setting( 'cc_footer_middle_text_color', array(
        'default'           => '#000000',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_footer_middle_text_color', array(
        'label'   => __( 'Warna Teks Baris Alamat (Middle)', 'crediblecompany' ),
        'section' => 'cc_footer_section',
    ) ) );

    // Kustomisasi Warna Latar & Teks Kartu Statistik (Views Counter)
    $wp_customize->add_setting( 'cc_footer_stats_bg', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_footer_stats_bg', array(
        'label'   => __( 'Warna Latar Kartu Statistik', 'crediblecompany' ),
        'section' => 'cc_footer_section',
    ) ) );

    $wp_customize->add_setting( 'cc_footer_stats_text_color', array(
        'default'           => '#000000',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_footer_stats_text_color', array(
        'label'   => __( 'Warna Teks Kartu Statistik', 'crediblecompany' ),
        'section' => 'cc_footer_section',
    ) ) );

    // Kustomisasi Warna Sosial Media
    $wp_customize->add_setting( 'cc_footer_social_brand_colors', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    $wp_customize->add_control( 'cc_footer_social_brand_colors', array(
        'label'   => __( 'Gunakan Warna Brand Resmi Sosial Media', 'crediblecompany' ),
        'section' => 'cc_footer_section',
        'type'    => 'checkbox',
    ) );

    // Callback aktif untuk sosial media kustom
    $custom_social_active = function() {
        return ! get_theme_mod( 'cc_footer_social_brand_colors', true );
    };

    $wp_customize->add_setting( 'cc_footer_social_bg', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_footer_social_bg', array(
        'label'           => __( 'Warna Latar Ikon Sosial Media', 'crediblecompany' ),
        'section'         => 'cc_footer_section',
        'active_callback' => $custom_social_active,
    ) ) );

    $wp_customize->add_setting( 'cc_footer_social_icon_color', array(
        'default'           => '#000000',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_footer_social_icon_color', array(
        'label'           => __( 'Warna Ikon Sosial Media', 'crediblecompany' ),
        'section'         => 'cc_footer_section',
        'active_callback' => $custom_social_active,
    ) ) );

    $wp_customize->add_setting( 'cc_footer_social_hover_bg', array(
        'default'           => '#d4af37',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_footer_social_hover_bg', array(
        'label'           => __( 'Warna Hover Latar Sosial Media', 'crediblecompany' ),
        'section'         => 'cc_footer_section',
        'active_callback' => $custom_social_active,
    ) ) );

    $wp_customize->add_setting( 'cc_footer_social_hover_icon_color', array(
        'default'           => '#000000',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_footer_social_hover_icon_color', array(
        'label'           => __( 'Warna Hover Ikon Sosial Media', 'crediblecompany' ),
        'section'         => 'cc_footer_section',
        'active_callback' => $custom_social_active,
    ) ) );

    // Kustomisasi Spasi & Padding (Slider)
    $wp_customize->add_setting( 'cc_footer_top_padding', array(
        'default'           => 40,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_footer_top_padding', array(
        'label'       => __( 'Padding Vertikal Baris Atas (px)', 'crediblecompany' ),
        'section'     => 'cc_footer_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 150, 'step' => 2 ),
    ) );

    $wp_customize->add_setting( 'cc_footer_middle_padding', array(
        'default'           => 24,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_footer_middle_padding', array(
        'label'       => __( 'Padding Vertikal Baris Alamat (px)', 'crediblecompany' ),
        'section'     => 'cc_footer_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 2 ),
    ) );

    $wp_customize->add_setting( 'cc_footer_bottom_padding', array(
        'default'           => 24,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_footer_bottom_padding', array(
        'label'       => __( 'Padding Vertikal Baris Bawah (px)', 'crediblecompany' ),
        'section'     => 'cc_footer_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 2 ),
    ) );

} );

