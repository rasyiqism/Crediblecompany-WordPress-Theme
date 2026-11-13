/**
 * Modul: AJAX Load More
 * Menangani pemuatan konten tambahan (posts/testimoni) via AJAX.
 *
 * @package CredibleCompany
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const loadMoreButtons = document.querySelectorAll('.btn-load-more');

        loadMoreButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const btn = this;
                const page = parseInt(btn.getAttribute('data-page'));
                const maxPages = parseInt(btn.getAttribute('data-max-pages'));
                const postType = btn.getAttribute('data-post-type');
                const orderby = btn.getAttribute('data-orderby') || 'date';
                const exclude = btn.getAttribute('data-exclude') || 0;
                const cat = btn.getAttribute('data-cat') || 0;
                const ppp = btn.getAttribute('data-ppp') || 0;
                const search = btn.getAttribute('data-search') || '';

                // Tentukan target container berdasar ID tombol atau konteks
                let targetContainer = document.getElementById('ajax-feed-container');
                if (btn.id === 'load-more-related-btn') {
                    targetContainer = document.getElementById('ajax-related-container');
                }

                if (!targetContainer) return;

                // Ubah teks tombol menjadi loading
                const originalText = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-loader"></span> Memuat...';
                btn.disabled = true;

                // Kirim request AJAX
                const formData = new FormData();
                formData.append('action', 'cc_load_more');
                formData.append('page', page);
                formData.append('post_type', postType);
                formData.append('orderby', orderby);
                formData.append('exclude', exclude);
                if (cat) formData.append('cat', cat);
                if (ppp) formData.append('ppp', ppp);
                if (search) formData.append('s', search);

                fetch(ccAjax.ajaxurl, {
                    method: 'POST',
                    body: formData
                })
                    .then(function (response) { return response.text(); })
                    .then(function (data) {
                        if (data.trim() !== '') {
                            // Append konten baru ke container
                            targetContainer.insertAdjacentHTML('beforeend', data);

                            // Update page attribute
                            var nextPage = page + 1;
                            btn.setAttribute('data-page', nextPage);

                            // Jika sudah halaman terakhir, sembunyikan tombol
                            if (nextPage >= maxPages) {
                                btn.style.display = 'none';
                            }
                        } else {
                            btn.style.display = 'none';
                        }
                    })
                    .catch(function (error) {
                        console.error('Error loading more posts:', error);
                    })
                    .finally(function () {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    });
            });
        });
    });
})();
