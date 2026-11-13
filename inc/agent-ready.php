<?php
/**
 * Agent-Readiness Module:
 * - Machine-readable company contact & key page helpers (About, Contact, Terms, Privacy, Pricing)
 * - RFC 8288 Link headers & Vary: Accept
 * - /llms.txt endpoint (llmstxt.org)
 * - Content negotiation for Accept: text/markdown (and ?format=markdown)
 * - MCP Server Card (SEP-1649) at /.well-known/mcp/server-card.json + Streamable-HTTP JSON-RPC endpoint
 * - Virtual fallback routes for Contact (/kontak/) and Terms (/syarat-ketentuan/) if not yet created in WP
 * - Graceful fallbacks for robots.txt Content-Signal, sitemap <lastmod>, and JSON-LD @graph
 *
 * @package CredibleCompany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* --------------------------------------------------------------------------
 * 1. Business Identity & Data Helpers (Single Source of Truth)
 * ---------------------------------------------------------------------- */

/**
 * Get normalized request path without leading/trailing slashes.
 *
 * @return string
 */
function cc_get_request_path() {
	$raw_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
	$path    = (string) wp_parse_url( $raw_uri, PHP_URL_PATH );

	// Support WordPress installed in a subdirectory.
	$home_path = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	$home_path = trim( $home_path, '/' );
	$trimmed   = trim( $path, '/' );

	if ( '' !== $home_path && 0 === strpos( $trimmed, $home_path ) ) {
		$trimmed = trim( substr( $trimmed, strlen( $home_path ) ), '/' );
	}

	return $trimmed;
}

/**
 * Get official business email address.
 *
 * @return string
 */
function cc_get_company_email() {
	$email = trim( (string) cc_get( 'company_email', 'naskah@penerbitkbm.com' ) );
	return is_email( $email ) ? $email : 'naskah@penerbitkbm.com';
}

/**
 * Get official business phone display string.
 *
 * @return string
 */
function cc_get_company_phone() {
	$phone = trim( (string) cc_get( 'company_phone', '' ) );
	if ( '' === $phone ) {
		$wa = preg_replace( '/[^0-9]/', '', (string) cc_get( 'whatsapp_number', '' ) );
		if ( '' !== $wa ) {
			$phone = '+' . ltrim( $wa, '+' );
		} else {
			$phone = '+6281357517526';
		}
	}
	return $phone;
}

/**
 * Get tel: URI safe phone string (digits and leading +).
 *
 * @return string
 */
function cc_get_company_phone_tel() {
	$raw = cc_get_company_phone();
	$tel = preg_replace( '/[^0-9+]/', '', $raw );
	if ( '' === $tel ) {
		return '+6281357517526';
	}
	if ( 0 === strpos( $tel, '0' ) ) {
		$tel = '+62' . substr( $tel, 1 );
	}
	return $tel;
}

/**
 * Get official office address string.
 *
 * @return string
 */
function cc_get_company_address() {
	return trim( (string) cc_get(
		'footer_address',
		'Kantor : Paingan, (Halaman Masjid Ash Sholihin), Maguwoharjo, Depok, Sleman, Daerah Istimewa Yogyakarta.'
	) );
}

/**
 * Get clean social media URLs configured in Customizer.
 *
 * @return array
 */
function cc_get_social_links() {
	$keys  = array( 'social_facebook', 'social_twitter', 'social_instagram', 'social_linkedin', 'social_youtube', 'social_tiktok' );
	$links = array();
	foreach ( $keys as $key ) {
		$url = trim( (string) cc_get( $key, '' ) );
		if ( '' !== $url && '#' !== $url && filter_var( $url, FILTER_VALIDATE_URL ) ) {
			$links[] = $url;
		}
	}
	return array_values( array_unique( $links ) );
}

/**
 * Resolve URL for key business/legal pages: about, contact, terms, privacy, pricing.
 * Checks Customizer override -> existing WordPress pages -> virtual fallback routes.
 *
 * @param string $type Page key: about|contact|terms|privacy|pricing.
 * @return string
 */
function cc_get_key_page_url( $type ) {
	$custom_map = array(
		'about'   => 'page_about_url',
		'contact' => 'page_contact_url',
		'terms'   => 'page_terms_url',
		'privacy' => 'page_privacy_url',
		'pricing' => 'page_pricing_url',
	);

	if ( isset( $custom_map[ $type ] ) ) {
		$override = trim( (string) cc_get( $custom_map[ $type ], '' ) );
		if ( '' !== $override ) {
			return esc_url_raw( $override );
		}
	}

	if ( 'privacy' === $type && function_exists( 'get_privacy_policy_url' ) ) {
		$wp_privacy = get_privacy_policy_url();
		if ( ! empty( $wp_privacy ) ) {
			return $wp_privacy;
		}
	}

	$slug_candidates = array(
		'about'   => array( 'tentang', 'tentang-kami', 'tentang-penerbit', 'about', 'about-us' ),
		'contact' => array( 'kontak', 'hubungi-kami', 'contact', 'contact-us' ),
		'terms'   => array( 'syarat-ketentuan', 'syarat-dan-ketentuan', 'ketentuan-layanan', 'terms', 'terms-and-conditions', 'vendors-terms-and-conditions' ),
		'privacy' => array( 'kebijakan-privasi', 'privacy-policy', 'vendors-privacy-policy' ),
		'pricing' => array( 'paket-harga', 'harga', 'pricing', 'paket-penerbitan' ),
	);

	if ( isset( $slug_candidates[ $type ] ) ) {
		foreach ( $slug_candidates[ $type ] as $slug ) {
			$page = get_page_by_path( $slug );
			if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
				return get_permalink( $page );
			}
		}
	}

	$fallback_paths = array(
		'about'   => '/tentang/',
		'contact' => '/kontak/',
		'terms'   => '/syarat-ketentuan/',
		'privacy' => '/kebijakan-privasi/',
		'pricing' => '/#daftar-paket',
	);

	return isset( $fallback_paths[ $type ] ) ? home_url( $fallback_paths[ $type ] ) : home_url( '/' );
}

/**
 * Return all key business, legal, and navigation pages with labels and URLs.
 *
 * @return array
 */
function cc_get_key_pages_map() {
	$shop_page_id = get_option( 'owwc_page_shop_id' );
	$shop_url     = $shop_page_id ? get_permalink( $shop_page_id ) : '';
	$testi_url    = get_post_type_archive_link( 'testimoni' );
	$posts_page   = get_option( 'page_for_posts' );
	$blog_url     = $posts_page ? get_permalink( $posts_page ) : home_url( '/blog/' );

	$pages = array(
		'about'   => array(
			'label'       => __( 'Tentang Kami', 'crediblecompany' ),
			'url'         => cc_get_key_page_url( 'about' ),
			'description' => __( 'Profil perusahaan dan layanan penerbitan buku.', 'crediblecompany' ),
		),
		'pricing' => array(
			'label'       => __( 'Paket Harga', 'crediblecompany' ),
			'url'         => cc_get_key_page_url( 'pricing' ),
			'description' => __( 'Daftar paket layanan penerbitan buku dan harga.', 'crediblecompany' ),
		),
		'contact' => array(
			'label'       => __( 'Kontak', 'crediblecompany' ),
			'url'         => cc_get_key_page_url( 'contact' ),
			'description' => __( 'Alamat kantor, email resmi, nomor telepon, dan WhatsApp.', 'crediblecompany' ),
		),
		'terms'   => array(
			'label'       => __( 'Syarat & Ketentuan', 'crediblecompany' ),
			'url'         => cc_get_key_page_url( 'terms' ),
			'description' => __( 'Syarat dan ketentuan layanan penerbitan.', 'crediblecompany' ),
		),
		'privacy' => array(
			'label'       => __( 'Kebijakan Privasi', 'crediblecompany' ),
			'url'         => cc_get_key_page_url( 'privacy' ),
			'description' => __( 'Kebijakan pengelolaan dan perlindungan data pengguna.', 'crediblecompany' ),
		),
	);

	if ( ! empty( $shop_url ) ) {
		$pages['shop'] = array(
			'label'       => __( 'Toko Buku', 'crediblecompany' ),
			'url'         => $shop_url,
			'description' => __( 'Katalog buku terbitan yang tersedia untuk dibeli.', 'crediblecompany' ),
		);
	}
	if ( ! empty( $testi_url ) ) {
		$pages['testimonials'] = array(
			'label'       => __( 'Testimoni', 'crediblecompany' ),
			'url'         => $testi_url,
			'description' => __( 'Ulasan penulis dan mitra penerbitan.', 'crediblecompany' ),
		);
	}
	if ( ! empty( $blog_url ) ) {
		$pages['blog'] = array(
			'label'       => __( 'Blog & Berita', 'crediblecompany' ),
			'url'         => $blog_url,
			'description' => __( 'Artikel panduan menulis dan dunia literasi.', 'crediblecompany' ),
		);
	}

	return $pages;
}

/**
 * Parse IDR price numbers from strings like "Rp 1.250.000" or
 * "Rp 250.000 (Sastra), Rp 400.000 (Non Sastra)".
 *
 * @param string $raw_price Raw price text.
 * @return int[] List of parsed integer prices in IDR.
 */
function cc_parse_idr_prices( $raw_price ) {
	$raw_price = (string) $raw_price;
	if ( '' === trim( $raw_price ) ) {
		return array();
	}

	$prices = array();
	if ( preg_match_all( '/(?:Rp\.?\s*)?(\d{1,3}(?:[.,]\d{3})+|\d{5,})/i', $raw_price, $matches ) ) {
		foreach ( $matches[1] as $num_str ) {
			$digits = preg_replace( '/[^0-9]/', '', $num_str );
			$val    = intval( $digits );
			if ( $val > 0 ) {
				$prices[] = $val;
			}
		}
	}

	return array_values( array_unique( $prices ) );
}

/**
 * Retrieve structured publishing packages from CPT 'paket_jasa'.
 *
 * @return array
 */
function cc_get_publishing_packages_data() {
	$query = new WP_Query( array(
		'post_type'      => 'paket_jasa',
		'posts_per_page' => 50,
		'post_status'    => 'publish',
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	) );

	$packages    = array();
	$pricing_url = cc_get_key_page_url( 'pricing' );

	foreach ( $query->posts as $pkg ) {
		$price_display = (string) get_post_meta( $pkg->ID, '_cc_price', true );
		$badge         = (string) get_post_meta( $pkg->ID, '_cc_badge', true );
		$eksemplar     = (string) get_post_meta( $pkg->ID, '_cc_eksemplar', true );
		$ukuran        = (string) get_post_meta( $pkg->ID, '_cc_ukuran', true );
		$catatan       = (string) get_post_meta( $pkg->ID, '_cc_catatan', true );
		$btn_url       = (string) get_post_meta( $pkg->ID, '_cc_btn_url', true );
		$features_raw  = (string) get_post_meta( $pkg->ID, '_cc_features', true );
		$poster_image  = (string) get_post_meta( $pkg->ID, '_cc_paket_image', true );
		$features      = array_values( array_filter( array_map( 'trim', explode( "\n", $features_raw ) ) ) );

		$parsed_prices = cc_parse_idr_prices( $price_display );
		$resolved_url  = ( ! empty( $btn_url ) && '#' !== $btn_url ) ? cc_dynamic_wa_url( $btn_url ) : $pricing_url;

		$packages[] = array(
			'id'            => $pkg->ID,
			'name'          => get_the_title( $pkg ),
			'badge'         => $badge,
			'price_display' => $price_display,
			'prices_idr'    => $parsed_prices,
			'currency'      => 'IDR',
			'copies'        => $eksemplar,
			'size'          => $ukuran,
			'notes'         => $catatan,
			'features'      => $features,
			'image'         => $poster_image,
			'url'           => $resolved_url,
		);
	}

	return $packages;
}

/**
 * Retrieve structured FAQ items from Customizer with default fallback.
 *
 * @return array
 */
function cc_get_faqs_data() {
	$faq_json = cc_get( 'faq_repeater_data', '' );
	$faqs     = json_decode( (string) $faq_json, true );

	if ( is_array( $faqs ) && ! empty( $faqs ) ) {
		$clean = array();
		foreach ( $faqs as $item ) {
			if ( ! empty( $item['q'] ) && isset( $item['a'] ) ) {
				$clean[] = array(
					'q' => wp_strip_all_tags( (string) $item['q'] ),
					'a' => wp_strip_all_tags( (string) $item['a'] ),
				);
			}
		}
		if ( ! empty( $clean ) ) {
			return $clean;
		}
	}

	return array(
		array(
			'q' => 'Berapa lama estimasi proses penerbitan buku?',
			'a' => 'Estimasi proses penerbitan berkisar antara 1 hingga 3 minggu, bergantung pada kelengkapan naskah dan antrean pendaftaran ISBN/QRSBN di Perpusnas. Kami juga menyediakan fasilitas kilat khusus untuk pengurusan ISBN dalam 1-2 hari.',
		),
		array(
			'q' => 'Apakah penulis mendapatkan royalti dari penjualan buku melalui penerbit?',
			'a' => 'Ya, penulis akan mendapatkan royalti penjualan buku sebesar 25%. Laporan penjualan buku akan disampaikan secara transparan melalui email setiap bulannya.',
		),
		array(
			'q' => 'Bagaimana cara mendaftarkan naskah untuk diterbitkan?',
			'a' => 'Anda cukup memilih paket penerbitan yang sesuai dengan kebutuhan Anda di website kami, kemudian mengirimkan naskah Anda beserta data diri melalui WhatsApp admin atau email resmi kami.',
		),
		array(
			'q' => 'Keuntungan apa saja yang didapat oleh penulis saat menerbitkan buku?',
			'a' => 'Penulis mendapatkan fasilitas gratis ongkos kirim ke seluruh Indonesia, buku terindeks di Google Scholar dan Google Playbook, laporan royalti bulanan yang transparan, hak cipta buku (HAKI) dari Kemenkumham, serta bantuan pemasaran di marketplace nasional milik KBM Indonesia.',
		),
		array(
			'q' => 'Penerbit buku KBM Indonesia bisa menerbitkan buku apa saja?',
			'a' => 'Kami menerima berbagai jenis naskah baik fiksi maupun non-fiksi (sastra dan non-sastra), seperti novel, kumpulan puisi, buku ajar/dosen, biografi, opini, hingga buku ilmiah/penelitian.',
		),
		array(
			'q' => 'Apakah semua naskah yang masuk di Penerbit buku KBM Indonesia pasti terbit?',
			'a' => 'Kami berkomitmen membantu semua penulis untuk menerbitkan karyanya. Naskah Anda pasti terbit selama tidak mengandung unsur SARA, pornografi, plagiasi, atau pelanggaran hukum lainnya.',
		),
		array(
			'q' => 'Apakah setiap naskah yang akan diterbitkan akan diedit oleh penerbit?',
			'a' => 'Ya, naskah Anda akan melewati proses layout (tata letak) dan desain cover oleh tim ahli kami sesuai dengan ketentuan paket yang Anda pilih agar buku tampil profesional dan siap cetak.',
		),
		array(
			'q' => 'Bagaimana cara mengirim naskah buku ke penerbit?',
			'a' => 'Sangat gampang, yaitu langsung dikirim naskah buku anda via email ke ' . cc_get_company_email() . ' atau di kirim melalui nomer whatsapp kantor pusat di ' . cc_get_company_phone() . ' dan buku anda akan kami rubah ukurannya sesuai keinginan dan kebutuhan penulis.',
		),
	);
}

/**
 * Retrieve published books from OwwCommerce if active.
 *
 * @param int $limit Max items to return.
 * @return array
 */
function cc_get_books_data( $limit = 5 ) {
	if ( ! class_exists( '\OwwCommerce\Repositories\ProductRepository' ) || ! class_exists( '\OwwCommerce\Frontend\Router' ) ) {
		return array();
	}

	try {
		$repo  = new \OwwCommerce\Repositories\ProductRepository();
		$books = $repo->get_all( $limit, 0, array( 'orderby' => 'newest' ) );
	} catch ( \Throwable $e ) {
		return array();
	}

	if ( ! is_array( $books ) ) {
		return array();
	}

	$items = array();
	foreach ( $books as $book ) {
		if ( ! is_object( $book ) || empty( $book->title ) || empty( $book->slug ) ) {
			continue;
		}
		$price_val = null;
		if ( isset( $book->sale_price ) && is_numeric( $book->sale_price ) && floatval( $book->sale_price ) > 0 ) {
			$price_val = floatval( $book->sale_price );
		} elseif ( isset( $book->price ) && is_numeric( $book->price ) && floatval( $book->price ) > 0 ) {
			$price_val = floatval( $book->price );
		}

		$items[] = array(
			'title'     => (string) $book->title,
			'url'       => \OwwCommerce\Frontend\Router::get_product_link( $book->slug ),
			'image_url' => ! empty( $book->image_url ) ? (string) $book->image_url : '',
			'price'     => $price_val,
			'currency'  => 'IDR',
		);
	}

	return $items;
}

/**
 * Get site-wide latest modified timestamp in ISO 8601 format.
 *
 * @return string
 */
function cc_get_site_last_modified_iso() {
	if ( function_exists( 'get_lastpostmodified' ) ) {
		$last_gmt = get_lastpostmodified( 'GMT' );
		if ( ! empty( $last_gmt ) ) {
			$ts = strtotime( $last_gmt . ' UTC' );
			if ( $ts ) {
				return wp_date( 'c', $ts );
			}
		}
	}
	return wp_date( 'c' );
}

/* --------------------------------------------------------------------------
 * 2. Complete JSON-LD @graph Builder (Shared by Plugin & Fallback)
 * ---------------------------------------------------------------------- */

/**
 * Build a complete Schema.org JSON-LD @graph for the current page.
 *
 * @return array
 */
function cc_build_structured_data_graph() {
	$site_name = get_bloginfo( 'name' );
	$site_desc = get_bloginfo( 'description' );
	$home_url  = esc_url_raw( home_url( '/' ) );
	$email     = cc_get_company_email();
	$phone     = cc_get_company_phone_tel();
	$address   = cc_get_company_address();
	$same_as   = cc_get_social_links();

	$logo_url = '';
	if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
		$logo_url = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
	}
	if ( empty( $logo_url ) && function_exists( 'get_site_icon_url' ) ) {
		$logo_url = get_site_icon_url( 512 );
	}

	$org_id   = $home_url . '#organization';
	$lb_id    = $home_url . '#localbusiness';
	$site_id  = $home_url . '#website';

	$postal_address = array(
		'@type'           => 'PostalAddress',
		'streetAddress'   => $address,
		'addressLocality' => 'Sleman',
		'addressRegion'   => 'Daerah Istimewa Yogyakarta',
		'addressCountry'  => 'ID',
	);

	$org_node = array(
		'@type'        => 'Organization',
		'@id'          => $org_id,
		'name'         => $site_name,
		'url'          => $home_url,
		'description'  => $site_desc,
		'email'        => $email,
		'telephone'    => $phone,
		'address'      => $postal_address,
		'contactPoint' => array(
			'@type'             => 'ContactPoint',
			'telephone'         => $phone,
			'email'             => $email,
			'contactType'       => 'customer service',
			'areaServed'        => 'ID',
			'availableLanguage' => array( 'Indonesian' ),
		),
	);
	if ( ! empty( $logo_url ) ) {
		$org_node['logo'] = array(
			'@type' => 'ImageObject',
			'url'   => $logo_url,
		);
		$org_node['image'] = $logo_url;
	}
	if ( ! empty( $same_as ) ) {
		$org_node['sameAs'] = $same_as;
	}

	$lb_node = array(
		'@type'              => 'LocalBusiness',
		'@id'                => $lb_id,
		'name'               => $site_name,
		'url'                => $home_url,
		'description'        => $site_desc,
		'email'              => $email,
		'telephone'          => $phone,
		'address'            => $postal_address,
		'parentOrganization' => array( '@id' => $org_id ),
	);
	if ( ! empty( $logo_url ) ) {
		$lb_node['image'] = $logo_url;
	}

	$website_node = array(
		'@type'           => 'WebSite',
		'@id'             => $site_id,
		'name'            => $site_name,
		'description'     => $site_desc,
		'url'             => $home_url,
		'publisher'       => array( '@id' => $org_id ),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => $home_url . '?s={search_term_string}',
			'query-input' => 'required name=search_term_string',
		),
	);

	$graph = array( $org_node, $lb_node, $website_node );

	if ( is_front_page() || is_home() ) {
		$home_desc = cc_get( 'seo_home_description', '' );
		if ( '' === $home_desc ) {
			$home_desc = $site_desc;
		}

		$graph[] = array(
			'@type'       => 'WebPage',
			'@id'         => $home_url . '#webpage',
			'url'         => $home_url,
			'name'        => $site_name,
			'description' => $home_desc,
			'isPartOf'    => array( '@id' => $site_id ),
			'about'       => array( '@id' => $org_id ),
		);

		// Publishing packages -> Service + Offer / AggregateOffer.
		$packages        = cc_get_publishing_packages_data();
		$catalog_items   = array();
		$all_price_vals  = array();

		foreach ( $packages as $idx => $pkg ) {
			$service_desc = trim( implode( ' • ', array_filter( array( $pkg['copies'], $pkg['size'], $pkg['notes'] ) ) ) );
			if ( '' === $service_desc && ! empty( $pkg['features'] ) ) {
				$service_desc = implode( ', ', array_slice( $pkg['features'], 0, 5 ) );
			}

			$service_node = array(
				'@type'       => 'Service',
				'@id'         => $home_url . '#service-' . $pkg['id'],
				'name'        => 'Paket Penerbitan ' . $pkg['name'],
				'serviceType' => 'Jasa Penerbitan Buku',
				'provider'    => array( '@id' => $org_id ),
				'areaServed'  => 'ID',
				'url'         => $pkg['url'],
			);
			if ( '' !== $service_desc ) {
				$service_node['description'] = $service_desc;
			}
			if ( ! empty( $pkg['image'] ) ) {
				$service_node['image'] = $pkg['image'];
			}

			$prices = $pkg['prices_idr'];
			if ( 1 === count( $prices ) ) {
				$all_price_vals[]     = $prices[0];
				$service_node['offers'] = array(
					'@type'         => 'Offer',
					'price'         => (string) $prices[0],
					'priceCurrency' => 'IDR',
					'availability'  => 'https://schema.org/InStock',
					'url'           => $pkg['url'],
				);
			} elseif ( count( $prices ) > 1 ) {
				$low              = min( $prices );
				$high             = max( $prices );
				$all_price_vals[] = $low;
				$all_price_vals[] = $high;
				$service_node['offers'] = array(
					'@type'         => 'AggregateOffer',
					'lowPrice'      => (string) $low,
					'highPrice'     => (string) $high,
					'offerCount'    => count( $prices ),
					'priceCurrency' => 'IDR',
					'url'           => $pkg['url'],
				);
			}

			$graph[]         = $service_node;
			$catalog_items[] = array(
				'@type'    => 'ListItem',
				'position' => $idx + 1,
				'item'     => array( '@id' => $service_node['@id'] ),
			);
		}

		if ( ! empty( $all_price_vals ) ) {
			$graph[1]['priceRange'] = 'Rp ' . number_format( min( $all_price_vals ), 0, ',', '.' ) . ' - Rp ' . number_format( max( $all_price_vals ), 0, ',', '.' );
		}

		if ( ! empty( $catalog_items ) ) {
			$graph[] = array(
				'@type'           => 'ItemList',
				'@id'             => $home_url . '#packages',
				'name'            => 'Paket Penerbitan Buku',
				'itemListElement' => $catalog_items,
			);
		}

		// Books from OwwCommerce -> Product (+ Offer if price is present).
		$books = cc_get_books_data( intval( cc_get( 'books_limit', 5 ) ) );
		foreach ( $books as $b_idx => $book ) {
			$product_node = array(
				'@type'  => 'Product',
				'@id'    => $home_url . '#book-' . ( $b_idx + 1 ),
				'name'   => $book['title'],
				'url'    => $book['url'],
				'brand'  => array( '@id' => $org_id ),
			);
			if ( ! empty( $book['image_url'] ) ) {
				$product_node['image'] = $book['image_url'];
			}
			if ( null !== $book['price'] ) {
				$product_node['offers'] = array(
					'@type'         => 'Offer',
					'price'         => (string) $book['price'],
					'priceCurrency' => $book['currency'],
					'availability'  => 'https://schema.org/InStock',
					'url'           => $book['url'],
				);
			}
			$graph[] = $product_node;
		}

		// Latest blog posts -> Article.
		$recent_posts = get_posts( array(
			'post_type'      => 'post',
			'posts_per_page' => 4,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		) );
		foreach ( $recent_posts as $article_post ) {
			$article_node = array(
				'@type'         => 'Article',
				'@id'           => get_permalink( $article_post ) . '#article',
				'headline'      => get_the_title( $article_post ),
				'url'           => get_permalink( $article_post ),
				'datePublished' => get_the_date( 'c', $article_post ),
				'dateModified'  => get_the_modified_date( 'c', $article_post ),
				'author'        => array(
					'@type' => 'Person',
					'name'  => get_the_author_meta( 'display_name', $article_post->post_author ),
				),
				'publisher'     => array( '@id' => $org_id ),
			);
			if ( has_post_thumbnail( $article_post->ID ) ) {
				$article_node['image'] = get_the_post_thumbnail_url( $article_post->ID, 'large' );
			}
			$graph[] = $article_node;
		}

		// Latest testimonials -> Review.
		$testi_posts = get_posts( array(
			'post_type'      => 'testimoni',
			'posts_per_page' => min( 6, max( 1, intval( cc_get( 'testimonials_limit', 6 ) ) ) ),
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		) );
		foreach ( $testi_posts as $testi ) {
			$rating = get_post_meta( $testi->ID, '_customer_rating', true );
			if ( empty( $rating ) ) {
				$rating = get_post_meta( $testi->ID, 'cc_testimonial_rating', true ) ?: ( get_post_meta( $testi->ID, '_cc_testimoni_rating', true ) ?: 5 );
			}
			$client_name = get_post_meta( $testi->ID, '_customer_name', true );
			if ( empty( $client_name ) ) {
				$client_name = get_post_meta( $testi->ID, '_cc_testimoni_client_name', true ) ?: get_the_title( $testi );
			}
			$graph[] = array(
				'@type'        => 'Review',
				'@id'          => get_permalink( $testi ) . '#review',
				'itemReviewed' => array( '@id' => $org_id ),
				'reviewRating' => array(
					'@type'       => 'Rating',
					'ratingValue' => (string) $rating,
					'bestRating'  => '5',
				),
				'author'       => array(
					'@type' => 'Person',
					'name'  => $client_name,
				),
				'reviewBody'   => wp_trim_words( wp_strip_all_tags( $testi->post_content ), 45, '...' ),
			);
		}
	} elseif ( is_singular( 'post' ) ) {
		global $post;
		$article = array(
			'@type'         => 'Article',
			'@id'           => get_permalink( $post ) . '#article',
			'headline'      => get_the_title( $post ),
			'url'           => get_permalink( $post ),
			'datePublished' => get_the_date( 'c', $post ),
			'dateModified'  => get_the_modified_date( 'c', $post ),
			'author'        => array(
				'@type' => 'Person',
				'name'  => get_the_author(),
			),
			'publisher'     => array( '@id' => $org_id ),
		);
		if ( has_post_thumbnail( $post->ID ) ) {
			$article['image'] = array( get_the_post_thumbnail_url( $post->ID, 'large' ) );
		}
		$graph[] = $article;
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
		$graph[] = array(
			'@type'        => 'Review',
			'@id'          => get_permalink( $post ) . '#review',
			'itemReviewed' => array( '@id' => $org_id ),
			'reviewRating' => array(
				'@type'       => 'Rating',
				'ratingValue' => (string) $rating,
				'bestRating'  => '5',
			),
			'author'       => array(
				'@type' => 'Person',
				'name'  => $client_name,
			),
			'reviewBody'   => wp_trim_words( wp_strip_all_tags( $post->post_content ), 50, '' ),
		);
	} elseif ( is_singular() ) {
		global $post;
		$graph[] = array(
			'@type'    => 'WebPage',
			'@id'      => get_permalink( $post ) . '#webpage',
			'url'      => get_permalink( $post ),
			'name'     => get_the_title( $post ),
			'isPartOf' => array( '@id' => $site_id ),
			'about'    => array( '@id' => $org_id ),
		);
	}

	return array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);
}

/* --------------------------------------------------------------------------
 * 3. RFC 8288 Link Headers & HTML Discovery Links
 * ---------------------------------------------------------------------- */

/**
 * Build RFC 8288 Link header value for the current request.
 *
 * @return string
 */
function cc_get_agent_link_header_value() {
	$home_url   = home_url( '/' );
	$llms_url   = home_url( '/llms.txt' );
	$mcp_card   = home_url( '/.well-known/mcp/server-card.json' );
	$req_path   = cc_get_request_path();
	$md_target  = ( '' === $req_path ) ? add_query_arg( 'format', 'markdown', $home_url ) : add_query_arg( 'format', 'markdown', home_url( '/' . $req_path . '/' ) );

	$links = array(
		'<' . esc_url_raw( $llms_url ) . '>; rel="llms-txt"; type="text/markdown"',
		'<' . esc_url_raw( $md_target ) . '>; rel="alternate"; type="text/markdown"',
		'<' . esc_url_raw( $mcp_card ) . '>; rel="mcp-server-card"; type="application/json"',
	);

	return implode( ', ', $links );
}

add_filter( 'wp_headers', 'cc_add_agent_http_headers', 20 );
/**
 * Add RFC 8288 Link header and Vary: Accept to public WordPress HTTP responses.
 *
 * @param array $headers Existing response headers.
 * @return array
 */
function cc_add_agent_http_headers( $headers ) {
	if ( is_admin() ) {
		return $headers;
	}

	$link_val = cc_get_agent_link_header_value();
	if ( ! empty( $headers['Link'] ) ) {
		$headers['Link'] .= ', ' . $link_val;
	} else {
		$headers['Link'] = $link_val;
	}

	if ( ! empty( $headers['Vary'] ) ) {
		if ( false === stripos( $headers['Vary'], 'Accept' ) ) {
			$headers['Vary'] .= ', Accept';
		}
	} else {
		$headers['Vary'] = 'Accept';
	}

	return $headers;
}

add_action( 'wp_head', 'cc_output_agent_discovery_head_links', 4 );
/**
 * Output HTML <link> tags matching the Link response headers.
 */
function cc_output_agent_discovery_head_links() {
	$md_url = is_singular() ? add_query_arg( 'format', 'markdown', get_permalink() ) : add_query_arg( 'format', 'markdown', home_url( '/' ) );
	echo '<link rel="alternate" type="text/markdown" title="Markdown" href="' . esc_url( $md_url ) . '">' . "\n";
	echo '<link rel="llms-txt" type="text/markdown" href="' . esc_url( home_url( '/llms.txt' ) ) . '">' . "\n";
	echo '<link rel="mcp-server-card" type="application/json" href="' . esc_url( home_url( '/.well-known/mcp/server-card.json' ) ) . '">' . "\n";
}

/* --------------------------------------------------------------------------
 * 4. Content Negotiation: Accept: text/markdown (and ?format=markdown)
 * ---------------------------------------------------------------------- */

/**
 * Determine whether the current HTTP request prefers Markdown over HTML.
 *
 * @return bool
 */
function cc_is_markdown_request() {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return false;
	}

	if ( isset( $_GET['format'] ) ) {
		$fmt = strtolower( sanitize_text_field( wp_unslash( $_GET['format'] ) ) );
		if ( in_array( $fmt, array( 'markdown', 'md' ), true ) ) {
			return true;
		}
	}

	$accept = isset( $_SERVER['HTTP_ACCEPT'] ) ? strtolower( (string) wp_unslash( $_SERVER['HTTP_ACCEPT'] ) ) : '';
	if ( '' === $accept || false === strpos( $accept, 'text/markdown' ) ) {
		return false;
	}

	$md_q   = 0.0;
	$html_q = 0.0;

	foreach ( explode( ',', $accept ) as $part ) {
		$segments = array_map( 'trim', explode( ';', trim( $part ) ) );
		$mime     = isset( $segments[0] ) ? $segments[0] : '';
		$q        = 1.0;

		foreach ( array_slice( $segments, 1 ) as $param ) {
			if ( 0 === strpos( $param, 'q=' ) ) {
				$q = floatval( substr( $param, 2 ) );
			}
		}

		if ( 'text/markdown' === $mime ) {
			$md_q = max( $md_q, $q );
		} elseif ( 'text/html' === $mime ) {
			$html_q = max( $html_q, $q );
		}
	}

	return $md_q > 0 && $md_q >= $html_q;
}

/**
 * Convert a fragment of WordPress HTML content into clean Markdown.
 *
 * @param string $html HTML string.
 * @return string
 */
function cc_html_to_markdown( $html ) {
	$html = (string) $html;
	if ( '' === trim( $html ) ) {
		return '';
	}

	$html = preg_replace( '#<(script|style|noscript)\b[^>]*>.*?</\1>#is', '', $html );

	for ( $level = 1; $level <= 6; $level++ ) {
		$hashes = str_repeat( '#', $level );
		$html   = preg_replace_callback(
			'#<h' . $level . '\b[^>]*>(.*?)</h' . $level . '>#is',
			function ( $m ) use ( $hashes ) {
				return "\n\n" . $hashes . ' ' . trim( wp_strip_all_tags( $m[1] ) ) . "\n\n";
			},
			$html
		);
	}

	$html = preg_replace_callback(
		'#<a\b[^>]*href=(["\'])(.*?)\1[^>]*>(.*?)</a>#is',
		function ( $m ) {
			$text = trim( wp_strip_all_tags( $m[3] ) );
			$href = html_entity_decode( $m[2], ENT_QUOTES, 'UTF-8' );
			if ( '' === $text ) {
				return $href;
			}
			return '[' . $text . '](' . $href . ')';
		},
		$html
	);

	$html = preg_replace_callback(
		'#<img\b[^>]*>#is',
		function ( $m ) {
			$alt = '';
			$src = '';
			if ( preg_match( '/alt=(["\'])(.*?)\1/i', $m[0], $am ) ) {
				$alt = trim( $am[2] );
			}
			if ( preg_match( '/src=(["\'])(.*?)\1/i', $m[0], $sm ) ) {
				$src = trim( $sm[2] );
			}
			return $src ? '![' . $alt . '](' . $src . ')' : '';
		},
		$html
	);

	$html = preg_replace_callback(
		'#<(strong|b)\b[^>]*>(.*?)</\1>#is',
		function ( $m ) {
			return '**' . trim( wp_strip_all_tags( $m[2] ) ) . '**';
		},
		$html
	);

	$html = preg_replace_callback(
		'#<(em|i)\b[^>]*>(.*?)</\1>#is',
		function ( $m ) {
			return '*' . trim( wp_strip_all_tags( $m[2] ) ) . '*';
		},
		$html
	);

	$html = preg_replace_callback(
		'#<blockquote\b[^>]*>(.*?)</blockquote>#is',
		function ( $m ) {
			$lines = preg_split( '/\r?\n/', trim( wp_strip_all_tags( $m[1] ) ) );
			$lines = array_map( function ( $l ) { return '> ' . trim( $l ); }, $lines );
			return "\n\n" . implode( "\n", $lines ) . "\n\n";
		},
		$html
	);

	$html = preg_replace( '#<li\b[^>]*>#i', "\n- ", $html );
	$html = preg_replace( '#</li>#i', '', $html );
	$html = preg_replace( '#<(p|div|section|article|tr|ul|ol|figure|figcaption)\b[^>]*>#i', "\n\n", $html );
	$html = preg_replace( '#</(p|div|section|article|tr|ul|ol|figure|figcaption)>#i', "\n\n", $html );
	$html = preg_replace( '#<br\s*/?>#i', "\n", $html );

	$text = wp_strip_all_tags( $html );
	$text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
	$text = preg_replace( "/[ \t]+\n/", "\n", $text );
	$text = preg_replace( "/\n{3,}/", "\n\n", $text );

	return trim( $text );
}

/**
 * Build full Markdown document for the front page.
 *
 * @return string
 */
function cc_build_homepage_markdown() {
	$site_name = get_bloginfo( 'name' );
	$site_desc = get_bloginfo( 'description' );
	$home_url  = home_url( '/' );
	$hero_title = cc_get( 'hero_title', 'Penerbit Buku Profesional' );
	$hero_desc  = cc_get( 'hero_desc', 'Penerbit Buku KBM Indonesia mempersembahkan lebih dari 5.000 judul buku. Yuk, wujudkan mimpimu menjadi penulis — jadikan naskahmu memiliki ISBN.' );
	$about_desc = cc_get( 'about_desc', 'Penerbit Buku KBM Indonesia telah terbukti menjadi mitra penerbitan buku ber ISBN yang terbaik di Indonesia.' );

	$lines   = array();
	$lines[] = '# ' . $site_name;
	$lines[] = '';
	if ( '' !== $site_desc ) {
		$lines[] = '> ' . $site_desc;
		$lines[] = '';
	}
	$lines[] = 'URL: ' . $home_url;
	$lines[] = '';
	$lines[] = '## ' . $hero_title;
	$lines[] = '';
	$lines[] = $hero_desc;
	$lines[] = '';
	$lines[] = '## Tentang Kami';
	$lines[] = '';
	$lines[] = $about_desc;
	$lines[] = '';

	// Features.
	$feat_defaults = array(
		array( 'Profesional', 'Naskah ditangani tim ahli layout, desain sampul, dan admin marketing.' ),
		array( 'Harga Murah', 'Biaya penerbitan terjangkau untuk cetakan 25 eksemplar ke atas.' ),
		array( 'Proses Cepat', 'Proses cepat dan dapat menyesuaikan jadwal kebutuhan cetak penulis.' ),
		array( 'Free Ongkir', 'Gratis ongkos kirim ke seluruh wilayah Indonesia.' ),
		array( 'Proses Bergaransi', 'Garansi cetak ulang tanpa tambahan biaya apabila terjadi cacat produksi.' ),
		array( 'Distribusi Luas', 'Dibantu distribusi ke marketplace nasional milik penerbit.' ),
	);
	$features_count = intval( cc_get( 'features_count', 3 ) );
	$lines[] = '## ' . cc_get( 'features_main_title', 'Mengapa Memilih Kami?' );
	$lines[] = '';
	for ( $i = 1; $i <= $features_count; $i++ ) {
		$idx   = $i - 1;
		$def_t = isset( $feat_defaults[ $idx ] ) ? $feat_defaults[ $idx ][0] : 'Keunggulan ' . $i;
		$def_d = isset( $feat_defaults[ $idx ] ) ? $feat_defaults[ $idx ][1] : '';
		$lines[] = '- **' . cc_get( "feat_title_{$i}", $def_t ) . '**: ' . cc_get( "feat_desc_{$i}", $def_d );
	}
	$lines[] = '';

	// Packages & Pricing.
	$packages = cc_get_publishing_packages_data();
	$lines[]  = '## ' . cc_dynamic_text( cc_get( 'pricing_title', 'PAKET PENERBIT BUKU KBM INDONESIA' ) );
	$lines[]  = '';
	if ( ! empty( $packages ) ) {
		foreach ( $packages as $pkg ) {
			$meta_parts = array_filter( array( $pkg['price_display'], $pkg['copies'], $pkg['size'] ) );
			$heading    = '### ' . $pkg['name'];
			if ( '' !== $pkg['badge'] ) {
				$heading .= ' (' . $pkg['badge'] . ')';
			}
			$lines[] = $heading;
			if ( ! empty( $meta_parts ) ) {
				$lines[] = '- Harga & Spesifikasi: ' . implode( ' | ', $meta_parts );
			}
			if ( '' !== $pkg['notes'] ) {
				$lines[] = '- Catatan: ' . $pkg['notes'];
			}
			if ( ! empty( $pkg['features'] ) ) {
				$lines[] = '- Fasilitas: ' . implode( '; ', $pkg['features'] );
			}
			$lines[] = '- Tautan Pemesanan: ' . $pkg['url'];
			$lines[] = '';
		}
	}

	// Books.
	$books = cc_get_books_data( intval( cc_get( 'books_limit', 5 ) ) );
	if ( ! empty( $books ) ) {
		$lines[] = '## ' . cc_get( 'books_title', 'Buku Terbitan KBM' );
		$lines[] = '';
		foreach ( $books as $book ) {
			$price_txt = ( null !== $book['price'] ) ? ' — Rp ' . number_format( $book['price'], 0, ',', '.' ) : '';
			$lines[]   = '- [' . $book['title'] . '](' . $book['url'] . ')' . $price_txt;
		}
		$lines[] = '';
	}

	// Recent Articles.
	$recent_posts = get_posts( array(
		'post_type'      => 'post',
		'posts_per_page' => 4,
		'post_status'    => 'publish',
	) );
	if ( ! empty( $recent_posts ) ) {
		$lines[] = '## ' . cc_get( 'blog_title', 'Blog & Berita' );
		$lines[] = '';
		foreach ( $recent_posts as $p ) {
			$lines[] = '- [' . get_the_title( $p ) . '](' . get_permalink( $p ) . ') (' . get_the_date( 'Y-m-d', $p ) . ')';
		}
		$lines[] = '';
	}

	// FAQs.
	$faqs = cc_get_faqs_data();
	if ( ! empty( $faqs ) ) {
		$lines[] = '## ' . cc_get( 'faq_title', 'Pertanyaan yang Sering Diajukan' );
		$lines[] = '';
		foreach ( $faqs as $faq ) {
			$lines[] = '### ' . $faq['q'];
			$lines[] = $faq['a'];
			$lines[] = '';
		}
	}

	// Contact & Legal links.
	$lines[] = '## Kontak & Halaman Penting';
	$lines[] = '';
	$lines[] = '- Alamat: ' . cc_get_company_address();
	$lines[] = '- Email: [' . cc_get_company_email() . '](mailto:' . cc_get_company_email() . ')';
	$lines[] = '- Telepon: [' . cc_get_company_phone() . '](tel:' . cc_get_company_phone_tel() . ')';
	foreach ( cc_get_key_pages_map() as $page_info ) {
		$lines[] = '- [' . $page_info['label'] . '](' . $page_info['url'] . '): ' . $page_info['description'];
	}
	$lines[] = '- [llms.txt](' . home_url( '/llms.txt' ) . ')';
	$lines[] = '- [MCP Server Card](' . home_url( '/.well-known/mcp/server-card.json' ) . ')';

	return implode( "\n", $lines ) . "\n";
}

/**
 * Build Markdown representation for any singular/archive/search WordPress view.
 *
 * @return string
 */
function cc_build_current_view_markdown() {
	if ( is_front_page() || ( is_home() && ! get_option( 'page_for_posts' ) ) ) {
		return cc_build_homepage_markdown();
	}

	if ( is_singular() ) {
		global $post;
		setup_postdata( $post );
		$title   = get_the_title( $post );
		$url     = get_permalink( $post );
		$date    = get_the_modified_date( 'c', $post ) ?: get_the_date( 'c', $post );
		$content = apply_filters( 'the_content', $post->post_content );
		wp_reset_postdata();

		$lines   = array();
		$lines[] = '# ' . $title;
		$lines[] = '';
		$lines[] = '- URL: ' . $url;
		$lines[] = '- Updated: ' . $date;
		$lines[] = '- Author: ' . get_the_author_meta( 'display_name', $post->post_author );
		$lines[] = '';
		$lines[] = cc_html_to_markdown( $content );
		$lines[] = '';
		return implode( "\n", $lines );
	}

	$lines   = array();
	$title   = wp_get_document_title();
	$lines[] = '# ' . wp_strip_all_tags( $title );
	$lines[] = '';
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$excerpt = wp_strip_all_tags( get_the_excerpt() );
			$lines[] = '## [' . get_the_title() . '](' . get_permalink() . ')';
			$lines[] = 'Date: ' . get_the_date( 'Y-m-d' );
			if ( '' !== $excerpt ) {
				$lines[] = '';
				$lines[] = $excerpt;
			}
			$lines[] = '';
		}
		wp_reset_postdata();
	}

	return implode( "\n", $lines ) . "\n";
}

/* --------------------------------------------------------------------------
 * 5. Virtual Fallback Pages (/kontak/, /syarat-ketentuan/)
 * ---------------------------------------------------------------------- */

/**
 * Return virtual page definition if current path matches a built-in fallback
 * route and no real WordPress post/page occupies that path.
 *
 * @param string $path Normalized request path.
 * @return array|null
 */
function cc_get_virtual_page_data( $path ) {
	$path = trim( $path, '/' );
	if ( '' === $path ) {
		return null;
	}

	// Only activate virtual fallback if no real WP page exists at this path.
	$existing = get_page_by_path( $path );
	if ( $existing instanceof WP_Post && 'publish' === $existing->post_status ) {
		return null;
	}

	$site_name = get_bloginfo( 'name' );
	$email     = cc_get_company_email();
	$phone     = cc_get_company_phone();
	$tel       = cc_get_company_phone_tel();
	$address   = cc_get_company_address();

	if ( in_array( $path, array( 'kontak', 'contact', 'hubungi-kami' ), true ) ) {
		$wa_num  = preg_replace( '/[^0-9]/', '', (string) cc_get( 'whatsapp_number', '6281357517526' ) );
		$wa_link = $wa_num ? 'https://wa.me/' . $wa_num : '';

		$html  = '<p>Silakan hubungi tim ' . esc_html( $site_name ) . ' untuk konsultasi penerbitan buku, pengajuan naskah, penawaran paket cetak, maupun kerja sama kelembagaan.</p>';
		$html .= '<h2>Informasi Kontak Resmi</h2>';
		$html .= '<ul>';
		$html .= '<li><strong>Alamat Kantor:</strong> ' . esc_html( $address ) . '</li>';
		$html .= '<li><strong>Email Resmi:</strong> <a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a></li>';
		$html .= '<li><strong>Telepon:</strong> <a href="tel:' . esc_attr( $tel ) . '">' . esc_html( $phone ) . '</a></li>';
		if ( '' !== $wa_link ) {
			$html .= '<li><strong>WhatsApp:</strong> <a href="' . esc_url( $wa_link ) . '" target="_blank" rel="noopener">' . esc_html( $phone ) . '</a></li>';
		}
		$html .= '</ul>';
		$html .= '<h2>Jam Layanan</h2>';
		$html .= '<p>Senin – Sabtu, 08.00 – 17.00 WIB.</p>';

		return array(
			'title' => __( 'Kontak Kami', 'crediblecompany' ),
			'html'  => $html,
		);
	}

	if ( in_array( $path, array( 'syarat-ketentuan', 'syarat-dan-ketentuan', 'terms', 'terms-and-conditions' ), true ) ) {
		$html  = '<p>Halaman ini memuat syarat dan ketentuan penggunaan layanan penerbitan buku di ' . esc_html( $site_name ) . '.</p>';
		$html .= '<h2>1. Keaslian Naskah &amp; Hak Cipta</h2>';
		$html .= '<p>Penulis menjamin bahwa naskah yang dikirimkan adalah karya asli, bebas dari plagiarisme, tidak melanggar hak cipta pihak lain, serta tidak mengandung unsur SARA, ujaran kebencian, pornografi, atau konten yang melanggar hukum di Indonesia.</p>';
		$html .= '<h2>2. Proses Produksi &amp; ISBN/QRSBN</h2>';
		$html .= '<p>Estimasi proses layout, desain cover, dan pengurusan ISBN/QRSBN mengikuti ketentuan Perpustakaan Nasional RI dan jadwal paket penerbitan yang dipilih oleh penulis.</p>';
		$html .= '<h2>3. Royalti &amp; Laporan Penjualan</h2>';
		$html .= '<p>Penulis berhak memperoleh royalti sebesar 25% dari penjualan buku melalui saluran penerbit sesuai ketentuan paket, dengan laporan berkala yang dikirimkan melalui email resmi.</p>';
		$html .= '<h2>4. Garansi Kualitas Cetak</h2>';
		$html .= '<p>Kami memberikan garansi cetak ulang apabila terdapat cacat produksi fisik pada buku yang diterima penulis, sesuai ketentuan konfirmasi layanan purna jual.</p>';
		$html .= '<h2>5. Hubungi Kami</h2>';
		$html .= '<p>Pertanyaan terkait syarat dan ketentuan dapat dikirimkan ke <a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a> atau <a href="tel:' . esc_attr( $tel ) . '">' . esc_html( $phone ) . '</a>.</p>';

		return array(
			'title' => __( 'Syarat & Ketentuan', 'crediblecompany' ),
			'html'  => $html,
		);
	}

	return null;
}

/* --------------------------------------------------------------------------
 * 6. /llms.txt & /.well-known/mcp/server-card.json & Virtual Route Handling
 * ---------------------------------------------------------------------- */

add_action( 'init', 'cc_register_agent_rewrite_rules' );
/**
 * Register rewrite rules for agent-discovery endpoints.
 */
function cc_register_agent_rewrite_rules() {
	add_rewrite_rule( '^llms\.txt$', 'index.php?cc_llms_txt=1', 'top' );
	add_rewrite_rule( '^\.well-known/mcp/server-card\.json$', 'index.php?cc_mcp_card=1', 'top' );
}

add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'cc_llms_txt';
	$vars[] = 'cc_mcp_card';
	return $vars;
} );

add_filter( 'redirect_canonical', function ( $redirect_url ) {
	$path = cc_get_request_path();
	if ( in_array( $path, array( 'llms.txt', 'sitemap.xml', '.well-known/mcp/server-card.json' ), true ) ) {
		return false;
	}
	return $redirect_url;
} );

add_filter( 'pre_handle_404', function ( $preempt ) {
	$path = cc_get_request_path();
	if ( in_array( $path, array( 'llms.txt', 'sitemap.xml', '.well-known/mcp/server-card.json' ), true ) ) {
		return true;
	}
	if ( null !== cc_get_virtual_page_data( $path ) ) {
		return true;
	}
	return $preempt;
} );

/**
 * Render /llms.txt Markdown payload.
 *
 * @return string
 */
function cc_build_llms_txt_content() {
	$site_name = get_bloginfo( 'name' );
	$site_desc = get_bloginfo( 'description' );
	$home_url  = home_url( '/' );
	$email     = cc_get_company_email();
	$phone     = cc_get_company_phone();
	$address   = cc_get_company_address();

	$lines   = array();
	$lines[] = '# ' . $site_name;
	$lines[] = '';
	$lines[] = '> ' . ( '' !== $site_desc ? $site_desc : 'Layanan penerbitan buku ber-ISBN, cetak buku, dan toko buku resmi.' );
	$lines[] = '';
	$lines[] = $site_name . ' menyediakan layanan penerbitan buku profesional (layout, desain sampul, pengurusan ISBN/QRSBN Perpusnas, HAKI, distribusi marketplace, dan gratis ongkir ke seluruh Indonesia).';
	$lines[] = '';
	$lines[] = '- Website: ' . $home_url;
	$lines[] = '- Email: ' . $email;
	$lines[] = '- Telepon: ' . $phone;
	$lines[] = '- Alamat: ' . $address;
	$lines[] = '';

	$lines[] = '## Halaman Utama & Informasi Bisnis';
	$lines[] = '';
	$lines[] = '- [Beranda](' . $home_url . '): Halaman utama berisi profil, keunggulan, paket harga, buku terbitan, testimoni, blog, dan FAQ.';
	foreach ( cc_get_key_pages_map() as $info ) {
		$lines[] = '- [' . $info['label'] . '](' . $info['url'] . '): ' . $info['description'];
	}
	$lines[] = '';

	$packages = cc_get_publishing_packages_data();
	if ( ! empty( $packages ) ) {
		$lines[] = '## Layanan & Paket Penerbitan';
		$lines[] = '';
		foreach ( $packages as $pkg ) {
			$details = array_filter( array( $pkg['price_display'], $pkg['copies'], $pkg['size'] ) );
			$summary = ! empty( $details ) ? implode( ', ', $details ) : 'Paket layanan penerbitan buku.';
			$lines[] = '- [' . $pkg['name'] . '](' . $pkg['url'] . '): ' . $summary;
		}
		$lines[] = '';
	}

	$books = cc_get_books_data( 10 );
	if ( ! empty( $books ) ) {
		$lines[] = '## Katalog Buku Terbitan';
		$lines[] = '';
		foreach ( $books as $book ) {
			$price_info = ( null !== $book['price'] ) ? ' (Rp ' . number_format( $book['price'], 0, ',', '.' ) . ')' : '';
			$lines[]    = '- [' . $book['title'] . '](' . $book['url'] . ')' . $price_info;
		}
		$lines[] = '';
	}

	$posts = get_posts( array(
		'post_type'      => 'post',
		'posts_per_page' => 10,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );
	if ( ! empty( $posts ) ) {
		$lines[] = '## Artikel Terbaru';
		$lines[] = '';
		foreach ( $posts as $post_obj ) {
			$lines[] = '- [' . get_the_title( $post_obj ) . '](' . get_permalink( $post_obj ) . '): ' . wp_trim_words( wp_strip_all_tags( $post_obj->post_content ), 20, '...' );
		}
		$lines[] = '';
	}

	$lines[] = '## Endpoint Machine-Readable';
	$lines[] = '';
	$lines[] = '- [XML Sitemap](' . home_url( '/sitemap.xml' ) . '): Daftar URL terbaru beserta tanggal pembaruan (<lastmod>).';
	$lines[] = '- [Markdown Homepage](' . add_query_arg( 'format', 'markdown', $home_url ) . '): Versi Markdown halaman utama (juga mendukung header Accept: text/markdown).';
	$lines[] = '- [MCP Server Card](' . home_url( '/.well-known/mcp/server-card.json' ) . '): Metadata SEP-1649 untuk endpoint MCP JSON-RPC.';

	return implode( "\n", $lines ) . "\n";
}

/**
 * Return tool definitions shared by MCP Server Card and MCP JSON-RPC endpoint.
 *
 * @return array
 */
function cc_get_mcp_tool_definitions() {
	return array(
		array(
			'name'        => 'get_publishing_packages',
			'description' => 'List all available book publishing packages with IDR prices, copy quantities, book sizes, and included facilities.',
			'inputSchema' => array(
				'type'       => 'object',
				'properties' => array(
					'max_price_idr' => array(
						'type'        => 'number',
						'description' => 'Optional maximum package price filter in IDR.',
					),
				),
			),
		),
		array(
			'name'        => 'search_site_content',
			'description' => 'Search published articles, pages, and testimonials on the site by keyword.',
			'inputSchema' => array(
				'type'       => 'object',
				'properties' => array(
					'query' => array(
						'type'        => 'string',
						'description' => 'Search keyword or phrase.',
					),
					'limit' => array(
						'type'        => 'integer',
						'description' => 'Maximum number of results to return (default 5, max 20).',
					),
				),
				'required'   => array( 'query' ),
			),
		),
		array(
			'name'        => 'get_contact_info',
			'description' => 'Get official company contact details (email, phone, WhatsApp, office address) and links to About, Contact, Terms, Privacy, and Pricing pages.',
			'inputSchema' => array(
				'type'       => 'object',
				'properties' => new \stdClass(),
			),
		),
		array(
			'name'        => 'get_faqs',
			'description' => 'Retrieve frequently asked questions and answers about book publishing services, ISBN, and royalties.',
			'inputSchema' => array(
				'type'       => 'object',
				'properties' => array(
					'topic' => array(
						'type'        => 'string',
						'description' => 'Optional keyword to filter FAQ entries.',
					),
				),
			),
		),
	);
}

/**
 * Build SEP-1649 MCP Server Card array.
 *
 * @return array
 */
function cc_build_mcp_server_card() {
	$site_name    = get_bloginfo( 'name' );
	$site_desc    = get_bloginfo( 'description' );
	$theme_ver    = wp_get_theme()->get( 'Version' ) ?: '1.0.0';
	$mcp_endpoint = rest_url( 'crediblecompany/v1/mcp' );
	$slug         = sanitize_title( $site_name ) ?: 'credible-company';

	return array(
		'$schema'         => 'https://modelcontextprotocol.io/schemas/server-card/sep-1649.json',
		'name'            => $slug . '-mcp',
		'version'         => $theme_ver,
		'description'     => ( '' !== $site_desc ? $site_desc : $site_name ) . ' — Public read-only MCP server for publishing packages, site search, FAQs, and official contact info.',
		'protocolVersion' => '2025-03-26',
		'serverInfo'      => array(
			'name'    => $slug . '-mcp',
			'title'   => $site_name,
			'version' => $theme_ver,
		),
		'transport'       => array(
			'type'     => 'streamable-http',
			'endpoint' => $mcp_endpoint,
		),
		'endpoint'        => $mcp_endpoint,
		'url'             => $mcp_endpoint,
		'capabilities'    => array(
			'tools'     => array( 'listChanged' => false ),
			'resources' => array(
				'subscribe'   => false,
				'listChanged' => false,
			),
		),
		'tools'           => cc_get_mcp_tool_definitions(),
		'resources'       => array(
			array(
				'uri'         => home_url( '/llms.txt' ),
				'name'        => 'llms.txt',
				'description' => 'Markdown summary of the site, publishing packages, books, articles, and key links.',
				'mimeType'    => 'text/markdown',
			),
		),
		'authentication'  => array(
			'required' => false,
		),
	);
}

add_action( 'template_redirect', 'cc_handle_agent_endpoints', 1 );
/**
 * Serve /llms.txt, /.well-known/mcp/server-card.json, virtual fallback pages,
 * and Accept: text/markdown responses before page caching runs.
 */
function cc_handle_agent_endpoints() {
	global $wp_query;

	$path = cc_get_request_path();

	// 1. /llms.txt
	if ( get_query_var( 'cc_llms_txt' ) || 'llms.txt' === $path ) {
		if ( $wp_query ) {
			$wp_query->is_404 = false;
		}
		status_header( 200 );
		header( 'Content-Type: text/markdown; charset=utf-8' );
		header( 'Vary: Accept' );
		header( 'X-Robots-Tag: all' );
		echo cc_build_llms_txt_content();
		exit;
	}

	// 2. /.well-known/mcp/server-card.json
	if ( get_query_var( 'cc_mcp_card' ) || '.well-known/mcp/server-card.json' === $path ) {
		if ( $wp_query ) {
			$wp_query->is_404 = false;
		}
		status_header( 200 );
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Access-Control-Allow-Origin: *' );
		echo wp_json_encode( cc_build_mcp_server_card(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		exit;
	}

	// 3. Fallback sitemap.xml if core plugin is not active or older than 1.0.3.
	if ( ( get_query_var( 'cc_sitemap' ) || 'sitemap.xml' === $path ) && ( ! defined( 'CC_CORE_VERSION' ) || version_compare( CC_CORE_VERSION, '1.0.3', '<' ) ) ) {
		cc_render_agent_ready_sitemap();
		exit;
	}

	// 4. Virtual fallback pages (/kontak/, /syarat-ketentuan/).
	$virtual = cc_get_virtual_page_data( $path );
	if ( null !== $virtual ) {
		if ( $wp_query ) {
			$wp_query->is_404  = false;
			$wp_query->is_page = true;
		}
		status_header( 200 );

		if ( cc_is_markdown_request() ) {
			header( 'Content-Type: text/markdown; charset=utf-8' );
			header( 'Vary: Accept' );
			header( 'Link: ' . cc_get_agent_link_header_value(), false );
			echo '# ' . $virtual['title'] . "\n\n" . cc_html_to_markdown( $virtual['html'] ) . "\n";
			exit;
		}

		add_filter( 'document_title_parts', function ( $parts ) use ( $virtual ) {
			$parts['title'] = $virtual['title'];
			return $parts;
		} );

		get_header();
		?>
		<div class="main-content-page">
			<article class="page type-page status-publish">
				<header class="page-hero">
					<div class="container">
						<h1 class="entry-title"><?php echo esc_html( $virtual['title'] ); ?></h1>
					</div>
				</header>
				<div class="page-content-wrapper">
					<div class="page-card">
						<div class="entry-content">
							<?php echo wp_kses_post( $virtual['html'] ); ?>
						</div>
					</div>
				</div>
			</article>
		</div>
		<?php
		get_footer();
		exit;
	}

	// 5. Accept: text/markdown content negotiation for standard WordPress pages.
	if ( cc_is_markdown_request() ) {
		$status = is_404() ? 404 : 200;
		status_header( $status );
		header( 'Content-Type: text/markdown; charset=utf-8' );
		header( 'Vary: Accept' );
		header( 'Link: ' . cc_get_agent_link_header_value(), false );
		echo cc_build_current_view_markdown();
		exit;
	}
}

/* --------------------------------------------------------------------------
 * 7. Live MCP JSON-RPC 2.0 Endpoint (/wp-json/crediblecompany/v1/mcp)
 * ---------------------------------------------------------------------- */

add_action( 'rest_api_init', function () {
	register_rest_route( 'crediblecompany/v1', '/mcp', array(
		array(
			'methods'             => 'GET',
			'callback'            => function () {
				return new WP_REST_Response( cc_build_mcp_server_card(), 200 );
			},
			'permission_callback' => '__return_true',
		),
		array(
			'methods'             => 'POST',
			'callback'            => 'cc_handle_mcp_jsonrpc',
			'permission_callback' => '__return_true',
		),
	) );
} );

/**
 * Execute an MCP tool by name and return formatted MCP tool result.
 *
 * @param string $tool_name Tool identifier.
 * @param array  $args      Tool arguments.
 * @return array
 */
function cc_execute_mcp_tool( $tool_name, $args = array() ) {
	switch ( $tool_name ) {
		case 'get_publishing_packages':
			$packages = cc_get_publishing_packages_data();
			if ( isset( $args['max_price_idr'] ) && is_numeric( $args['max_price_idr'] ) ) {
				$max      = floatval( $args['max_price_idr'] );
				$packages = array_values( array_filter( $packages, function ( $p ) use ( $max ) {
					if ( empty( $p['prices_idr'] ) ) {
						return false;
					}
					return min( $p['prices_idr'] ) <= $max;
				} ) );
			}
			return array(
				'content' => array(
					array(
						'type' => 'text',
						'text' => wp_json_encode( $packages, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ),
					),
				),
			);

		case 'search_site_content':
			$query = isset( $args['query'] ) ? sanitize_text_field( (string) $args['query'] ) : '';
			$limit = isset( $args['limit'] ) ? min( 20, max( 1, intval( $args['limit'] ) ) ) : 5;
			if ( '' === $query ) {
				return array(
					'isError' => true,
					'content' => array( array( 'type' => 'text', 'text' => 'Missing required parameter: query' ) ),
				);
			}
			$q = new WP_Query( array(
				's'              => $query,
				'post_type'      => array( 'post', 'page', 'testimoni' ),
				'post_status'    => 'publish',
				'posts_per_page' => $limit,
			) );
			$results = array();
			foreach ( $q->posts as $p ) {
				$results[] = array(
					'title'     => get_the_title( $p ),
					'url'       => get_permalink( $p ),
					'post_type' => $p->post_type,
					'date'      => get_the_date( 'Y-m-d', $p ),
					'excerpt'   => wp_trim_words( wp_strip_all_tags( $p->post_content ), 30, '...' ),
				);
			}
			return array(
				'content' => array(
					array(
						'type' => 'text',
						'text' => wp_json_encode( $results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ),
					),
				),
			);

		case 'get_contact_info':
			$info = array(
				'name'      => get_bloginfo( 'name' ),
				'email'     => cc_get_company_email(),
				'phone'     => cc_get_company_phone(),
				'tel_uri'   => 'tel:' . cc_get_company_phone_tel(),
				'mailto'    => 'mailto:' . cc_get_company_email(),
				'address'   => cc_get_company_address(),
				'pages'     => cc_get_key_pages_map(),
				'social'    => cc_get_social_links(),
			);
			return array(
				'content' => array(
					array(
						'type' => 'text',
						'text' => wp_json_encode( $info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ),
					),
				),
			);

		case 'get_faqs':
			$faqs  = cc_get_faqs_data();
			$topic = isset( $args['topic'] ) ? strtolower( sanitize_text_field( (string) $args['topic'] ) ) : '';
			if ( '' !== $topic ) {
				$faqs = array_values( array_filter( $faqs, function ( $item ) use ( $topic ) {
					return false !== stripos( $item['q'] . ' ' . $item['a'], $topic );
				} ) );
			}
			return array(
				'content' => array(
					array(
						'type' => 'text',
						'text' => wp_json_encode( $faqs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ),
					),
				),
			);

		default:
			return array(
				'isError' => true,
				'content' => array( array( 'type' => 'text', 'text' => 'Unknown tool: ' . $tool_name ) ),
			);
	}
}

/**
 * Handle MCP JSON-RPC 2.0 POST requests.
 *
 * @param WP_REST_Request $request Incoming request.
 * @return WP_REST_Response
 */
function cc_handle_mcp_jsonrpc( WP_REST_Request $request ) {
	$body   = $request->get_json_params();
	$id     = isset( $body['id'] ) ? $body['id'] : null;
	$method = isset( $body['method'] ) ? (string) $body['method'] : '';
	$params = isset( $body['params'] ) && is_array( $body['params'] ) ? $body['params'] : array();

	if ( 'notifications/initialized' === $method ) {
		return new WP_REST_Response( null, 202 );
	}

	$site_name = get_bloginfo( 'name' );
	$theme_ver = wp_get_theme()->get( 'Version' ) ?: '1.0.0';
	$slug      = sanitize_title( $site_name ) ?: 'credible-company';

	switch ( $method ) {
		case 'initialize':
			$result = array(
				'protocolVersion' => '2025-03-26',
				'capabilities'    => array(
					'tools'     => array( 'listChanged' => false ),
					'resources' => array( 'subscribe' => false, 'listChanged' => false ),
				),
				'serverInfo'      => array(
					'name'    => $slug . '-mcp',
					'title'   => $site_name,
					'version' => $theme_ver,
				),
			);
			break;

		case 'ping':
			$result = new \stdClass();
			break;

		case 'tools/list':
			$result = array( 'tools' => cc_get_mcp_tool_definitions() );
			break;

		case 'tools/call':
			$tool_name = isset( $params['name'] ) ? (string) $params['name'] : '';
			$tool_args = isset( $params['arguments'] ) && is_array( $params['arguments'] ) ? $params['arguments'] : array();
			$result    = cc_execute_mcp_tool( $tool_name, $tool_args );
			break;

		case 'resources/list':
			$result = array(
				'resources' => array(
					array(
						'uri'      => home_url( '/llms.txt' ),
						'name'     => 'llms.txt',
						'mimeType' => 'text/markdown',
					),
				),
			);
			break;

		case 'resources/read':
			$uri    = isset( $params['uri'] ) ? (string) $params['uri'] : '';
			$result = array(
				'contents' => array(
					array(
						'uri'      => $uri ?: home_url( '/llms.txt' ),
						'mimeType' => 'text/markdown',
						'text'     => cc_build_llms_txt_content(),
					),
				),
			);
			break;

		default:
			return new WP_REST_Response( array(
				'jsonrpc' => '2.0',
				'id'      => $id,
				'error'   => array(
					'code'    => -32601,
					'message' => 'Method not found: ' . $method,
				),
			), 200 );
	}

	return new WP_REST_Response( array(
		'jsonrpc' => '2.0',
		'id'      => $id,
		'result'  => $result,
	), 200 );
}

/* --------------------------------------------------------------------------
 * 8. Backward-Compatible Fallbacks (if core plugin < 1.0.3 or inactive)
 * ---------------------------------------------------------------------- */

// Ensure Content-Signal is always present in robots.txt even if core plugin is < 1.0.3.
add_filter( 'robots_txt', function ( $output, $public ) {
	if ( '0' == $public ) {
		return $output;
	}
	if ( false === stripos( $output, 'Content-Signal:' ) ) {
		$signal = trim( (string) cc_get( 'seo_content_signal', 'search=yes, ai-input=yes, ai-train=no' ) );
		if ( '' === $signal ) {
			$signal = 'search=yes, ai-input=yes, ai-train=no';
		}
		$directive = 'Content-Signal: ' . $signal;
		if ( preg_match( '/^User-agent:\s*\*/mi', $output ) ) {
			$output = preg_replace( '/^(User-agent:\s*\*)/mi', "$1\n" . $directive, $output, 1 );
		} else {
			$output .= "\nUser-agent: *\n" . $directive . "\n";
		}
	}
	return $output;
}, 20, 2 );

// Ensure wp-sitemap.xml and sub-sitemaps also carry <lastmod>.
add_filter( 'wp_sitemaps_index_entry', function ( $entry ) {
	if ( empty( $entry['lastmod'] ) ) {
		$entry['lastmod'] = cc_get_site_last_modified_iso();
	}
	return $entry;
} );
add_filter( 'wp_sitemaps_posts_entry', function ( $entry, $post ) {
	if ( empty( $entry['lastmod'] ) && $post instanceof WP_Post ) {
		$entry['lastmod'] = get_the_modified_date( 'c', $post ) ?: get_the_date( 'c', $post );
	}
	return $entry;
}, 10, 2 );
add_filter( 'wp_sitemaps_taxonomies_entry', function ( $entry ) {
	if ( empty( $entry['lastmod'] ) ) {
		$entry['lastmod'] = cc_get_site_last_modified_iso();
	}
	return $entry;
} );
add_filter( 'wp_sitemaps_users_entry', function ( $entry ) {
	if ( empty( $entry['lastmod'] ) ) {
		$entry['lastmod'] = cc_get_site_last_modified_iso();
	}
	return $entry;
} );

/**
 * Render XML Sitemap with <lastmod> on all URLs (homepage, posts, pages, testimoni, taxonomies).
 * Used by both seo-optimizer.php (v1.0.3+) and theme fallback when plugin is older/inactive.
 */
function cc_render_agent_ready_sitemap() {
	global $wpdb, $wp_query;

	if ( $wp_query ) {
		$wp_query->is_404 = false;
	}
	status_header( 200 );
	header( 'Content-Type: application/xml; charset=utf-8' );

	$site_lastmod = cc_get_site_last_modified_iso();

	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

	// Homepage.
	echo "  <url>\n";
	echo '    <loc>' . esc_url( home_url( '/' ) ) . "</loc>\n";
	echo '    <lastmod>' . esc_html( $site_lastmod ) . "</lastmod>\n";
	echo "    <changefreq>daily</changefreq>\n";
	echo "    <priority>1.0</priority>\n";
	echo "  </url>\n";

	// Posts, Pages & Testimoni ordered by most recently modified.
	$posts = get_posts( array(
		'post_type'      => array( 'post', 'page', 'testimoni' ),
		'post_status'    => 'publish',
		'posts_per_page' => 500,
		'orderby'        => 'modified',
		'order'          => 'DESC',
		'fields'         => 'ids',
	) );
	foreach ( $posts as $post_id ) {
		$post_date = get_the_modified_date( 'c', $post_id ) ?: get_the_date( 'c', $post_id );
		echo "  <url>\n";
		echo '    <loc>' . esc_url( get_permalink( $post_id ) ) . "</loc>\n";
		echo '    <lastmod>' . esc_html( $post_date ) . "</lastmod>\n";
		echo "    <changefreq>weekly</changefreq>\n";
		echo "    <priority>0.8</priority>\n";
		echo "  </url>\n";
	}

	// Precompute latest post_modified_gmt per term_taxonomy_id in one query.
	$term_lastmod_map = array();
	$rows             = $wpdb->get_results(
		"SELECT tr.term_taxonomy_id, MAX(p.post_modified_gmt) AS latest_mod
		 FROM {$wpdb->term_relationships} tr
		 INNER JOIN {$wpdb->posts} p ON p.ID = tr.object_id
		 WHERE p.post_status = 'publish'
		 GROUP BY tr.term_taxonomy_id"
	);
	if ( is_array( $rows ) ) {
		foreach ( $rows as $row ) {
			if ( ! empty( $row->latest_mod ) && '0000-00-00 00:00:00' !== $row->latest_mod ) {
				$ts = strtotime( $row->latest_mod . ' UTC' );
				if ( $ts ) {
					$term_lastmod_map[ intval( $row->term_taxonomy_id ) ] = wp_date( 'c', $ts );
				}
			}
		}
	}

	foreach ( array( 'category', 'post_tag' ) as $tax ) {
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
				$tt_id   = intval( $term->term_taxonomy_id );
				$lastmod = isset( $term_lastmod_map[ $tt_id ] ) ? $term_lastmod_map[ $tt_id ] : $site_lastmod;
				echo "  <url>\n";
				echo '    <loc>' . esc_url( $term_link ) . "</loc>\n";
				echo '    <lastmod>' . esc_html( $lastmod ) . "</lastmod>\n";
				echo "    <changefreq>weekly</changefreq>\n";
				echo "    <priority>0.5</priority>\n";
				echo "  </url>\n";
			}
		}
	}

	echo '</urlset>';
}

// Fallback JSON-LD graph in theme when plugin < 1.0.3 is running.
add_action( 'wp_head', function () {
	if ( defined( 'CC_CORE_AGENT_SCHEMA' ) ) {
		return;
	}
	remove_action( 'wp_head', 'cc_json_ld_schema', 2 );
	$schema = cc_build_structured_data_graph();
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}, 1 );
