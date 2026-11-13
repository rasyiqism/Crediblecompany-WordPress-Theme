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

    // 3. Content-Signal untuk robots.txt
    $wp_customize->add_setting( 'cc_seo_content_signal', array(
        'default'           => 'search=yes, ai-input=yes, ai-train=no',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_seo_content_signal', array(
        'label'       => __( 'Content-Signal (robots.txt)', 'crediblecompany' ),
        'description' => __( 'Direktif Content-Signal di robots.txt (contoh: search=yes, ai-input=yes, ai-train=no).', 'crediblecompany' ),
        'section'     => 'cc_seo_section',
        'type'        => 'text',
    ) );

    // 4. Email & Telepon Resmi Bisnis (Machine-Readable & JSON-LD)
    $wp_customize->add_setting( 'cc_company_email', array(
        'default'           => 'naskah@penerbitkbm.com',
        'sanitize_callback' => 'sanitize_email',
    ) );
    $wp_customize->add_control( 'cc_company_email', array(
        'label'       => __( 'Email Resmi Bisnis', 'crediblecompany' ),
        'description' => __( 'Ditampilkan sebagai tautan mailto: di footer serta di schema JSON-LD Organization/LocalBusiness.', 'crediblecompany' ),
        'section'     => 'cc_seo_section',
        'type'        => 'email',
    ) );

    $wp_customize->add_setting( 'cc_company_phone', array(
        'default'           => '+6281357517526',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_company_phone', array(
        'label'       => __( 'Nomor Telepon Resmi Bisnis', 'crediblecompany' ),
        'description' => __( 'Format internasional (cth: +6281357517526) untuk tautan tel: dan JSON-LD.', 'crediblecompany' ),
        'section'     => 'cc_seo_section',
        'type'        => 'text',
    ) );

    // 5. URL Halaman Bisnis & Legal (Opsional — otomatis mendeteksi jika dikosongkan)
    $key_page_controls = array(
        'about'   => __( 'URL Halaman Tentang Kami (Opsional)', 'crediblecompany' ),
        'contact' => __( 'URL Halaman Kontak (Opsional)', 'crediblecompany' ),
        'terms'   => __( 'URL Halaman Syarat & Ketentuan (Opsional)', 'crediblecompany' ),
        'privacy' => __( 'URL Halaman Kebijakan Privasi (Opsional)', 'crediblecompany' ),
        'pricing' => __( 'URL Halaman Paket Harga (Opsional)', 'crediblecompany' ),
    );
    foreach ( $key_page_controls as $key => $label ) {
        $wp_customize->add_setting( "cc_page_{$key}_url", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        $wp_customize->add_control( "cc_page_{$key}_url", array(
            'label'       => $label,
            'description' => __( 'Kosongkan untuk deteksi otomatis halaman WordPress / rute bawaan tema.', 'crediblecompany' ),
            'section'     => 'cc_seo_section',
            'type'        => 'url',
        ) );
    }
} );
