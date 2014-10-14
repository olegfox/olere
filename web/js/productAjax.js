$(function () {
    $('.inner_box a').click(function () {
        productShow(this);
    });
});

function productShow(object) {
    var $clone_overlay = $('.productWindow_hide .productWindow_overlay').clone().appendTo('body'),
        $clone_productWindow = $('.productWindow_hide .productWindow').clone().appendTo('body');

    $('body').css({
        'overflow': 'hidden'
    });
    $clone_overlay
        .click(function () {
            productClose($clone_productWindow, $clone_overlay);
        })
        .css({
            'z-index': parseInt($('.productWindow_overlay:visible').last().css('z-index')) + 1
        })
        .fadeIn(200);
    $clone_productWindow
        .find('.productWindow_content')
        .html('')
        .load($(object).attr('href'), function () {
            $clone_productWindow
                .find('.pictureProduct img')
                .one("load",function () {
                    $clone_productWindow
                        .animate({
                            'top': 0
                        }, 200)
                        .find('.close a').click(function () {
                            productClose($clone_productWindow, $clone_overlay);
                        })
                        .end()
                        .find('.latestProducts a').click(function () {
                            productShow(this);
                        })
                        .end();
                    if ($clone_productWindow.find('.productWindow_content .buyBlock').height() < $('body').height()) {
                        $clone_productWindow
                            .find('.productWindow_content')
                            .css({
                                'max-height': $('body').height() - 40
                            });
                        $clone_productWindow
                            .find('.productWindow_content')
                            .css({
                                'top': ($('body').height() - $clone_productWindow.find('.productWindow_content').height()) / 2
                            })
                            .find('.pictureProduct')
                            .css({
                                'max-height': $clone_productWindow.find('.productWindow_content').height() - $clone_productWindow.find('.latestProducts').height() - 10
                            });
                    }

                    smoothZoom();
                    addCart();
                    addCartRing();
                }).each(function () {
                    if (this.complete) $(this).load();
                });
        })
        .end()
        .on('click', function (e) {
            if (e.target !== this)
                return;
            productClose($clone_productWindow, $clone_overlay);
        })
        .css({
            'top': '100%',
            'z-index': parseInt($('.productWindow:visible').last().css('z-index')) + 1
        })
        .show();
}

function productClose($productWindow, $overlay) {
    $productWindow.animate({
        'top': '100%'
    }, {
        duration: 200,
        complete: function () {
            $productWindow.remove();
            if ($('.productWindow').length <= 1) {
                $('body').css({
                    'overflow': 'auto'
                });
            }
            $overlay.fadeOut(200, function () {
                $(this).remove();
            });
        }
    });
}