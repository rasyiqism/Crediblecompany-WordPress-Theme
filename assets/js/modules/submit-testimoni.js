/**
 * Modul: Validasi & Submit Form Testimoni dengan Cropper.js
 * Memotong foto profil menjadi kotak (1:1) dan mengirimkannya sebagai Base64.
 * Mengambil nonce CSRF fresh via AJAX sebelum submit (kompatibel dengan halaman cache).
 *
 * @package CredibleCompany
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const form        = document.querySelector('.cc-submit-form');
        const fileInput   = document.getElementById('client_photo');
        const hiddenPhoto = document.getElementById('client_photo_base64');
        const uploadUi    = document.querySelector('.custom-file-upload .upload-ui span');
        const nonceInput  = document.getElementById('cc_form_nonce');

        // Elemen Cropper Modal
        const cropperModal = document.getElementById('cc-cropper-modal');
        const cropperImage = document.getElementById('cc-cropper-image');
        const closeBtn     = document.getElementById('cc-cropper-close');
        const cancelBtn    = document.getElementById('cc-cropper-cancel');
        const saveBtn      = document.getElementById('cc-cropper-save');

        let cropper = null;
        let currentFile = null;

        // Ambil ajaxurl dari ccAjax (di-localize oleh enqueue.php)
        const ajaxUrl = (typeof ccAjax !== 'undefined') ? ccAjax.ajaxurl : '/wp-admin/admin-ajax.php';

        /**
         * Reset tampilan input file dan labelnya.
         */
        function resetUploadUI() {
            if (fileInput) fileInput.value = '';
            if (hiddenPhoto) hiddenPhoto.value = '';
            if (uploadUi) {
                uploadUi.textContent = 'Pilih Gambar';
                uploadUi.parentElement.classList.remove('has-file');
                uploadUi.parentElement.style.borderColor = '';
                uploadUi.parentElement.style.background = '';
            }
        }

        /**
         * Ambil nonce segar dari server via AJAX.
         */
        function fetchFreshNonce() {
            const data = new FormData();
            data.append('action', 'cc_get_testimoni_nonce');
            return fetch(ajaxUrl, { method: 'POST', body: data })
                .then(function (res) { return res.json(); })
                .then(function (json) {
                    if (json && json.success && json.data && json.data.nonce) {
                        return json.data.nonce;
                    }
                    throw new Error('Gagal mengambil nonce dari server.');
                });
        }

        // Ketika file dipilih oleh user
        if (fileInput && uploadUi) {
            fileInput.addEventListener('change', function () {
                if (this.files && this.files.length > 0) {
                    const file = this.files[0];

                    // Soft limit ukuran berkas (misal: 10MB) agar browser tidak crash
                    if (file.size > 10 * 1024 * 1024) {
                        alert('Ukuran file terlalu besar. Batas maksimal memilih file adalah 10MB.');
                        resetUploadUI();
                        return;
                    }

                    // Tampilkan file dalam Cropper modal
                    currentFile = file;
                    const objectUrl = URL.createObjectURL(file);
                    cropperImage.src = objectUrl;
                    cropperModal.classList.add('active');

                    // Inisialisasi Cropper.js
                    if (cropper) {
                        cropper.destroy();
                    }
                    cropper = new Cropper(cropperImage, {
                        aspectRatio: 1, // Memaksa rasio kotak 1:1
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 1,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                    });
                } else {
                    resetUploadUI();
                }
            });
        }

        // Fungsi Menutup Modal Cropper
        function closeModal() {
            cropperModal.classList.remove('active');
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            if (cropperImage.src.startsWith('blob:')) {
                URL.revokeObjectURL(cropperImage.src);
            }
            cropperImage.src = '';
        }

        // Batal / Close Cropper
        if (closeBtn) closeBtn.addEventListener('click', function() {
            closeModal();
            if (!hiddenPhoto.value) resetUploadUI();
        });
        if (cancelBtn) cancelBtn.addEventListener('click', function() {
            closeModal();
            if (!hiddenPhoto.value) resetUploadUI();
        });

        // Simpan Hasil Potong (Crop)
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                if (cropper) {
                    // Dapatkan canvas dengan resolusi output terkontrol (500x500 px)
                    const canvas = cropper.getCroppedCanvas({
                        width: 500,
                        height: 500,
                        imageSmoothingEnabled: true,
                        imageSmoothingQuality: 'high',
                    });

                    if (canvas) {
                        // Konversi ke base64 JPEG berkualitas tinggi (90%)
                        const base64Data = canvas.toDataURL('image/jpeg', 0.9);
                        hiddenPhoto.value = base64Data;

                        // Perbarui UI label unggahan
                        uploadUi.textContent = currentFile.name + ' (Sudah Dipotong)';
                        uploadUi.parentElement.classList.add('has-file');
                        uploadUi.parentElement.style.borderColor = '#c01314'; // KBM Red
                        uploadUi.parentElement.style.background = '#fff5f5'; // Light KBM red
                    }

                    closeModal();
                }
            });
        }

        // Intercept form submit: cek base64, ambil nonce, kirim
        if (form && nonceInput) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const hasExistingPhoto = hiddenPhoto && hiddenPhoto.getAttribute('data-existing-photo') === 'true';

                if (!hiddenPhoto.value && !hasExistingPhoto) {
                    alert('Mohon pilih dan potong foto profil Anda terlebih dahulu.');
                    return;
                }

                const submitBtn = form.querySelector('[type="submit"]');
                const origSubmitText = submitBtn ? submitBtn.textContent : 'Kirim Ulasan';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Memproses...';
                }

                fetchFreshNonce()
                    .then(function (nonce) {
                        nonceInput.value = nonce;
                        form.submit();
                    })
                    .catch(function (err) {
                        console.error('Nonce error:', err);
                        alert('Terjadi kesalahan keamanan. Silakan refresh halaman dan coba lagi.');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = origSubmitText;
                        }
                    });
            });
        }
        // Reset input foto jika halaman dimuat dari cache navigasi browser (bfcache)
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                resetUploadUI();
            }
        });
    });
})();
