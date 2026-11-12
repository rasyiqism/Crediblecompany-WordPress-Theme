<?php
/**
 * Halaman Hasil Pencarian
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
        <div class="app-page-title" style="font-size: 1.1rem; font-weight: 600; text-align: center; color: var(--text-dark); margin: 0;">
            <?php
            /* translators: %s: query pencarian */
            printf( esc_html__( 'Hasil Pencarian: %s', 'crediblecompany' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
            ?>
        </div>
    </div>

    <!-- Menambahkan form pencarian di atas hasil, in case user want to search again -->
    <div class="search-page-header" style="padding: 1rem 1.25rem 0.5rem; display: flex; flex-direction: column; align-items: center;">
        <?php get_search_form(); ?>

        <?php if ( have_posts() ) : ?>
            <p class="search-results-count" style="margin-top: 1.5rem; margin-bottom: 0; font-size: 0.95rem; font-weight: 500; color: var(--text-dark); text-align: center; width: 100%;">
                <?php
                global $wp_query;
                printf( esc_html__( 'Ditemukan %d hasil.', 'crediblecompany' ), intval( $wp_query->found_posts ) );
                ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="app-feed-container" style="padding-top: 1rem;">
        <?php if ( have_posts() ) : ?>

            <?php while ( have_posts() ) : the_post(); ?>
                <?php if ( 'post' === get_post_type() ) : ?>
                    <?php get_template_part( 'template-parts/card-post', null, array( 'style' => 'app' ) ); ?>
                <?php elseif ( 'testimoni' === get_post_type() ) : ?>
                    <?php get_template_part( 'template-parts/card-testimoni', null, array( 'is_link' => true ) ); ?>
                <?php else : ?>
                    <article class="app-post-card">
                        <a href="<?php echo esc_url( get_permalink() ); ?>" class="card-link-overlay"></a>
                        <div class="card-content">
                            <h2 class="card-title"><?php the_title(); ?></h2>
                            <p class="card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 15, '...' ) ); ?></p>
                        </div>
                    </article>
                <?php endif; ?>
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
            <div class="app-no-results" style="text-align: center; padding: 3rem 1rem;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🔍</div>
                <h3 style="font-size: 1.25rem; color: var(--text-dark); margin-bottom: 0.5rem;"><?php esc_html_e( 'Tidak ada hasil yang ditemukan', 'crediblecompany' ); ?></h3>
                <p style="color: var(--text-muted);"><?php esc_html_e( 'Maaf, tetapi tidak ada yang cocok dengan istilah pencarian Anda. Silakan coba lagi dengan beberapa kata kunci yang berbeda.', 'crediblecompany' ); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
