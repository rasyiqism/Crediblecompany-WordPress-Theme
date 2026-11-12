<?php
/**
 * Customizer: SEO Settings
 *
 * @package CredibleCompany
 */

add_action( 'customize_register', function ( $wp_customize ) {
    // Tambahkan Section SEO di dalam Panel Homepage
    $wp_customize->add_section( 'cc_seo_section', array(
        'title'    => __( 'Optimasi SEO', 'crediblecompany' ),
        'panel'    => 'cc_global_panel',
        'priority' => 60,
    ) );

    // 1. Meta Description Homepage (Manual)
    $wp_customize->add_setting( 'cc_seo_home_description', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'cc_seo_home_description', array(
        'label'       => __( 'Meta Deskripsi Beranda (Manual)', 'crediblecompany' ),
        'description' => __( 'Jika diisi, ini akan menjadi deskripsi utama di hasil pencarian Google. Jika kosong, sistem akan menggunakan mode otomatis (Excerpt/Konten).', 'crediblecompany' ),
        'section'     => 'cc_seo_section',
        'type'        => 'textarea',
    ) );

    // 2. Social Share Image (Open Graph)
    $wp_customize->add_setting( 'cc_seo_og_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'cc_seo_og_image', array(
        'label'       => __( 'Gambar Berbagi Sosial (OG Image)', 'crediblecompany' ),
        'description' => __( 'Unggah gambar yang akan muncul saat link dibagikan (WA, FB, dll). Rekomendasi: 1200x630px. Jika kosong, sistem akan mencoba mengambil logo situs.', 'crediblecompany' ),
        'section'     => 'cc_seo_section',
    ) ) );
} );
