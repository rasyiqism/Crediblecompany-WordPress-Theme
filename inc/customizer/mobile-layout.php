<?php
/**
 * Customizer: Layout Mobile Settings
 * Menampung toggle untuk horizontal scroll dll.
 *
 * @package CredibleCompany
 */

add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section( 'cc_mobile_layout_section', array(
        'title'       => __( 'Pengaturan Layout Mobile', 'crediblecompany' ),
        'panel'       => 'cc_homepage_panel',
        'description' => __( 'Atur tampilan beranda khusus untuk perangkat mobile.', 'crediblecompany' ),
        'priority'    => 120,
    ) );

    // Daftar section yang mendukung horizontal scroll
    $sections = array(
        'pricing'      => __( 'Paket Jasa (Pricing)', 'crediblecompany' ),
        'testimonials' => __( 'Testimoni', 'crediblecompany' ),
        'books'        => __( 'Buku Terbitan', 'crediblecompany' ),
        'features'     => __( 'Fitur (Mengapa Memilih)', 'crediblecompany' ),
        'blog'         => __( 'Artikel Blog', 'crediblecompany' ),
        'partners'     => __( 'Mitra / Partners', 'crediblecompany' ),
    );

    foreach ( $sections as $id => $label ) {
        $setting_id = "cc_mobile_scroll_{$id}";
        
        $wp_customize->add_setting( $setting_id, array(
            'default'           => true, // Default aktif (App-style)
            'sanitize_callback' => 'cc_sanitize_checkbox',
        ) );

        $wp_customize->add_control( $setting_id, array(
            'label'       => sprintf( __( 'Enable Horizontal Scroll: %s', 'crediblecompany' ), $label ),
            'section'     => 'cc_mobile_layout_section',
            'type'        => 'checkbox',
            'description' => __( 'Jika dinonaktifkan, tampilan akan kembali ke grid vertikal standar.', 'crediblecompany' ),
        ) );
    }

} );

