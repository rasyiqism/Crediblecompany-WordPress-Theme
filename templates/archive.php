<?php
/**
 * Halaman Arsip Kategori, Tanggal, Tag (Blog)
 * Berdesain App-Mobile First
 *
 * @package CredibleCompany
 */

get_header(); ?>

<div class="app-main-content">
    <div class="app-header-bar">
        <!-- Tombol kembali gaya App -->
        <a href="javascript:history.back()" class="app-back-btn">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div class="app-page-title" style="font-size: 1.1rem; font-weight: 600; text-align: center; color: var(--text-dark); margin: 0; padding: 0 10px;">
            <?php
            if ( is_category() ) {
                single_cat_title();
            } elseif ( is_tag() ) {
                single_tag_title();
            } elseif ( is_author() ) {
                echo '<span class="vcard">' . esc_html( get_the_author() ) . '</span>';
            } elseif ( is_day() ) {
                echo esc_html( get_the_date() );
            } elseif ( is_month() ) {
                echo esc_html( get_the_date( _x( 'F Y', 'monthly archives date format', 'crediblecompany' ) ) );
            } elseif ( is_year() ) {
                echo esc_html( get_the_date( _x( 'Y', 'yearly archives date format', 'crediblecompany' ) ) );
            } else {
                esc_html_e( 'Arsip', 'crediblecompany' );
            }
            ?>
        </div>
    </div>

    <div class="app-feed-container">
        <?php if ( have_posts() ) : ?>

            <?php while ( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'template-parts/card-post', null, array( 'style' => 'app' ) ); ?>
            <?php endwhile; ?>

            <?php
            // Pagination App Style
            echo '<div class="app-pagination">';
            echo paginate_links( array(
                'prev_text' => '&laquo; Prev',
                'next_text' => 'Next &raquo;',
            ) );
            echo '</div>';
            ?>

        <?php else : ?>
            <div class="app-no-results">
                <h3>Belum ada artikel.</h3>
                <p>Tidak ada post yang ditemukan pada arsip ini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
