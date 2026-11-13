# Credible Company — WordPress Theme

![Version](https://img.shields.io/badge/Theme-v2.2.4-c01314?style=for-the-badge)
![Core Plugin](https://img.shields.io/badge/Core%20Plugin-v1.0.3-1e293b?style=for-the-badge)
![WordPress](https://img.shields.io/badge/WordPress-5.9+-21759B?style=for-the-badge&logo=wordpress&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php&logoColor=white)

Tema WordPress custom untuk website **Credible Company / Penerbit KBM** dengan pendekatan *mobile-first*, arsitektur modular (`templates/`, `template-parts/`, `inc/`, `assets/`), plugin pendamping **Credible Company Core**, serta dukungan penuh **Agent-Readiness** untuk mesin pencari dan AI Agent modern.

---

## ✨ Fitur Utama

### 1. Tampilan, Customizer & Performa
- **Arsitektur Modular**: Seluruh template halaman berada di `templates/`, komponen UI di `template-parts/`, CSS di `assets/css/`, dan modul JS di `assets/js/modules/`.
- **Advanced Customizer**: Pengaturan lengkap di *Appearance → Customize* untuk Header (classic, centered, glass), Hero (v1–v3), Statistik, Fitur, Tentang Kami, Paket Jasa/Harga, Katalog Buku, Testimoni, Blog, FAQ, Mitra, Footer, Media Sosial, Whitelabel Admin, serta SEO Settings.
- **Anchor Section Landing Page**: Navigasi mulus ke section `#hero`, `#how-it-works`, `#daftar-paket`, `#books`, `#testimonials`, `#blog`, `#faq`, dan `#mitra`.
- **TOC Generator & Load More AJAX**: Daftar isi otomatis untuk artikel blog dan paginasi AJAX tanpa reload penuh.

### 2. Plugin Pendamping (`credible-company-core` v1.0.3) & Auto-Update
Fitur Custom Post Types (**Paket Jasa**, **Tim Marketing**, **Testimoni**), **Dynamic WhatsApp Routing** (`?ref=sales`), serta optimizer SEO, keamanan, dan performa (minifikasi HTML & CC Page Cache) dipisahkan secara bersih ke plugin pendamping di `bundled-plugins/credible-company-core/`.
- **Auto-Update Otomatis**: Setiap tema diperbarui, `inc/plugin-installer.php` otomatis memperbarui plugin `credible-company-core` dari `bundled-plugins/credible-company-core.zip` melalui WordPress `Plugin_Upgrader` (dengan fallback tombol update 1-klik jika `WP_Filesystem` membutuhkan kredensial FTP/SSH).
- **Graceful Fallbacks**: Jika plugin core belum aktif, `inc/plugin-fallbacks.php` memastikan tema tetap berjalan aman tanpa fatal error.

### 3. Sistem Testimoni Lengkap
- **Submit & Cropper Foto 1:1**: Halaman `/submit-testimoni/` dilengkapi Cropper.js untuk memotong foto profil persegi (500×500 px), dikonversi menjadi berkas WebP dengan penamaan unik (`foto-testimoni-[slug]-[uniqid].webp`) untuk mencegah tabrakan nama file, serta CSRF nonce dinamis via AJAX agar tetap kompatibel dengan page cache.
- **Pencarian Nama Klien**: Kolom pencarian nama pengulas di arsip `/testimoni/`.
- **Edit Mandiri & Moderasi Admin**: Tombol edit ikon halus pada kartu testimoni (muncul saat hover di desktop) dan halaman detail mengarah ke `/submit-testimoni/?edit=<id>` yang otomatis mengisi data & pratinjau foto lama; setelah dikirim ulang, status post otomatis kembali ke **`pending`** untuk ditinjau admin sebelum tayang.

### 4. Kesiapan AI Agent (*Agent-Readiness* — `inc/agent-ready.php`)
- **Content Signals (`robots.txt`)**: Menyertakan arahan `Content-Signal` yang dapat dikonfigurasi via Customizer (default `search=yes, ai-input=yes, ai-train=no`).
- **XML Sitemap dengan `<lastmod>`**: `/sitemap.xml` dan `/wp-sitemap.xml` memancarkan tanggal pembaruan `<lastmod>` pada seluruh URL tanpa trailing-slash redirect paksa.
- **JSON-LD `@graph` Komprehensif**: Memancarkan node `Organization`, `LocalBusiness`, `WebSite`, `WebPage`, `Service` (termasuk `OfferCatalog` & `Offer` harga IDR), `ItemList`, `Article`, `Review`, dan `FAQPage`.
- **Informasi Kontak, Legal & Harga Statis**: Tautan halaman Tentang, Paket Harga, Kontak (`/kontak/`), Syarat & Ketentuan (`/syarat-ketentuan/`), dan Kebijakan Privasi otomatis dilengkapi di footer beserta link `mailto:`, `tel:`, `<address>`, dan daftar harga paket statis tanpa perlu JavaScript/login.
- **`/llms.txt` & Negosiasi Markdown**: Endpoint `/llms.txt` (HTTP 200) serta dukungan `Accept: text/markdown` dan `?format=markdown` yang menghasilkan representasi Markdown bersih (`Content-Type: text/markdown; charset=utf-8`).
- **RFC 8288 `Link` Headers**: Response header `Link` otomatis menunjuk ke `/llms.txt`, versi Markdown `alternate`, dan MCP Server Card, disertai `Vary: Accept`.
- **WebMCP & MCP Server Card (SEP-1649)**:
  - `assets/js/modules/webmcp-tools.js` mendaftarkan tool WebMCP (`search_site_content`, `get_publishing_packages`, `get_contact_and_business_info`, `get_faqs`) melalui `navigator.modelContext.registerTool()` saat tersedia, serta atribut deklaratif (`toolname`, `tooldescription`, `toolparamdescription`) pada form pencarian.
  - Kartu server MCP dipublikasikan di `/.well-known/mcp/server-card.json` dan dilayani endpoint JSON-RPC 2.0 di `/wp-json/crediblecompany/v1/mcp`.

---

## 🚀 Instalasi & Pembaruan di WordPress

1. Unduh **`Crediblecompany.zip`** dari halaman [GitHub Releases](https://github.com/rasyiqism/Crediblecompany-WordPress-Theme/releases).
2. Di WordPress Admin, buka **Tampilan → Tema → Tambah Baru → Unggah Tema**, pilih `Crediblecompany.zip`, lalu aktifkan atau pilih **Ganti tema yang aktif dengan yang diunggah**.
3. Buka Dashboard WordPress: jika `credible-company-core` versi lama sudah terpasang, tema akan otomatis memperbaruinya ke versi bundled terbaru (`v1.0.3`). Jika belum terpasang, klik tombol aktivasi/instalasi 1-klik pada notice admin.
4. Setelah update tema/plugin, lakukan **Purge / Clear All Cache** (FlyingPress, CDN, dan CC Cache).
5. Lengkapi data email resmi, nomor telepon, `Content-Signal`, dan URL halaman kunci melalui **Appearance → Customize → Global Settings → SEO Settings**.

---

## 📂 Struktur Direktori

```text
├── assets/
│   ├── css/                # Modular CSS (base, components, landing, responsive, sections)
│   ├── images/             # Aset gambar tema
│   └── js/                 # customizer-controls/preview & modules/ (webmcp-tools, submit-testimoni, dll.)
├── bundled-plugins/
│   ├── credible-company-core/      # Source plugin pendamping (CPT, WA routing, optimizers)
│   └── credible-company-core.zip   # Paket instalasi & auto-update plugin (v1.0.3)
├── inc/
│   ├── agent-ready.php             # Schema JSON-LD, llms.txt, Markdown, Link header, MCP endpoint
│   ├── customizer/                 # Panel & setting WordPress Customizer
│   ├── enqueue.php                 # Enqueue CSS/JS modular + cache busting versi tema
│   ├── helpers.php                 # Helper tema
│   ├── plugin-fallbacks.php        # Fallback saat plugin core belum aktif
│   ├── plugin-installer.php        # Auto-updater & 1-click installer plugin core
│   ├── testimoni-edit-handler.php  # Handler submit & edit testimoni + WebP upload
│   └── ...
├── template-parts/         # Komponen section & kartu (hero, pricing, books, card-testimoni, faq, dll.)
├── templates/              # Template halaman penuh (front-page, page-submit-testimoni, single/archive)
├── tools/                  # Utilitas bantu (fix-testimoni-photos)
├── functions.php           # Bootstrap loader & template resolver
├── header.php / footer.php # Layout global header & footer
├── Crediblecompany.zip     # Arsip tema siap unggah ke WordPress
└── style.css               # Metadata & versi tema (2.2.4)
```

---

## 📝 Lisensi

Dirilis di bawah lisensi [MIT License](https://opensource.org/licenses/MIT) oleh **Rasyiqi — Crediblemark** (`https://crediblemark.com/`).
