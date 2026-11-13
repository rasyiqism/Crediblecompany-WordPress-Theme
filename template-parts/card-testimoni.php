<?php
/**
 * Template Part: Card Testimoni
 *
 * @package CredibleCompany
 */

$name       = get_post_meta( get_the_ID(), '_customer_name', true );
$city       = get_post_meta( get_the_ID(), '_customer_city', true );
$profession = get_post_meta( get_the_ID(), '_customer_profession', true );
$rating     = get_post_meta( get_the_ID(), '_customer_rating', true );
$quote      = get_the_content();

// Fallback nama: gunakan post title jika meta kosong
if ( ! $name ) $name = get_the_title();

// Ambil argument 'is_link' jika ada
$is_link = isset( $args['is_link'] ) ? $args['is_link'] : false;
?>

<div class="testimonial-card" style="position: relative;">
    <!-- Tombol Edit Ulasan / Ganti Foto -->
    <a href="<?php echo esc_url( add_query_arg( 'edit', get_the_ID(), site_url( '/submit-testimoni/' ) ) ); ?>" 
       class="btn-card-edit-testi" 
       title="Edit ulasan atau ganti foto"
       onclick="event.stopPropagation();">
        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
        <span>Edit</span>
    </a>

    <?php if ( $is_link ) : ?>
        <a href="<?php the_permalink(); ?>" class="testi-card-link-wrapper">
    <?php endif; ?>

        <!-- Info Profil -->
        <div class="testimonial-profile">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'thumbnail', array(
                    'class' => 'testimonial-avatar',
                    'alt'   => esc_attr( $name ),
                ) ); ?>
            <?php else : ?>
                <div class="testimonial-avatar testimonial-avatar--placeholder">
                    <?php echo mb_strtoupper( mb_substr( $name, 0, 1 ) ); ?>
                </div>
            <?php endif; ?>

            <div class="testimonial-info">
                <h4><?php echo esc_html( $name ); ?></h4>
                <?php if ( $profession ) : ?>
                    <p class="testimonial-profession"><?php echo esc_html( $profession ); ?></p>
                <?php endif; ?>
                <?php if ( $city ) : ?>
                    <p class="testimonial-city">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <?php echo esc_html( $city ); ?>
                    </p>
                <?php endif; ?>
                <?php if ( $rating ) : ?>
                    <div class="testimonial-rating">
                        <?php echo cc_render_stars( $rating ); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kutipan -->
        <?php if ( $quote ) : ?>
            <blockquote>"<?php echo esc_html( wp_trim_words( wp_strip_all_tags( $quote ), 30, '...' ) ); ?>"</blockquote>
        <?php endif; ?>

    <?php if ( $is_link ) : ?>
        </a>
    <?php endif; ?>
</div>
