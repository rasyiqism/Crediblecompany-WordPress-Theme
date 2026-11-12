<?php
/**
 * Seed Data — Paket Jasa Default
 *
 * File ini hanya dijalankan saat tema pertama kali diaktifkan (after_switch_theme)
 * atau jika data belum pernah di-seed (opsi 'cc_paket_jasa_seeded' belum ada).
 *
 * Untuk reset seed secara manual: hapus opsi 'cc_paket_jasa_seeded' dari tabel wp_options.
 * Atau gunakan WP-CLI: wp option delete cc_paket_jasa_seeded
 *
 * @package CredibleCompany
 */

/**
 * Daftarkan seed hanya satu kali — saat tema diaktifkan.
 * Menggunakan 'after_switch_theme' agar seed tidak berjalan setiap boot.
 */
add_action( 'after_switch_theme', 'cc_seed_paket_jasa' );

/**
 * Jalankan seed via init HANYA jika opsi belum ada sama sekali.
 * Guard ganda: fungsi cc_seed_paket_jasa() sendiri juga cek opsi di dalamnya.
 * Ini memastikan seed tetap berjalan pada instalasi fresh yang melewatkan after_switch_theme
 * (misalnya saat tema diinstall via WP-CLI tanpa activate event).
 */
if ( ! get_option( 'cc_paket_jasa_seeded' ) ) {
    add_action( 'init', 'cc_seed_paket_jasa', 20 );
}

/**
 * Seed paket jasa default dengan data placeholder.
 * Aman untuk dijalankan berkali-kali — fungsi idempoten via opsi flag.
 *
 * @return void
 */
function cc_seed_paket_jasa() {
    // Guard: hanya jalankan sekali seumur hidup instalasi
    if ( get_option( 'cc_paket_jasa_seeded' ) ) {
        return;
    }

    // Hapus paket jasa yang mungkin ada dari instalasi sebelumnya
    $existing = get_posts( array(
        'post_type'      => 'paket_jasa',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'fields'         => 'ids',
    ) );
    foreach ( $existing as $post_id ) {
        wp_delete_post( $post_id, true ); // true = bypass trash
    }

    // Data placeholder — sesuaikan via dashboard admin setelah tema aktif
    $seed_packages = array(
        array(
            'title'     => 'Terima Beres',
            'badge'     => 'Populer',
            'price'     => 'Rp 1.250.000',
            'eksemplar' => '5 Eksemplar',
            'ukuran'    => 'A5/Unesco',
            'catatan'   => 'Layanan ini berlaku untuk 150 Halaman. Jika lebih, dikenai tarif tambahan.',
            'btn_text'  => 'Ambil Paket',
            'btn_url'   => '#',
            'features'  => "Sertifikat Hak Cipta Buku Dari Kemenkumham\nLayout\n1 Cover 2 Model\nMock Up Promosi\nWrapping/Pembungkus Buku\nLem Buku Sangat Kuat\nLaminasi Doff & Glossy\nISBN/QRSBN Perpusnas/QRCBN\nSerah Simpan Ke Perpusnas RI\nBebas Revisi 2 Kali\nLembar Pemisah Halaman\nKeanggotaan IKAPI\nTerindeks di Google Playbook\nTerindeks di Google Schoolar\nDi Upload Repository OMP\nSertifikat Penulis\nRoyalty 25% Penjualan\nGratis 100% Ongkos Kirim Buku ke Seluruh Indonesia\nFile Ebook",
        ),
        array(
            'title'     => 'Mataram',
            'badge'     => 'Premium',
            'price'     => 'Rp 1.406.250',
            'eksemplar' => '25 Eksemplar',
            'ukuran'    => 'A5/Unesco',
            'catatan'   => 'Layanan ini berlaku untuk 150 Halaman. Jika lebih, dikenai tarif tambahan.',
            'btn_text'  => 'Ambil Paket',
            'btn_url'   => '#',
            'features'  => "Layout\n2 Pilihan Cover\nMock Up Promosi\nWrapping/Pembungkus Buku\nLem Buku Sangat Kuat\nLaminasi Doff & Glossy\nISBN/ QRSBN Perpusnas/QRCBN\nSerah Simpan Ke Perpusnas RI\nBebas Revisi 3 Kali\nLembar Pemisah Halaman\nKeanggotaan IKAPI\nTerindeks di Google Playbook\nTerindeks di Google Schoolar\nUpload di Repository OMP\nSertifikat Penulis\nRoyalty 25% Penjualan\nFile Pdf-Ebook\nGratis 100% Ongkos Kirim Buku ke Seluruh Indonesia\nTambahan 2 Eksemplar",
        ),
        array(
            'title'     => 'Majapahit',
            'badge'     => 'Premium',
            'price'     => 'Rp 2.250.000',
            'eksemplar' => '50 Eksemplar',
            'ukuran'    => 'A5/Unesco',
            'catatan'   => 'Layanan ini berlaku untuk 150 Halaman. Jika lebih, dikenai tarif tambahan.',
            'btn_text'  => 'Ambil Paket',
            'btn_url'   => '#',
            'features'  => "Layout\n2 Pilihan Cover\nMock Up Promosi\nWrapping/Pembungkus Buku\nLem Buku Sangat Kuat\nLaminasi Doff & Glossy\nISBN/QRSBN Perpusnas/QRCBN\nSerah Simpan Ke Perpusnas RI\nBebas Revisi 3 Kali\nLembar Pemisah Halaman\nKeanggotaan IKAPI\nDi Upload di Repository OMP\nTerindek di Google Schoolar\nTerindek di Google Playbook\nSertifikat Penulis\nPasang Logo\nRoyalty 25% Penjualan\nFile Pdf-Ebook\nGratis 100% Ongkos Kirim Buku ke Seluruh Indonesia\nTambahan 2 Eksemplar",
        ),
        array(
            'title'     => 'Pajajaran',
            'badge'     => 'Best Promo',
            'price'     => 'Rp 4.687.000',
            'eksemplar' => '100 Eksemplar',
            'ukuran'    => 'A5/Unesco',
            'catatan'   => 'Layanan ini berlaku untuk 150 Halaman. Jika lebih, dikenai tarif tambahan.',
            'btn_text'  => 'Ambil Paket',
            'btn_url'   => '#',
            'features'  => "1 Kaos berlogo KBM\nLayout\n2 Pilihan Cover\nMock Up Promosi\nWrapping/Pembungkus Buku\nLem Buku Sangat Kuat\nLaminasi Doff & Glossy\nISBN/QRSBN Perpusnas/QRCBN\nSerah Simpan Ke Perpusnas RI\nBebas Revisi 3 Kali\nLembar Pemisah Halaman\nKeanggotaan IKAPI\nFile Pdf-Ebook\nTerindeks di Google Schoolar\nTerindeks di Google Playbook\nDi Upload di Repository OMP\nPasang Logo\nRoyalty 25% Penjualan\nSertifikat Penulis\nGratis 100% Ongkos Kirim Buku ke Seluruh Indonesia\nTambahan 2 Eksemplar",
        ),
        array(
            'title'     => 'Gowa Tallo',
            'badge'     => 'Best Promo',
            'price'     => 'Rp 7.375.000',
            'eksemplar' => '200 Eksemplar',
            'ukuran'    => 'A5/Unesco',
            'catatan'   => 'Layanan ini berlaku untuk 150 Halaman. Jika lebih, dikenai tarif tambahan.',
            'btn_text'  => 'Ambil Paket',
            'btn_url'   => '#',
            'features'  => "Tambahan 5 Eksemplar\n1 Kaos berlogo KBM\nLayout\n2 Pilihan Cover\nMock Up Promosi\n1 Hal Warna\nWrapping/Pembungkus Buku\nLem Buku Sangat Kuat\nLaminasi Doff & Glossy,\nISBN/QRSBN Perpusnas/QRCBN\nSerah Simpan Ke Perpusnas RI\nBebas Revisi 3 Kali\nLembar Pemisah Halaman\nKeanggotaan IKAPI\nFile Pdf-Ebook\nTerindeks di Google Schoolar\nTerindeks di Google Playbook\nDi Upload di Repository OMP\nPasang Logo\nRoyalty 25% Penjualan\nSertifikat Penulis\nGratis 100% Ongkos Kirim Buku ke Seluruh Indonesia",
        ),
        array(
            'title'     => 'Samudera Pasai',
            'badge'     => 'Best Promo',
            'price'     => 'Rp 9.375.000',
            'eksemplar' => '300 Eksemplar',
            'ukuran'    => 'A5/Unesco',
            'catatan'   => 'Layanan ini berlaku untuk 150 Halaman. Jika lebih, dikenai tarif tambahan.',
            'btn_text'  => 'Ambil Paket',
            'btn_url'   => '#',
            'features'  => "Tambahan 7 Eksemplar\n1 Kaos berlogo KBM\nLayout\n2 Pilihan Cover\nMock Up Promosi\n2 Hal Warna\nWrapping/Pembungkus Buku\nLem Buku Sangat Kuat\nLaminasi Doff & Glossy,\nISBN/ QRSBN Perpusnas/QRCBN\nSerah Simpan Ke Perpusnas RI\nBebas Revisi 3 Kali\nLembar Pemisah Halaman\nKeanggotaan IKAPI\nTerindeks di Google Playbook\nTerindeks di Google Schoolar\nDi Upload di Repository OMP\nSertifikat Penulis\nPasang Logo\nRoyalty 25% Penjualan\nFile Ebook\nGratis 100% Ongkos Kirim Buku ke Seluruh Indonesia",
        ),
        array(
            'title'     => 'Kutai Kartanegara',
            'badge'     => 'Premium',
            'price'     => 'Rp12.500.000',
            'eksemplar' => '500 Eksemplar',
            'ukuran'    => 'A5/Unesco',
            'catatan'   => 'Layanan ini berlaku untuk 150 Halaman. Jika lebih, dikenai tarif tambahan.',
            'btn_text'  => 'Ambil Paket',
            'btn_url'   => '#',
            'features'  => "2 Kaos berlogo KBM\nLayout\n2 Pilihan Cover\n2 Mockup Promosi\n2 Hal Warna\nWrapping/Pembungkus Buku\nLem Buku Sangat Kuat\nLaminasi Doff & Glossy\nISBN/QRSBN/QRCBN Perpusnas\nSerah Simpan Ke Perpusnas RI\nBebas Revisi 3 kali\nLembar Pemisah Halaman\nKeanggotaan IKAPI\nFile Pdf-Ebook\nDi Upload di Repository OMP\nTerindeks di oogle Schoolar\nTerindeks di Google Playbook\nSertifikat Penulis\nPasang Logo\nRoyalty 25% Penjualan\nGratis 100% Ongkos Kirim Buku\nTambahan 10 Eksemplar",
        ),
        array(
            'title'     => 'Ebook',
            'badge'     => 'Pemula',
            'price'     => 'Rp 250.000 (Sastra), Rp 400.000 (Non Sastra)',
            'eksemplar' => '',
            'ukuran'    => 'A5/Unesco',
            'catatan'   => 'Layanan ini berlaku untuk 200 Halaman. Jika lebih, dikenai tarif tambahan.',
            'btn_text'  => 'Ambil Paket',
            'btn_url'   => '#',
            'features'  => "Layout\n1 Cover\nMock Up Promosi\nQRSBN Perpusnas/QRCBN/ISBN\nSerah Simpan Ke Perpusnas RI\nBebas Revisi 2 Kali\nRoyalty / Bagi hasil 25%\nKeanggotaan IKAPI\nFile Pdf-Ebook berwatermark\nDi Upload di Repository OMP\nTerindek di Google Schoolar\nTerindek di Google Playbook\nDi Upload di Repository OMP\nSertifikat Penulis\nDapat Diajukan Hak Cipta Buku (HAKI) Dari Kemenkumham",
        ),
        array(
            'title'     => 'Nusantara New',
            'badge'     => 'Populer',
            'price'     => 'Rp 750.000',
            'eksemplar' => '5 Eksemplar',
            'ukuran'    => 'A5/Unesco',
            'catatan'   => 'Layanan ini berlaku untuk 150 Halaman. Jika lebih, dikenai tarif tambahan.',
            'btn_text'  => 'Ambil Paket',
            'btn_url'   => '#',
            'features'  => "Gratis 100% Ongkos Kirim Buku ke Seluruh Indonesia\nLayout\n1 Cover 2 Model\nMock Up Promosi\nWrapping/Pembungkus Buku\nLem Buku Sangat Kuat\nLaminasi Doff & Glossy\nISBN/QRSBN/QRCBN\nSerah Simpan Ke Perpusnas RI\nBebas Revisi 2 Kali\nLembar Pemisah Halaman\nKeanggotaan IKAPI\nFile Pdf-Ebook\nRoyalty 25% Penjualan\nTerindeks di Google Schoolar\nTerindeks di Google Playbook\nDi upload di Repository OMP\nSertifikat Penulis",
        ),
    );

    foreach ( $seed_packages as $order => $pkg ) {
        $post_id = wp_insert_post( array(
            'post_title'  => $pkg['title'],
            'post_type'   => 'paket_jasa',
            'post_status' => 'publish',
            'menu_order'  => $order + 1,
        ) );

        if ( ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, '_cc_badge',     $pkg['badge'] );
            update_post_meta( $post_id, '_cc_price',     $pkg['price'] );
            update_post_meta( $post_id, '_cc_eksemplar', $pkg['eksemplar'] );
            update_post_meta( $post_id, '_cc_ukuran',    $pkg['ukuran'] );
            update_post_meta( $post_id, '_cc_catatan',   $pkg['catatan'] );
            update_post_meta( $post_id, '_cc_btn_text',  $pkg['btn_text'] );
            update_post_meta( $post_id, '_cc_btn_url',   $pkg['btn_url'] );
            update_post_meta( $post_id, '_cc_features',  $pkg['features'] );
        }
    }

    // Tandai bahwa seed sudah selesai — tidak akan dijalankan lagi
    update_option( 'cc_paket_jasa_seeded', true, false ); // false = tidak autoload
}
