<?php
/**
 * Template untuk form pencarian
 *
 * @package CredibleCompany
 */
?>
<form role="search" method="get" class="app-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" toolname="search_site" tooldescription="Search published books, articles, and testimonials on this site.">
    <div class="search-input-wrapper">
        <span class="search-icon">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </span>
        <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Cari sesuatu...', 'placeholder', 'cc' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Cari:', 'label', 'cc' ); ?>" toolparamdescription="Search keyword or phrase" required />
        <button type="submit" class="search-submit" aria-label="<?php echo esc_attr_x( 'Cari', 'submit button', 'cc' ); ?>">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
            </svg>
        </button>
    </div>
</form>
