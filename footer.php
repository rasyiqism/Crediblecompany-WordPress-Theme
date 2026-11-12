<?php
/**
 * Template Footer — Credible Company.
 * Berisi footer, social links, copyright, dan penutup body.
 *
 * @package CredibleCompany
 */
?>
    </main><!-- /#main -->

    <footer class="site-footer">
        <!-- Row 1: Logo, Social, Stats -->
        <div class="footer-top">
            <div class="container footer-flex">
                <!-- Logo (Boxed) -->
                <div class="footer-logo-box">
                    <?php if ( has_custom_logo() ) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-logo-text">
                            <?php bloginfo( 'name' ); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Eksplor (Social Media Version Lama) -->
                <div class="footer-social <?php echo cc_get( 'footer_social_brand_colors', true ) ? 'social-brand-mode' : 'social-custom-mode'; ?>">
                    <span class="footer-social-label">Eksplor:</span>
                    <?php
                    $social_fb = cc_get( 'social_facebook', '#' );
                    $social_tw = cc_get( 'social_twitter', '#' );
                    $social_ig = cc_get( 'social_instagram', '#' );
                    $social_yt = cc_get( 'social_youtube', '' );
                    $social_tk = cc_get( 'social_tiktok', '' );
                    ?>
                    <a href="<?php echo esc_url( $social_fb ); ?>" class="social-fb" aria-label="Facebook" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24"><path d="M22.675 0h-21.35C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24h11.495v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.323-.593 1.323-1.325V1.325C24 .593 23.407 0 22.675 0z"/></svg>
                    </a>
                    <a href="<?php echo esc_url( $social_tw ); ?>" class="social-tw" aria-label="Twitter" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                    <a href="<?php echo esc_url( $social_ig ); ?>" class="social-ig" aria-label="Instagram" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92C2.013 15.584 2 15.205 2 12c0-3.205.012-3.584.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <?php if ( ! empty( $social_yt ) ) : ?>
                    <a href="<?php echo esc_url( $social_yt ); ?>" class="social-yt" aria-label="YouTube" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $social_tk ) ) : ?>
                    <a href="<?php echo esc_url( $social_tk ); ?>" class="social-tk" aria-label="TikTok" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24"><path d="M12.525.02c1.31 0 2.591.214 3.75.606V7.06a5.451 5.451 0 01-3.235-1.042l-.004 8.016a5.571 5.571 0 11-5.572-5.572c.162 0 .322.007.48.021l-.001-3.765a9.324 9.324 0 108.847 9.316l.006-11.458c1.696 1.22 3.76 1.944 6 1.944l-.002-3.725a5.539 5.539 0 01-4-2.203V.02z"/></svg>
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Stats Widget -->
                <div class="footer-stats">
                    <div class="stats-card">
                        <span class="stats-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </span>
                        <?php
                        $total_views = cc_get_total_site_views();
                        $views_today = cc_get_views_today();
                        ?>
                        <span class="stats-text"><?php echo number_format($total_views); ?> total views, <?php echo number_format($views_today); ?> views today</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Alamat (White BG) -->
        <div class="footer-middle">
            <div class="container container--narrow">
                <?php $footer_address = cc_get( 'footer_address', 'Kantor : Paingan, (Halaman Masjid Ash Sholihin), Maguwoharjo, Depok, Sleman, Daerah Istimewa Yogyakarta.' ); ?>
                <?php if ( ! empty( $footer_address ) ) : ?>
                    <p class="footer-address-text">
                        <?php echo nl2br( esc_html( $footer_address ) ); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Row 3: Bottom (Copyright & Menu) -->
        <div class="footer-bottom">
            <div class="container footer-flex">
                <!-- Footer Links -->
                <ul class="footer-nav" style="list-style: none; margin: 0; padding: 0;">
                    <?php
                    if ( has_nav_menu( 'footer_navigation' ) ) {
                        wp_nav_menu( array(
                            'theme_location' => 'footer_navigation',
                            'container'      => false,
                            'items_wrap'     => '%3$s',
                            'depth'          => 1,
                            'fallback_cb'    => false,
                        ) );
                    } else {
                        echo '<li><a href="#">About Us</a></li>';
                    }
                    ?>
                </ul>

                <!-- Copyright -->
                <div class="footer-copyright">
                    &copy; <?php echo date( 'Y' ); ?> <?php cc_e( 'footer_copyright', 'Credible Company.' ); ?> All Rights Reserved.
                </div>
            </div>
        </div>
    </footer>

</div><!-- /#page -->

<?php
/**
 * Floating WhatsApp Chat Button.
 * Hanya ditampilkan jika nomor WA diisi di Customizer > Social Media.
 */
$wa_number  = cc_get( 'whatsapp_number', '' );
$wa_message = cc_get( 'whatsapp_message', cc_get_default_wa_message() );

if ( ! empty( $wa_number ) && ! is_single() ) :
    // Bersihkan nomor dari karakter non-digit
    $wa_number_clean = preg_replace( '/[^0-9]/', '', $wa_number );
    $wa_url = 'https://wa.me/' . $wa_number_clean . '?text=' . rawurlencode( $wa_message );
?>
<div class="floating-whatsapp" id="floating-whatsapp">
    <span class="floating-whatsapp__label">Chat via WhatsApp</span>
    <a href="<?php echo esc_url( cc_dynamic_wa_url( $wa_url ) ); ?>"
       class="floating-whatsapp__btn"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="Chat WhatsApp">
        <!-- Ikon WhatsApp (SVG resmi) -->
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.999-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>
</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
