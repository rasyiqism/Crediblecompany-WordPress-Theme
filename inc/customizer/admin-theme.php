<?php
/**
 * Customizer: Admin Theme Section
 * Pengaturan kustomisasi halaman login dan dashboard admin (WP Admin) secara otomatis.
 *
 * @package CredibleCompany
 */

// Mencegah akses langsung
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'customize_register', function( $wp_customize ) {

    // 1. Tambahkan Section Keamanan & Tampilan Admin di dalam Panel Global
    $wp_customize->add_section( 'cc_admin_theme_section', array(
        'title'       => __( 'Keamanan & Tampilan Admin', 'crediblecompany' ),
        'description' => __( 'Atur keamanan URL login dan kustomisasi halaman login serta dashboard admin (WP Admin).', 'crediblecompany' ),
        'panel'       => 'cc_global_panel',
        'priority'    => 70,
    ) );

    // 2. Setting: Aktifkan Kustomisasi Tema Admin & Login
    $wp_customize->add_setting( 'cc_enable_admin_theme', array(
        'default'           => false,
        'sanitize_callback' => 'cc_sanitize_checkbox',
    ) );

    $wp_customize->add_control( 'cc_enable_admin_theme', array(
        'label'       => __( 'Aktifkan Tema Admin & Login Kustom', 'crediblecompany' ),
        'description' => __( 'Jika diaktifkan, halaman login dan dashboard admin akan disesuaikan secara otomatis menggunakan skema warna tema utama (warna header/footer) dan logo situs Anda.', 'crediblecompany' ),
        'section'     => 'cc_admin_theme_section',
        'type'        => 'checkbox',
    ) );

    // 2b. Setting: Aktifkan URL Login Kustom (/masuk)
    $wp_customize->add_setting( 'cc_enable_custom_login', array(
        'default'           => false,
        'sanitize_callback' => 'cc_sanitize_checkbox',
    ) );

    $wp_customize->add_control( 'cc_enable_custom_login', array(
        'label'       => __( 'Aktifkan URL Login Kustom (/masuk)', 'crediblecompany' ),
        'description' => __( 'Jika diaktifkan, halaman login WordPress hanya dapat diakses melalui URL /masuk. Akses langsung ke wp-login.php tanpa token keamanan akan diblokir.', 'crediblecompany' ),
        'section'     => 'cc_admin_theme_section',
        'type'        => 'checkbox',
    ) );

    // 2c. Setting & Control: Warna Utama Admin (Aksen/Tombol)
    $wp_customize->add_setting( 'cc_admin_primary_color', array(
        'default'           => '#c01314',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_admin_primary_color', array(
        'label'       => __( 'Warna Utama Admin (Aksen/Tombol)', 'crediblecompany' ),
        'description' => __( 'Warna untuk tombol utama, menu aktif, dan aksen di dashboard admin.', 'crediblecompany' ),
        'section'     => 'cc_admin_theme_section',
    ) ) );

    // 2d. Setting & Control: Warna Latar Sidebar & Bar Admin
    $wp_customize->add_setting( 'cc_admin_dark_color', array(
        'default'           => '#0b1c3f',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_admin_dark_color', array(
        'label'       => __( 'Warna Latar Sidebar & Bar Admin', 'crediblecompany' ),
        'description' => __( 'Warna dasar untuk sidebar menu kiri dan top bar admin.', 'crediblecompany' ),
        'section'     => 'cc_admin_theme_section',
    ) ) );


    // 3. Setting: Aktifkan Fitur Komentar Situs

    $wp_customize->add_setting( 'cc_enable_comments', array(
        'default'           => true,
        'sanitize_callback' => 'cc_sanitize_checkbox',
    ) );

    $wp_customize->add_control( 'cc_enable_comments', array(
        'label'       => __( 'Aktifkan Fitur Komentar Global', 'crediblecompany' ),
        'description' => __( 'Jika dinonaktifkan, seluruh fitur komentar di website (sidebar menu, kolom daftar pos, dan form komentar) akan disembunyikan secara otomatis.', 'crediblecompany' ),
        'section'     => 'cc_blog_archive_section',
        'type'        => 'checkbox',
    ) );

    // 4. Setting & Control: WhatsApp KBM Support
    $wp_customize->add_setting( 'cc_kbm_support_wa', array(
        'default'           => '6281357517526',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'cc_kbm_support_wa', array(
        'label'       => __( 'WhatsApp KBM Support', 'crediblecompany' ),
        'description' => __( 'Format angka saja tanpa spasi/karakter spesial (contoh: 6281357517526)', 'crediblecompany' ),
        'section'     => 'cc_admin_theme_section',
        'type'        => 'text',
    ) );

        // 5. Setting & Control: Website KBM Support
    $wp_customize->add_setting( 'cc_kbm_support_web', array(
        'default'           => 'https://penerbitkbm.com/',
        'sanitize_callback' => 'esc_url_raw',
    ) );

    $wp_customize->add_control( 'cc_kbm_support_web', array(
        'label'       => __( 'Website KBM Support', 'crediblecompany' ),
        'description' => __( 'Masukkan URL website resmi KBM Support', 'crediblecompany' ),
        'section'     => 'cc_admin_theme_section',
        'type'        => 'url',
    ) );

    // 5c. Setting & Control: Teks Link KBM Support
    $wp_customize->add_setting( 'cc_kbm_support_web_text', array(
        'default'           => 'penerbitkbm.com',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'cc_kbm_support_web_text', array(
        'label'       => __( 'Teks Link KBM Support', 'crediblecompany' ),
        'description' => __( 'Teks yang akan tampil sebagai hyperlink website (contoh: penerbitkbm.com)', 'crediblecompany' ),
        'section'     => 'cc_admin_theme_section',
        'type'        => 'text',
    ) );

    // 5b. Setting & Control: Deskripsi KBM Support
    $wp_customize->add_setting( 'cc_kbm_support_desc', array(
        'default'           => 'Dapatkan layanan bantuan penuh untuk penerbitan buku Anda mulai dari proses penyuntingan naskah, desain cover, pengurusan ISBN, pencetakan, hingga strategi pemasaran dan distribusi buku secara luas. Tim KBM Support kami siap mendampingi perjalanan kepenulisan Anda melalui {link}.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );

    $wp_customize->add_control( 'cc_kbm_support_desc', array(
        'label'       => __( 'Deskripsi KBM Support', 'crediblecompany' ),
        'description' => __( 'Tuliskan deskripsi lengkap. Gunakan penanda {link} di dalam teks untuk meletakkan hyperlink website.', 'crediblecompany' ),
        'section'     => 'cc_admin_theme_section',
        'type'        => 'textarea',
    ) );

    // 6. Setting & Control: WhatsApp Developer Support
    $wp_customize->add_setting( 'cc_developer_support_wa', array(
        'default'           => '6285183131249',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'cc_developer_support_wa', array(
        'label'       => __( 'WhatsApp Developer Support', 'crediblecompany' ),
        'description' => __( 'Format angka saja tanpa spasi/karakter spesial (contoh: 6285183131249)', 'crediblecompany' ),
        'section'     => 'cc_admin_theme_section',
        'type'        => 'text',
    ) );

    // 7. Setting & Control: Website Developer Support
    $wp_customize->add_setting( 'cc_developer_support_web', array(
        'default'           => 'https://crediblemark.com/',
        'sanitize_callback' => 'esc_url_raw',
    ) );

    $wp_customize->add_control( 'cc_developer_support_web', array(
        'label'       => __( 'Website Developer Support', 'crediblecompany' ),
        'description' => __( 'Masukkan URL website resmi Developer Support', 'crediblecompany' ),
        'section'     => 'cc_admin_theme_section',
        'type'        => 'url',
    ) );

    // 7c. Setting & Control: Teks Link Developer Support
    $wp_customize->add_setting( 'cc_developer_support_web_text', array(
        'default'           => 'crediblemark.com',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'cc_developer_support_web_text', array(
        'label'       => __( 'Teks Link Developer Support', 'crediblecompany' ),
        'description' => __( 'Teks yang akan tampil sebagai hyperlink website (contoh: crediblemark.com)', 'crediblecompany' ),
        'section'     => 'cc_admin_theme_section',
        'type'        => 'text',
    ) );

    // 7b. Setting & Control: Deskripsi Developer Support
    $wp_customize->add_setting( 'cc_developer_support_desc', array(
        'default'           => 'Butuh bantuan teknis? Tim {link} (mitra resmi KBM) siap melayani tanya jawab bebas mengenai kendala teknis operasional pada website Anda secara gratis. Mohon diperhatikan bahwa layanan pemeliharaan berkala (IT Maintenance), pembaruan sistem, atau pengerjaan fitur kustom merupakan layanan terpisah di luar dukungan gratis ini.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );


    $wp_customize->add_control( 'cc_developer_support_desc', array(
        'label'       => __( 'Deskripsi Developer Support', 'crediblecompany' ),
        'description' => __( 'Tuliskan deskripsi lengkap. Gunakan penanda {link} di dalam teks untuk meletakkan hyperlink website.', 'crediblecompany' ),
        'section'     => 'cc_admin_theme_section',
        'type'        => 'textarea',
    ) );

} );
