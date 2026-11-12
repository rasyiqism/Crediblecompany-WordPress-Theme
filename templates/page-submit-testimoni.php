<?php
/**
 * Template Name: Submit Testimoni
 *
 * Laman Front-End Form Entry Testimonial.
 * Nonce CSRF diambil secara dinamis via AJAX sebelum form disubmit
 * sehingga halaman ini boleh di-cache oleh plugin caching.
 * 
 * @package CredibleCompany
 */

get_header(); ?>

<div class="app-main-content">
    <div class="app-header-bar">
        <a href="javascript:history.back()" class="app-back-btn">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="app-page-title">Bagikan Pengalaman Anda</h1>
    </div>

    <div class="app-feed-container form-container-app">
        
        <?php if ( isset( $_GET['success'] ) && $_GET['success'] == 'true' ) : ?>
            <div class="alert-success-app">
                <div class="alert-icon">✓</div>
                <div class="alert-text">
                    <strong>Terima kasih!</strong><br>
                    Ulasan Anda telah kami terima dan akan melalui proses moderasi sebelum ditayangkan.
                </div>
            </div>
        <?php endif; ?>

        <?php if ( isset( $_GET['err'] ) ) : ?>
            <div class="alert-error-app">Mohon lengkapi semua bidang yang wajib diisi.</div>
        <?php endif; ?>

        <div class="form-split-wrapper">
            <div class="form-instruction-side">
                <div class="instruction-card">
                    <div class="instruction-icon">
                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11_a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h2>Mengapa ulasan Anda berharga?</h2>
                    <p>Ulasan jujur Anda membantu kami terus tumbuh dan memberikan layanan terbaik bagi Anda dan pelanggan lainnya.</p>
                    
                    <ul class="instruction-steps">
                        <li>
                            <span class="step-num">1</span>
                            <div class="step-text">
                                <strong>Tulis Pengalaman Anda</strong>
                                <p>Ceritakan hal yang paling berkesan bagi Anda.</p>
                            </div>
                        </li>
                        <li>
                            <span class="step-num">2</span>
                            <div class="step-text">
                                <strong>Beri Rating</strong>
                                <p>Seberapa puas Anda dengan layanan kami?</p>
                            </div>
                        </li>
                        <li>
                            <span class="step-num">3</span>
                            <div class="step-text">
                                <strong>Lengkapi Profil</strong>
                                <p>Opsional, tambahkan foto agar ulasan terasa personal.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="form-actual-side">
                <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" class="cc-submit-form" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="submit_testimoni_action">
                    <!-- Nonce CSRF diisi oleh JS sebelum submit (kompatibel dengan cache plugin) -->
                    <input type="hidden" name="_cc_nonce" id="cc_form_nonce" value="">
                    
                    <!-- Honeypot Field untuk memblokir spam bots secara pasif -->
                    <div style="display: none;" aria-hidden="true">
                        <label for="cc_hp_email">Confirm Email</label>
                        <input type="text" id="cc_hp_email" name="cc_hp_email" value="" tabindex="-1" autocomplete="off">
                    </div>
                    
                    <div class="form-group-app">
                        <label for="client_name">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" id="client_name" name="client_name" class="form-control" required placeholder="Cth: Budi Santoso">
                    </div>

                    <div class="form-group-app">
                        <label for="client_title">Pekerjaan / Jabatan <span class="req">*</span></label>
                        <input type="text" id="client_title" name="client_title" class="form-control" required placeholder="Cth: Penulis Buku, C.E.O">
                    </div>

                    <div class="form-group-app">
                        <label for="client_city">Kota Asal <span class="req">*</span></label>
                        <input type="text" id="client_city" name="client_city" class="form-control" required placeholder="Cth: Jakarta, Surabaya">
                    </div>

                    <div class="form-group-app">
                        <label>Rating Kepuasan <span class="req">*</span></label>
                        <div class="star-rating-input">
                            <select name="client_rating" id="client_rating" class="form-control" required>
                                <option value="5">⭐⭐⭐⭐⭐ (Sangat Puas)</option>
                                <option value="4">⭐⭐⭐⭐ (Puas)</option>
                                <option value="3">⭐⭐⭐ (Cukup)</option>
                                <option value="2">⭐⭐ (Kurang)</option>
                                <option value="1">⭐ (Sangat Kurang)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group-app">
                        <label for="client_review">Detail Ulasan <span class="req">*</span></label>
                        <textarea id="client_review" name="client_review" class="form-control" rows="5" required placeholder="Ceritakan pengalaman memuaskan Anda..."></textarea>
                    </div>

                    <div class="form-group-app">
                        <label for="client_photo">Foto Profil <span class="req">*</span></label>
                        <div class="custom-file-upload">
                            <!-- name="client_photo" removed so the original large file is not uploaded in $_FILES -->
                            <input type="file" id="client_photo" class="form-control-file" required accept="image/png, image/jpeg, image/jpg, image/webp">
                            <input type="hidden" name="client_photo_base64" id="client_photo_base64" value="" required>
                            <div class="upload-ui">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <span>Pilih Gambar</span>
                            </div>
                        </div>
                        <small class="form-help">Pilih foto apa saja (JPG/PNG/WEBP) untuk dipotong menjadi kotak (square).</small>
                    </div>

                    <div class="form-actions-app">
                        <button type="submit" class="btn-primary-app btn-submit-full">Kirim Ulasan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- Cropper Modal Overlay -->
<div id="cc-cropper-modal" class="cc-cropper-modal">
    <div class="cc-cropper-modal-content">
        <div class="cc-cropper-modal-header">
            <h3>Potong Foto Profil</h3>
            <button type="button" id="cc-cropper-close" class="cc-cropper-close-btn">&times;</button>
        </div>
        <div class="cc-cropper-modal-body">
            <div class="cc-cropper-img-container">
                <img id="cc-cropper-image" src="" alt="Image to crop">
            </div>
        </div>
        <div class="cc-cropper-modal-footer">
            <button type="button" id="cc-cropper-cancel" class="btn btn-secondary-app">Batal</button>
            <button type="button" id="cc-cropper-save" class="btn btn-primary-app">Potong & Simpan</button>
        </div>
    </div>
</div>



<?php get_footer(); ?>
