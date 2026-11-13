<?php
/**
 * Testimoni Submission & Edit Handler
 * Menangani form submission baru dan mode edit / ganti foto testimoni.
 * Jika status postingan sebelumnya 'publish', akan otomatis ditangguhkan kembali ('pending')
 * agar dimoderasi oleh admin.
 *
 * @package CredibleCompany
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_post_nopriv_submit_testimoni_action', 'cc_theme_handle_submit_testimoni', 5 );
add_action( 'admin_post_submit_testimoni_action', 'cc_theme_handle_submit_testimoni', 5 );

function cc_theme_handle_submit_testimoni() {
    // 1. Proteksi Spam Honeypot
    if ( ! empty( $_POST['cc_hp_email'] ) ) {
        wp_die( 'Aktivitas mencurigakan terdeteksi (Spam).', 'Akses Ditolak', array( 'response' => 403 ) );
    }

    // 2. Validasi CSRF Nonce
    if ( ! isset( $_POST['_cc_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_cc_nonce'] ) ), 'cc_submit_testimoni' ) ) {
        wp_die( 'Sesi keamanan tidak valid atau kedaluwarsa. Silakan refresh halaman dan coba lagi.', 'Akses Ditolak', array( 'response' => 403 ) );
    }

    $edit_post_id = isset( $_POST['edit_post_id'] ) ? intval( $_POST['edit_post_id'] ) : 0;
    $is_edit      = false;

    if ( $edit_post_id > 0 ) {
        $existing_post = get_post( $edit_post_id );
        if ( $existing_post && 'testimoni' === $existing_post->post_type ) {
            $is_edit = true;
        }
    }

    $name         = isset( $_POST['client_name'] ) ? sanitize_text_field( wp_unslash( $_POST['client_name'] ) ) : '';
    $title        = isset( $_POST['client_title'] ) ? sanitize_text_field( wp_unslash( $_POST['client_title'] ) ) : '';
    $city         = isset( $_POST['client_city'] ) ? sanitize_text_field( wp_unslash( $_POST['client_city'] ) ) : '';
    $rating_raw   = isset( $_POST['client_rating'] ) ? intval( $_POST['client_rating'] ) : 0;
    $rating       = max( 1, min( 5, $rating_raw ) );
    $review       = isset( $_POST['client_review'] ) ? sanitize_textarea_field( wp_unslash( $_POST['client_review'] ) ) : '';
    $base64_photo = isset( $_POST['client_photo_base64'] ) ? wp_unslash( $_POST['client_photo_base64'] ) : '';

    $referer = wp_get_referer() ? wp_get_referer() : home_url();

    // Validasi data wajib (jika mode edit dan sudah punya foto, upload foto baru bersifat opsional)
    $photo_required = ( ! $is_edit || ( $is_edit && ! has_post_thumbnail( $edit_post_id ) ) );
    if ( empty( $name ) || empty( $title ) || empty( $city ) || $rating_raw < 1 || empty( $review ) || ( $photo_required && empty( $base64_photo ) ) ) {
        wp_safe_redirect( add_query_arg( 'err', '1', $referer ) );
        exit;
    }

    if ( $is_edit ) {
        // Mode Edit: Update postingan yang ada & UBAH STATUS MENJADI PENDING (ditangguhkan kembali)
        $post_id = wp_update_post( array(
            'ID'           => $edit_post_id,
            'post_title'   => $name,
            'post_content' => $review,
            'post_status'  => 'pending', // DITANGGUHKAN LAGI MESKI SEBELUMNYA SUDAH PUBLISH
        ) );
    } else {
        // Mode Baru: Buat postingan testimoni pending
        $post_id = wp_insert_post( array(
            'post_title'   => $name,
            'post_content' => $review,
            'post_status'  => 'pending',
            'post_type'    => 'testimoni',
        ) );
    }

    if ( $post_id && ! is_wp_error( $post_id ) ) {
        // 1. Simpan metadata canonical tema
        update_post_meta( $post_id, '_customer_name', $name );
        update_post_meta( $post_id, '_customer_profession', $title );
        update_post_meta( $post_id, '_customer_city', $city );
        update_post_meta( $post_id, '_customer_rating', $rating );

        // 2. Simpan metadata SEO
        update_post_meta( $post_id, 'cc_testimonial_title', $title );
        update_post_meta( $post_id, 'cc_testimonial_rating', $rating );

        // Tangani unggahan foto baru jika ada
        if ( ! empty( $base64_photo ) ) {
            if ( preg_match( '/^data:image\/(jpeg|jpg|png|webp);base64,(.+)$/i', $base64_photo, $matches ) ) {
                $image_ext = strtolower( $matches[1] );
                if ( 'jpg' === $image_ext ) {
                    $image_ext = 'jpeg';
                }

                if ( strlen( $matches[2] ) <= 7000000 ) {
                    $image_data = base64_decode( $matches[2], true );
                    $image_info = ( false !== $image_data ) ? @getimagesizefromstring( $image_data ) : false;

                    if ( false !== $image_data && false !== $image_info ) {
                        require_once ABSPATH . 'wp-admin/includes/image.php';
                        require_once ABSPATH . 'wp-admin/includes/file.php';
                        require_once ABSPATH . 'wp-admin/includes/media.php';

                        $temp_file = wp_tempnam( 'testi_' );

                        if ( $temp_file && file_put_contents( $temp_file, $image_data ) !== false ) {
                            $name_slug = sanitize_title( $name );
                            if ( empty( $name_slug ) ) {
                                $name_slug = 'pelanggan';
                            }
                            $unique_suffix  = wp_generate_password( 6, false, false );
                            $final_filename = 'foto-testimoni-' . $name_slug . '-' . $unique_suffix . '.webp';
                            $upload_target  = $temp_file;

                            $editor = wp_get_image_editor( $temp_file );
                            if ( ! is_wp_error( $editor ) ) {
                                $editor->set_quality( 80 );
                                $webp_temp = wp_tempnam( 'webp_' );
                                $saved     = $editor->save( $webp_temp, 'image/webp' );
                                if ( ! is_wp_error( $saved ) ) {
                                    @unlink( $temp_file );
                                    $upload_target = $saved['path'];
                                } else {
                                    @unlink( $webp_temp );
                                    $final_filename = 'foto-testimoni-' . $name_slug . '-' . $unique_suffix . '.' . $image_ext;
                                }
                            } else {
                                $final_filename = 'foto-testimoni-' . $name_slug . '-' . $unique_suffix . '.' . $image_ext;
                            }

                            $file_array = array(
                                'name'     => $final_filename,
                                'tmp_name' => $upload_target,
                            );

                            $attachment_data = array(
                                'post_title' => 'Foto Testimoni - ' . sanitize_text_field( $name ),
                            );

                            $attachment_id = media_handle_sideload( $file_array, $post_id, null, $attachment_data );

                            if ( ! is_wp_error( $attachment_id ) ) {
                                set_post_thumbnail( $post_id, $attachment_id );
                                update_post_meta( $attachment_id, '_wp_attachment_image_alt', 'Foto Testimoni ' . sanitize_text_field( $name ) );
                            } else {
                                @unlink( $upload_target );
                            }
                        }
                    }
                }
            }
        }

        // Hentikan eksekusi plugin agar tidak diproses ganda
        remove_action( 'admin_post_nopriv_submit_testimoni_action', 'cc_handle_submit_testimoni' );
        remove_action( 'admin_post_submit_testimoni_action', 'cc_handle_submit_testimoni' );

        $redirect_url = $is_edit ? add_query_arg( 'success', 'updated', site_url( '/submit-testimoni/' ) ) : add_query_arg( 'success', 'true', $referer );
        wp_safe_redirect( $redirect_url );
        exit;
    } else {
        wp_die( 'Gagal menyimpan data ke Sistem.' );
    }
}
