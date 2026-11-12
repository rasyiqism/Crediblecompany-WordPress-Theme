<?php
/**
 * Halaman Post / Baca Artikel
 * Berdesain App-Mobile First dengan Top Bar & Content Reader
 *
 * @package CredibleCompany
 */

get_header(); ?>

<section class="app-single-content">
    <?php while ( have_posts() ) : the_post(); ?>
    
        <div class="app-header-bar single-header">
            <!-- Tombol Kembali ala App -->
            <a href="javascript:history.back()" class="app-back-btn">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div class="app-page-title" style="font-size: 1.1rem; font-weight: 600; flex: 1; text-align: center; color: var(--text-dark); margin: 0; padding: 0 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php the_title(); ?></div>
            <div class="header-actions-app">
                <!-- Icon Share -->
                <button class="app-share-btn" data-share-title="<?php the_title_attribute(); ?>" data-share-url="<?php echo esc_url( get_permalink() ); ?>">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                </button>
                <button class="sidebar-toggle-btn" id="sidebarToggle">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                </button>
            </div>
        </div>

        <?php 
        // Generate TOC string
        $content_for_toc = apply_filters( 'the_content', get_the_content() );
        $toc_html = cc_generate_toc_html( $content_for_toc ); 
        ?>

        <div class="single-page-layout layout-3-cols">
            
            <!-- Left: Sidebar -->
            <aside class="single-sidebar sidebar-left">
                <?php if ( ! empty( $toc_html ) ) : ?>
                    <!-- Daftar Isi: Bottom Drawer di Mobile, Sticky di Desktop -->
                    <div id="toc-drawer" class="toc-drawer-wrapper">
                        <div class="toc-drawer-header">
                            <h3 class="toc-title">Daftar Isi</h3>
                            <button id="close-toc-btn" class="close-drawer-btn" aria-label="Tutup Daftar Isi">
                                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <div class="toc-drawer-body">
                            <?php echo $toc_html; ?>
                        </div>
                    </div>
                    <!-- Overlay TOC untuk mobile -->
                    <div id="toc-overlay" class="drawer-overlay"></div>

                    <!-- Tombol Floating TOC (Mobile Only melalui CSS) -->
                    <button id="open-toc-btn" class="floating-toc-btn" aria-label="Buka Daftar Isi">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                        <span>Daftar Isi</span>
                    </button>
                <?php endif; ?>

            </aside>

            <!-- Center: Main Article -->
            <div class="single-main-column">
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'app-article-reader' ); ?>>
                    
                    <header class="app-article-header">
                        <div class="app-article-meta">
                            <?php
                            $categories = get_the_category();
                            if ( ! empty( $categories ) ) {
                                echo '<span class="article-cat">' . esc_html( $categories[0]->name ) . '</span>';
                            }
                            ?>
                            <span class="meta-separator">•</span>
                            <span class="meta-date"><?php echo get_the_date(); ?></span>
                        </div>
                        
                        <?php 
                        // Tampilkan Breadcrumbs
                        if ( function_exists( 'cc_breadcrumbs' ) ) {
                            cc_breadcrumbs();
                        }
                        ?>
                        <h1 class="app-article-title"><?php the_title(); ?></h1>
                        
                        <div class="app-author-row">
                            <div class="author-avatar">
                                <?php echo get_avatar( get_the_author_meta( 'ID' ),  40 ); ?>
                            </div>
                            <div class="author-info">
                                <span class="author-label">Ditulis oleh</span>
                                <span class="author-name"><?php the_author(); ?></span>
                            </div>
                            <div class="article-views">
                                <span class="views-count">👁 <?php echo cc_get_post_views( get_the_ID() ); ?></span>
                            </div>
                        </div>
                    </header>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="app-article-thumbnail">
                            <?php the_post_thumbnail( 'large', ['class' => 'featured-media'] ); ?>
                        </div>
                    <?php endif; ?>

                    <div class="app-article-body">
                        <?php
                        // Gunakan konten yang sudah diproses the_content untuk TOC agar filter tidak berjalan 2x
                        echo $content_for_toc; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        ?>

                        <?php
                        wp_link_pages( array(
                            'before' => '<div class="page-links">' . __( 'Halaman:', 'crediblecompany' ),
                            'after'  => '</div>',
                        ) );
                        ?>
                    </div>
                    
                    <?php if ( function_exists('cc_social_share') ) cc_social_share(); ?>

                    <footer class="app-article-footer">
                        <?php $tags = get_the_tags(); if ( $tags ) : ?>
                            <div class="app-article-tags" style="justify-content: center;">
                                <?php foreach ( $tags as $tag ) : ?>
                                    <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="tag-pill">#<?php echo esc_html( $tag->name ); ?></a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </footer>

                </article>

                <!-- Artikel Terkait Section (Desktop Only - Above Comments) -->
                <div class="related-posts-section desktop-only" style="margin: 60px 0;">
                    <h3 class="section-title-premium">Artikel Terkait</h3>
                    <div class="testi-feed-container">
                        <div class="testi-related-grid">
                            <?php
                            // Gunakan helper untuk menghindari ORDER BY RAND() MySQL
                            $related_cat_ids     = ! empty( $categories ) ? array( $categories[0]->term_id ) : array();
                            $related_query_desktop = cc_get_related_posts( get_the_ID(), $related_cat_ids, 4 );

                            if ( $related_query_desktop->have_posts() ) :
                                while ( $related_query_desktop->have_posts() ) : $related_query_desktop->the_post();
                                    get_template_part( 'template-parts/card-post', null, array( 'style' => 'app' ) );
                                endwhile;
                                wp_reset_postdata();
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
                
                <?php
                // Komentar
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
                ?>
            </div><!-- /.single-main-column -->

            <!-- Right: Ad / Banner Sidebar -->
            <aside class="single-sidebar sidebar-right desktop-only">
                <?php if ( is_active_sidebar( 'sidebar-iklan' ) ) : ?>
                    <?php dynamic_sidebar( 'sidebar-iklan' ); ?>
                <?php else : ?>
                    <div class="sidebar-widget widget-ad">
                        <div class="ad-placeholder" style="background: #f1f5f9; border: 2px dashed #cbd5e1; border-radius: 12px; height: 600px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; text-align: center; padding: 20px;">
                            <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-bottom: 12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                            <span style="font-weight: 600; font-size: 14px;">Ruang Iklan / Banner</span>
                            <span style="font-size: 12px; margin-top: 4px;">Dapat diisi widget dinamis</span>
                        </div>
                    </div>
                <?php endif; ?>
            </aside>

        </div><!-- /.single-page-layout -->

        <!-- Side Drawer Mobile: Menu Tambahan / Artikel Terkait -->
        <aside class="app-drawer" id="mobileDrawer">
            <div class="drawer-header-mobile">
                <h3 style="visibility: hidden;">Menu</h3>
                <button class="close-drawer" id="closeDrawer">&times;</button>
            </div>
                    <?php if ( wp_is_mobile() ) : ?>
                <div class="sidebar-widget widget-recent-posts">
                    <h3 class="section-title-premium">Artikel Terkait</h3>
                    <div class="recent-posts-list">
                        <?php
                        // Gunakan helper yang sama — DRY, tidak duplikasi query
                        $related_cat_ids    = ! empty( $categories ) ? array( $categories[0]->term_id ) : array();
                        $related_query_mobile = cc_get_related_posts( get_the_ID(), $related_cat_ids, 5 );

                        if ( $related_query_mobile->have_posts() ) :
                            while ( $related_query_mobile->have_posts() ) : $related_query_mobile->the_post();
                                ?>
                                <div class="recent-post-item">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <div class="post-thumb">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail( 'thumbnail' ); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="post-info">
                                        <h4 class="post-title"><a href="<?php the_permalink(); ?>"><?php echo wp_trim_words( get_the_title(), 7, '...' ); ?></a></h4>
                                        <span class="post-date"><?php echo get_the_date( 'd M Y' ); ?></span>
                                    </div>
                                </div>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </aside>

        <!-- Backdrop untuk Drawer Mobile -->
        <div class="drawer-backdrop" id="drawerBackdrop"></div>

    <?php endwhile; ?>
</section>

<?php get_footer(); ?>
