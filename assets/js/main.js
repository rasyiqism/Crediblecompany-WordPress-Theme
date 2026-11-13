/**
 * Credible Company — Main JavaScript (Orchestrator)
 *
 * File ini berfungsi sebagai entry point dan dokumentasi modul JS.
 * Setiap modul fungsional dipecah ke file terpisah di /modules/
 * dan di-enqueue secara conditional via WordPress (inc/enqueue.php).
 *
 * DAFTAR MODUL:
 * ┌─────────────────────────────┬──────────────────────────────────────┬─────────────────────────────────┐
 * │ File                        │ Fungsi                               │ Di-load pada                    │
 * ├─────────────────────────────┼──────────────────────────────────────┼─────────────────────────────────┤
 * │ modules/menu-toggle.js      │ Mobile hamburger menu toggle         │ Semua halaman (global)          │
 * │ modules/faq-accordion.js    │ FAQ section expand/collapse          │ Front page & home               │
 * │ modules/toc-drawer.js       │ TOC drawer + IntersectionObserver    │ Single post saja                │
 * │ modules/sidebar-drawer.js   │ Sidebar drawer (swipe-aware)         │ Single post & single testimoni  │
 * │ modules/share-btn.js        │ Web Share API + clipboard fallback   │ Single post & single testimoni  │
 * │ modules/load-more.js        │ AJAX load more posts/testimoni       │ Home, archive, single testimoni │
 * │ modules/submit-testimoni.js │ Form validator + AJAX nonce fetch    │ Page template submit-testimoni  │
 * │ modules/webmcp-tools.js     │ WebMCP tool registration             │ Semua halaman (global)          │
 * └─────────────────────────────┴──────────────────────────────────────┴─────────────────────────────────┘
 *
 * KONVENSI:
 * - Setiap modul menggunakan IIFE pattern: (function() { 'use strict'; ... })();
 * - Setiap modul self-initializing — tidak perlu dipanggil dari luar.
 * - Untuk menambah fitur global baru, tambahkan di bawah file ini.
 *
 * @package CredibleCompany
 */

// Semua logika ada di modules/ — file ini menjadi dokumentasi hidup.
// Tambahkan script global di bawah baris ini jika diperlukan di masa depan.
