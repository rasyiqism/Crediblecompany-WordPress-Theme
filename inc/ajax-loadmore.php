<?php
/**
 * AJAX Load More Handler
 * Menangani permintaan pemuatan postingan tambahan via AJAX.
 *
 * @package CredibleCompany
 */

add_action( 'wp_ajax_cc_load_more', 'cc_ajax_load_more_handler' );
add_action( 'wp_ajax_nopriv_cc_load_more', 'cc_ajax_load_more_handler' );

function cc_ajax_load_more_handler() {
    $paged              = isset( $_POST['page'] ) ? intval( $_POST['page'] ) + 1 : 1;
    $post_type_raw      = isset( $_POST['post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) : 'post';
    $allowed_post_types = array( 'post', 'testimoni' );
    $post_type          = in_array( $post_type_raw, $allowed_post_types, true ) ? $post_type_raw : 'post';
    $orderby            = isset( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : 'date';
    $exclude            = isset( $_POST['exclude'] ) ? intval( $_POST['exclude'] ) : 0;
    $cat                = isset( $_POST['cat'] ) ? intval( $_POST['cat'] ) : 0;
    $ppp_raw            = isset( $_POST['ppp'] ) ? intval( $_POST['ppp'] ) : 0;
    $search             = isset( $_POST['s'] ) ? sanitize_text_field( wp_unslash( $_POST['s'] ) ) : '';

    // Pastikan PPP (Posts Per Page) konsisten dengan halaman pemanggil
    $default_ppp = ( 'testimoni' === $post_type ) ? 9 : 10;
    $ppp         = ( $ppp_raw > 0 ) ? min( 20, $ppp_raw ) : $default_ppp;

    $args = array(
        'post_type'      => $post_type,
        'posts_per_page' => $ppp,
        'paged'          => $paged,
        'post_status'    => 'publish',
    );

    if ( ! empty( $search ) ) {
        $args['s'] = $search;
    }

    if ( $cat > 0 && 'post' === $post_type ) {
        $args['cat'] = $cat;
    }

    if ( $exclude ) {
        $args['post__not_in'] = array( $exclude );
    }

    if ( 'popular' === $orderby ) {
        $args['meta_key'] = 'cc_post_views';
        $args['orderby']  = 'meta_value_num';
        $args['order']    = 'DESC';
    } else {
        $args['orderby']  = 'date';
        $args['order']    = 'DESC';
    }

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            
            if ( 'testimoni' === $post_type ) {
                get_template_part( 'template-parts/card-testimoni', null, array( 'is_link' => true ) );
            } else {
                // Untuk blog di halaman index (home.php) menggunakan style 'app'
                get_template_part( 'template-parts/card-post', null, array( 'style' => 'app' ) );
            }
        }
    }

    wp_reset_postdata();
    wp_die();
}

/**
 * Filter pencarian testimoni agar juga memeriksa custom meta `_customer_name`
 */
add_filter( 'posts_clauses', 'cc_filter_testimoni_search_clauses', 20, 2 );
function cc_filter_testimoni_search_clauses( $clauses, $query ) {
    global $wpdb;

    if ( ! is_admin() && $query->is_search() ) {
        $post_type = $query->get( 'post_type' );
        $is_testimoni = ( 'testimoni' === $post_type || ( is_array( $post_type ) && in_array( 'testimoni', $post_type, true ) ) );

        if ( $is_testimoni && ! empty( $clauses['where'] ) ) {
            $search_term = $query->get( 's' );
            if ( ! empty( $search_term ) ) {
                $like = '%' . $wpdb->esc_like( $search_term ) . '%';
                $clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS cc_pm_cname ON ({$wpdb->posts}.ID = cc_pm_cname.post_id AND cc_pm_cname.meta_key = '_customer_name') ";
                $clauses['where'] = preg_replace(
                    "/\({$wpdb->posts}\.post_title\s+LIKE\s*'[^\']+'\)/i",
                    "({$wpdb->posts}.post_title LIKE '{$like}' OR cc_pm_cname.meta_value LIKE '{$like}')",
                    $clauses['where'],
                    1
                );
                $clauses['distinct'] = 'DISTINCT';
            }
        }
    }

    return $clauses;
}
