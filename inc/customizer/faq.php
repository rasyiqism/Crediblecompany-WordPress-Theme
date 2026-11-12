<?php
/**
 * Customizer: FAQ Section
 *
 * @package CredibleCompany
 */

add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section( 'cc_faq_section', array(
        'title'    => __( '10. FAQ', 'crediblecompany' ),
        'panel'    => 'cc_homepage_panel',
        'priority' => 100,
    ) );

    // Custom Control Class untuk Repeater JSON
    if ( class_exists( 'WP_Customize_Control' ) ) {
        class CC_FAQ_Repeater_Control extends WP_Customize_Control {
            public $type = 'cc_faq_repeater';

            public function render_content() {
                ?>
                <label>
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                    <?php if ( ! empty( $this->description ) ) : ?>
                        <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
                    <?php endif; ?>
                </label>

                <div class="cc-faq-repeater-wrapper">
                    <!-- Textarea disembunyikan untuk menampung data JSON yg akan disimpan WP -->
                    <textarea class="cc-faq-data-hidden" style="display:none;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>

                    <div class="cc-faq-items"></div>

                    <button type="button" class="button cc-faq-add-btn" style="margin-top:10px;">+ Tambah FAQ</button>
                </div>
                <?php
            }
        }
    }

    // Default FAQ data in JSON
    $faq_defaults_json = json_encode(array(
        array( 
            'q' => 'Berapa lama estimasi proses penerbitan buku?', 
            'a' => 'Estimasi proses penerbitan berkisar antara 1 hingga 3 minggu, bergantung pada kelengkapan naskah dan antrean pendaftaran ISBN/QRSBN di Perpusnas. Kami juga menyediakan fasilitas kilat khusus untuk pengurusan ISBN dalam 1-2 hari.' 
        ),
        array( 
            'q' => 'Apakah penulis mendapatkan royalti dari penjualan buku melalui penerbit?', 
            'a' => 'Ya, penulis akan mendapatkan royalti penjualan buku sebesar 25%. Laporan penjualan buku akan disampaikan secara transparan melalui email setiap bulannya.' 
        ),
        array( 
            'q' => 'Bagaimana cara mendaftarkan naskah untuk diterbitkan?', 
            'a' => 'Anda cukup memilih paket penerbitan yang sesuai dengan kebutuhan Anda di website kami, kemudian mengirimkan naskah Anda beserta data diri melalui WhatsApp admin atau email resmi kami.' 
        ),
        array( 
            'q' => 'Keuntungan apa saja yang didapat oleh penulis saat menerbitkan buku?', 
            'a' => 'Penulis mendapatkan fasilitas gratis ongkos kirim ke seluruh Indonesia, buku terindeks di Google Scholar dan Google Playbook, laporan royalti bulanan yang transparan, hak cipta buku (HAKI) dari Kemenkumham, serta bantuan pemasaran di marketplace nasional milik KBM Indonesia.' 
        ),
        array( 
            'q' => 'Penerbit buku KBM Indonesia bisa menerbitkan buku apa saja?', 
            'a' => 'Kami menerima berbagai jenis naskah baik fiksi maupun non-fiksi (sastra dan non-sastra), seperti novel, kumpulan puisi, buku ajar/dosen, biografi, opini, hingga buku ilmiah/penelitian.' 
        ),
        array( 
            'q' => 'Apakah semua naskah yang masuk di Penerbit buku KBM Indonesia pasti terbit?', 
            'a' => 'Kami berkomitmen membantu semua penulis untuk menerbitkan karyanya. Naskah Anda pasti terbit selama tidak mengandung unsur SARA, pornografi, plagiasi, atau pelanggaran hukum lainnya.' 
        ),
        array( 
            'q' => 'Apakah setiap naskah yang akan diterbitkan akan diedit oleh penerbit?', 
            'a' => 'Ya, naskah Anda akan melewati proses layout (tata letak) dan desain cover oleh tim ahli kami sesuai dengan ketentuan paket yang Anda pilih agar buku tampil profesional dan siap cetak.' 
        ),
        array( 
            'q' => 'Bagaimana cara mengirim naskah buku ke penerbit?', 
            'a' => 'Sangat gampang, yaitu langsung dikirim naskah buku anda via email ke naskah@penerbitkbm.com atau di kirim melalui nomer whatsapp kantor pusat di 081357517526 dan buku anda akan kami rubah ukurannya sesuai keinginan dan kebutuhan penulis.' 
        ),
    ));

    // Judul Seksi FAQ
    $wp_customize->add_setting( 'cc_faq_title', array(
        'default'           => __( 'Pertanyaan yang Sering Diajukan', 'crediblecompany' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cc_faq_title', array(
        'label'   => __( 'Judul Seksi FAQ', 'crediblecompany' ),
        'section' => 'cc_faq_section',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'cc_faq_repeater_data', array(
        'default'           => $faq_defaults_json,
        'sanitize_callback' => 'cc_sanitize_faq_json',
    ) );

    $wp_customize->add_control( new CC_FAQ_Repeater_Control( $wp_customize, 'cc_faq_repeater_data', array(
        'label'       => __( 'Daftar Pertanyaan & Jawaban', 'crediblecompany' ),
        'description' => __( 'Klik Tambah FAQ untuk menambah kolom, biarkan kosong atau klik Remove untuk menghapus.', 'crediblecompany' ),
        'section'     => 'cc_faq_section',
    ) ) );

    // Pengaturan Warna Section FAQ
    $wp_customize->add_setting( 'cc_faq_bg_color', array(
        'default'           => '#c01314',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_faq_bg_color', array(
        'label'   => __( 'Warna Latar Belakang Section', 'crediblecompany' ),
        'section' => 'cc_faq_section',
    ) ) );

    $wp_customize->add_setting( 'cc_faq_title_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_faq_title_color', array(
        'label'   => __( 'Warna Font Judul Seksi', 'crediblecompany' ),
        'section' => 'cc_faq_section',
    ) ) );

    $wp_customize->add_setting( 'cc_faq_question_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_faq_question_color', array(
        'label'   => __( 'Warna Font Pertanyaan', 'crediblecompany' ),
        'section' => 'cc_faq_section',
    ) ) );

    $wp_customize->add_setting( 'cc_faq_answer_color', array(
        'default'           => '#f3f4f6',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cc_faq_answer_color', array(
        'label'   => __( 'Warna Font Jawaban', 'crediblecompany' ),
        'section' => 'cc_faq_section',
    ) ) );

} );

