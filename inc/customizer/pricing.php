<?php
/**
 * Customizer: Pricing Section (Paket Jasa)
 *
 * @package CredibleCompany
 */

add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section( 'cc_pricing_section', array(
        'title'    => __( '5. Paket Jasa (Pricing)', 'crediblecompany' ),
        'panel'    => 'cc_homepage_panel',
        'priority' => 50,
    ) );

    $wp_customize->add_setting( 'cc_pricing_title', array(
        'default'           => 'PAKET PENERBIT BUKU KBM INDONESIA',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_pricing_title', array(
        'label'   => __( 'Judul Section', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'cc_pricing_subtitle', array(
        'default'           => 'Silahkan pilih paket penerbitan buku di bawah ini. Dan jika anda seorang Guru ajar, Dosen, Pelajar dan Mahasiswa maka dapatkan diskon harga khusus untuk anda.',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_pricing_subtitle', array(
        'label'   => __( 'Subtitle Section', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'cc_pricing_grid_columns', array(
        'default'           => 3,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_pricing_grid_columns', array(
        'label'   => __( 'Jumlah Kolom Desktop', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
        'type'    => 'select',
        'choices' => array(
            2 => '2 Kolom',
            3 => '3 Kolom',
            4 => '4 Kolom',
            5 => '5 Kolom',
            6 => '6 Kolom',
        ),
    ) );

    // Jarak antara judul/deskripsi section dengan card (margin-bottom) - Desktop
    $wp_customize->add_setting( 'cc_pricing_header_margin_bottom_desktop', array(
        'default'           => 48,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_pricing_header_margin_bottom_desktop', array(
        'label'       => __( 'Desktop: Jarak Antara Judul & Card (px)', 'crediblecompany' ),
        'description' => __( 'Mengatur jarak (margin-bottom) antara judul/deskripsi dengan kartu paket jasa di layar desktop.', 'crediblecompany' ),
        'section'     => 'cc_pricing_section',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 8,
            'max'  => 120,
            'step' => 4,
        ),
    ) );

    // Jarak antara judul/deskripsi section dengan card (margin-bottom) - Mobile
    $wp_customize->add_setting( 'cc_pricing_header_margin_bottom_mobile', array(
        'default'           => 24,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_pricing_header_margin_bottom_mobile', array(
        'label'       => __( 'Mobile: Jarak Antara Judul & Card (px)', 'crediblecompany' ),
        'description' => __( 'Mengatur jarak (margin-bottom) antara judul/deskripsi dengan kartu paket jasa di layar mobile.', 'crediblecompany' ),
        'section'     => 'cc_pricing_section',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 4,
            'max'  => 80,
            'step' => 4,
        ),
    ) );

    // Jarak antar kartu (Gap) - Desktop
    $wp_customize->add_setting( 'cc_pricing_card_gap_desktop', array(
        'default'           => 24,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_pricing_card_gap_desktop', array(
        'label'       => __( 'Desktop: Jarak Antar Card (px)', 'crediblecompany' ),
        'description' => __( 'Mengatur jarak spasi (gap) antar kartu paket jasa di layar desktop.', 'crediblecompany' ),
        'section'     => 'cc_pricing_section',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 8,
            'max'  => 64,
            'step' => 2,
        ),
    ) );

    // Jarak antar kartu (Gap) - Mobile
    $wp_customize->add_setting( 'cc_pricing_card_gap_mobile', array(
        'default'           => 16,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_pricing_card_gap_mobile', array(
        'label'       => __( 'Mobile: Jarak Antar Card (px)', 'crediblecompany' ),
        'description' => __( 'Mengatur jarak spasi (gap) antar kartu paket jasa di layar mobile.', 'crediblecompany' ),
        'section'     => 'cc_pricing_section',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 4,
            'max'  => 48,
            'step' => 2,
        ),
    ) );

    // Pengaturan Warna Section Pricing
    $wp_customize->add_setting( 'cc_pricing_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_bg_color', array(
        'label'   => __( 'Warna Latar Belakang Section', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    $wp_customize->add_setting( 'cc_pricing_text_color', array(
        'default'           => '#0f172a',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_text_color', array(
        'label'   => __( 'Warna Font Judul/Sub', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    $wp_customize->add_setting( 'cc_pricing_card_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_card_bg_color', array(
        'label'   => __( 'Warna Latar Belakang Card', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    // Warna Background Header Card
    $wp_customize->add_setting( 'cc_pricing_card_header_bg', array(
        'default'           => '#c01314',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_card_header_bg', array(
        'label'   => __( 'Warna Latar Header Card', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    // Warna Teks Header Card
    $wp_customize->add_setting( 'cc_pricing_card_header_text', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_card_header_text', array(
        'label'   => __( 'Warna Teks Header Card', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    // Warna Background Body Card
    $wp_customize->add_setting( 'cc_pricing_card_body_bg', array(
        'default'           => '#f1f5f9',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_card_body_bg', array(
        'label'   => __( 'Warna Latar Body Card', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    // Warna Teks Harga Paket
    $wp_customize->add_setting( 'cc_pricing_card_price_color', array(
        'default'           => '#c01314',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_card_price_color', array(
        'label'   => __( 'Warna Teks Harga', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    // Warna Teks Spesifikasi (Eksemplar & Ukuran)
    $wp_customize->add_setting( 'cc_pricing_card_spec_color', array(
        'default'           => '#0f172a',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_card_spec_color', array(
        'label'   => __( 'Warna Teks Spesifikasi', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    // Warna Teks Judul Fasilitas
    $wp_customize->add_setting( 'cc_pricing_card_feat_title_color', array(
        'default'           => '#c01314',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_card_feat_title_color', array(
        'label'   => __( 'Warna Judul Fasilitas', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    // Warna Latar Badge
    $wp_customize->add_setting( 'cc_pricing_card_badge_bg', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_card_badge_bg', array(
        'label'   => __( 'Warna Latar Badge', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    // Warna Teks Badge
    $wp_customize->add_setting( 'cc_pricing_card_badge_text', array(
        'default'           => '#c01314',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_card_badge_text', array(
        'label'   => __( 'Warna Teks Badge', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    // Warna Latar Tombol CTA Card
    $wp_customize->add_setting( 'cc_pricing_card_btn_bg', array(
        'default'           => '#c01314',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_card_btn_bg', array(
        'label'   => __( 'Warna Latar Tombol Paket', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    // Warna Teks Tombol CTA Card
    $wp_customize->add_setting( 'cc_pricing_card_btn_text', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_card_btn_text', array(
        'label'   => __( 'Warna Teks Tombol Paket', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    // Warna Latar Hover Tombol CTA Card
    $wp_customize->add_setting( 'cc_pricing_card_btn_hover_bg', array(
        'default'           => '#a00f10',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_card_btn_hover_bg', array(
        'label'   => __( 'Warna Hover Latar Tombol', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    // Warna Teks Hover Tombol CTA Card
    $wp_customize->add_setting( 'cc_pricing_card_btn_hover_text', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_card_btn_hover_text', array(
        'label'   => __( 'Warna Hover Teks Tombol', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    // Warna Border Card
    $wp_customize->add_setting( 'cc_pricing_card_border_color', array(
        'default'           => '#e2e8f0',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_card_border_color', array(
        'label'   => __( 'Warna Border Card', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    // Warna Teks Catatan / Keterangan Card
    $wp_customize->add_setting( 'cc_pricing_card_note_color', array(
        'default'           => '#64748b',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_card_note_color', array(
        'label'   => __( 'Warna Teks Keterangan / Catatan', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    // Warna Teks List Fasilitas
    $wp_customize->add_setting( 'cc_pricing_card_feat_text_color', array(
        'default'           => '#334155',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_card_feat_text_color', array(
        'label'   => __( 'Warna Teks List Fasilitas', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    // Warna Ikon Centang Fasilitas
    $wp_customize->add_setting( 'cc_pricing_card_check_icon_color', array(
        'default'           => '#10b981',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_pricing_card_check_icon_color', array(
        'label'   => __( 'Warna Ikon Centang Fasilitas', 'crediblecompany' ),
        'section' => 'cc_pricing_section',
    ) ) );

    // 8a. [Desktop] Spasi Padding Atas/Bawah Section (Tinggi Section)
    $wp_customize->add_setting( 'cc_pricing_padding_desktop', array(
        'default'           => 64,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_pricing_padding_desktop', array(
        'label'       => __( 'Desktop: Tinggi Spasi Section (Pixel)', 'crediblecompany' ),
        'description' => __( 'Mengatur padding vertikal atas dan bawah seksi Paket Jasa di layar desktop.', 'crediblecompany' ),
        'section'     => 'cc_pricing_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 20, 'max' => 150, 'step' => 2 ),
    ) );

    // 8b. [Mobile] Spasi Padding Atas/Bawah Section (Tinggi Section)
    $wp_customize->add_setting( 'cc_pricing_padding_mobile', array(
        'default'           => 40,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_pricing_padding_mobile', array(
        'label'       => __( 'Mobile: Tinggi Spasi Section (Pixel)', 'crediblecompany' ),
        'description' => __( 'Mengatur padding vertikal atas dan bawah seksi Paket Jasa di layar mobile.', 'crediblecompany' ),
        'section'     => 'cc_pricing_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 10, 'max' => 120, 'step' => 2 ),
    ) );

} );

/**
 * Suntikkan Variabel CSS Dinamis untuk Pricing Section ke Header.
 */
add_action( 'wp_head', function() {
    $margin_bottom_desktop = cc_get( 'pricing_header_margin_bottom_desktop', 48 );
    $margin_bottom_mobile  = cc_get( 'pricing_header_margin_bottom_mobile', 24 );
    $bg_color              = cc_get( 'pricing_bg_color', '#ffffff' );
    $text_color            = cc_get( 'pricing_text_color', '#0f172a' );
    $card_bg               = cc_get( 'pricing_card_bg_color', '#ffffff' );

    $card_hdr_bg   = cc_get( 'pricing_card_header_bg', '#c01314' );
    $card_hdr_txt  = cc_get( 'pricing_card_header_text', '#ffffff' );
    $card_body_bg  = cc_get( 'pricing_card_body_bg', '#f1f5f9' );
    $card_price    = cc_get( 'pricing_card_price_color', '#c01314' );
    $card_spec     = cc_get( 'pricing_card_spec_color', '#0f172a' );
    $card_feat     = cc_get( 'pricing_card_feat_title_color', '#c01314' );
    $card_bdg_bg   = cc_get( 'pricing_card_badge_bg', '#ffffff' );
    $card_bdg_txt  = cc_get( 'pricing_card_badge_text', '#c01314' );

    $card_btn_bg   = cc_get( 'pricing_card_btn_bg', '#c01314' );
    $card_btn_txt  = cc_get( 'pricing_card_btn_text', '#ffffff' );
    $card_btn_hbg  = cc_get( 'pricing_card_btn_hover_bg', '#a00f10' );
    $card_btn_htxt = cc_get( 'pricing_card_btn_hover_text', '#ffffff' );

    $card_border   = cc_get( 'pricing_card_border_color', '#e2e8f0' );
    $card_note     = cc_get( 'pricing_card_note_color', '#64748b' );
    $card_feat_txt = cc_get( 'pricing_card_feat_text_color', '#334155' );
    $card_check    = cc_get( 'pricing_card_check_icon_color', '#10b981' );

    $pad_desktop   = cc_get( 'pricing_padding_desktop', 64 );
    $pad_mobile    = cc_get( 'pricing_padding_mobile', 40 );

    $card_gap_desktop = cc_get( 'pricing_card_gap_desktop', 24 );
    $card_gap_mobile  = cc_get( 'pricing_card_gap_mobile', 16 );
    ?>
    <style type="text/css" id="cc-pricing-dynamic-css">
        :root {
            --cc-pricing-header-margin-bottom-desktop: <?php echo esc_attr( $margin_bottom_desktop ) . 'px'; ?>;
            --cc-pricing-header-margin-bottom-mobile: <?php echo esc_attr( $margin_bottom_mobile ) . 'px'; ?>;
            --cc-pricing-bg-color: <?php echo esc_attr( $bg_color ); ?>;
            --cc-pricing-text-color: <?php echo esc_attr( $text_color ); ?>;
            --cc-pricing-card-bg-color: <?php echo esc_attr( $card_bg ); ?>;

            --cc-pricing-card-header-bg: <?php echo esc_attr( $card_hdr_bg ); ?>;
            --cc-pricing-card-header-text: <?php echo esc_attr( $card_hdr_txt ); ?>;
            --cc-pricing-card-body-bg: <?php echo esc_attr( $card_body_bg ); ?>;
            --cc-pricing-card-price-color: <?php echo esc_attr( $card_price ); ?>;
            --cc-pricing-card-spec-color: <?php echo esc_attr( $card_spec ); ?>;
            --cc-pricing-card-feat-title-color: <?php echo esc_attr( $card_feat ); ?>;
            --cc-pricing-card-badge-bg: <?php echo esc_attr( $card_bdg_bg ); ?>;
            --cc-pricing-card-badge-text: <?php echo esc_attr( $card_bdg_txt ); ?>;

            --cc-pricing-card-btn-bg: <?php echo esc_attr( $card_btn_bg ); ?>;
            --cc-pricing-card-btn-text: <?php echo esc_attr( $card_btn_txt ); ?>;
            --cc-pricing-card-btn-hover-bg: <?php echo esc_attr( $card_btn_hbg ); ?>;
            --cc-pricing-card-btn-hover-text: <?php echo esc_attr( $card_btn_htxt ); ?>;

            --cc-pricing-card-border-color: <?php echo esc_attr( $card_border ); ?>;
            --cc-pricing-card-note-color: <?php echo esc_attr( $card_note ); ?>;
            --cc-pricing-card-feat-text-color: <?php echo esc_attr( $card_feat_txt ); ?>;
            --cc-pricing-card-check-icon-color: <?php echo esc_attr( $card_check ); ?>;

            --cc-pricing-padding-desktop: <?php echo esc_attr( $pad_desktop ) . 'px'; ?>;
            --cc-pricing-padding-mobile: <?php echo esc_attr( $pad_mobile ) . 'px'; ?>;

            --cc-pricing-card-gap-desktop: <?php echo esc_attr( $card_gap_desktop ) . 'px'; ?>;
            --cc-pricing-card-gap-mobile: <?php echo esc_attr( $card_gap_mobile ) . 'px'; ?>;
        }
    </style>
    <?php
}, 100 );


