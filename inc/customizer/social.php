<?php
/**
 * Customizer: Social Media Section
 *
 * @package CredibleCompany
 */

add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section( 'cc_social_section', array(
        'title'    => __( 'Sosial Media', 'crediblecompany' ),
        'panel'    => 'cc_global_panel',
        'priority' => 30,
    ) );

    $socials = array( 'facebook', 'twitter', 'instagram', 'youtube', 'tiktok' );
    foreach ( $socials as $social ) {
        $wp_customize->add_setting( "cc_social_{$social}", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        $wp_customize->add_control( "cc_social_{$social}", array(
            'label'   => sprintf( __( 'URL %s', 'crediblecompany' ), ucfirst( $social ) ),
            'section' => 'cc_social_section',
            'type'    => 'url',
        ) );
    }

    /* --- Nomor WhatsApp untuk Floating Chat (dikelompokkan ke WhatsApp & Tim Marketing) --- */
    $wp_customize->add_setting( 'cc_whatsapp_number', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_whatsapp_number', array(
        'label'       => __( 'Nomor WhatsApp Utama', 'crediblecompany' ),
        'description' => __( 'Format internasional tanpa + (cth: 6281234567890). Kosongkan untuk menonaktifkan floating chat.', 'crediblecompany' ),
        'section'     => 'cc_marketing_section',
        'type'        => 'text',
        'priority'    => 10,
    ) );

    /* --- Pesan Default WhatsApp --- */
    $wp_customize->add_setting( 'cc_whatsapp_message', array(
        'default'           => function_exists( 'cc_get_default_wa_message' ) ? cc_get_default_wa_message() : 'Halo {Nama Marketing}, saya tertarik dengan layanan Anda.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'cc_whatsapp_message', array(
        'label'    => __( 'Pesan Default WhatsApp', 'crediblecompany' ),
        'section'  => 'cc_marketing_section',
        'type'     => 'textarea',
        'priority' => 15,
    ) );

} );
