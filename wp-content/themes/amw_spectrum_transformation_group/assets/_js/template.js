(function ($) {
    /*EQUAL HEIGHTS FUNC*/

    $.fn.equalHeights = function () {
        var maxHeight = 0,
            $this = $(this);

        $this.each(function () {
            var height = $(this).innerHeight();

            if (height > maxHeight) { maxHeight = height; }
        });

        return $this.css('height', maxHeight);
    };

    // auto-initialize plugin
    $('[data-equal]').each(function () {
        var $this = $(this),
            target = $this.data('equal');
        $this.find(target).equalHeights();
    });

    $(document).ready(function () {
        if ($('.hero-section').find('.hero-section__title').length) {
            $('.hero-section__title').find('a.btn').wrap("<div class='btn-wrapper'></div>");
        }

        $('<span class="menu-trigger"><i class="fa fa-angle-down"></i></span>').insertAfter('.main-header .menu-item-has-children > a');

        //counter
        $('.counter').counterUp({
            delay: 10,
            time: 1000
        });

        //accordion
        $('.accordion-item__header').click(function () {
            if (!$(this).parent().hasClass('open')) {
                $('.accordion-item').removeClass('open');
                $('.accordion-item__body').slideUp();
            }
            $(this).parent().toggleClass('open');
            $(this).parent().find('.accordion-item__body').slideToggle();
        })

        $('<span class="indicator"><i class="fa fa-angle-down"></i></span>').insertAfter('.accordion-section .accordion-item .accordion-item__header h4');
        $('<span class="indicator"><i class="fa fa-angle-down"></i></span>').insertAfter('.blog-section .accordion-item__header h4');
        $('<span class="indicator"><i class="fa fa-angle-down"></i></span>').insertAfter('.aside-wrapper .aside-sticky li a');

    });

    $('.menu-button').click(function () {
        $(this).toggleClass('open');
        $('.navbar-nav').toggleClass('open');
        $('html, body').toggleClass('fix');
        $('.navbar-nav li.menu-item-has-children:not(.logout-link) .sub-menu').removeClass('open').slideUp();
        $('.navbar-nav li.menu-item-has-children:not(.logout-link) .sub-menu li, .navbar-nav li.menu-item-has-children:not(.logout-link)').removeClass('open');
    });

    //mobile menu
    if ($(window).width() < 1025) {
        $('li.menu-item-has-children:not(.logout-link) a').one('click', function (e) {
            e.preventDefault();
            $(this).parent().find('> .sub-menu').slideDown();
            $(this).parent().addClass('open');
        })
    }


    $(window).scroll(function () {
    });

    $(window).resize(function () {

    });

    $(window).load(function () {

    });
})(jQuery);