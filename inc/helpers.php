<?php
/**
 * Fungsi helper untuk tema Credible Company.
 * Wrapper untuk get_theme_mod() agar lebih ringkas.
 *
 * @package CredibleCompany
 */

/**
 * Ambil nilai customizer dengan fallback default.
 *
 * @param string $key   ID setting customizer (tanpa prefix 'cc_').
 * @param mixed  $default Nilai default jika belum diatur.
 * @return mixed
 */
/**
 * Konversi URL HTTP menjadi HTTPS secara dinamis jika koneksi aman (SSL) aktif.
 *
 * @param mixed $value Nilai string URL yang akan dikonversi.
 * @return mixed
 */
function cc_maybe_ssl_url( $value ) {
    if ( is_ssl() && is_string( $value ) && ! empty( $value ) ) {
        return str_replace( 'http://', 'https://', $value );
    }
    return $value;
}

if ( ! function_exists( 'cc_get' ) ) {
    function cc_get( $key, $default = '' ) {
        $value = get_theme_mod( 'cc_' . $key, '' );
        if ( $value === '' || $value === false || $value === null ) {
            $value = $default;
        }
        return cc_maybe_ssl_url( $value );
    }
}

/**
 * Cetak nilai customizer yang sudah di-escape untuk HTML.
 *
 * @param string $key     ID setting customizer (tanpa prefix 'cc_').
 * @param string $default Nilai default.
 */
function cc_e( $key, $default = '' ) {
    echo esc_html( cc_get( $key, $default ) );
}

/**
 * Ambil URL gambar dari customizer (atau fallback placeholder).
 *
 * @param string $key     ID setting customizer (tanpa prefix 'cc_').
 * @param string $default URL default.
 * @return string
 */
function cc_img( $key, $default = '' ) {
    // Ambil nilai theme mod tanpa default — hindari WordPress memanggil
    // sprintf() pada string default yang mungkin berisi karakter % (data URI).
    $value = get_theme_mod( 'cc_' . $key, '' );

    if ( $value ) {
        return esc_url( cc_maybe_ssl_url( $value ) ); // URL nyata: aman di-escape
    }

    return cc_maybe_ssl_url( $default ); // Fallback (data URI, empty string) dikembalikan apa adanya
}

/**
 * Hasilkan URL placeholder gambar berbasis SVG inline (data URI).
 * Tidak membutuhkan koneksi internet — aman untuk environment lokal/staging.
 *
 * @param int    $width  Lebar gambar placeholder dalam pixel.
 * @param int    $height Tinggi gambar placeholder dalam pixel.
 * @param string $bg     Warna latar (hex tanpa #, default: e2e8f0).
 * @param string $color  Warna teks (hex tanpa #, default: 94a3b8).
 * @param string $text   Teks label placeholder.
 * @return string Data URI SVG yang bisa dipakai langsung di atribut src.
 */
function cc_placeholder_svg( $width = 600, $height = 400, $bg = 'e2e8f0', $color = '94a3b8', $text = 'Placeholder' ) {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">'
         . '<rect width="100%" height="100%" fill="#' . $bg . '"/>'
         . '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" '
         . 'font-family="system-ui,sans-serif" font-size="18" fill="#' . $color . '">' . esc_html( $text ) . '</text>'
         . '</svg>';

    // Gunakan base64 — hanya menghasilkan A-Z, a-z, 0-9, +, /, =
    // Tidak ada karakter % sehingga aman ketika WordPress memanggil sprintf()
    // pada string default di get_theme_mod().
    return 'data:image/svg+xml;base64,' . base64_encode( $svg );
}


/**
 * --------------------------------------------------------------------------
 * Render bintang rating (1-5) menggunakan SVG
 * ---------------------------------------------------------------------- */
if ( ! function_exists( 'cc_render_stars' ) ) {
    function cc_render_stars( $rating ) {
        $rating = intval( $rating );
        if ( $rating < 1 ) return '';

        $stars = '';
        for ( $i = 1; $i <= 5; $i++ ) {
            if ( $i <= $rating ) {
                // Bintang penuh
                $stars .= '<svg class="star star--filled" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
            } else {
                // Bintang kosong
                $stars .= '<svg class="star star--empty" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
            }
        }
        return $stars;
    }
}

/**
 * Parse warna Hex menjadi array RGB [r, g, b].
 *
 * @param string $hex Warna berformat hex (misal: #ffffff atau #fff).
 * @return array|false Array [int $r, int $g, int $b] atau false jika tidak valid.
 */
if ( ! function_exists( 'cc_hex_to_rgb' ) ) {
    function cc_hex_to_rgb( $hex ) {
        $hex = ltrim( trim( (string) $hex ), '#' );

        if ( strlen( $hex ) === 3 ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        if ( strlen( $hex ) !== 6 || ! ctype_xdigit( $hex ) ) {
            return false;
        }

        return array(
            hexdec( substr( $hex, 0, 2 ) ),
            hexdec( substr( $hex, 2, 2 ) ),
            hexdec( substr( $hex, 4, 2 ) ),
        );
    }
}

/**
 * Konversi warna Hex ke RGBA.
 *
 * @param string $hex   Warna berformat hex (misal: #ffffff atau #fff).
 * @param float  $alpha Nilai opasitas (0 hingga 1).
 * @return string
 */
if ( ! function_exists( 'cc_hex_to_rgba' ) ) {
    function cc_hex_to_rgba( $hex, $alpha = 1.0 ) {
        $rgb = cc_hex_to_rgb( $hex );
        if ( false === $rgb ) {
            return $hex;
        }

        return "rgba({$rgb[0]}, {$rgb[1]}, {$rgb[2]}, {$alpha})";
    }
}

/**
 * Ambil template pesan default WhatsApp secara terpusat (DRY).
 *
 * @return string
 */
if ( ! function_exists( 'cc_get_default_wa_message' ) ) {
    function cc_get_default_wa_message() {
        return 'Halo {Nama Marketing}, saya tertarik dengan layanan Anda.';
    }
}

/**
 * Ambil daftar post terkait berdasarkan kategori, menghindari ORDER BY RAND() MySQL
 * yang sangat lambat pada tabel besar.
 *
 * Strategi: ambil N*3 post terbaru dari kategori yang sama, lalu acak di PHP.
 * Hasilnya tidak deterministik per request tapi tidak membebani DB.
 *
 * @param int   $current_id    ID post yang sedang dibuka (dikecualikan dari hasil).
 * @param array $category_ids  Array ID kategori untuk filter terkait.
 * @param int   $limit         Jumlah post yang dikembalikan.
 * @return WP_Query
 */
if ( ! function_exists( 'cc_get_related_posts' ) ) {
    function cc_get_related_posts( $current_id, $category_ids = array(), $limit = 5 ) {
        // Ambil pool lebih besar untuk diacak di PHP (menghindari ORDER BY RAND di SQL)
        $pool_size = max( $limit * 3, 15 );

        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => $pool_size,
            'post__not_in'   => array( $current_id ),
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'fields'         => 'ids',
        );

        if ( ! empty( $category_ids ) ) {
            $args['category__in'] = $category_ids;
        }

        $query = new WP_Query( $args );
        $ids   = $query->posts;

        // Acak di PHP, ambil sejumlah $limit
        if ( count( $ids ) > $limit ) {
            shuffle( $ids );
            $ids = array_slice( $ids, 0, $limit );
        }

        // Query ulang dengan ID yang sudah diacak agar template part bisa pakai the_post()
        return new WP_Query( array(
            'post_type'      => 'post',
            'post__in'       => $ids,
            'orderby'        => 'post__in',
            'posts_per_page' => $limit,
            'post_status'    => 'publish',
        ) );
    }
}
