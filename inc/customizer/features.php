<?php
/**
 * Customizer: Features Section
 *
 * @package CredibleCompany
 */

add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section( 'cc_features_section', array(
        'title'    => __( '4. Keunggulan (Features)', 'crediblecompany' ),
        'panel'    => 'cc_homepage_panel',
        'priority' => 40,
    ) );

    // Judul Utama (Mengapa Memilih Kami?)
    $wp_customize->add_setting( 'cc_features_main_title', array(
        'default'           => __( 'Mengapa Memilih Kami?', 'crediblecompany' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_features_main_title', array(
        'label'   => __( 'Judul Utama Seksi', 'crediblecompany' ),
        'section' => 'cc_features_section',
        'type'    => 'text',
    ) );

    $feat_defaults = array(
        array( 'Profesional', 'Naskah anda akan ditangani langsung oleh tim ahli layout naskah, desain sampul dan admin marketing yang berkompeten. Bahkan anda bisa terhubung dengan Owner Penerbit buku KBM Indonesia.' ),
        array( 'Harga Murah', 'Anda dapat membuktikan dan melakukan riset ke tempat lain, untuk cetakan 25 eksemplar ke atas, maka biaya di Penerbit buku KBM Indonesia sangat terjangkau dan lebih murah.' ),
        array( 'Proses Cepat', 'Penerbit buku KBM Indonesia selalu berusaha menyesuaikan waktu anda. Dengan jumlah minimal cetak 100 eksemplar, anda bisa melakukan request waktu selesai cetak.' ),
        array( 'Free Ongkir', 'Penerbit buku KBM Indonesia akan memberikan Gratis ongkos kirim ke seluruh wilayah Indonesia, dari Aceh sampai Papua. Bahkan ada layanan kirim buku melalui ekspedisi Travel.' ),
        array( 'Proses Bergaransi', 'Penerbit buku KBM Indonesia akan memberikan GARANSI cetak ulang tanpa tambahan biaya cetak dan ongkos kirim apabila terjadi cacat 100% pada buku yang kami kirim.' ),
        array( 'Distribusi Luas', 'Buku yang kamu terbitkan di Penerbit buku KBM Indonesia akan dibantu jualkan ke berbagai marketplace nasional milik penerbit buku KBM Indonesia.' ),
    );

    $icon_defaults = array(
        1 => 'user',
        2 => 'dollar',
        3 => 'lightning',
        4 => 'mail',
        5 => 'shield',
        6 => 'globe',
    );

    $icon_choices = array(
        'user'      => __( 'User / Tim', 'crediblecompany' ),
        'dollar'    => __( 'Dollar / Finansial', 'crediblecompany' ),
        'lightning' => __( 'Lightning / Cepat', 'crediblecompany' ),
        'mail'      => __( 'Mail / Kontak', 'crediblecompany' ),
        'shield'    => __( 'Shield / Keamanan', 'crediblecompany' ),
        'globe'     => __( 'Globe / Internasional', 'crediblecompany' ),
        'award'     => __( 'Award / Kualitas', 'crediblecompany' ),
        'chart'     => __( 'Chart / Statistik', 'crediblecompany' ),
        'gear'      => __( 'Gear / Pengaturan', 'crediblecompany' ),
        'clock'     => __( 'Clock / Waktu', 'crediblecompany' ),
    );

    // Opsi: Jumlah Fitur yang Ditampilkan
    $wp_customize->add_setting( 'cc_features_count', array(
        'default'           => 3,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_features_count', array(
        'label'       => __( 'Jumlah Fitur yang Ditampilkan', 'crediblecompany' ),
        'section'     => 'cc_features_section',
        'type'        => 'select',
        'choices'     => array(
            1 => '1 Fitur',
            2 => '2 Fitur',
            3 => '3 Fitur',
            4 => '4 Fitur',
            5 => '5 Fitur',
            6 => '6 Fitur',
        ),
        'priority'    => 2,
    ) );

    for ( $i = 1; $i <= 6; $i++ ) {
        $idx = $i - 1;

        // Callback aktif: hanya muncul jika urutan fitur lebih kecil/sama dengan jumlah terpilih
        $active_callback = function() use ( $i ) {
            $count = intval( get_theme_mod( 'cc_features_count', 3 ) );
            return $count >= $i;
        };

        $wp_customize->add_setting( "cc_feat_title_{$i}", array(
            'default'           => $feat_defaults[ $idx ][0],
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "cc_feat_title_{$i}", array(
            'label'           => sprintf( __( 'Fitur %d — Judul', 'crediblecompany' ), $i ),
            'section'         => 'cc_features_section',
            'type'            => 'text',
            'active_callback' => $active_callback,
        ) );

        $wp_customize->add_setting( "cc_feat_icon_{$i}", array(
            'default'           => $icon_defaults[ $i ],
            'sanitize_callback' => 'sanitize_key',
        ) );
        $wp_customize->add_control( "cc_feat_icon_{$i}", array(
            'label'           => sprintf( __( 'Fitur %d — Ikon', 'crediblecompany' ), $i ),
            'section'         => 'cc_features_section',
            'type'            => 'select',
            'choices'         => $icon_choices,
            'active_callback' => $active_callback,
        ) );

        $wp_customize->add_setting( "cc_feat_desc_{$i}", array(
            'default'           => $feat_defaults[ $idx ][1],
            'sanitize_callback' => 'sanitize_textarea_field',
        ) );
        $wp_customize->add_control( "cc_feat_desc_{$i}", array(
            'label'           => sprintf( __( 'Fitur %d — Deskripsi', 'crediblecompany' ), $i ),
            'section'         => 'cc_features_section',
            'type'            => 'textarea',
            'active_callback' => $active_callback,
        ) );
    }

    // Pengaturan Warna Section Features
    $wp_customize->add_setting( 'cc_features_bg_color', array(
        'default'           => '#c01314',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_features_bg_color', array(
        'label'   => __( 'Warna Latar Belakang Section', 'crediblecompany' ),
        'section' => 'cc_features_section',
    ) ) );

    $wp_customize->add_setting( 'cc_features_title_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_features_title_color', array(
        'label'   => __( 'Warna Font Judul', 'crediblecompany' ),
        'section' => 'cc_features_section',
    ) ) );

    $wp_customize->add_setting( 'cc_features_desc_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_features_desc_color', array(
        'label'   => __( 'Warna Font Deskripsi', 'crediblecompany' ),
        'section' => 'cc_features_section',
    ) ) );

    // Kustomisasi Warna Detail Item Fitur (Card & Ikon)
    $wp_customize->add_setting( 'cc_features_item_bg_color', array(
        'default'           => 'transparent',
        'sanitize_callback' => 'sanitize_text_field', // menggunakan sanitize_text_field karena 'transparent' bukan hex
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_features_item_bg_color', array(
        'label'   => __( 'Warna Latar Belakang Item Fitur', 'crediblecompany' ),
        'section' => 'cc_features_section',
    ) ) );

    $wp_customize->add_setting( 'cc_features_icon_bg_color', array(
        'default'           => '#dc2626',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_features_icon_bg_color', array(
        'label'   => __( 'Warna Latar Belakang Ikon Fitur', 'crediblecompany' ),
        'section' => 'cc_features_section',
    ) ) );

    $wp_customize->add_setting( 'cc_features_icon_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_features_icon_color', array(
        'label'   => __( 'Warna Ikon Fitur', 'crediblecompany' ),
        'section' => 'cc_features_section',
    ) ) );

    // Kustomisasi Spasi & Geometri (Slider)
    $wp_customize->add_setting( 'cc_features_padding_desktop', array(
        'default'           => 64,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_features_padding_desktop', array(
        'label'       => __( 'Padding Vertikal Seksi (Desktop px)', 'crediblecompany' ),
        'section'     => 'cc_features_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 200, 'step' => 2 ),
    ) );

    $wp_customize->add_setting( 'cc_features_padding_mobile', array(
        'default'           => 40,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_features_padding_mobile', array(
        'label'       => __( 'Padding Vertikal Seksi (Mobile px)', 'crediblecompany' ),
        'section'     => 'cc_features_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 150, 'step' => 2 ),
    ) );

    $wp_customize->add_setting( 'cc_features_gap_desktop', array(
        'default'           => 32,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_features_gap_desktop', array(
        'label'       => __( 'Jarak Antar Item/Gap Grid (Desktop px)', 'crediblecompany' ),
        'section'     => 'cc_features_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 2 ),
    ) );

    $wp_customize->add_setting( 'cc_features_gap_mobile', array(
        'default'           => 20,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'cc_features_gap_mobile', array(
        'label'       => __( 'Jarak Antar Item/Gap Grid (Mobile px)', 'crediblecompany' ),
        'section'     => 'cc_features_section',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 80, 'step' => 2 ),
    ) );

} );

