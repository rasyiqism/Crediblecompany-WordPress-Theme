<?php
/**
 * Dynamic WhatsApp URL Logic.
 * Bertugas mencatat kunjungan referral ke cookie & menyediakan 
 * fungsi helper untuk mereplace URL wa.me dengan nomor Marketing terdaftar.
 *
 * @package CredibleCompany
 */

// Nama identitas Cookie & Durasi
define( 'CC_MARKETER_COOKIE', 'cc_marketer_ref' );
define( 'CC_MARKETER_EXPIRE', 30 * DAY_IN_SECONDS ); // 30 hari

/* --------------------------------------------------------------------------
 * 1. Tangkap referral '?ref=' di URL lalu simpan ke Cookie
 * ---------------------------------------------------------------------- */
add_action( 'template_redirect', 'cc_track_marketing_referral' );

function cc_track_marketing_referral() {
    // a. Cek apakah ada param '?ref='
    if ( ! isset( $_GET['ref'] ) || empty( $_GET['ref'] ) ) {
        return;
    }

    // b. Cek First-Click: Jika cookie sudah ada (artinya dia udah dapet dr marketing lain), abaikan!
    // Ini mengimplementasikan 'First-Click Attribution'.
    // Hapus logika ini jika ingin Last-Click Attribution.
    if ( isset( $_COOKIE[ CC_MARKETER_COOKIE ] ) && ! empty( $_COOKIE[ CC_MARKETER_COOKIE ] ) ) {
        return; 
    }

    $ref_slug = sanitize_title( $_GET['ref'] );

    // c. Validasi ke tabel wp_posts: Adakah marketing dengan slug tersebut?
    $marketing_query = new WP_Query( array(
        'post_type'      => 'marketing',
        'name'           => $ref_slug,
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'fields'         => 'ids', // Kita butuh ID-nya buat ngecek meta nanti
    ) );

    if ( $marketing_query->have_posts() ) {
        $marketing_id = $marketing_query->posts[0];
        // d. Pastikan marketing ini beneran punya input nomor wa
        $wa_number = get_post_meta( $marketing_id, '_cc_wa_number', true );
        
        if ( ! empty( $wa_number ) ) {
            // e. Rekam ID marketing ke Cookie pembaca
            // Cookie diset ke '/' agar berlaku di semua halaman (/paket, /kontak, depan, dsb)
            setcookie( CC_MARKETER_COOKIE, $marketing_id, time() + CC_MARKETER_EXPIRE, COOKIEPATH ?: '/', COOKIE_DOMAIN );
            
            // Masukkan variabel sementara ke superglobal supaya di reload yg sama (kalau belum kecatat di browser),
            // script bawah sudah bisa lgs membaca
            $_COOKIE[ CC_MARKETER_COOKIE ] = $marketing_id;
        }
    }
}

/* --------------------------------------------------------------------------
 * 2. Helper CC_DYNAMIC_WA_URL (Core Engine Replacement)
 * ---------------------------------------------------------------------- */
/**
 * cc_dynamic_wa_url
 * Wrapper untuk mengganti wa.me/DEFAULT menjadi wa.me/MARKETING bila cookie aktif.
 * Mempertahankan parameter text jika ada.
 *
 * @param string $original_url URL href yang sudah ada di panel (misal dari Customizer yg dilimit).
 * @return string URL utuh yang sudah berpotensi terganti nomornya.
 */
if ( ! function_exists( 'cc_dynamic_wa_url' ) ) {
function cc_dynamic_wa_url( $original_url ) {
    // 1. Cek dulu apakah pengunjung punya data referral
    if ( ! isset( $_COOKIE[ CC_MARKETER_COOKIE ] ) || empty( $_COOKIE[ CC_MARKETER_COOKIE ] ) ) {
        $fallback = get_theme_mod( 'cc_marketing_fallback_name', 'Admin' );
        return str_replace( array( '{Nama Marketing}', '{nama marketing}' ), $fallback, $original_url );
    }

    $marketing_id = intval( $_COOKIE[ CC_MARKETER_COOKIE ] );

    // 2. Ambil Nomor Marketing dari DB
    $marketing_wa = get_post_meta( $marketing_id, '_cc_wa_number', true );

    if ( empty( $marketing_wa ) ) {
        return $original_url; // Kalau sewaktu-waktu nomornya dihapus sama admin, fallback.
    }

    // 3. Pastikan string asli memang menunjuk ke WhatsApp (wa.me atau api.whatsapp.com)
    $is_wa_me = strpos( $original_url, 'wa.me/' ) !== false;
    $is_api_wa = strpos( $original_url, 'api.whatsapp.com/send' ) !== false;

    if ( ! $is_wa_me && ! $is_api_wa ) {
        return $original_url; // Ternyata ini URL order website langsung, biarkan saja.
    }

    // 4. Ekstraksi Query Param asal (terutama text=Halo...)
    $parsed_url = parse_url( $original_url );
    $query_array = array();
    
    if ( isset( $parsed_url['query'] ) ) {
        parse_str( $parsed_url['query'], $query_array );
    }

    // 5. Tambahkan teks bawaan dari user request jika 'text' belum ada atau admin ingin override
    // Misalnya Admin tidak nyetting text sama sekali di tombol, maka kita bantu buatin.
    $marketing_custom_text = get_post_meta( $marketing_id, '_cc_wa_text', true );

    // Jika marketing ini DI-SETT punya teks kustom sendiri oleh Admin, WAJIB paksa pakai teks dia
    if ( ! empty( $marketing_custom_text ) ) {
        $query_array['text'] = $marketing_custom_text;
    } 
    // Jika tidak diisi custom text agen, dan ternyata tombol dari Elementor emang ga ada param text-nya
    elseif ( ! isset( $query_array['text'] ) || empty( $query_array['text'] ) ) {
         $query_array['text'] = cc_get( 'whatsapp_message', cc_get_default_wa_message() );
    }
    
    // String Replace: Jika teks berisi {Nama Marketing}, ganti dengan nama aslinya
    $marketing_name = get_the_title( $marketing_id );
    $query_array['text'] = str_replace( array( '{Nama Marketing}', '{nama marketing}' ), $marketing_name, $query_array['text'] );

    $query_string = http_build_query( $query_array, '', '&', PHP_QUERY_RFC3986 );
    
    // 6. Rakit kembali URL menggunakan Standarisasi API wa.me
    $new_url = 'https://wa.me/' . $marketing_wa . '?' . $query_string;

    return $new_url;
}
}

/* --------------------------------------------------------------------------
 * 3. Helper CC_DYNAMIC_TEXT (String Replacer UI)
 * ---------------------------------------------------------------------- */
/**
 * cc_dynamic_text
 * Fungsi pembantu untuk merender teks di layar (H1, p, btn text)
 * Jika cookie marketer aktif, maka string "{Nama Marketing}" akan diganti dengan 
 * nama asli marketing tsb.
 *
 * @param string $text Teks yang akan dicetak ke layar
 * @return string Teks yang sudah difilter
 */
if ( ! function_exists( 'cc_dynamic_text' ) ) {
function cc_dynamic_text( $text ) {
    // Daftar template tag — varian dengan spasi depan diletakkan lebih dulu agar tidak terlewat
    $placeholders = array( ' {Nama Marketing}', ' {nama marketing}', '{Nama Marketing}', '{nama marketing}' );

    if ( ! isset( $_COOKIE[ CC_MARKETER_COOKIE ] ) || empty( $_COOKIE[ CC_MARKETER_COOKIE ] ) ) {
        $fallback = get_theme_mod( 'cc_marketing_fallback_name', 'Admin' );
        return str_replace( $placeholders, $fallback, $text );
    }

    $marketing_id   = intval( $_COOKIE[ CC_MARKETER_COOKIE ] );
    $marketing_name = get_the_title( $marketing_id );

    if ( empty( $marketing_name ) ) {
        $fallback = get_theme_mod( 'cc_marketing_fallback_name', 'Admin' );
        return str_replace( $placeholders, $fallback, $text );
    }

    return str_replace( $placeholders, $marketing_name, $text );
}
}
