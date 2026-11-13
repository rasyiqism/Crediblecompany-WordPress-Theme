<?php
/**
 * Laman Arsip Testimoni
 * Berdesain App-Mobile First
 *
 * @package CredibleCompany
 */

get_header(); ?>

<style>
/* Inline styling to ensure immediate render bypassing CDN caches */
.app-testi-search-wrapper {
    grid-column: 1 / -1 !important;
    width: 100% !important;
    max-width: 1200px !important;
    margin: 0 auto 16px auto !important;
    padding: 10px 20px 0 !important;
    box-sizing: border-box !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
}
.app-testi-search-form {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
    max-width: 580px;
    margin: 0 auto;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 9999px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    transition: all 0.2s ease;
    overflow: hidden;
    box-sizing: border-box;
}
.app-testi-search-form:focus-within {
    border-color: var(--brand-red, #c01314);
    box-shadow: 0 0 0 3px rgba(192, 19, 20, 0.12);
}
.app-testi-search-icon {
    position: absolute;
    left: 16px;
    color: #94a3b8;
    display: flex;
    align-items: center;
    pointer-events: none;
    z-index: 2;
}
.app-testi-search-input {
    width: 100%;
    padding: 12px 50px 12px 46px;
    border: none;
    background: transparent;
    font-size: 0.95rem;
    color: #1e293b;
    outline: none;
    font-family: inherit;
}
.app-testi-search-input::placeholder {
    color: #94a3b8;
}
.app-testi-search-btn {
    position: absolute;
    right: 6px;
    top: 50%;
    transform: translateY(-50%);
    background: var(--brand-red, #c01314);
    color: #ffffff;
    border: none;
    border-radius: 9999px;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s ease;
    z-index: 2;
}
.app-testi-search-btn:hover {
    background: #990f10;
}
.app-testi-search-notice {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    max-width: 580px;
    margin: 10px auto 0 auto;
    padding: 10px 16px;
    background: #fff5f5;
    border: 1px solid #fed7d7;
    border-radius: 10px;
    font-size: 0.875rem;
    color: #2d3748;
    box-sizing: border-box;
}
.app-testi-reset-link {
    color: var(--brand-red, #c01314);
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.app-testi-reset-link:hover {
    text-decoration: underline;
}
.btn-card-edit-testi {
    position: absolute;
    top: 12px;
    right: 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 9px;
    background: rgba(248, 250, 252, 0.95);
    border: 1px solid #e2e8f0;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    text-decoration: none;
    z-index: 10;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
.btn-card-edit-testi:hover {
    background: var(--brand-red, #c01314);
    border-color: var(--brand-red, #c01314);
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(192, 19, 20, 0.25);
    transform: translateY(-1px);
}
.app-action-row {
    grid-column: 1 / -1 !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    width: 100% !important;
    margin: 0 auto 24px auto !important;
}
</style>

<section class="app-main-content">
    <div class="app-header-bar">
        <a href="javascript:history.back()" class="app-back-btn">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div class="app-page-title" style="font-size: 1.1rem; font-weight: 600; text-align: center; color: var(--text-dark); margin: 0; padding: 0 10px;">
            <?php 
            if ( is_search() ) {
                printf( esc_html__( 'Cari: %s', 'credible-company' ), esc_html( get_search_query() ) );
            } else {
                post_type_archive_title(); 
            }
            ?>
        </div>
    </div>

    <div class="app-feed-container">
        <!-- Form Pencarian Testimoni Berdasarkan Nama -->
        <div class="app-testi-search-wrapper">
            <form role="search" method="get" class="app-testi-search-form" action="<?php echo esc_url( get_post_type_archive_link( 'testimoni' ) ); ?>">
                <input type="hidden" name="post_type" value="testimoni">
                <span class="app-testi-search-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="search" class="app-testi-search-input" placeholder="Cari testimoni berdasarkan nama..." value="<?php echo esc_attr( get_search_query() ); ?>" name="s" autocomplete="off" />
                <button type="submit" class="app-testi-search-btn" aria-label="Cari">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>
            <?php if ( is_search() && get_search_query() ) : ?>
                <div class="app-testi-search-notice">
                    <span>Hasil pencarian nama: <strong>"<?php echo esc_html( get_search_query() ); ?>"</strong></span>
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'testimoni' ) ); ?>" class="app-testi-reset-link">
                        ✕ Reset
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tombol Tulis Ulasan -->
        <div class="app-action-row" style="margin-bottom: 24px;">
            <a href="<?php echo esc_url( site_url( '/submit-testimoni' ) ); ?>" class="btn-primary-app">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:8px;vertical-align:middle;display:inline-block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tulis Ulasan Anda
            </a>
        </div>

        <?php if ( have_posts() ) : ?>
            <div class="testimonials-grid archive-grid-override" id="ajax-feed-container">
                <?php while ( have_posts() ) : the_post(); 
                    get_template_part( 'template-parts/card-testimoni', null, array( 'is_link' => true ) );
                endwhile; ?>
            </div>

            <!-- AJAX Load More Button -->
            <?php if ( $wp_query->max_num_pages > 1 ) : ?>
                <div class="load-more-container">
                    <button id="load-more-btn" 
                            class="btn-load-more" 
                            data-page="1" 
                            data-max-pages="<?php echo $wp_query->max_num_pages; ?>" 
                            data-post-type="testimoni"
                            data-orderby="date"
                            data-search="<?php echo esc_attr( get_search_query() ); ?>">
                        Muat Lebih Banyak
                    </button>
                </div>
            <?php endif; ?>

        <?php else : ?>
            <div class="app-no-results" style="text-align: center; padding: 40px 20px;">
                <?php if ( is_search() ) : ?>
                    <div style="font-size: 2.5rem; margin-bottom: 12px;">🔍</div>
                    <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 8px;">Tidak ada testimoni dengan nama "<?php echo esc_html( get_search_query() ); ?>"</h3>
                    <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 18px;">Coba gunakan kata kunci nama lain atau kembali ke seluruh daftar testimoni.</p>
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'testimoni' ) ); ?>" class="btn-primary-app" style="display: inline-flex; width: auto; padding: 10px 24px;">
                        Tampilkan Semua Testimoni
                    </a>
                <?php else : ?>
                    <h3>Belum ada testimoni.</h3>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
