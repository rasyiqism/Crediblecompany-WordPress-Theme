<?php
/**
 * Modular Form Handler: Submit Testimoni.
 * Menangani pemrosesan pengiriman ulasan/testimoni dari front-end.
 *
 * @package CredibleCompany
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_post_nopriv_submit_testimoni_action', 'cc_handle_submit_testimoni' );
add_action( 'admin_post_submit_testimoni_action', 'cc_handle_submit_testimoni' );

/**
 * AJAX Endpoint: Memberikan nonce segar untuk form testimoni.
 * Dipanggil oleh JS sebelum submit agar kompatibel dengan halaman yang di-cache.
 * Nonce tidak ada di markup statis — hanya diambil saat user hendak submit.
 */
add_action( 'wp_ajax_nopriv_cc_get_testimoni_nonce', 'cc_ajax_get_testimoni_nonce' );
add_action( 'wp_ajax_cc_get_testimoni_nonce', 'cc_ajax_get_testimoni_nonce' );
function cc_ajax_get_testimoni_nonce() {
    wp_send_json_success( array(
        'nonce' => wp_create_nonce( 'cc_submit_testimoni' ),
    ) );
}

/**
 * Handle form submission untuk testimoni publik.
 */
function cc_handle_submit_testimoni() {
    // 1. Proteksi Spam Honeypot
    if ( ! empty( $_POST['cc_hp_email'] ) ) {
        wp_die( 'Aktivitas mencurigakan terdeteksi (Spam).', 'Akses Ditolak', array( 'response' => 403 ) );
    }

    // 2. Validasi CSRF Nonce (dikirim JS sebelum submit untuk kompatibilitas cache)
    if ( ! isset( $_POST['_cc_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_cc_nonce'] ) ), 'cc_submit_testimoni' ) ) {
        wp_die( 'Sesi keamanan tidak valid atau kedaluwarsa. Silakan refresh halaman dan coba lagi.', 'Akses Ditolak', array( 'response' => 403 ) );
    }

    $name         = isset( $_POST['client_name'] ) ? sanitize_text_field( wp_unslash( $_POST['client_name'] ) ) : '';
    $title        = isset( $_POST['client_title'] ) ? sanitize_text_field( wp_unslash( $_POST['client_title'] ) ) : '';
    $city         = isset( $_POST['client_city'] ) ? sanitize_text_field( wp_unslash( $_POST['client_city'] ) ) : '';
    $rating_raw   = isset( $_POST['client_rating'] ) ? intval( $_POST['client_rating'] ) : 0;
    $rating       = max( 1, min( 5, $rating_raw ) );
    $review       = isset( $_POST['client_review'] ) ? sanitize_textarea_field( wp_unslash( $_POST['client_review'] ) ) : '';
    $base64_photo = isset( $_POST['client_photo_base64'] ) ? wp_unslash( $_POST['client_photo_base64'] ) : '';

    $referer = wp_get_referer() ? wp_get_referer() : home_url();

    if ( empty( $name ) || empty( $title ) || empty( $city ) || $rating_raw < 1 || empty( $review ) || empty( $base64_photo ) ) {
        wp_safe_redirect( add_query_arg( 'err', '1', $referer ) );
        exit;
    }

    // Buat Postingan Testimoni "Menunggu Peninjauan" (Pending)
    $post_id = wp_insert_post( array(
        'post_title'   => $name,
        'post_content' => $review,
        'post_status'  => 'pending',
        'post_type'    => 'testimoni',
    ) );

    if ( $post_id ) {
        // 1. Simpan ke meta key canonical tema (_customer_*) - Digunakan untuk Kolom Admin & Kartu Testimoni
        update_post_meta( $post_id, '_customer_name', $name );
        update_post_meta( $post_id, '_customer_profession', $title );
        update_post_meta( $post_id, '_customer_city', $city );
        update_post_meta( $post_id, '_customer_rating', $rating );

        // 2. Simpan ke meta key legacy/SEO (cc_testimonial_*) - Digunakan untuk SEO Optimizer & kompatibilitas lama
        update_post_meta( $post_id, 'cc_testimonial_title', $title );
        update_post_meta( $post_id, 'cc_testimonial_rating', $rating );

        // Tangani Unggahan Foto
        if ( ! empty( $base64_photo ) ) {
            // Validasi format data URI (harus base64 image/jpeg, image/png, atau image/webp)
            if ( preg_match( '/^data:image\/(jpeg|jpg|png|webp);base64,(.+)$/i', $base64_photo, $matches ) ) {
                $image_ext = strtolower( $matches[1] );
                if ( 'jpg' === $image_ext ) {
                    $image_ext = 'jpeg';
                }

                // Batasi ukuran payload base64 (~5MB binary) sebelum decode
                if ( strlen( $matches[2] ) <= 7000000 ) {
                    $image_data = base64_decode( $matches[2], true );
                    $image_info = ( false !== $image_data ) ? @getimagesizefromstring( $image_data ) : false;

                    if ( false !== $image_data && false !== $image_info ) {
                        // Simpan sementara di folder uploads WordPress
                        $wp_upload_dir = wp_upload_dir();
                        $temp_dir      = $wp_upload_dir['path'];

                        // Buat file sementara
                        $temp_name = 'testimoni_' . uniqid() . '.' . $image_ext;
                        $temp_path = $temp_dir . '/' . $temp_name;

                        if ( file_put_contents( $temp_path, $image_data ) !== false ) {
                            // Sediakan library WordPress
                            require_once ABSPATH . 'wp-admin/includes/image.php';
                            require_once ABSPATH . 'wp-admin/includes/file.php';
                            require_once ABSPATH . 'wp-admin/includes/media.php';

                            // Coba convert gambar ke webp secara on-the-fly demi optimalisasi ukuran
                            $editor = wp_get_image_editor( $temp_path );
                            if ( ! is_wp_error( $editor ) ) {
                                $editor->set_quality( 80 );
                                $tmp_webp = $temp_dir . '/' . wp_unique_filename( $temp_dir, 'converted.webp' );
                                $saved    = $editor->save( $tmp_webp, 'image/webp' );
                                if ( ! is_wp_error( $saved ) ) {
                                    // Hapus file jpeg/png sementara, ganti dengan webp
                                    @unlink( $temp_path );
                                    $temp_path = $saved['path'];
                                    $temp_name = basename( $temp_path );
                                }
                            }

                            // Kirim ke media library
                            $file_array = array(
                                'name'     => $temp_name,
                                'tmp_name' => $temp_path,
                            );

                            $attachment_id = media_handle_sideload( $file_array, $post_id );

                            if ( ! is_wp_error( $attachment_id ) ) {
                                set_post_thumbnail( $post_id, $attachment_id );
                            } else {
                                // Hapus file sisa jika sideload gagal
                                @unlink( $temp_path );
                            }
                        }
                    }
                }
            }
        }

        // redirect ke asal dg url arg status=berhasil
        wp_safe_redirect( add_query_arg( 'success', 'true', $referer ) );
        exit;
    } else {
        wp_die( 'Gagal menyimpan data ke Sistem.' );
    }
}
