<?php
/**
 * Custom Post Type: Paket Jasa.
 * Mendaftarkan CPT 'paket_jasa' dan meta box untuk data pricing.
 * Admin bisa menambah paket secara dinamis dari dashboard.
 *
 * Struktur data per paket:
 * - Nama Paket (post_title)
 * - Badge (label kecil, misal "Best Promo")
 * - Harga
 * - Eksemplar (jumlah cetak, misal "50 Eksemplar")
 * - Ukuran (misal "A5/Unesco")
 * - Catatan (spesifikasi layanan)
 * - Teks & URL Tombol
 * - Fasilitas & Bonus (textarea, satu per baris)
 *
 * @package CredibleCompany
 */

/* --------------------------------------------------------------------------
 * 1. Register Custom Post Type
 * ---------------------------------------------------------------------- */
add_action( 'init', function () {
    $labels = array(
        'name'               => __( 'Paket Jasa', 'crediblecompany' ),
        'singular_name'      => __( 'Paket Jasa', 'crediblecompany' ),
        'add_new'            => __( 'Tambah Paket', 'crediblecompany' ),
        'add_new_item'       => __( 'Tambah Paket Baru', 'crediblecompany' ),
        'edit_item'          => __( 'Edit Paket', 'crediblecompany' ),
        'new_item'           => __( 'Paket Baru', 'crediblecompany' ),
        'all_items'          => __( 'Semua Paket', 'crediblecompany' ),
        'view_item'          => __( 'Lihat Paket', 'crediblecompany' ),
        'search_items'       => __( 'Cari Paket', 'crediblecompany' ),
        'not_found'          => __( 'Tidak ada paket ditemukan.', 'crediblecompany' ),
        'not_found_in_trash' => __( 'Tidak ada paket di Trash.', 'crediblecompany' ),
        'menu_name'          => __( 'Paket Jasa', 'crediblecompany' ),
    );

    register_post_type( 'paket_jasa', array(
        'labels'       => $labels,
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-money-alt',
        'menu_position'=> 25,
        'supports'     => array( 'title', 'page-attributes' ),
        'has_archive'  => false,
        'rewrite'      => false,
    ) );
} );

/* --------------------------------------------------------------------------
 * 2. Meta Box — Detail Paket
 * ---------------------------------------------------------------------- */
// Enqueue script media WordPress di halaman editor admin post type paket_jasa
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    global $post;
    if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && isset( $post->post_type ) && 'paket_jasa' === $post->post_type ) {
        wp_enqueue_media();
    }
} );

add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'cc_paket_detail',
        __( 'Detail Paket', 'crediblecompany' ),
        'cc_render_paket_meta_box',
        'paket_jasa',
        'normal',
        'high'
    );
} );

/**
 * Render isi meta box — field-field untuk detail paket.
 *
 * @param WP_Post $post Post object.
 */
function cc_render_paket_meta_box( $post ) {
    wp_nonce_field( 'cc_paket_save', 'cc_paket_nonce' );

    // Ambil data tersimpan
    $badge       = get_post_meta( $post->ID, '_cc_badge', true );
    $price       = get_post_meta( $post->ID, '_cc_price', true );
    $eksemplar   = get_post_meta( $post->ID, '_cc_eksemplar', true );
    $ukuran      = get_post_meta( $post->ID, '_cc_ukuran', true );
    $catatan     = get_post_meta( $post->ID, '_cc_catatan', true );
    $btn_text    = get_post_meta( $post->ID, '_cc_btn_text', true ) ?: 'Ambil Promo';
    $btn_url     = get_post_meta( $post->ID, '_cc_btn_url', true ) ?: '#';
    $features    = get_post_meta( $post->ID, '_cc_features', true );
    $paket_image = get_post_meta( $post->ID, '_cc_paket_image', true );
    ?>
    <style>
        .cc-meta-table { width: 100%; border-collapse: collapse; }
        .cc-meta-table th { text-align: left; padding: 10px 10px 10px 0; width: 180px; vertical-align: top; }
        .cc-meta-table td { padding: 8px 0; }
        .cc-meta-table input[type="text"],
        .cc-meta-table input[type="url"],
        .cc-meta-table textarea { width: 100%; padding: 6px 10px; }
        .cc-meta-table textarea { min-height: 200px; font-family: monospace; }
        .cc-meta-help { color: #666; font-size: 12px; margin-top: 4px; }
        .cc-meta-separator { background: #f0f0f1; padding: 8px 12px; font-weight: 600; margin: 6px 0; }
    </style>

    <table class="cc-meta-table">
        <!-- Opsi Gambar Poster -->
        <tr><td colspan="2"><div class="cc-meta-separator">🖼️ Opsi Gambar Poster (Alternatif Input Teks)</div></td></tr>
        <tr>
            <th><label for="cc_paket_image"><?php esc_html_e( 'Gambar Paket / Poster', 'crediblecompany' ); ?></label></th>
            <td>
                <input type="text" id="cc_paket_image" name="cc_paket_image" value="<?php echo esc_url( $paket_image ); ?>" style="width: 70%;" placeholder="https://example.com/image.jpg">
                <button type="button" class="button cc-upload-image-btn" data-target="cc_paket_image"><?php esc_html_e( 'Pilih Gambar', 'crediblecompany' ); ?></button>
                <button type="button" class="button cc-clear-image-btn" data-target="cc_paket_image"><?php esc_html_e( 'Hapus', 'crediblecompany' ); ?></button>
                <div style="margin-top: 10px;">
                    <img id="cc_paket_image_preview" src="<?php echo esc_url( $paket_image ); ?>" style="max-width: 150px; max-height: 200px; display: <?php echo $paket_image ? 'block' : 'none'; ?>; border: 1px solid #ccc; padding: 4px;" />
                </div>
                <p class="cc-meta-help">Upload gambar flyer/poster paket jasa. Jika diisi, kartu paket jasa di website akan menampilkan gambar ini secara penuh (mengabaikan teks harga & fasilitas).</p>
            </td>
        </tr>

        <!-- Info Utama -->
        <tr><td colspan="2"><div class="cc-meta-separator">📋 Informasi Utama</div></td></tr>
        <tr>
            <th><label for="cc_badge"><?php esc_html_e( 'Badge / Label', 'crediblecompany' ); ?></label></th>
            <td>
                <input type="text" id="cc_badge" name="cc_badge" value="<?php echo esc_attr( $badge ); ?>" placeholder="Contoh: Best Promo, Populer, Premium">
                <p class="cc-meta-help">Label kecil di pojok kiri atas card (kosongkan jika tidak perlu).</p>
            </td>
        </tr>
        <tr>
            <th><label for="cc_price"><?php esc_html_e( 'Harga', 'crediblecompany' ); ?></label></th>
            <td>
                <input type="text" id="cc_price" name="cc_price" value="<?php echo esc_attr( $price ); ?>" placeholder="Rp. 2.250.000">
            </td>
        </tr>
        <tr>
            <th><label for="cc_eksemplar"><?php esc_html_e( 'Jumlah Eksemplar', 'crediblecompany' ); ?></label></th>
            <td>
                <input type="text" id="cc_eksemplar" name="cc_eksemplar" value="<?php echo esc_attr( $eksemplar ); ?>" placeholder="50 Eksemplar">
            </td>
        </tr>
        <tr>
            <th><label for="cc_ukuran"><?php esc_html_e( 'Ukuran Buku', 'crediblecompany' ); ?></label></th>
            <td>
                <input type="text" id="cc_ukuran" name="cc_ukuran" value="<?php echo esc_attr( $ukuran ); ?>" placeholder="A5/Unesco">
            </td>
        </tr>
        <tr>
            <th><label for="cc_catatan"><?php esc_html_e( 'Catatan / Keterangan', 'crediblecompany' ); ?></label></th>
            <td>
                <input type="text" id="cc_catatan" name="cc_catatan" value="<?php echo esc_attr( $catatan ); ?>" placeholder="Layanan ini berlaku untuk 150 Halaman. Jika lebih, dikenai tarif tambahan.">
                <p class="cc-meta-help">Teks kecil di bawah harga untuk syarat/ketentuan.</p>
            </td>
        </tr>

        <!-- Tombol -->
        <tr><td colspan="2"><div class="cc-meta-separator">🔗 Tombol CTA</div></td></tr>
        <tr>
            <th><label for="cc_btn_text"><?php esc_html_e( 'Teks Tombol', 'crediblecompany' ); ?></label></th>
            <td>
                <input type="text" id="cc_btn_text" name="cc_btn_text" value="<?php echo esc_attr( $btn_text ); ?>" placeholder="Ambil Promo">
            </td>
        </tr>
        <tr>
            <th><label for="cc_btn_url"><?php esc_html_e( 'URL Tombol', 'crediblecompany' ); ?></label></th>
            <td>
                <input type="url" id="cc_btn_url" name="cc_btn_url" value="<?php echo esc_url( $btn_url ); ?>" placeholder="https://wa.me/628xxx">
                <p class="cc-meta-help">Link WhatsApp atau halaman order.</p>
            </td>
        </tr>

        <!-- Fasilitas -->
        <tr><td colspan="2"><div class="cc-meta-separator">✅ Fasilitas & Bonus</div></td></tr>
        <tr>
            <th><label for="cc_features"><?php esc_html_e( 'Daftar Fasilitas', 'crediblecompany' ); ?></label></th>
            <td>
                <textarea id="cc_features" name="cc_features" placeholder="Satu fasilitas per baris:&#10;Layout&#10;2 Pilihan Cover&#10;Mock Up Promosi&#10;ISBN/QRSBN Perpusnas"><?php echo esc_textarea( $features ); ?></textarea>
                <p class="cc-meta-help">Tulis satu fasilitas per baris. Setiap baris tampil sebagai item checklist (✔) di card.</p>
            </td>
        </tr>
    </table>

    <script>
    jQuery(document).ready(function($) {
        $('.cc-upload-image-btn').click(function(e) {
            e.preventDefault();
            var btn = $(this);
            var target_id = btn.data('target');
            var image = wp.media({ 
                title: 'Upload Gambar Paket',
                multiple: false
            }).open()
            .on('select', function(e){
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $('#' + target_id).val(image_url);
                $('#' + target_id + '_preview').attr('src', image_url).show();
            });
        });

        $('.cc-clear-image-btn').click(function(e) {
            e.preventDefault();
            var btn = $(this);
            var target_id = btn.data('target');
            $('#' + target_id).val('');
            $('#' + target_id + '_preview').attr('src', '').hide();
        });
    });
    </script>
    <?php
}

/* --------------------------------------------------------------------------
 * 3. Simpan Meta Data saat Post di-Save
 * ---------------------------------------------------------------------- */
add_action( 'save_post_paket_jasa', function ( $post_id ) {
    if ( ! isset( $_POST['cc_paket_nonce'] ) || ! wp_verify_nonce( $_POST['cc_paket_nonce'], 'cc_paket_save' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Field text
    $text_fields = array( '_cc_badge', '_cc_price', '_cc_eksemplar', '_cc_ukuran', '_cc_catatan', '_cc_btn_text' );
    foreach ( $text_fields as $key ) {
        $field_name = ltrim( $key, '_' );
        if ( isset( $_POST[ $field_name ] ) ) {
            update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ $field_name ] ) );
        }
    }

    // URL tombol
    if ( isset( $_POST['cc_btn_url'] ) ) {
        update_post_meta( $post_id, '_cc_btn_url', esc_url_raw( $_POST['cc_btn_url'] ) );
    }

    // Gambar paket / poster
    if ( isset( $_POST['cc_paket_image'] ) ) {
        update_post_meta( $post_id, '_cc_paket_image', esc_url_raw( $_POST['cc_paket_image'] ) );
    }

    // Fasilitas (textarea)
    if ( isset( $_POST['cc_features'] ) ) {
        update_post_meta( $post_id, '_cc_features', sanitize_textarea_field( $_POST['cc_features'] ) );
    }
} );

/* --------------------------------------------------------------------------
 * 4. Custom Columns di Admin List Table
 * ---------------------------------------------------------------------- */
add_filter( 'manage_paket_jasa_posts_columns', function ( $columns ) {
    unset( $columns['date'] );
    $columns['cc_price']     = __( 'Harga', 'crediblecompany' );
    $columns['cc_eksemplar'] = __( 'Eksemplar', 'crediblecompany' );
    $columns['cc_badge']     = __( 'Badge', 'crediblecompany' );
    $columns['date']         = __( 'Tanggal', 'crediblecompany' );
    return $columns;
} );

add_action( 'manage_paket_jasa_posts_custom_column', function ( $column, $post_id ) {
    switch ( $column ) {
        case 'cc_price':
            echo esc_html( get_post_meta( $post_id, '_cc_price', true ) ?: '—' );
            break;
        case 'cc_eksemplar':
            echo esc_html( get_post_meta( $post_id, '_cc_eksemplar', true ) ?: '—' );
            break;
        case 'cc_badge':
            $badge = get_post_meta( $post_id, '_cc_badge', true );
            echo $badge ? '<span style="background:#c01314;color:#fff;padding:2px 8px;border-radius:3px;font-size:11px;">' . esc_html( $badge ) . '</span>' : '—';
            break;
    }
}, 10, 2 );

/* --------------------------------------------------------------------------
 * 5. Seed Data — Dipindahkan ke file terpisah untuk modularisasi.
 * Lihat: inc/seed-paket-jasa.php
 * ---------------------------------------------------------------------- */
require_once __DIR__ . '/seed-paket-jasa.php';
