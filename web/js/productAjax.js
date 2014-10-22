$(function () {
    $('.inner_box a').click(function () {
        productShow(this);
    });
});

function initWindow($clone_productWindow){
    $clone_productWindow
        .find('.pictureProduct img')
        .css({'opacity' : 0})
        .one("load",function () {
            $(this).css({'opacity' : 1});
            smoothZoom($clone_productWindow.find('.pictureProduct')[0]);
        }).each(function () {
            if (this.complete) $(this).load();
        });

    addCart();
    addCartRing();
}

function productShow(object, time) {
    time = time || 200;

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
        .fadeIn(time);
    $clone_productWindow
        .find('.productWindow_content')
        .html('')
        .load($(object).attr('href'), function () {
            var $navright = $clone_productWindow.find('.navright'),
                $navleft = $clone_productWindow.find('.navleft');

            $navleft
                .unbind('click')
                .click(function(){
                    var tmp = $(object)
                        .parent()
                        .parent()
                        .prev()
                        .find('.inner_box a')
                        .get(0);
                    if($(tmp).length <= 0){
                        object = $(object)
                            .parent()
                            .parent()
                            .parent()
                            .prev()
                            .find('.box3 .inner_box a')
                            .get(0);
                    }
                    if($(tmp).length > 0){
                        object = tmp;
                        $clone_productWindow
                            .find('.productWindow_content')
                            .css({
                                'height' : $clone_productWindow.find('.productWindow_content').height()
                            })
                            .html('')
                            .load($(object).attr('href'), function(){
                                $clone_productWindow
                                    .find('.close a').click(function () {
                                        productClose($clone_productWindow, $clone_overlay);
                                    })
                                    .end()
                                    .find('.productWindow_content')
                                    .css({
                                        'height' : 'auto'
                                    });
                                initWindow($clone_productWindow);
                            });
                    }
                });

            $navright
                .unbind('click')
                .click(function(){
                    var tmp = $(object)
                        .parent()
                        .parent()
                        .next()
                        .find('.inner_box a')
                        .get(0);
                    if($(tmp).length <= 0){
                        tmp = $(object)
                            .parent()
                            .parent()
                            .parent()
                            .next()
                            .find('.box0 .inner_box a')
                            .get(0);
                    }
                    if($(tmp).length > 0){
                        object = tmp;
                        $clone_productWindow
                            .find('.productWindow_content')
                            .css({
                                'height' : $clone_productWindow.find('.productWindow_content').height()
                            })
                            .html('')
                            .load($(object).attr('href'), function(){
                                $clone_productWindow
                                    .find('.productWindow_content')
                                    .css({
                                        'height' : $clone_productWindow.find('.productWindow_content').height()
                                    })
                                    .html('')
                                    .load($(object).attr('href'), function(){
                                        $clone_productWindow
                                            .find('.close a').click(function () {
                                                productClose($clone_productWindow, $clone_overlay);
                                            })
                                            .end()
                                            .find('.productWindow_content')
                                            .css({
                                                'height' : 'auto'
                                            });
                                        initWindow($clone_productWindow);
                                    });
                            });
                    }
                });

            $(window)
                .unbind('keyup')
                .keyup(function (event) {
                    if ( event.keyCode == 37 ){
                        $navleft.click();
                    }
                    if ( event.keyCode == 39 ){
                        $navright.click();
                    }
                });

            $clone_productWindow
                .animate({
                    'top': 0
                }, time)
                .find('.close a').click(function () {
                    productClose($clone_productWindow, $clone_overlay);
                })
                .end()
                .find('.latestProducts a').click(function () {
                    productShow(this);
                })
                .end();
            if ($clone_productWindow.find('.productWindow_content .buyBlock .inner_buyBlock').height() < $('body').height()) {
                $clone_productWindow
                    .find('.productWindow_content')
                    .css({
                        'max-height': $('body').height(),
                        'margin' : 0
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

            initWindow($clone_productWindow);
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

function productClose($productWindow, $overlay, time) {
    time = time || 200;

    $(window).unbind('keyup');
    $productWindow.animate({
        'top': '100%'
    }, {
        duration: time,
        complete: function () {
            $productWindow.remove();
            if ($('.productWindow').length <= 1) {
                $('body').css({
                    'overflow': 'auto'
                });
            }
            $overlay.fadeOut(time, function () {
                $(this).remove();
            });
        }
    });
}