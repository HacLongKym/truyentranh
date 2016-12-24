(function ($) {
    'use strict';
    $(".rt-tpg-container").each(function () {
        var $isotopeHolder = $(this).find('.tpg-isotope');
        var $isotope = $isotopeHolder.find('.rt-tpg-isotope');
        if ($isotope.length) {
            var isotope = $isotope.imagesLoaded(function () {
                $.when(tgpHeightResize()).done(function () {
                    isotope.isotope({
                        itemSelector: '.isotope-item',
                    });
                    setTimeout(function () {
                        isotope.isotope();
                    }, 100);
                });
            });
            var $isotopeButtonGroup = $isotopeHolder.find('.rt-tpg-isotope-buttons');
            $isotopeButtonGroup.on('click', 'button', function (e) {
                e.preventDefault();
                var filterValue = $(this).attr('data-filter');
                isotope.isotope({filter: filterValue});
                $(this).parent().find('.selected').removeClass('selected');
                $(this).addClass('selected');
            });
        }
    });


    $(window).resize(tgpHeightResize);
    $(window).resize(overlayIconResizeTpg);
    $(window).load(tgpHeightResize);
    $(window).load(overlayIconResizeTpg);


    function tgpHeightResize() {
        $(document).imagesLoaded(function () {
            $(".rt-tpg-container").each(function () {
                var rtMaxH = 0;
                $(this).children('.row').children(".equal-height").height("auto");
                $(this).children('.row').children('.equal-height').each(function () {
                    var $thisH = $(this).actual('outerHeight');
                    if ($thisH > rtMaxH) {
                        rtMaxH = $thisH;
                    }
                });
                $(this).children('.row').children(".equal-height").css('height', rtMaxH + "px");

            });

            $(".rt-tpg-container .rt-tpg-isotope").each(function () {
                var rtMaxH = 0;
                $(this).children(".equal-height").height("auto");
                $(this).children('.equal-height').each(function () {
                    var $thisH = $(this).actual('outerHeight');
                    if ($thisH > rtMaxH) {
                        rtMaxH = $thisH;
                    }
                });
                $(this).children(".equal-height").css('height', rtMaxH + "px");

            });
        });
    }
    
    function overlayIconResizeTpg() {
        $('.overlay').each(function () {
            var holder_height = $(this).height();
            var target = $(this).children('.link-holder');
            var targetd = $(this).children('.view-details');
            var a_height = target.height();
            var ad_height = targetd.height();
            var h = (holder_height - a_height) / 2;
            var hd = (holder_height - ad_height) / 2;
            target.css('top', h + 'px');
            targetd.css('margin-top', hd + 'px');
        });
    }


})(jQuery);
