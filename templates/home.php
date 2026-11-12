<?php
/**
 * Halaman Indeks Blog (Archive Utama Berita)
 * Berdesain App-Mobile First
 *
 * @package CredibleCompany
 */

get_header(); 
?>

<!-- TEMPLATE: templates/home.php -->

<?php
// Customizer Settings
$hero_title    = get_theme_mod( 'cc_blog_title', __( 'Blog', 'crediblecompany' ) );
$hero_subtitle = get_theme_mod( 'cc_blog_subtitle', __( 'Temukan wawasan dan cerita menarik terbaru dari kami.', 'crediblecompany' ) );
$bg_start      = get_theme_mod( 'cc_blog_hero_bg_start', '#6366f1' );
$bg_end        = get_theme_mod( 'cc_blog_hero_bg_end', '#a855f7' );
?>

<div class="blog-hero" style="background: linear-gradient(135deg, <?php echo esc_attr( $bg_start ); ?> 0%, <?php echo esc_attr( $bg_end ); ?> 100%);">
    <div class="blog-hero-content">
        <h1><?php echo esc_html( $hero_title ); ?></h1>
        <?php if ( $hero_subtitle ) : ?>
            <p class="blog-hero-subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>
        <?php endif; ?>
    </div>
    <div class="blog-hero-decor">
        <svg width="100%" height="100%" viewBox="0 0 1000 300" preserveAspectRatio="none">
            <circle cx="100" cy="50" r="40" fill="currentColor" opacity="0.1" />
            <circle cx="900" cy="250" r="60" fill="currentColor" opacity="0.1" />
            <path d="M 0 150 Q 250 50 500 150 T 1000 150" fill="none" stroke="currentColor" stroke-width="2" opacity="0.1" />
        </svg>
    </div>
</div>

<div class="blog-search-container">
    <form role="search" method="get" class="blog-search-wrap" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <input type="search" placeholder="Cari artikel..." value="<?php echo get_search_query(); ?>" name="s" />
        <input type="hidden" name="post_type" value="post" />
        <button type="submit">Cari</button>
    </form>
</div>

<div class="blog-categories-pills">
    <a href="<?php echo esc_url( get_post_type_archive_link( 'post' ) ); ?>" class="pill-item <?php echo !isset($_GET['cat']) ? 'active' : ''; ?>">All</a>
    <?php
    $categories = get_categories();
    foreach ( $categories as $category ) :
        $cat_link = add_query_arg( 'cat', $category->term_id, get_post_type_archive_link( 'post' ) );
        $active_class = ( isset($_GET['cat']) && $_GET['cat'] == $category->term_id ) ? 'active' : '';
        echo '<a href="' . esc_url( $cat_link ) . '" class="pill-item ' . $active_class . '">' . esc_html( $category->name ) . '</a>';
    endforeach;
    ?>
</div>

<section class="app-main-content">
    <?php
    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
    $orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'date';
    $cat = isset( $_GET['cat'] ) ? intval( $_GET['cat'] ) : '';

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 10, // 1 featured + 9 grid
        'paged'          => $paged
    );

    if ( $cat ) $args['cat'] = $cat;
    if ( 'popular' === $orderby ) {
        $args['meta_key'] = 'cc_post_views';
        $args['orderby']  = 'meta_value_num';
        $args['order']    = 'DESC';
    }

    $blog_query = new WP_Query( $args );

    if ( $blog_query->have_posts() ) :
        if ( 1 === $paged ) : ?>
            <div class="featured-post-container">
                <?php
                $blog_query->the_post(); // Ambil post pertama untuk featured
                get_template_part( 'template-parts/card-post', null, array( 'style' => 'featured' ) );
                ?>
            </div>
        <?php endif; ?>

        <div class="app-feed-container" id="ajax-feed-container">
            <?php
            while ( $blog_query->have_posts() ) :
                $blog_query->the_post();
                get_template_part( 'template-parts/card-post', null, array( 'style' => 'app' ) );
            endwhile;
            ?>
        </div>

        <?php if ( $blog_query->max_num_pages > 1 ) : ?>
            <div class="load-more-container">
                <button id="load-more-btn"
                        class="btn-load-more"
                        data-page="1"
                        data-max-pages="<?php echo esc_attr( $blog_query->max_num_pages ); ?>"
                        data-post-type="post"
                        data-orderby="<?php echo esc_attr( $orderby ); ?>"
                        data-cat="<?php echo esc_attr( $cat ); ?>">
                    Muat Lebih Banyak
                </button>
            </div>
        <?php endif; ?>

    <?php
    else :
        echo '<div class="app-no-results"><h3>Belum ada artikel.</h3><p>Pantau terus pembaruan informasi dari kami.</p></div>';
    endif;
    wp_reset_postdata();
    ?>
</section>

<?php get_footer(); ?>
