<?php
/**
 * Plugin Name:       Fix Testimoni Photos (Perbaiki Foto Testimoni)
 * Plugin URI:        https://crediblemark.com/
 * Description:       Alat bantu satu-klik untuk mendeteksi foto testimoni tertukar, ganti foto/nama langsung, serta bersihkan nama media 'converted' di Media Library.
 * Version:           1.1.0
 * Author:            Credible Company
 * Text Domain:       fix-testimoni-photos
 * Requires at least: 5.8
 * Requires PHP:      7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue WordPress Media Uploader di halaman tools plugin
 */
add_action( 'admin_enqueue_scripts', function( $hook ) {
    if ( 'tools_page_fix-testimoni-photos' === $hook ) {
        wp_enqueue_media();
    }
} );

/**
 * Daftarkan menu halaman di bawah menu Tools (Alat).
 */
add_action( 'admin_menu', 'ft_register_admin_menu' );
function ft_register_admin_menu() {
    add_management_page(
        __( 'Perbaiki Foto Testimoni', 'fix-testimoni-photos' ),
        __( 'Perbaiki Foto Testimoni', 'fix-testimoni-photos' ),
        'manage_options',
        'fix-testimoni-photos',
        'ft_render_admin_page'
    );
}

/**
 * Render halaman admin.
 */
function ft_render_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'Anda tidak memiliki hak akses untuk halaman ini.', 'fix-testimoni-photos' ) );
    }

    $message = '';

    // 1. Aksi Ubah Nama Pelanggan Secara Cepat
    if ( isset( $_POST['ft_quick_rename'] ) && check_admin_referer( 'ft_quick_rename_nonce' ) ) {
        $post_id  = intval( $_POST['post_id'] );
        $new_name = sanitize_text_field( $_POST['new_name'] );

        if ( $post_id && ! empty( $new_name ) ) {
            wp_update_post( array(
                'ID'         => $post_id,
                'post_title' => $new_name,
            ) );
            update_post_meta( $post_id, '_customer_name', $new_name );

            // Perbarui juga judul foto utamanya jika ada
            $thumb_id = get_post_thumbnail_id( $post_id );
            if ( $thumb_id ) {
                wp_update_post( array(
                    'ID'         => $thumb_id,
                    'post_title' => 'Foto Testimoni - ' . $new_name,
                ) );
                update_post_meta( $thumb_id, '_wp_attachment_image_alt', 'Foto Testimoni ' . $new_name );
            }
            $message = sprintf( __( 'Berhasil mengubah nama postingan #%d menjadi "%s".', 'fix-testimoni-photos' ), $post_id, esc_html( $new_name ) );
        }
    }

    // 2. Aksi Ganti Foto Secara Manual
    if ( isset( $_POST['ft_set_manual_photo'] ) && check_admin_referer( 'ft_set_manual_photo_nonce' ) ) {
        $post_id       = intval( $_POST['post_id'] );
        $attachment_id = intval( $_POST['attachment_id'] );
        $cust_name     = get_post_meta( $post_id, '_customer_name', true ) ?: get_the_title( $post_id );

        if ( $post_id && $attachment_id ) {
            set_post_thumbnail( $post_id, $attachment_id );
            update_post_meta( $post_id, '_thumbnail_id', $attachment_id );

            wp_update_post( array(
                'ID'         => $attachment_id,
                'post_title' => 'Foto Testimoni - ' . sanitize_text_field( $cust_name ),
            ) );
            update_post_meta( $attachment_id, '_wp_attachment_image_alt', 'Foto Testimoni ' . sanitize_text_field( $cust_name ) );

            $message = sprintf( __( 'Berhasil menetapkan foto baru (ID #%d) untuk "%s".', 'fix-testimoni-photos' ), $attachment_id, esc_html( $cust_name ) );
        }
    }

    // 3. Aksi Hapus Postingan Duplikat / Salah
    if ( isset( $_POST['ft_delete_post'] ) && check_admin_referer( 'ft_delete_post_nonce' ) ) {
        $post_id = intval( $_POST['post_id'] );
        if ( $post_id ) {
            wp_delete_post( $post_id, true );
            $message = sprintf( __( 'Postingan testimoni #%d telah berhasil dihapus.', 'fix-testimoni-photos' ), $post_id );
        }
    }

    // 4. Aksi Otomatis Bersihkan Semua Judul Media 'converted-*'
    if ( isset( $_POST['ft_action'] ) && 'clean_converted_titles' === $_POST['ft_action'] && check_admin_referer( 'ft_fix_action_nonce' ) ) {
        global $wpdb;
        $renamed = 0;

        $attachments = $wpdb->get_results( "SELECT ID, post_title, post_parent FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_title LIKE 'converted%'" );

        foreach ( $attachments as $att ) {
            $parent_id = $att->post_parent;
            if ( $parent_id && 'testimoni' === get_post_type( $parent_id ) ) {
                $p_name = get_post_meta( $parent_id, '_customer_name', true ) ?: get_the_title( $parent_id );
                $new_title = 'Foto Testimoni - ' . sanitize_text_field( $p_name );
            } else {
                $new_title = 'Foto Media #' . $att->ID;
            }

            wp_update_post( array(
                'ID'         => $att->ID,
                'post_title' => $new_title,
            ) );
            $renamed++;
        }

        $message = sprintf( __( 'Berhasil merapikan %d judul media yang sebelumnya bernama "converted".', 'fix-testimoni-photos' ), $renamed );
    }

    // 5. Aksi Perbaikan Otomatis Masal
    if ( isset( $_POST['ft_action'] ) && 'run_fix' === $_POST['ft_action'] && check_admin_referer( 'ft_fix_action_nonce' ) ) {
        $testimonials = get_posts( array(
            'post_type'      => 'testimoni',
            'posts_per_page' => -1,
            'post_status'    => 'any',
        ) );

        $fixed_count = 0;
        foreach ( $testimonials as $testi ) {
            $cust_name     = get_post_meta( $testi->ID, '_customer_name', true ) ?: $testi->post_title;
            $curr_thumb_id = get_post_thumbnail_id( $testi->ID );

            $attachments = get_posts( array(
                'post_type'      => 'attachment',
                'post_parent'    => $testi->ID,
                'post_mime_type' => 'image',
                'posts_per_page' => 1,
                'orderby'        => 'ID',
                'order'          => 'ASC',
            ) );

            if ( ! empty( $attachments ) ) {
                $orig_id = $attachments[0]->ID;

                if ( (int) $curr_thumb_id !== (int) $orig_id ) {
                    set_post_thumbnail( $testi->ID, $orig_id );
                    update_post_meta( $testi->ID, '_thumbnail_id', $orig_id );
                    $fixed_count++;
                }

                $curr_title = get_the_title( $orig_id );
                if ( false !== stripos( $curr_title, 'converted' ) || '' === trim( $curr_title ) ) {
                    wp_update_post( array(
                        'ID'         => $orig_id,
                        'post_title' => 'Foto Testimoni - ' . sanitize_text_field( $cust_name ),
                    ) );
                    update_post_meta( $orig_id, '_wp_attachment_image_alt', 'Foto Testimoni ' . sanitize_text_field( $cust_name ) );
                }
            }
        }

        $message = sprintf( __( 'Berhasil! Sebanyak %d foto testimoni telah dikembalikan ke pemilik aslinya dan nama media telah dirapikan.', 'fix-testimoni-photos' ), $fixed_count );
    }

    // Ambil daftar testimoni untuk tabel
    $testimonials = get_posts( array(
        'post_type'      => 'testimoni',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'orderby'        => 'ID',
        'order'          => 'DESC',
    ) );
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php esc_html_e( 'Alat Perbaikan Foto Testimoni', 'fix-testimoni-photos' ); ?></h1>
        <hr class="wp-header-end">

        <?php if ( ! empty( $message ) ) : ?>
            <div class="notice notice-success is-dismissible" style="margin-top: 15px;">
                <p><strong><?php echo esc_html( $message ); ?></strong></p>
            </div>
        <?php endif; ?>

        <div class="card" style="max-width: 100%; margin-top: 15px; padding: 15px 20px;">
            <h2><?php esc_html_e( 'Aksi Cepat Masal', 'fix-testimoni-photos' ); ?></h2>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <form method="post">
                    <?php wp_nonce_field( 'ft_fix_action_nonce' ); ?>
                    <input type="hidden" name="ft_action" value="run_fix">
                    <button type="submit" class="button button-primary" onclick="return confirm('Kembalikan semua foto ke lampiran asli pemiliknya?');">
                        <?php esc_html_e( 'Perbaiki Semua Relasi Foto Otomatis', 'fix-testimoni-photos' ); ?>
                    </button>
                </form>

                <form method="post">
                    <?php wp_nonce_field( 'ft_fix_action_nonce' ); ?>
                    <input type="hidden" name="ft_action" value="clean_converted_titles">
                    <button type="submit" class="button button-secondary" onclick="return confirm('Bersihkan seluruh judul media bernama converted-* di Media Library?');">
                        <?php esc_html_e( 'Ganti Semua Judul Media "converted-*" di Media Library', 'fix-testimoni-photos' ); ?>
                    </button>
                </form>
            </div>
        </div>

        <h2 style="margin-top: 30px;"><?php esc_html_e( 'Daftar Testimoni & Manajemen Langsung', 'fix-testimoni-photos' ); ?></h2>

        <?php if ( empty( $testimonials ) ) : ?>
            <p><?php esc_html_e( 'Belum ada data postingan testimoni.', 'fix-testimoni-photos' ); ?></p>
        <?php else : ?>
            <table class="wp-list-table widefat fixed striped" style="margin-top: 10px;">
                <thead>
                    <tr>
                        <th style="width: 55px;"><?php esc_html_e( 'ID', 'fix-testimoni-photos' ); ?></th>
                        <th style="width: 220px;"><?php esc_html_e( 'Nama Pelanggan (Bisa Diedit)', 'fix-testimoni-photos' ); ?></th>
                        <th style="width: 120px;"><?php esc_html_e( 'Foto Saat Ini', 'fix-testimoni-photos' ); ?></th>
                        <th style="width: 120px;"><?php esc_html_e( 'Foto Asli Sistem', 'fix-testimoni-photos' ); ?></th>
                        <th><?php esc_html_e( 'Judul Media Saat Ini', 'fix-testimoni-photos' ); ?></th>
                        <th style="width: 260px;"><?php esc_html_e( 'Tindakan Manual', 'fix-testimoni-photos' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $testimonials as $testi ) :
                        $cust_name     = get_post_meta( $testi->ID, '_customer_name', true ) ?: $testi->post_title;
                        $curr_thumb_id = get_post_thumbnail_id( $testi->ID );
                        $curr_img_url  = $curr_thumb_id ? wp_get_attachment_image_url( $curr_thumb_id, 'thumbnail' ) : '';

                        $attachments = get_posts( array(
                            'post_type'      => 'attachment',
                            'post_parent'    => $testi->ID,
                            'post_mime_type' => 'image',
                            'posts_per_page' => 1,
                            'orderby'        => 'ID',
                            'order'          => 'ASC',
                        ) );

                        $orig_id       = ! empty( $attachments ) ? $attachments[0]->ID : null;
                        $orig_img_url  = $orig_id ? wp_get_attachment_image_url( $orig_id, 'thumbnail' ) : '';
                        $curr_title    = $curr_thumb_id ? get_the_title( $curr_thumb_id ) : '-';
                    ?>
                        <tr>
                            <td>#<?php echo esc_html( $testi->ID ); ?></td>
                            <td>
                                <!-- Form Cepat Ganti Nama Pelanggan -->
                                <form method="post" style="display: flex; gap: 4px; align-items: center; margin-bottom: 4px;">
                                    <?php wp_nonce_field( 'ft_quick_rename_nonce' ); ?>
                                    <input type="hidden" name="post_id" value="<?php echo esc_attr( $testi->ID ); ?>">
                                    <input type="text" name="new_name" value="<?php echo esc_attr( $cust_name ); ?>" style="width: 140px; height: 28px; font-size: 12px; font-weight: bold;">
                                    <button type="submit" name="ft_quick_rename" class="button button-small" title="Simpan perubahan nama">💾</button>
                                </form>
                                <small style="color: #64748b;"><?php echo esc_html( $testi->post_date ); ?></small>
                                <br><a href="<?php echo esc_url( get_edit_post_link( $testi->ID ) ); ?>" target="_blank" style="font-size: 11px;">Edit Post Lengkap &rarr;</a>
                            </td>
                            <td>
                                <?php if ( $curr_img_url ) : ?>
                                    <img src="<?php echo esc_url( $curr_img_url ); ?>" width="60" height="60" style="object-fit: cover; border-radius: 4px; display: block;">
                                    <small>ID: <?php echo esc_html( $curr_thumb_id ); ?></small>
                                <?php else : ?>
                                    <span style="color: #94a3b8;"><?php esc_html_e( '(Kosong)', 'fix-testimoni-photos' ); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ( $orig_img_url ) : ?>
                                    <img src="<?php echo esc_url( $orig_img_url ); ?>" width="60" height="60" style="object-fit: cover; border-radius: 4px; display: block;">
                                    <small>ID: <?php echo esc_html( $orig_id ); ?></small>
                                <?php else : ?>
                                    <span style="color: #94a3b8;"><?php esc_html_e( '(Tidak ada)', 'fix-testimoni-photos' ); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ( false !== stripos( $curr_title, 'converted' ) ) : ?>
                                    <span style="background: #fed7aa; color: #9a3412; padding: 2px 6px; border-radius: 3px; font-family: monospace; font-size: 12px;">
                                        <?php echo esc_html( $curr_title ); ?>
                                    </span>
                                <?php else : ?>
                                    <span style="font-family: monospace; font-size: 12px; color: #166534;">
                                        <?php echo esc_html( $curr_title ); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Tombol Ganti Foto Manual via Media Modal -->
                                <button type="button" class="button button-small ft-select-photo-btn" data-post-id="<?php echo esc_attr( $testi->ID ); ?>" style="margin-bottom: 4px;">
                                    🖼️ <?php esc_html_e( 'Pilih Foto...', 'fix-testimoni-photos' ); ?>
                                </button>

                                <!-- Tombol Hapus Post (jika duplikat) -->
                                <form method="post" style="display: inline-block;" onsubmit="return confirm('Hapus postingan testimoni #<?php echo esc_attr( $testi->ID ); ?> (<?php echo esc_js( $cust_name ); ?>) secara permanen?');">
                                    <?php wp_nonce_field( 'ft_delete_post_nonce' ); ?>
                                    <input type="hidden" name="post_id" value="<?php echo esc_attr( $testi->ID ); ?>">
                                    <button type="submit" name="ft_delete_post" class="button button-small button-link-delete">
                                        🗑️ <?php esc_html_e( 'Hapus', 'fix-testimoni-photos' ); ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Hidden form untuk upload manual via media frame -->
    <form id="ft-manual-photo-form" method="post" style="display: none;">
        <?php wp_nonce_field( 'ft_set_manual_photo_nonce' ); ?>
        <input type="hidden" name="ft_set_manual_photo" value="1">
        <input type="hidden" name="post_id" id="ft-target-post-id">
        <input type="hidden" name="attachment_id" id="ft-target-attachment-id">
    </form>

    <script>
    jQuery(document).ready(function($) {
        var fileFrame;
        var currentPostId;

        $('.ft-select-photo-btn').on('click', function(e) {
            e.preventDefault();
            currentPostId = $(this).data('post-id');

            if (fileFrame) {
                fileFrame.open();
                return;
            }

            fileFrame = wp.media({
                title: 'Pilih Foto untuk Testimoni Ini',
                button: { text: 'Gunakan Foto Ini' },
                multiple: false
            });

            fileFrame.on('select', function() {
                var attachment = fileFrame.state().get('selection').first().toJSON();
                $('#ft-target-post-id').val(currentPostId);
                $('#ft-target-attachment-id').val(attachment.id);
                $('#ft-manual-photo-form').submit();
            });

            fileFrame.open();
        });
    });
    </script>
    <?php
}
