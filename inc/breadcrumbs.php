<?php
/**
 * Helper Fungsi Breadcrumbs (Aman untuk On-Page SEO / Schema)
 *
 * @package CredibleCompany
 */

function cc_breadcrumbs() {
    if ( is_front_page() || is_home() ) {
        return;
    }

    echo '<nav aria-label="Breadcrumb" class="cc-breadcrumbs">';
    echo '<ol class="cc-breadcrumbs__list">';

    // Item 1: Home
    echo '<li class="cc-breadcrumbs__item">';
    echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="cc-breadcrumbs__link">Beranda</a>';
    echo '<span class="cc-breadcrumbs__sep">/</span>';
    echo '</li>';

    if ( is_category() || is_single() ) {
        // Item 2: Categori
        $categories = get_the_category();
        if ( ! empty( $categories ) ) {
            echo '<li class="cc-breadcrumbs__item">';
            echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '" class="cc-breadcrumbs__link">' . esc_html( $categories[0]->name ) . '</a>';
            echo '<span class="cc-breadcrumbs__sep">/</span>';
            echo '</li>';
        }

        if ( is_single() ) {
            // Item 3: Current Post
            echo '<li aria-current="page" class="cc-breadcrumbs__item cc-breadcrumbs__current">';
            echo '<span class="cc-breadcrumbs__current-text">' . esc_html( get_the_title() ) . '</span>';
            echo '</li>';
        }
    } elseif ( is_page() ) {
        echo '<li aria-current="page" class="cc-breadcrumbs__item cc-breadcrumbs__current">';
        echo '<span>' . esc_html( get_the_title() ) . '</span>';
        echo '</li>';
    } 

    echo '</ol>';
    echo '</nav>';

    // --- Tambahan JSON-LD untuk Breadcrumb SEO ---
    $breadcrumb_schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => array(),
    );

    // 1. Beranda
    $breadcrumb_schema['itemListElement'][] = array(
        '@type'    => 'ListItem',
        'position' => 1,
        'name'     => 'Beranda',
        'item'     => home_url( '/' ),
    );

    $pos = 2;
    if ( is_category() || is_single() ) {
        $categories = get_the_category();
        if ( ! empty( $categories ) ) {
            $breadcrumb_schema['itemListElement'][] = array(
                '@type'    => 'ListItem',
                'position' => $pos++,
                'name'     => $categories[0]->name,
                'item'     => get_category_link( $categories[0]->term_id ),
            );
        }
        if ( is_single() ) {
            $breadcrumb_schema['itemListElement'][] = array(
                '@type'    => 'ListItem',
                'position' => $pos++,
                'name'     => get_the_title(),
                'item'     => get_permalink(),
            );
        }
    } elseif ( is_page() ) {
        $breadcrumb_schema['itemListElement'][] = array(
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    }

    if ( ! empty( $breadcrumb_schema['itemListElement'] ) ) {
        echo '<script type="application/ld+json">' . wp_json_encode( $breadcrumb_schema ) . '</script>' . "\n";
    }
}
