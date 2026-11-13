<?php
/**
 * Modul Optimasi Performa & Speed Booster (No-Plugin)
 * Menangani Caching, Bloat Removal, Asset Optimization, dan DB Cleanup.
 *
 * @package CredibleCompany
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* --------------------------------------------------------------------------
 * 1. Bloat Removal (Membersihkan Fitur Tak Terpakai)
 * ---------------------------------------------------------------------- */

// Mematikan Emojis
add_action( 'init', 'cc_disable_emojis' );
function cc_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    add_filter( 'tiny_mce_plugins', 'cc_disable_emojis_tinymce' );
    add_filter( 'wp_resource_hints', 'cc_disable_emojis_remove_dns_prefetch', 10, 2 );
}
function cc_disable_emojis_tinymce( $plugins ) {
    if ( is_array( $plugins ) ) {
        return array_diff( $plugins, array( 'wpemoji' ) );
    } else {
        return array();
    }
}
function cc_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
    if ( 'dns-prefetch' === $relation_type ) {
        $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/12.0.0-1/svg/' );
        $urls = array_diff( $urls, array( $emoji_svg_url ) );
    }
    return $urls;
}

// Mematikan oEmbeds (Mencegah request eksternal tambahan)
add_action( 'init', 'cc_disable_oembeds', 9999 );
function cc_disable_oembeds() {
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
}

// Mematikan XML-RPC (Keamanan & Performa)
add_filter( 'xmlrpc_enabled', '__return_false' );

// Mematikan jQuery Migrate (Mengurangi 1 request JS)
add_action( 'wp_default_scripts', 'cc_dequeue_jquery_migrate' );
function cc_dequeue_jquery_migrate( $scripts ) {
    if ( ! is_admin() && ! empty( $scripts->registered['jquery'] ) ) {
        $jquery_dependencies = $scripts->registered['jquery']->deps;
        $scripts->registered['jquery']->deps = array_diff( $jquery_dependencies, array( 'jquery-migrate' ) );
    }
}

// Mematikan Dashicons di Frontend untuk User Non-Login
add_action( 'wp_enqueue_scripts', 'cc_dequeue_dashicons', 100 );
function cc_dequeue_dashicons() {
    if ( ! is_user_logged_in() ) {
        wp_dequeue_style( 'dashicons' );
    }
}

/* --------------------------------------------------------------------------
 * 2. System Control (Limit Revisions & Heartbeat)
 * ---------------------------------------------------------------------- */

// Batasi Revisions (Maksimal 5 untuk hemat database)
if ( ! defined( 'WP_POST_REVISIONS' ) ) {
    define( 'WP_POST_REVISIONS', 5 );
}

// Kontrol Heartbeat API di frontend saja (tetap aktif di wp-admin untuk autosave & post locking)
add_action( 'init', 'cc_stop_heartbeat', 1 );
function cc_stop_heartbeat() {
    if ( ! is_admin() ) {
        wp_deregister_script( 'heartbeat' );
    }
}

/* --------------------------------------------------------------------------
 * 3. Asset Optimization (HTML Minify, Defer Scripts)
 * ---------------------------------------------------------------------- */

// Minifikasi HTML (Menghilangkan Whitespace tak perlu tanpa merusak script/style/pre/textarea)
add_action( 'get_header', 'cc_start_html_minify' );
function cc_start_html_minify() {
    if ( ! is_admin() ) {
        ob_start( 'cc_minify_html_output' );
    }
}
function cc_minify_html_output( $buffer ) {
    if ( is_admin() || empty( $buffer ) ) {
        return $buffer;
    }

    // Lindungi blok <pre>, <textarea>, <script>, dan <style> agar komentar // atau whitespace-nya tidak rusak
    $placeholders = array();
    $buffer = preg_replace_callback(
        '/<(pre|textarea|script|style)\b[^>]*>.*?<\/\1>/is',
        function ( $matches ) use ( &$placeholders ) {
            $key = '___CC_MINIFY_BLOCK_' . count( $placeholders ) . '___';
            $placeholders[ $key ] = $matches[0];
            return $key;
        },
        $buffer
    );

    $search = array(
        '/\>[^\S ]+/s',          // Menghilangkan whitespace setelah tag
        '/[^\S ]+\</s',          // Menghilangkan whitespace sebelum tag
        '/(\s)+/s',              // Menggabungkan spasi/newline berlebih
        '/<!--(?!\[if|\s*Served from CC Cache).*?-->/s', // Hapus komentar HTML biasa
    );
    $replace = array( '>', '<', '\\1', '' );
    $buffer  = preg_replace( $search, $replace, $buffer );

    if ( ! empty( $placeholders ) ) {
        $buffer = strtr( $buffer, $placeholders );
    }

    return $buffer;
}

// Menambahkan atribut Defer ke Script (Non-Kritikal)
add_filter( 'script_loader_tag', 'cc_defer_scripts', 10, 3 );
function cc_defer_scripts( $tag, $handle, $src ) {
    // Abaikan di admin, customizer preview, atau untuk jQuery inti agar tidak merusak inline script
    if ( is_admin() || is_customize_preview() || 'jquery' === $handle || 'jquery-core' === $handle ) {
        return $tag;
    }
    return str_replace( ' src', ' defer src', $tag );
}

/* --------------------------------------------------------------------------
 * 4. Media & Font Optimization (Lazy Load iFrame & Fonts)
 * ---------------------------------------------------------------------- */

// Lazy Load iFrames & YouTube Videos
add_filter( 'the_content', 'cc_lazy_load_iframes' );
function cc_lazy_load_iframes( $content ) {
    if ( ! is_admin() ) {
        $content = str_replace( '<iframe', '<iframe loading="lazy"', $content );
    }
    return $content;
}

// Optimasi Font (Swap Display)
add_filter( 'style_loader_tag', 'cc_font_display_swap', 10, 2 );
function cc_font_display_swap( $tag, $handle ) {
    if ( strpos( $tag, 'fonts.googleapis.com' ) !== false && strpos( $tag, 'display=swap' ) === false ) {
        // Tentukan separator (? atau &) berdasarkan keberadaan query string
        $sep = ( strpos( $tag, '?' ) !== false ) ? '&' : '?';
        // Injeksi parameter hanya ke dalam atribut href
        $tag = preg_replace( '/href=(["\'])(.*?)\1/', 'href=$1$2' . $sep . 'display=swap$1', $tag );
    }
    return $tag;
}

// Preload Logo & Featured Image Beranda
add_action( 'wp_head', 'cc_preload_critical_assets', 1 );
function cc_preload_critical_assets() {
    // Preload Logo
    if ( has_custom_logo() ) {
        $logo_id = get_theme_mod( 'custom_logo' );
        $logo_url = wp_get_attachment_image_src( $logo_id, 'full' );
        if ( $logo_url ) {
            echo '<link rel="preload" as="image" href="' . esc_url( $logo_url[0] ) . '">' . "\n";
        }
    }

    // Preload Featured Image di Single Post
    if ( is_singular() && has_post_thumbnail() ) {
        $img_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
        if ( $img_url ) {
            echo '<link rel="preload" as="image" href="' . esc_url( $img_url ) . '">' . "\n";
        }
    }
}

/* --------------------------------------------------------------------------
 * 5. Database & System Housekeeping (Auto Cleanup)
 * ---------------------------------------------------------------------- */

// Jadwal Pembersihan Mingguan via WP Cron (hanya dicek di admin/aktivasi tema)
add_action( 'admin_init', 'cc_schedule_weekly_db_cleanup' );
add_action( 'after_switch_theme', 'cc_schedule_weekly_db_cleanup' );
function cc_schedule_weekly_db_cleanup() {
    if ( ! wp_next_scheduled( 'cc_weekly_db_cleanup' ) ) {
        wp_schedule_event( time(), 'weekly', 'cc_weekly_db_cleanup' );
    }
}

add_action( 'cc_weekly_db_cleanup', 'cc_perform_db_cleanup' );
function cc_perform_db_cleanup() {
    global $wpdb;
    $now = time();

    // 1. Bersihkan HANYA Transients yang benar-benar sudah kadaluarsa
    $wpdb->query(
        $wpdb->prepare(
            "DELETE a, b FROM {$wpdb->options} a
             INNER JOIN {$wpdb->options} b
                ON b.option_name = REPLACE(a.option_name, '_transient_timeout_', '_transient_')
             WHERE a.option_name LIKE %s
               AND CAST(a.option_value AS UNSIGNED) < %d",
            $wpdb->esc_like( '_transient_timeout_' ) . '%',
            $now
        )
    );

    // 2. Bersihkan Auto-Drafts lama (> 7 hari)
    $wpdb->query( "DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft' AND post_date < DATE_SUB(NOW(), INTERVAL 7 DAY)" );

    // 3. Optimasi Tabel (Hanya untuk tabel inti)
    $wpdb->query( "OPTIMIZE TABLE $wpdb->posts, $wpdb->postmeta, $wpdb->options, $wpdb->comments, $wpdb->commentmeta" );
}

/* --------------------------------------------------------------------------
 * 6. Simple Page Caching (PHP-based File Cache)
 * ---------------------------------------------------------------------- */

// Prioritas 20: jalankan SETELAH referral tracking & view counter (prioritas 10)
add_action( 'template_redirect', 'cc_auto_page_cache_start', 20 );
function cc_auto_page_cache_start() {
    $method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( $_SERVER['REQUEST_METHOD'] ) : 'GET';

    // Jangan cache untuk user login, admin, non-GET, AJAX, REST, preview, 404, feed, search, atau halaman submit testimoni
    if (
        'GET' !== $method ||
        is_user_logged_in() ||
        is_admin() ||
        ! empty( $_POST ) ||
        wp_doing_ajax() ||
        ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ||
        is_preview() ||
        is_customize_preview() ||
        is_404() ||
        is_feed() ||
        is_search() ||
        isset( $_GET['ref'] ) ||
        is_page_template( 'templates/page-submit-testimoni.php' ) ||
        is_page_template( 'page-submit-testimoni.php' )
    ) {
        return;
    }

    $cache_dir       = WP_CONTENT_DIR . '/cache/cc-cache/';
    $marketer_cookie = defined( 'CC_MARKETER_COOKIE' ) ? CC_MARKETER_COOKIE : 'cc_marketer_ref';
    $marketer_id     = isset( $_COOKIE[ $marketer_cookie ] ) ? intval( $_COOKIE[ $marketer_cookie ] ) : 0;
    $device_key      = wp_is_mobile() ? 'mob' : 'desk';

    // Normalisasi URI: hapus parameter tracking (utm_*, fbclid, gclid) agar cache tidak membengkak
    $raw_uri    = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '/';
    $parsed_uri = wp_parse_url( $raw_uri );
    $path       = isset( $parsed_uri['path'] ) ? $parsed_uri['path'] : '/';
    $query_str  = '';
    if ( ! empty( $parsed_uri['query'] ) ) {
        parse_str( $parsed_uri['query'], $q_params );
        foreach ( array_keys( $q_params ) as $k ) {
            if ( 0 === strpos( $k, 'utm_' ) || in_array( $k, array( 'fbclid', 'gclid', 'ref' ), true ) ) {
                unset( $q_params[ $k ] );
            }
        }
        if ( ! empty( $q_params ) ) {
            ksort( $q_params );
            $query_str = '?' . http_build_query( $q_params );
        }
    }

    // Cache key dikombinasikan dengan ID marketer aktif dan device type
    $cache_key  = md5( $path . $query_str . '_' . $marketer_id . '_' . $device_key );
    $cache_file = $cache_dir . $cache_key . '.html';

    // Buat folder jika belum ada
    if ( ! is_dir( $cache_dir ) ) {
        wp_mkdir_p( $cache_dir );
    }

    // Buat file index.php kosong untuk mencegah listing direktori jika belum ada
    $index_file = $cache_dir . 'index.php';
    if ( ! file_exists( $index_file ) ) {
        file_put_contents( $index_file, "<?php\n// Silence is golden.\n" );
    }

    // Jika file cache ada dan masih segar (< 24 jam), sajikan langsung
    if ( file_exists( $cache_file ) && ( time() - filemtime( $cache_file ) < 86400 ) ) {
        readfile( $cache_file );
        echo "\n<!-- Served from CC Cache / " . gmdate( 'Y-m-d H:i:s', filemtime( $cache_file ) ) . " UTC -->";
        exit;
    }

    // Mulai buffer untuk menyimpan hasil render hanya jika response 200 OK
    ob_start( function( $buffer ) use ( $cache_file ) {
        $status = http_response_code();
        if ( ( false === $status || 200 === $status ) && ! empty( $buffer ) && strlen( $buffer ) > 1000 ) {
            file_put_contents( $cache_file, $buffer, LOCK_EX );
        }
        return $buffer;
    });
}

/**
 * Bersihkan semua file cache halaman (.html) jika ada perubahan di Customizer atau konten baru.
 */
function cc_clear_all_page_cache() {
    $cache_dir = WP_CONTENT_DIR . '/cache/cc-cache/';
    if ( is_dir( $cache_dir ) ) {
        $files = glob( $cache_dir . '*.html' );
        if ( is_array( $files ) ) {
            foreach ( $files as $file ) {
                if ( is_file( $file ) ) {
                    unlink( $file );
                }
            }
        }
    }
}
add_action( 'customize_save_after', 'cc_clear_all_page_cache' );
add_action( 'save_post', 'cc_clear_all_page_cache' );
add_action( 'activated_plugin', 'cc_clear_all_page_cache' );
add_action( 'deactivated_plugin', 'cc_clear_all_page_cache' );
add_action( 'switch_theme', 'cc_clear_all_page_cache' );

/* --------------------------------------------------------------------------
 * 7. Media Storage Optimization (Hemat Disk Space Tanpa Mematikan Responsive Sizes)
 *    Pertahankan thumbnail & medium untuk kartu/avatar agar LCP & srcset tetap optimal,
 *    hanya nonaktifkan ukuran raksasa yang jarang dipakai (1536x1536, 2048x2048).
 * ---------------------------------------------------------------------- */

// 1. Menonaktifkan ukuran gambar ekstra besar bawaan WordPress (thumbnail & medium tetap aktif)
add_filter( 'intermediate_image_sizes_advanced', 'cc_disable_image_sizes' );
function cc_disable_image_sizes( $sizes ) {
    unset( $sizes['1536x1536'] );
    unset( $sizes['2048x2048'] );
    return $sizes;
}

// 2. Menonaktifkan threshold penskalaan gambar besar agar tidak ada versi -scaled
add_filter( 'big_image_size_threshold', '__return_false' );

// 3. Cabut seluruh jadwal event cron penghapus attachment (mencegah penghapusan otomatis gambar WooCommerce/Media)
add_action( 'init', 'cc_unschedule_unsafe_attachment_cleanup' );
function cc_unschedule_unsafe_attachment_cleanup() {
    if ( wp_next_scheduled( 'cc_cleanup_unused_attachments_cron' ) ) {
        wp_clear_scheduled_hook( 'cc_cleanup_unused_attachments_cron' );
    }
}





