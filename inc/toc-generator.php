<?php
/**
 * Table of Contents Generator
 * 
 * Secara otomatis menyisipkan ID ke elemen H2/H3 pada fungsi the_content()
 * dan menghasilkan struktur HTML daftar isi.
 *
 * @package CredibleCompany
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Filter the_content untuk menyisipkan ID ke setiap H2 dan H3
 */
function cc_add_ids_to_headings( $content ) {
    // Hanya proses pada single post
    if ( ! is_single() || ! in_the_loop() || ! is_main_query() ) {
        return $content;
    }

    // Pola regex untuk mencari H2 dan H3 (termasuk multi-baris)
    $pattern  = '/<h([2-3])(.*?)>(.*?)<\/h[2-3]>/is';
    $used_ids = array();

    $content = preg_replace_callback( $pattern, function( $matches ) use ( &$used_ids ) {
        $level      = $matches[1];
        $attributes = $matches[2];
        $text       = $matches[3];

        // Jika heading sudah memiliki ID, catat dan biarkan saja
        if ( preg_match( '/\bid\s*=\s*[\'"]([^\'"]+)[\'"]/i', $attributes, $id_match ) ) {
            $used_ids[ $id_match[1] ] = true;
            return $matches[0];
        }

        // Buat slug ID unik dari teks heading
        $base_slug = sanitize_title_with_dashes( wp_strip_all_tags( $text ) );
        if ( empty( $base_slug ) ) {
            $base_slug = 'heading-' . uniqid();
        }

        $slug    = $base_slug;
        $counter = 2;
        while ( isset( $used_ids[ $slug ] ) ) {
            $slug = $base_slug . '-' . $counter;
            $counter++;
        }
        $used_ids[ $slug ] = true;

        return sprintf( '<h%1$s%2$s id="%3$s">%4$s</h%1$s>', $level, $attributes, esc_attr( $slug ), $text );

    }, $content );

    return $content;
}
add_filter( 'the_content', 'cc_add_ids_to_headings' );

/**
 * Fungsi pembantu untuk membuat dan me-*return* HTML struktur Daftar Isi (TOC)
 */
function cc_generate_toc_html( $content ) {
    $pattern = '/<h([2-3])\b[^>]*\bid=[\'"]([^\'"]+)[\'"][^>]*>(.*?)<\/h[2-3]>/is';
    preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER );

    if ( empty( $matches ) ) {
        return ''; // Tidak ada heading H2/H3 yang ditemukan
    }

    $toc_html  = '<nav class="cc-post-toc-navigation">';
    $toc_html .= '<ul class="toc-list">';

    $in_sublist   = false;
    $has_top_item = false;

    foreach ( $matches as $match ) {
        $level = intval( $match[1] );
        $id    = $match[2];
        $text  = trim( wp_strip_all_tags( $match[3] ) );

        $item_html = sprintf(
            '<li class="toc-item toc-level-%d"><a href="#%s" class="toc-link">%s</a>',
            $level,
            esc_attr( $id ),
            esc_html( $text )
        );

        if ( 2 === $level ) {
            if ( $in_sublist ) {
                $toc_html  .= '</li></ul>';
                $in_sublist = false;
            }
            if ( $has_top_item ) {
                $toc_html .= '</li>';
            }
            $toc_html    .= $item_html;
            $has_top_item = true;
        } else {
            // Level 3: jika belum ada parent H2 sebelumnya, render sejajar di top-level agar HTML valid
            if ( ! $has_top_item ) {
                if ( $in_sublist ) {
                    $toc_html .= '</li>';
                }
                $toc_html    .= $item_html . '</li>';
            } elseif ( ! $in_sublist ) {
                $toc_html  .= '<ul class="toc-sublist">' . $item_html;
                $in_sublist = true;
            } else {
                $toc_html .= '</li>' . $item_html;
            }
        }
    }

    if ( $in_sublist ) {
        $toc_html .= '</li></ul>';
    }
    if ( $has_top_item ) {
        $toc_html .= '</li>';
    }

    $toc_html .= '</ul>';
    $toc_html .= '</nav>';

    return $toc_html;
}
