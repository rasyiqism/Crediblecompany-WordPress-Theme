<?php
/**
 * Template Part: Header Search Overlay - Full Page Model.
 * Desain pencarian layar penuh yang premium, modern, dan sangat responsif.
 * Komentar di kode menggunakan bahasa Indonesia.
 *
 * @package CredibleCompany
 */
?>
<!-- Form Pencarian Overlay Layar Penuh -->
<div id="header-search-overlay" class="header-search-overlay-full" style="display: none;">
    <!-- Tombol Tutup/Close -->
    <button type="button" class="search-close-btn" id="header-search-close" aria-label="<?php esc_attr_e( 'Tutup Pencarian', 'crediblecompany' ); ?>">&times;</button>
    
    <!-- Konten Box Tengah -->
    <div class="search-overlay-content">
        <h2 class="search-overlay-title">
            <?php 
            /* Menampilkan judul pencarian dinamis berdasarkan nama situs */
            printf( esc_html__( 'Cari di %s', 'crediblecompany' ), esc_html( get_bloginfo( 'name' ) ) ); 
            ?>
        </h2>
        
        <form role="search" method="get" class="search-form-full" action="<?php echo esc_url( home_url( '/' ) ); ?>" toolname="search_site_overlay" tooldescription="Search articles, books, and testimonials on this website.">
            <div class="search-input-wrapper-full">
                <!-- Input kata kunci -->
                <input type="search" class="search-field-full" placeholder="<?php esc_attr_e( 'Ketik kata kunci dan tekan Enter...', 'crediblecompany' ); ?>" value="<?php echo get_search_query(); ?>" name="s" toolparamdescription="Search keyword or phrase" autocomplete="off" />
                <!-- Tombol submit -->
                <button type="submit" class="search-submit-full" aria-label="<?php esc_attr_e( 'Kirim Pencarian', 'crediblecompany' ); ?>">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
        </form>
        
        <p class="search-tip"><?php esc_html_e( 'Tekan ESC untuk menutup pencarian', 'crediblecompany' ); ?></p>
    </div>
</div>

<style>
/* --------------------------------------------------------------------------
 * Desain Search Full Page Overlay
 * ---------------------------------------------------------------------- */
.header-search-overlay-full {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background-color: rgba(15, 23, 42, 0.97) !important; /* Gelap pekat transparan */
    backdrop-filter: blur(20px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
    z-index: 999999 !important; /* Di atas elemen sticky apa pun */
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-sizing: border-box !important;
}

.header-search-overlay-full.active {
    opacity: 1 !important;
    pointer-events: auto !important;
}

/* Tombol Close Silang */
.search-close-btn {
    position: absolute !important;
    top: 2.5rem !important;
    right: 3rem !important;
    background: transparent !important;
    border: none !important;
    color: rgba(255, 255, 255, 0.6) !important;
    font-size: 3.5rem !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    line-height: 1 !important;
    padding: 0.25rem !important;
    z-index: 1000000 !important;
    font-family: Arial, sans-serif !important;
}

.search-close-btn:hover {
    color: var(--brand-yellow, #EAB308) !important;
    transform: rotate(90deg) !important;
}

/* Box Content Tengah */
.search-overlay-content {
    width: 100% !important;
    max-width: 800px !important;
    padding: 2.5rem !important;
    text-align: center !important;
    transform: translateY(40px) !important;
    transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) !important; /* Efek bounce halus */
    box-sizing: border-box !important;
}

.header-search-overlay-full.active .search-overlay-content {
    transform: translateY(0) !important;
}

.search-overlay-title {
    color: #ffffff !important;
    font-size: 2.25rem !important;
    font-weight: 800 !important;
    margin-bottom: 3rem !important;
    letter-spacing: -0.5px !important;
}

.search-form-full {
    width: 100% !important;
}

.search-input-wrapper-full {
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    border-bottom: 3px solid rgba(255, 255, 255, 0.15) !important;
    padding-bottom: 0.75rem !important;
    transition: border-color 0.3s ease !important;
}

.search-input-wrapper-full:focus-within {
    border-bottom-color: var(--brand-yellow, #EAB308) !important;
}

.search-field-full {
    width: 100% !important;
    background: transparent !important;
    border: none !important;
    outline: none !important;
    font-size: 2rem !important;
    color: #ffffff !important;
    padding: 0 4rem 0 0.5rem !important;
    font-weight: 600 !important;
    line-height: 1.2 !important;
    box-sizing: border-box !important;
}

.search-field-full::placeholder {
    color: rgba(255, 255, 255, 0.3) !important;
}

.search-submit-full {
    position: absolute !important;
    right: 0.5rem !important;
    background: transparent !important;
    border: none !important;
    color: rgba(255, 255, 255, 0.5) !important;
    cursor: pointer !important;
    padding: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.2s ease !important;
}

.search-submit-full:hover {
    color: var(--brand-yellow, #EAB308) !important;
    transform: scale(1.1) !important;
}

.search-submit-full svg {
    width: 32px !important;
    height: 32px !important;
    stroke: currentColor !important;
}

.search-tip {
    color: rgba(255, 255, 255, 0.35) !important;
    font-size: 0.875rem !important;
    margin-top: 2rem !important;
    font-weight: 500 !important;
    letter-spacing: 0.5px !important;
}

/* Responsif Mobile */
@media (max-width: 768px) {
    .search-overlay-title {
        font-size: 1.75rem !important;
        margin-bottom: 2rem !important;
    }
    
    .search-field-full {
        font-size: 1.35rem !important;
    }
    
    .search-close-btn {
        top: 1.5rem !important;
        right: 1.5rem !important;
        font-size: 2.75rem !important;
    }
    
    .search-overlay-content {
        padding: 1.5rem !important;
    }
}
</style>

<script>
    /* Skrip Interaktivitas Open/Close Search Layar Penuh */
    document.addEventListener('DOMContentLoaded', function() {
        var searchToggle  = document.getElementById('header-search-toggle');
        var searchOverlay = document.getElementById('header-search-overlay');
        var searchClose   = document.getElementById('header-search-close');
        
        if (searchToggle && searchOverlay) {
            var searchInput = searchOverlay.querySelector('input[type="search"]');

            /* Fungsi Buka Overlay */
            function openSearch(e) {
                e.preventDefault();
                e.stopPropagation();
                
                /* Tampilkan wadah kontainer secara flexbox */
                searchOverlay.style.setProperty('display', 'flex', 'important');
                
                /* Delay mikro agar transisi opacity opacity terpicu */
                setTimeout(function() {
                    searchOverlay.classList.add('active');
                    if (searchInput) searchInput.focus();
                }, 15);
            }

            /* Fungsi Tutup Overlay */
            function closeSearch(e) {
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                
                searchOverlay.classList.remove('active');
                
                /* Sembunyikan display setelah efek fade-out opacity selesai (300ms) */
                setTimeout(function() {
                    if (!searchOverlay.classList.contains('active')) {
                        searchOverlay.style.setProperty('display', 'none', 'important');
                    }
                }, 300);
            }

            /* Pemicu Klik */
            searchToggle.addEventListener('click', openSearch);
            if (searchClose) {
                searchClose.addEventListener('click', closeSearch);
            }

            /* Integrasi Tombol Keyboard ESC (Escape) */
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && searchOverlay.classList.contains('active')) {
                    closeSearch(e);
                }
            });
        }
    });
</script>
