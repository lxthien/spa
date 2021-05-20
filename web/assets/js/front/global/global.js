'use strict';

require('bxslider/dist/jquery.bxslider');
require('@fancyapps/fancybox');
require('../../../libs/starrating/js/rating.js');
require('../../../libs/lightslider/js/lightslider.js');
require('jquery-validation');

function initSearchBox() {
    var $formSearch = $('#form-search');
    var $searchField = $('.search-field');

    $searchField.keypress(function (e) {
        if (e.which == 13) {
            e.preventDefault();

            if ($searchField.val() === '') {
                $searchField.focus();
            } else {
                $formSearch.submit();
            }
        }
    });
}

function initProtectedContent() {
    $('body').bind('cut copy', function (e) {
        e.preventDefault();
    });
}

function initGoToTop() {
    var $goToTop = $('.go-to-top');

    $goToTop.click(function() {
        $("html, body").animate({ scrollTop: 0 }, "slow");
        return false;
    });
}

function initFixedMenu() {
    $(window).scroll(function() {
        var $nav = $("#nav");
        var $scrollUp = $('.td-scroll-up');
        var scroll = $(window).scrollTop();
    
        if (scroll > 85) {
            $nav.addClass("navbar-fixed-top");
            $scrollUp.removeClass("hidden");
        } else {
            $nav.removeClass("navbar-fixed-top");
            $scrollUp.addClass("hidden");
        }
    });
}

function initFlickity() {
    $('#image-gallery').lightSlider({
        gallery: true,
        item: 1,
        thumbItem: 4,
        slideMargin: 0,
        loop: true,
        enableDrag: true,
        currentPagerPosition: 'left',

        onSliderLoad: function() {
            $('#image-gallery').removeClass('cS-hidden');

            $('[data-fancybox="images"]').fancybox({
                buttons : [
                    'share',
                    'zoom',
                    'fullScreen',
                    'close'
                ],
                thumbs : {
                    autoStart : true
                }
            });
        }  
    });

    $('#video-gallery').lightSlider({
        gallery: true,
        item: 1,
        thumbItem: 4,
        slideMargin: 0,
        loop: true,
        onSliderLoad: function() {
            $('#image-gallery').removeClass('cS-hidden');

            $('[data-fancybox="video"]').fancybox({
                youtube: {
                    autoplay: 1,
                }
            });
        }  
    });

    $('#image-gallery-3').lightSlider({
        item: 3,
        loop: true,
        slideMove: 1,
        easing: 'cubic-bezier(0.25, 0, 0.25, 1)',
        speed: 600,
        responsive : [
            {
                breakpoint: 800,
                settings: {
                    item: 2,
                    slideMove: 1
                }
            },
            {
                breakpoint: 480,
                settings: {
                    item: 1,
                    slideMove: 1
                }
            }
        ]
    });

    $('#image-gallery-sidebar').lightSlider({
        gallery: true,
        item: 1,
        thumbItem: 4,
        slideMargin: 0,
        loop: true,
        enableDrag: true,
        currentPagerPosition: 'left',
        auto: true,

        onSliderLoad: function() {
            $('#image-gallery-sidebar').removeClass('cS-hidden');

            $('[data-fancybox="images"]').fancybox({
                buttons : [
                    'share',
                    'zoom',
                    'fullScreen',
                    'close'
                ],
                thumbs : {
                    autoStart : true
                }
            });
        }  
    });
}

function intHandleFormContact() {
    var $formComment = $('#form-comment');

    $formComment.on('click', '#form_send', function(e) {
        if ($formComment.valid()) {
            $.ajax({
                type: "POST",
                url: $formComment.attr('action'),
                data: $formComment.serialize(),
                success: function(data) {
                    var response = JSON.parse(data);
                    if (response.status === 'success') {
                        $('#contact-form-message').html(response.message);
                        $.fancybox.open({
                            src: '#contact-form-message',
                            touch : false
                        });
    
                        // Clear form comment
                        $formComment[0].reset();
                    } else {
                        $('#contact-form-message').html(response.message);
                        $.fancybox.open({
                            src: '#contact-form-message',
                            touch : false
                        });
                    }
                }
            });
        }
    })
}

exports.init = function () {
    initSearchBox();
    initProtectedContent();
    initGoToTop();
    initFixedMenu();
    initFlickity();
    intHandleFormContact();
};