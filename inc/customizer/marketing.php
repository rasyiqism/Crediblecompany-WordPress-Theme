<?php
/**
 * Customizer: Marketing Options
 *
 * @package CredibleCompany
 */

add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section( 'cc_marketing_section', array(
        'title'    => __( 'WhatsApp & Tim Marketing', 'crediblecompany' ),
        'panel'    => 'cc_global_panel',
        'priority' => 40,
    ) );

    // Aktifkan / Sembunyikan Fitur Marketing
    $wp_customize->add_setting( 'cc_marketing_enable', array(
        'default'           => true,
        'sanitize_callback' => 'cc_sanitize_checkbox',
    ) );

    $wp_customize->add_control( 'cc_marketing_enable', array(
        'label'       => __( 'Aktifkan Fitur Marketing (Referral)', 'crediblecompany' ),
        'description' => __( 'Aktifkan untuk mengelola tim marketing dan menggunakan sistem pelacakan tautan referral WhatsApp (?ref=nama).', 'crediblecompany' ),
        'section'     => 'cc_marketing_section',
        'type'        => 'checkbox',
        'priority'    => 1,
    ) );

    // Fallback Nama Marketing
    $wp_customize->add_setting( 'cc_marketing_fallback_name', array(
        'default'           => 'Admin',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'cc_marketing_fallback_name', array(
        'label'       => __( 'Fallback Nama Marketing', 'crediblecompany' ),
        'description' => __( 'Nama yang akan muncul jika pengunjung tidak melalui link referral marketing (misal: Admin, Customer Service, dll).', 'crediblecompany' ),
        'section'     => 'cc_marketing_section',
        'type'        => 'text',
        'priority'    => 5,
    ) );

} );
