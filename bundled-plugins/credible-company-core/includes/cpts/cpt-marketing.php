<?php
/**
 * Custom Post Type: Marketing.
 * Mendaftarkan CPT 'marketing' dan meta box untuk menyimpan nomor WhatsApp masing-masing marketing.
 * Data marketing akan digunakan sebagai rujukan dynamic WA url saat referral terjadi.
 *
 * Struktur data per marketing:
 * - Nama Marketing (post_title)
 * - Slug / ID Refferal (post_name) -> misal 'budi' dari `?ref=budi`
 * - Nomor WA (meta: _cc_wa_number)
 *
 * @package CredibleCompany
 */

/* --------------------------------------------------------------------------
 * 1. Register Custom Post Type Marketing
 * ---------------------------------------------------------------------- */
add_action( 'init', 'cc_register_cpt_marketing' );

/**
 * Register Custom Post Type: marketing.
 */
function cc_register_cpt_marketing() {
    // Jika fitur marketing dinonaktifkan di Customizer, jangan daftarkan CPT
    if ( ! get_theme_mod( 'cc_marketing_enable', true ) ) {
        return;
    }

    $labels = array(
        'name'               => __( 'Marketing', 'crediblecompany' ),
        'singular_name'      => __( 'Marketing', 'crediblecompany' ),
        'add_new'            => __( 'Tambah Marketing', 'crediblecompany' ),
        'add_new_item'       => __( 'Tambah Marketing Baru', 'crediblecompany' ),
        'edit_item'          => __( 'Edit Marketing', 'crediblecompany' ),
        'new_item'           => __( 'Marketing Baru', 'crediblecompany' ),
        'all_items'          => __( 'Semua Marketing', 'crediblecompany' ),
        'view_item'          => __( 'Lihat Marketing', 'crediblecompany' ),
        'search_items'       => __( 'Cari Marketing', 'crediblecompany' ),
        'not_found'          => __( 'Tidak ada marketing ditemukan.', 'crediblecompany' ),
        'not_found_in_trash' => __( 'Tidak ada marketing di Trash.', 'crediblecompany' ),
        'menu_name'          => __( 'Tim Marketing', 'crediblecompany' ),
    );

    register_post_type( 'marketing', array(
        'labels'        => $labels,
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_icon'     => 'dashicons-businessman',
        'menu_position' => 26,
        'supports'      => array( 'title' ),
        'has_archive'   => false,
        'rewrite'       => false,
    ) );
}

/* --------------------------------------------------------------------------
 * 1.5. Custom Placeholder 'Add title'
 * ---------------------------------------------------------------------- */
add_filter( 'enter_title_here', 'cc_marketing_title_placeholder', 10, 2 );

/**
 * Ganti placeholder 'Add title' di editor Marketing.
 *
 * @param string  $title Teks placeholder default.
 * @param WP_Post $post  Post object.
 * @return string
 */
function cc_marketing_title_placeholder( $title, $post ) {
    if ( 'marketing' === $post->post_type ) {
        return __( 'Nama Marketing: Thanos', 'crediblecompany' );
    }
    return $title;
}

/* --------------------------------------------------------------------------
 * 2. Meta Box — Detail Kontak
 * ---------------------------------------------------------------------- */
add_action( 'add_meta_boxes', 'cc_marketing_add_meta_boxes' );

/**
 * Daftarkan meta box untuk CPT marketing.
 */
function cc_marketing_add_meta_boxes() {
    add_meta_box(
        'cc_marketing_detail',
        __( 'Info Kontak Marketing', 'crediblecompany' ),
        'cc_render_marketing_meta_box',
        'marketing',
        'normal',
        'high'
    );
}

/**
 * Render isi meta box field kontak marketing
 *
 * @param WP_Post $post Post object.
 */
function cc_render_marketing_meta_box( $post ) {
    wp_nonce_field( 'cc_marketing_save', 'cc_marketing_nonce' );

    $wa_number = get_post_meta( $post->ID, '_cc_wa_number', true );
    $wa_text   = get_post_meta( $post->ID, '_cc_wa_text', true );

    // Tampilkan informasi URL slug yang digunakan untuk URL tracking
    $slug = $post->post_name;
    $ref_url = get_site_url() . '/?ref=' . $slug;
    ?>
    <style>
        .cc-marketing-table { width: 100%; border-collapse: collapse; }
        .cc-marketing-table th { text-align: left; padding: 10px 10px 10px 0; width: 150px; }
        .cc-marketing-table td { padding: 8px 0; }
        .cc-marketing-table input[type="text"] { width: 100%; max-width: 400px; padding: 6px 10px; }
        .cc-marketing-table textarea { width: 100%; max-width: 400px; min-height: 80px; padding: 6px 10px; font-family: monospace; }
        .cc-meta-help { color: #666; font-size: 13px; margin-top: 4px; }
        .cc-info-box { background: #f0f0f1; border-left: 4px solid #00a0d2; padding: 12px; margin-bottom: 20px; font-size: 14px; }
    </style>

    <?php if ( $slug ) : ?>
    <div class="cc-info-box">
        <strong>URL Referral:</strong> <br>
        <code><?php echo esc_html( $ref_url ); ?></code><br>
        <span class="cc-meta-help">Berikan URL ini kepada marketing tersebut. Siapapun yang mengklik URL ini, tombol-tombol WA akan otomatis diarahkan ke nomor marketing ini.</span>
    </div>
    <?php else: ?>
    <div class="cc-info-box">
        Silakan Simpan/Terbitkan marketing ini terlebih dahulu untuk melihat URL Referral-nya.
    </div>
    <?php endif; ?>

    <table class="cc-marketing-table">
        <tr>
            <th><label for="cc_wa_number"><?php esc_html_e( 'Nomor WhatsApp', 'crediblecompany' ); ?></label></th>
            <td>
                <input type="text" id="cc_wa_number" name="cc_wa_number" value="<?php echo esc_attr( $wa_number ); ?>" placeholder="Contoh: 628123456789">
                <p class="cc-meta-help">Awali dengan <b>62</b>. Tolong hilangkan karakter plus (+) dan spasi/koma/strip.</p>
            </td>
        </tr>
        <tr>
            <th><label for="cc_wa_text"><?php esc_html_e( 'Teks Pembuka WA (Opsional)', 'crediblecompany' ); ?></label></th>
            <td>
                <textarea id="cc_wa_text" name="cc_wa_text" placeholder="Halo kak {Nama Marketing}, saya tertarik pendaftaran..."><?php echo esc_textarea( $wa_text ); ?></textarea>
                <p class="cc-meta-help">Pesan default ini akan mereset parameter pesan di seluruh tombol website Anda ke WhatsApp agen ini. Anda dapat menyisipkan kata <code>{Nama Marketing}</code> di dalamnya.</p>
            </td>
        </tr>
    </table>
    <?php
}

/* --------------------------------------------------------------------------
 * 3. Simpan Meta Data
 * ---------------------------------------------------------------------- */
add_action( 'save_post_marketing', 'cc_marketing_save_meta', 10, 1 );

/**
 * Simpan meta data marketing saat post disave.
 *
 * @param int $post_id ID post.
 */
function cc_marketing_save_meta( $post_id ) {
    if ( ! isset( $_POST['cc_marketing_nonce'] ) || ! wp_verify_nonce( $_POST['cc_marketing_nonce'], 'cc_marketing_save' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['cc_wa_number'] ) ) {
        // Bersihkan semua karakter non-angka
        $clean_wa = preg_replace( '/[^0-9]/', '', sanitize_text_field( $_POST['cc_wa_number'] ) );
        update_post_meta( $post_id, '_cc_wa_number', $clean_wa );
    }

    if ( isset( $_POST['cc_wa_text'] ) ) {
        update_post_meta( $post_id, '_cc_wa_text', sanitize_textarea_field( $_POST['cc_wa_text'] ) );
    }
}

/* --------------------------------------------------------------------------
 * 4. Custom Columns Admin
 * ---------------------------------------------------------------------- */
add_filter( 'manage_marketing_posts_columns', 'cc_marketing_posts_columns' );

/**
 * Tambahkan kolom kustom di daftar post marketing.
 *
 * @param array $columns Kolom default.
 * @return array
 */
function cc_marketing_posts_columns( $columns ) {
    unset( $columns['date'] );
    $columns['cc_wa_number'] = __( 'Nomor WhatsApp', 'crediblecompany' );
    $columns['cc_ref_url']   = __( 'URL Referral', 'crediblecompany' );
    $columns['date']         = __( 'Tanggal', 'crediblecompany' );
    return $columns;
}

add_action( 'manage_marketing_posts_custom_column', 'cc_marketing_custom_column', 10, 2 );

/**
 * Isi konten kolom kustom marketing.
 *
 * @param string $column  Nama kolom.
 * @param int    $post_id ID post.
 */
function cc_marketing_custom_column( $column, $post_id ) {
    switch ( $column ) {
        case 'cc_wa_number':
            $ph = get_post_meta( $post_id, '_cc_wa_number', true );
            echo $ph ? '<code>+' . esc_html( $ph ) . '</code>' : '—';
            break;
        case 'cc_ref_url':
            $post_obj = get_post( $post_id );
            $url      = get_site_url() . '/?ref=' . $post_obj->post_name;
            echo '<code>?ref=' . esc_html( $post_obj->post_name ) . '</code><br>';
            echo '<a href="' . esc_url( $url ) . '" target="_blank" style="font-size:11px;">Test Link ↗</a>';
            break;
    }
}
