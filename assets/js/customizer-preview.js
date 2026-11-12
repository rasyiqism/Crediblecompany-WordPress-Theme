/**
 * Customizer Live Preview (postMessage handlers).
 *
 * @package CredibleCompany
 */
(function ($) {
    'use strict';

    wp.customize('cc_hero_title', function (value) {
        value.bind(function (newval) {
            $('.hero-title, .hero-v2-title, .hero-v3-headline').text(newval);
        });
    });

    wp.customize('cc_hero_desc', function (value) {
        value.bind(function (newval) {
            $('.hero-desc, .hero-v2-desc, .hero-v3-paragraph').text(newval);
        });
    });
})(jQuery);
