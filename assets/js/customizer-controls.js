/**
 * Customizer Controls Enhancement:
 * 1. Live numeric value badge pada seluruh kontrol <input type="range">.
 * 2. FAQ Repeater Control (safe escaping + tombol reorder Naik/Turun).
 *
 * @package CredibleCompany
 */
(function ($) {
    'use strict';

    function initRangeBadges() {
        $('.customize-control input[type="range"]').each(function () {
            var $range = $(this);
            if ($range.data('ccRangeInit')) {
                return;
            }
            $range.data('ccRangeInit', true);

            var $badge = $('<span class="cc-range-badge"></span>').text($range.val());
            $range.after($badge);

            $range.on('input change', function () {
                $badge.text($range.val());
            });
        });
    }

    function initFaqRepeaters() {
        $('.cc-faq-repeater-wrapper').each(function () {
            var $wrapper = $(this);
            if ($wrapper.data('ccFaqInit')) {
                return;
            }
            $wrapper.data('ccFaqInit', true);

            var $hiddenInput = $wrapper.find('.cc-faq-data-hidden');
            var $itemsContainer = $wrapper.find('.cc-faq-items');
            var $addBtn = $wrapper.find('.cc-faq-add-btn');

            var data = [];
            try {
                if ($hiddenInput.val()) {
                    data = JSON.parse($hiddenInput.val());
                }
            } catch (e) {
                data = [];
            }

            function renderItem(q, a) {
                var $item = $('<div class="cc-faq-item"></div>');
                var $header = $('<div class="cc-faq-item-actions"></div>');
                var $upBtn = $('<button type="button" class="button-link cc-faq-move-up" title="Naik">&#8593;</button>');
                var $downBtn = $('<button type="button" class="button-link cc-faq-move-down" title="Turun">&#8595;</button>');
                var $removeBtn = $('<button type="button" class="button-link cc-faq-remove-btn">Hapus</button>');

                $header.append($upBtn, ' ', $downBtn, ' ', $removeBtn);

                var $qInput = $('<input type="text" class="faq-q" placeholder="Pertanyaan">').val(q || '');
                var $aInput = $('<textarea class="faq-a" placeholder="Jawaban" rows="3"></textarea>').val(a || '');

                $item.append($header, $qInput, $aInput);
                $itemsContainer.append($item);
            }

            if (Array.isArray(data) && data.length > 0) {
                $.each(data, function (index, item) {
                    renderItem(item.q, item.a);
                });
            } else {
                renderItem('', '');
            }

            function syncData() {
                var newData = [];
                $itemsContainer.find('.cc-faq-item').each(function () {
                    var q = $(this).find('.faq-q').val();
                    var a = $(this).find('.faq-a').val();
                    if (q !== '' || a !== '') {
                        newData.push({ q: q, a: a });
                    }
                });
                $hiddenInput.val(JSON.stringify(newData)).trigger('change');
            }

            $itemsContainer.on('input', 'input, textarea', syncData);

            $addBtn.on('click', function (e) {
                e.preventDefault();
                renderItem('', '');
            });

            $itemsContainer.on('click', '.cc-faq-remove-btn', function () {
                $(this).closest('.cc-faq-item').remove();
                syncData();
            });

            $itemsContainer.on('click', '.cc-faq-move-up', function () {
                var $item = $(this).closest('.cc-faq-item');
                var $prev = $item.prev('.cc-faq-item');
                if ($prev.length) {
                    $item.insertBefore($prev);
                    syncData();
                }
            });

            $itemsContainer.on('click', '.cc-faq-move-down', function () {
                var $item = $(this).closest('.cc-faq-item');
                var $next = $item.next('.cc-faq-item');
                if ($next.length) {
                    $item.insertAfter($next);
                    syncData();
                }
            });
        });
    }

    $(document).ready(function () {
        initRangeBadges();
        initFaqRepeaters();
    });
})(jQuery);
