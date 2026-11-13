<?php
/**
 * Template Name: Submit Testimoni
 *
 * Laman Front-End Form Entry Testimonial (dan Edit Mode).
 * Nonce CSRF diambil secara dinamis via AJAX sebelum form disubmit
 * sehingga halaman ini boleh di-cache oleh plugin caching.
 * 
 * @package CredibleCompany
 */

get_header(); 

$edit_id   = isset( $_GET['edit'] ) ? intval( $_GET['edit'] ) : 0;
$edit_post = ( $edit_id > 0 ) ? get_post( $edit_id ) : null;
$is_edit   = ( $edit_post && 'testimoni' === $edit_post->post_type );

$name       = $is_edit ? ( get_post_meta( $edit_id, '_customer_name', true ) ?: $edit_post->post_title ) : '';
$title      = $is_edit ? get_post_meta( $edit_id, '_customer_profession', true ) : '';
$city       = $is_edit ? get_post_meta( $edit_id, '_customer_city', true ) : '';
$rating     = $is_edit ? ( intval( get_post_meta( $edit_id, '_customer_rating', true ) ) ?: 5 ) : 5;
$review     = $is_edit ? $edit_post->post_content : '';
$has_thumb  = $is_edit ? has_post_thumbnail( $edit_id ) : false;
$thumb_url  = $has_thumb ? get_the_post_thumbnail_url( $edit_id, 'medium' ) : '';
?>

<div class="app-main-content">
    <div class="app-header-bar">
        <a href="javascript:history.back()" class="app-back-btn">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="app-page-title"><?php echo $is_edit ? 'Perbarui Ulasan Anda' : 'Bagikan Pengalaman Anda'; ?></h1>
    </div>

    <div class="app-feed-container form-container-app">
        
        <?php if ( isset( $_GET['success'] ) && $_GET['success'] == 'updated' ) : ?>
            <div class="alert-success-app">
                <div class="alert-icon">✓</div>
                <div class="alert-text">
                    <strong>Pembaruan Berhasil Dikirim!</strong><br>
                    Ulasan & foto Anda telah berhasil diperbarui dan saat ini <em>berstatus ditangguhkan (pending)</em> untuk diverifikasi ulang oleh tim admin sebelum ditayangkan kembali.
                </div>
            </div>
        <?php elseif ( isset( $_GET['success'] ) && $_GET['success'] == 'true' ) : ?>
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

        <?php if ( $is_edit ) : ?>
            <div class="alert-info-app" style="display: flex; align-items: flex-start; gap: 12px; background: #eff6ff; border: 1.5px solid #bfdbfe; color: #1e40af; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; font-size: 0.9rem; line-height: 1.5;">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <strong>Mode Pembaruan Ulasan:</strong><br>
                    Anda sedang memperbarui ulasan untuk <strong><?php echo esc_html( $name ); ?></strong>. Setelah disimpan, ulasan akan <em>ditangguhkan kembali (pending)</em> oleh sistem untuk ditinjau ulang oleh admin sebelum aktif kembali.
                </div>
            </div>
        <?php endif; ?>

        <div class="form-split-wrapper">
            <div class="form-instruction-side">
                <div class="instruction-card">
                    <div class="instruction-icon">
                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h2><?php echo $is_edit ? 'Perbarui Pengalaman Anda' : 'Mengapa ulasan Anda berharga?'; ?></h2>
                    <p><?php echo $is_edit ? 'Pastikan data dan foto yang Anda kirimkan sudah sesuai agar memudahkan admin memverifikasi pembaruan Anda.' : 'Ulasan jujur Anda membantu kami terus tumbuh dan memberikan layanan terbaik bagi Anda dan pelanggan lainnya.'; ?></p>
                    
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
                                <strong>Lengkapi Profil & Foto</strong>
                                <p>Unggah foto terbaik agar ulasan terasa personal.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="form-actual-side">
                <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" class="cc-submit-form" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="submit_testimoni_action">
                    <?php if ( $is_edit ) : ?>
                        <input type="hidden" name="edit_post_id" value="<?php echo esc_attr( $edit_id ); ?>">
                    <?php endif; ?>
                    <!-- Nonce CSRF diisi oleh JS sebelum submit (kompatibel dengan cache plugin) -->
                    <input type="hidden" name="_cc_nonce" id="cc_form_nonce" value="">
                    
                    <!-- Honeypot Field untuk memblokir spam bots secara pasif -->
                    <div style="display: none;" aria-hidden="true">
                        <label for="cc_hp_email">Confirm Email</label>
                        <input type="text" id="cc_hp_email" name="cc_hp_email" value="" tabindex="-1" autocomplete="off">
                    </div>
                    
                    <div class="form-group-app">
                        <label for="client_name">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" id="client_name" name="client_name" class="form-control" required placeholder="Cth: Budi Santoso" value="<?php echo esc_attr( $name ); ?>">
                    </div>

                    <div class="form-group-app">
                        <label for="client_title">Pekerjaan / Jabatan <span class="req">*</span></label>
                        <input type="text" id="client_title" name="client_title" class="form-control" required placeholder="Cth: Penulis Buku, C.E.O" value="<?php echo esc_attr( $title ); ?>">
                    </div>

                    <div class="form-group-app">
                        <label for="client_city">Kota Asal <span class="req">*</span></label>
                        <input type="text" id="client_city" name="client_city" class="form-control" required placeholder="Cth: Jakarta, Surabaya" value="<?php echo esc_attr( $city ); ?>">
                    </div>

                    <div class="form-group-app">
                        <label>Rating Kepuasan <span class="req">*</span></label>
                        <div class="star-rating-input">
                            <select name="client_rating" id="client_rating" class="form-control" required>
                                <option value="5" <?php selected( $rating, 5 ); ?>>⭐⭐⭐⭐⭐ (Sangat Puas)</option>
                                <option value="4" <?php selected( $rating, 4 ); ?>>⭐⭐⭐⭐ (Puas)</option>
                                <option value="3" <?php selected( $rating, 3 ); ?>>⭐⭐⭐ (Cukup)</option>
                                <option value="2" <?php selected( $rating, 2 ); ?>>⭐⭐ (Kurang)</option>
                                <option value="1" <?php selected( $rating, 1 ); ?>>⭐ (Sangat Kurang)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group-app">
                        <label for="client_review">Detail Ulasan <span class="req">*</span></label>
                        <textarea id="client_review" name="client_review" class="form-control" rows="5" required placeholder="Ceritakan pengalaman memuaskan Anda..."><?php echo esc_textarea( $review ); ?></textarea>
                    </div>

                    <div class="form-group-app">
                        <label for="client_photo">Foto Profil <?php echo $has_thumb ? '<span style="font-weight: normal; color: #64748b; font-size: 0.85rem;">(Opsional jika tidak diganti)</span>' : '<span class="req">*</span>'; ?></label>
                        
                        <?php if ( $has_thumb ) : ?>
                            <div class="current-photo-preview" style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; padding: 10px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
                                <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $name ); ?>" style="width: 52px; height: 52px; border-radius: 10px; object-fit: cover; border: 2px solid #cbd5e1;">
                                <div style="font-size: 0.85rem; color: #475569;">
                                    <strong style="display: block; color: #1e293b;">Foto Saat Ini</strong>
                                    Pilih foto baru di bawah ini hanya jika Anda ingin menggantinya.
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="custom-file-upload">
                            <!-- name="client_photo" removed so the original large file is not uploaded in $_FILES -->
                            <input type="file" id="client_photo" class="form-control-file" <?php echo $has_thumb ? '' : 'required'; ?> accept="image/png, image/jpeg, image/jpg, image/webp">
                            <input type="hidden" name="client_photo_base64" id="client_photo_base64" value="" data-existing-photo="<?php echo $has_thumb ? 'true' : 'false'; ?>">
                            <div class="upload-ui">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <span><?php echo $has_thumb ? 'Pilih Foto Baru' : 'Pilih Gambar'; ?></span>
                            </div>
                        </div>
                        <small class="form-help">Pilih foto apa saja (JPG/PNG/WEBP) untuk dipotong menjadi kotak (square 1:1).</small>
                    </div>

                    <div class="form-actions-app">
                        <button type="submit" class="btn-primary-app btn-submit-full">
                            <?php echo $is_edit ? 'Simpan Pembaruan & Kirim' : 'Kirim Ulasan'; ?>
                        </button>
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
