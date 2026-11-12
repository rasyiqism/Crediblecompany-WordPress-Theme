<?php
/**
 * Advanced SEO Module (Stand-Alone / Tanpa Plugin)
 * Menangani Header Cleanup, Open Graph, Meta Deskripsi Statis & JSON-LD.
 *
 * @package CredibleCompany
 */

// 1. Head Cleanup (Membersihkan tag meta bawaan WP yang tidak diperlukan)
add_action( 'init', function() {
    remove_action( 'wp_head', 'wp_generator' );
    remove_action( 'wp_head', 'rsd_link' );
    remove_action( 'wp_head', 'wlwmanifest_link' );
    remove_action( 'wp_head', 'wp_shortlink_wp_head' );
} );

// 2. Dynamic Meta Description & Open Graph Tags
add_action( 'wp_head', 'cc_advanced_seo_meta', 1 );
function cc_advanced_seo_meta() {
    $site_name = get_bloginfo( 'name' );
    $site_desc = get_bloginfo( 'description' );
    $url       = esc_url( home_url( '/' ) );
    $type      = 'website';
    $image     = '';

    if ( is_singular() ) {
        global $post;
        $title = get_the_title();
        $url   = esc_url( get_permalink() );
        $type  = 'article';
        
        $desc = get_the_excerpt();
        if ( empty( $desc ) ) {
            $desc = wp_trim_words( strip_shortcodes( strip_tags( $post->post_content ) ), 25, '...' );
        }
        if ( empty( $desc ) ) {
            $desc = $site_desc;
        }

        if ( has_post_thumbnail() ) {
            $image = get_the_post_thumbnail_url( $post->ID, 'large' );
        }
    } else {
        $title = $site_name;
        $desc  = $site_desc;
        
        // Homepage SEO: Hybrid (Manual > Excerpt > Konten > Global)
        if ( is_front_page() && ! is_home() ) {
            // 1. Cek Input Manual dari Customizer
            $manual_desc = cc_get( 'seo_home_description', '' );
            
            if ( ! empty( $manual_desc ) ) {
                $desc = $manual_desc;
            } else {
                // 2. Fallback Otomatis: Excerpt atau Konten
                global $post;
                if ( $post instanceof WP_Post ) {
                    $desc = get_the_excerpt( $post->ID );
                    if ( empty( $desc ) ) {
                        $desc = wp_trim_words( strip_shortcodes( wp_strip_all_tags( $post->post_content ) ), 25, '...' );
                    }
                }
                if ( empty( $desc ) ) $desc = $site_desc;
            }
        }

        if ( is_category() ) {
            $title = single_cat_title( '', false ) . ' - ' . $site_name;
            $desc  = wp_trim_words( strip_tags( category_description() ), 25, '...' );
            if ( empty($desc) ) $desc = $site_desc;
        } elseif ( is_search() ) {
            $title = 'Hasil Pencarian: ' . get_search_query() . ' - ' . $site_name;
        }
    }

    // Penanganan Gambar Open Graph (Prioritas: Post Thumbnail > Manual OG Image > Logo > Icon)
    if ( empty( $image ) ) {
        // 1. Cek Manual OG Image dari Customizer
        $manual_og_image = cc_get( 'seo_og_image', '' );
        
        if ( ! empty( $manual_og_image ) ) {
            $image = $manual_og_image;
        } elseif ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
            // 2. Fallback Logo Situs
            $logo_id = get_theme_mod( 'custom_logo' );
            $logo_data = wp_get_attachment_image_src( $logo_id, 'full' );
            if ( $logo_data ) $image = $logo_data[0];
        } elseif ( has_site_icon() ) {
            // 3. Fallback Site Icon (Favicon)
            $image = get_site_icon_url( 512 );
        }
    }

    $title = esc_attr( $title );
    $desc  = esc_attr( $desc );

    echo "\n" . '<!-- Advanced SEO Meta by CredibleCompany -->' . "\n";
    echo '<meta name="description" content="' . $desc . '">' . "\n";
    echo '<meta property="og:title" content="' . $title . '">' . "\n";
    echo '<meta property="og:description" content="' . $desc . '">' . "\n";
    echo '<meta property="og:url" content="' . $url . '">' . "\n";
    echo '<meta property="og:type" content="' . $type . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    
    if ( ! empty( $image ) ) {
        $image_url = esc_url( $image );
        echo '<meta property="og:image" content="' . $image_url . '">' . "\n";
        echo '<meta property="og:image:secure_url" content="' . $image_url . '">' . "\n";
        
        // Detect image type roughly
        $ext = pathinfo( $image, PATHINFO_EXTENSION );
        if ( in_array( $ext, ['jpg', 'jpeg'] ) ) echo '<meta property="og:image:type" content="image/jpeg">' . "\n";
        elseif ( $ext === 'png' ) echo '<meta property="og:image:type" content="image/png">' . "\n";
        elseif ( $ext === 'webp' ) echo '<meta property="og:image:type" content="image/webp">' . "\n";
    }
}

// 3. JSON-LD Schema (Article & Review)
add_action( 'wp_head', 'cc_json_ld_schema', 2 );
function cc_json_ld_schema() {
    if ( is_singular( 'post' ) ) {
        global $post;
        $schema = array(
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => get_the_title(),
            'datePublished' => get_the_date( 'c' ),
            'dateModified'  => get_the_modified_date( 'c' ),
            'author'        => array(
                '@type' => 'Person',
                'name'  => get_the_author(),
            ),
        );
        if ( has_post_thumbnail() ) {
            $schema['image'] = array( get_the_post_thumbnail_url( $post->ID, 'large' ) );
        }
        echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
        echo '<!-- /Advanced SEO Meta -->' . "\n";
    } elseif ( is_singular( 'testimoni' ) ) {
        global $post;
        $rating = get_post_meta( $post->ID, '_customer_rating', true );
        if ( empty( $rating ) ) {
            $rating = get_post_meta( $post->ID, 'cc_testimonial_rating', true ) ?: ( get_post_meta( $post->ID, '_cc_testimoni_rating', true ) ?: 5 );
        }
        $client_name = get_post_meta( $post->ID, '_customer_name', true );
        if ( empty( $client_name ) ) {
            $client_name = get_post_meta( $post->ID, '_cc_testimoni_client_name', true ) ?: get_the_title();
        }

        $schema = array(
            '@context'     => 'https://schema.org',
            '@type'        => 'Review',
            'itemReviewed' => array(
                '@type' => 'Organization',
                'name'  => get_bloginfo( 'name' )
            ),
            'reviewRating' => array(
                '@type'       => 'Rating',
                'ratingValue' => (string) $rating,
                'bestRating'  => '5',
            ),
            'author' => array(
                '@type' => 'Person',
                'name'  => $client_name,
            ),
            'reviewBody' => wp_trim_words( wp_strip_all_tags( $post->post_content ), 50, '' ),
        );
        echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
        echo '<!-- /Advanced SEO Meta -->' . "\n";
    } elseif ( is_front_page() || is_home() ) {
        $same_as = array_values( array_filter( array(
            cc_get( 'social_facebook', '' ),
            cc_get( 'social_twitter', '' ),
            cc_get( 'social_instagram', '' ),
            cc_get( 'social_linkedin', '' ),
            cc_get( 'social_youtube', '' ),
            cc_get( 'social_tiktok', '' ),
        ), function( $u ) {
            return ! empty( $u ) && '#' !== $u;
        } ) );

        $schema = array(
            '@context'      => 'https://schema.org',
            '@type'         => 'WebSite',
            'name'          => get_bloginfo( 'name' ),
            'description'   => get_bloginfo( 'description' ),
            'url'           => esc_url( home_url( '/' ) ),
            'potentialAction' => array(
                '@type'       => 'SearchAction',
                'target'      => esc_url( home_url( '/' ) ) . '?s={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name'  => get_bloginfo( 'name' ),
                'logo'  => array(
                    '@type' => 'ImageObject',
                    'url'   => has_custom_logo() ? wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ) : get_site_icon_url( 512 ),
                )
            ),
        );
        if ( ! empty( $same_as ) ) {
            $schema['sameAs'] = $same_as;
        }
        echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
        echo '<!-- /Advanced SEO Meta -->' . "\n";
    }
}

// 4. Attachment Page Redirect (Pencegah Thin Content Penalty)
add_action( 'template_redirect', 'cc_attachment_redirect' );
function cc_attachment_redirect() {
    global $post;
    if ( is_attachment() ) {
        if ( ! empty( $post->post_parent ) ) {
            // Arahkan ke artikel induknya
            wp_redirect( get_permalink( $post->post_parent ), 301 );
            exit;
        } else {
            // Arahkan ke beranda jika tidak ada induk
            wp_redirect( home_url( '/' ), 301 );
            exit;
        }
    }
}

// 5. XML Sitemap Mandiri (Sitemap Generator Tanpa Plugin)
add_action( 'init', 'cc_add_sitemap_endpoint' );
function cc_add_sitemap_endpoint() {
    add_rewrite_rule( '^sitemap\.xml$', 'index.php?cc_sitemap=1', 'top' );
}

add_filter( 'query_vars', 'cc_sitemap_query_var' );
function cc_sitemap_query_var( $vars ) {
    $vars[] = 'cc_sitemap';
    return $vars;
}

add_action( 'template_redirect', 'cc_render_sitemap' );
function cc_render_sitemap() {
    if ( get_query_var( 'cc_sitemap' ) ) {
        // Output tipe MIME XML
        header( 'Content-Type: application/xml; charset=utf-8' );

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Beranda
        echo '  <url>' . "\n";
        echo '    <loc>' . esc_url( home_url( '/' ) ) . '</loc>' . "\n";
        echo '    <changefreq>daily</changefreq>' . "\n";
        echo '    <priority>1.0</priority>' . "\n";
        echo '  </url>' . "\n";

        // Query Post, Page & Testimoni (Limit untuk skalabilitas)
        $args = array(
            'post_type'      => array( 'post', 'page', 'testimoni' ),
            'post_status'    => 'publish',
            'posts_per_page' => 500, // Limit aman untuk sitemap satu file
            'fields'         => 'ids',
        );
        
        $posts = get_posts( $args );
        foreach ( $posts as $post_id ) {
            $post_date = get_the_modified_date( 'c', $post_id );
            if ( ! $post_date ) {
                $post_date = get_the_date( 'c', $post_id );
            }
            
            echo '  <url>' . "\n";
            echo '    <loc>' . esc_url( get_permalink( $post_id ) ) . '</loc>' . "\n";
            echo '    <lastmod>' . esc_html( $post_date ) . '</lastmod>' . "\n";
            echo '    <changefreq>weekly</changefreq>' . "\n";
            echo '    <priority>0.8</priority>' . "\n";
            echo '  </url>' . "\n";
        }

        // Tambah Kategori & Tag
        $taxonomies = array( 'category', 'post_tag' );
        foreach ( $taxonomies as $tax ) {
            $terms = get_terms( array(
                'taxonomy'   => $tax,
                'hide_empty' => true,
            ) );
            if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
                foreach ( $terms as $term ) {
                    $term_link = get_term_link( $term );
                    if ( is_wp_error( $term_link ) ) {
                        continue;
                    }
                    echo '  <url>' . "\n";
                    echo '    <loc>' . esc_url( $term_link ) . '</loc>' . "\n";
                    echo '    <changefreq>weekly</changefreq>' . "\n";
                    echo '    <priority>0.5</priority>' . "\n";
                    echo '  </url>' . "\n";
                }
            }
        }

        echo '</urlset>';
        exit;
    }
}

// 6. Social Share Buttons (Mobile & Desktop Friendly)
function cc_social_share() {
    $url   = rawurlencode( get_permalink() );
    $title = rawurlencode( html_entity_decode( get_the_title(), ENT_QUOTES, 'UTF-8' ) );

    echo '<div class="cc-share-buttons">';
    echo '<span class="cc-share-buttons__label">Bagikan:</span>';

    // Facebook
    echo '<a href="https://www.facebook.com/sharer/sharer.php?u=' . $url . '" target="_blank" rel="noopener noreferrer" class="cc-share-btn cc-share-btn--fb" aria-label="Share on Facebook"><svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35C.597 0 0 .597 0 1.325v21.351C0 23.403.597 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.323-.597 1.323-1.325V1.325C24 .593 23.403 0 22.675 0z"/></svg></a>';

    // Twitter/X
    echo '<a href="https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title . '" target="_blank" rel="noopener noreferrer" class="cc-share-btn cc-share-btn--x" aria-label="Share on X (Twitter)"><svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>';

    // WhatsApp
    echo '<a href="https://api.whatsapp.com/send?text=' . $title . ' - ' . $url . '" target="_blank" rel="noopener noreferrer" class="cc-share-btn cc-share-btn--wa" aria-label="Share on WhatsApp"><svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a5.8 5.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg></a>';

    // LinkedIn
    echo '<a href="https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&title=' . $title . '" target="_blank" rel="noopener noreferrer" class="cc-share-btn cc-share-btn--li" aria-label="Share on LinkedIn"><svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>';

    echo '</div>';
}

// 7. Robots.txt Optimizer (Instruksi Crawler)
add_filter( 'robots_txt', 'cc_optimize_robots_txt', 10, 2 );
function cc_optimize_robots_txt( $output, $public ) {
    if ( '0' == $public ) {
        return $output;
    }
    $sitemap_url = home_url( '/sitemap.xml' );
    $output .= "\nUser-agent: *\n";
    $output .= "Disallow: /wp-admin/\n";
    $output .= "Allow: /wp-admin/admin-ajax.php\n";
    $output .= "Disallow: /trackback/\n";
    $output .= "Disallow: /xmlrpc.php\n";

    // AI & Crawler Control (Generative Engine Optimization)
    $output .= "\nUser-agent: GPTBot\nDisallow: /wp-admin/\n";
    $output .= "User-agent: CCBot\nDisallow: /\n"; // Common Crawl seringkali berat
    $output .= "User-agent: PerplexityBot\nDisallow: /wp-admin/\n";

    $output .= "\nSitemap: " . esc_url( $sitemap_url ) . "\n";
    return $output;
}

// 8. Canonical URL & Noindex untuk Halaman Kualitas Rendah
add_action( 'wp_head', 'cc_seo_head_extra', 5 );
function cc_seo_head_extra() {
    // 1. Canonical URL: Mencegah Duplikasi Konten
    if ( is_singular() ) {
        echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '">' . "\n";
    } elseif ( is_front_page() || is_home() ) {
        echo '<link rel="canonical" href="' . esc_url( home_url( '/' ) ) . '">' . "\n";
    }

    // 2. Noindex: Memberi instruksi Google jangan indeks halaman Sampah/404/Search atau Paginasi
    if ( is_404() || is_search() || is_paged() ) {
        echo '<meta name="robots" content="noindex, follow">' . "\n";
    }
}

// 9. SEO Title Template (Format: Judul | Nama Situs)
add_filter( 'document_title_parts', 'cc_seo_title_parts' );
function cc_seo_title_parts( $title ) {
    if ( is_front_page() || is_home() ) {
        $title['tagline'] = get_bloginfo( 'description' );
    } else {
        $site_name = get_bloginfo( 'name' );
        if ( isset( $title['site'] ) ) {
            $title['site'] = $site_name;
        }
    }

    // Tambahkan info halaman jika ada paginasi
    if ( is_paged() ) {
        $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : ( get_query_var( 'page' ) ? get_query_var( 'page' ) : 1 );
        $title['page'] = sprintf( 'Halaman %d', $paged );
    }

    return $title;
}

/**
 * 10. Optimasi Metadata Gambar Unggulan (Featured Image) Otomatis
 * Mengisi alt, title, caption, dan deskripsi gambar unggulan menggunakan judul pos + "(Ilustrasi)".
 *
 * @param int $post_id ID postingan.
 */
add_action( 'publish_post', 'cc_update_featured_image_meta', 10, 2 );
function cc_update_featured_image_meta( $post_id ) {
    // Menghapus aksi untuk mencegah loop tak terbatas
    remove_action( 'publish_post', 'cc_update_featured_image_meta', 10 );

    // Mendapatkan objek post
    $post = get_post( $post_id );

    // Cek jika post memiliki featured image
    if ( has_post_thumbnail( $post_id ) ) {
        $thumbnail_id = get_post_thumbnail_id( $post_id );
        $post_title   = $post->post_title . ' (Ilustrasi)'; // Menambahkan "(Ilustrasi)" ke judul post

        // Ambil metadata gambar dan simpan dalam variabel untuk menghindari pemanggilan berulang
        $alt         = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
        $caption     = wp_get_attachment_caption( $thumbnail_id );
        $description = get_post_field( 'post_content', $thumbnail_id );

        // Buat array untuk menyimpan data yang akan diperbarui
        $update_data = array( 'ID' => $thumbnail_id );

        // Jika metadata alt kosong, update dengan judul post yang ditambah "(Ilustrasi)"
        if ( empty( $alt ) ) {
            update_post_meta( $thumbnail_id, '_wp_attachment_image_alt', $post_title );
        }

        // Perbarui judul jika berbeda
        if ( get_the_title( $thumbnail_id ) !== $post_title ) {
            $update_data['post_title'] = $post_title;
        }
        
        // Perbarui caption jika kosong
        if ( empty( $caption ) ) {
            $update_data['post_excerpt'] = $post_title;
        }
        
        // Perbarui deskripsi jika kosong
        if ( empty( $description ) ) {
            $update_data['post_content'] = $post_title;
        }

        // Perbarui post attachment jika ada data yang perlu diperbarui
        if ( count( $update_data ) > 1 ) {
            wp_update_post( $update_data );
        }
    }

    // Menambahkan aksi kembali
    add_action( 'publish_post', 'cc_update_featured_image_meta', 10, 2 );
}


