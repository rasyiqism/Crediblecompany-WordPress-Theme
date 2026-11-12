<?php
/**
 * Custom Post Type: Testimoni (Built-in di tema).
 *
 * Kompatibel 100% dengan plugin "Customer Says":
 * - Slug CPT: 'testimoni' (sama persis)
 * - Meta keys: _customer_name, _customer_city, _customer_profession, _customer_rating
 * - Featured Image = foto profil
 * - Post Content (editor) = kutipan testimoni
 *
 * Jika plugin Customer Says masih aktif, tema TIDAK akan mendaftarkan CPT
 * (menghindari konflik). Data di database tetap aman karena key identik.
 *
 * @package CredibleCompany
 */

/* --------------------------------------------------------------------------
 * 1. Register CPT — hanya jika plugin Customer Says TIDAK aktif
 * ---------------------------------------------------------------------- */
add_action( 'init', function () {
    // Jika seksi testimoni dinonaktifkan di Customizer, jangan daftarkan CPT
    if ( ! get_theme_mod( 'cc_testimonials_enable', true ) ) {
        return;
    }

    // Jika plugin customer-says sudah aktif, skip registrasi CPT
    if ( post_type_exists( 'testimoni' ) ) {
        return;
    }

    $labels = array(
        'name'               => __( 'Testimoni', 'crediblecompany' ),
        'singular_name'      => __( 'Testimoni', 'crediblecompany' ),
        'add_new'            => __( 'Tambah Baru', 'crediblecompany' ),
        'add_new_item'       => __( 'Tambah Testimoni Baru', 'crediblecompany' ),
        'edit_item'          => __( 'Edit Testimoni', 'crediblecompany' ),
        'new_item'           => __( 'Testimoni Baru', 'crediblecompany' ),
        'all_items'          => __( 'Semua Testimoni', 'crediblecompany' ),
        'view_item'          => __( 'Lihat Testimoni', 'crediblecompany' ),
        'search_items'       => __( 'Cari Testimoni', 'crediblecompany' ),
        'not_found'          => __( 'Tidak ada testimoni ditemukan.', 'crediblecompany' ),
        'not_found_in_trash' => __( 'Tidak ada testimoni di Trash.', 'crediblecompany' ),
        'featured_image'     => __( 'Foto Profil', 'crediblecompany' ),
        'set_featured_image' => __( 'Atur foto profil', 'crediblecompany' ),
        'menu_name'          => __( 'Testimoni', 'crediblecompany' ),
    );

    register_post_type( 'testimoni', array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-format-quote',
        'menu_position'      => 26,
        'supports'           => array( 'title', 'editor', 'thumbnail' ),
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'testimoni', 'with_front' => false ),
        'show_in_rest'       => true,
    ) );
}, 20 ); // Priority 20: jalankan setelah plugin (priority default = 10)

/**
 * Paksa rute untuk halaman Submit Testimoni agar tidak terjebak prefix /blog/
 */
add_action( 'init', function() {
    add_rewrite_rule( '^submit-testimoni/?$', 'index.php?pagename=submit-testimoni', 'top' );
}, 30 );

/* --------------------------------------------------------------------------
 * 2. Meta Box — Informasi Pelanggan
 *    Meta keys IDENTIK dengan plugin Customer Says agar data kompatibel.
 * ---------------------------------------------------------------------- */
add_action( 'add_meta_boxes', function () {
    // Jika plugin customer-says aktif, skip (plugin sudah punya meta box sendiri)
    if ( class_exists( 'Customer_Says_Metabox' ) ) {
        return;
    }

    add_meta_box(
        'cc_testimoni_detail',
        __( 'Informasi Pelanggan', 'crediblecompany' ),
        'cc_render_testimoni_meta_box',
        'testimoni',
        'normal',
        'high'
    );
} );

/**
 * Render meta box testimoni.
 *
 * @param WP_Post $post Post object.
 */
function cc_render_testimoni_meta_box( $post ) {
    wp_nonce_field( 'cc_testimoni_save', 'cc_testimoni_nonce' );

    // Menggunakan meta key yang SAMA dengan plugin customer-says
    $name       = get_post_meta( $post->ID, '_customer_name', true );
    $city       = get_post_meta( $post->ID, '_customer_city', true );
    $profession = get_post_meta( $post->ID, '_customer_profession', true );
    $rating     = get_post_meta( $post->ID, '_customer_rating', true );
    ?>
    <style>
        .cc-testi-meta label { display: block; font-weight: 600; margin-bottom: 4px; }
        .cc-testi-meta p { margin-bottom: 14px; }
        .cc-testi-meta input, .cc-testi-meta select { width: 100%; padding: 6px 10px; }
    </style>

    <div class="cc-testi-meta">
        <p>
            <label for="customer_name"><?php esc_html_e( 'Nama Lengkap:', 'crediblecompany' ); ?></label>
            <input type="text" id="customer_name" name="customer_name" value="<?php echo esc_attr( $name ); ?>">
        </p>
        <p>
            <label for="customer_profession"><?php esc_html_e( 'Profesi / Jabatan:', 'crediblecompany' ); ?></label>
            <input type="text" id="customer_profession" name="customer_profession" value="<?php echo esc_attr( $profession ); ?>" placeholder="Dosen, Penulis, Guru, dll.">
        </p>
        <p>
            <label for="customer_city"><?php esc_html_e( 'Kota:', 'crediblecompany' ); ?></label>
            <input type="text" id="customer_city" name="customer_city" value="<?php echo esc_attr( $city ); ?>">
        </p>
        <p>
            <label for="customer_rating"><?php esc_html_e( 'Rating:', 'crediblecompany' ); ?></label>
            <select id="customer_rating" name="customer_rating">
                <option value=""><?php esc_html_e( 'Pilih Rating', 'crediblecompany' ); ?></option>
                <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                    <option value="<?php echo $i; ?>" <?php selected( $rating, $i ); ?>>
                        <?php echo str_repeat( '⭐', $i ); ?>
                    </option>
                <?php endfor; ?>
            </select>
        </p>
    </div>
    <?php
}

/* --------------------------------------------------------------------------
 * 3. Simpan Meta Data
 * ---------------------------------------------------------------------- */
add_action( 'save_post_testimoni', function ( $post_id ) {
    // Jika plugin customer-says aktif, biarkan plugin yang handle save
    if ( class_exists( 'Customer_Says_Metabox' ) ) {
        return;
    }

    if ( ! isset( $_POST['cc_testimoni_nonce'] ) || ! wp_verify_nonce( $_POST['cc_testimoni_nonce'], 'cc_testimoni_save' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Simpan dengan meta key yang SAMA: _customer_*
    $fields = array( 'customer_name', 'customer_city', 'customer_profession', 'customer_rating' );
    foreach ( $fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, '_' . $field, sanitize_text_field( $_POST[ $field ] ) );
        }
    }
} );

/* --------------------------------------------------------------------------
 * 4. Custom Columns di Admin List
 * ---------------------------------------------------------------------- */
add_filter( 'manage_testimoni_posts_columns', function ( $columns ) {
    // Jangan override jika plugin aktif (plugin punya kolom sendiri)
    if ( class_exists( 'Customer_Says_Admin_Columns' ) ) {
        return $columns;
    }

    unset( $columns['date'] );
    $columns['cc_name']   = __( 'Nama', 'crediblecompany' );
    $columns['cc_city']   = __( 'Kota', 'crediblecompany' );
    $columns['cc_rating'] = __( 'Rating', 'crediblecompany' );
    $columns['date']      = __( 'Tanggal', 'crediblecompany' );
    return $columns;
} );

add_action( 'manage_testimoni_posts_custom_column', function ( $column, $post_id ) {
    switch ( $column ) {
        case 'cc_name':
            echo esc_html( get_post_meta( $post_id, '_customer_name', true ) ?: '—' );
            break;
        case 'cc_city':
            echo esc_html( get_post_meta( $post_id, '_customer_city', true ) ?: '—' );
            break;
        case 'cc_rating':
            $r = intval( get_post_meta( $post_id, '_customer_rating', true ) );
            echo $r ? str_repeat( '⭐', $r ) : '—';
            break;
    }
}, 10, 2 );
