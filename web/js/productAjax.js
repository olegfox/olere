var cache = [];
var pages = [];
var lastScrollTop = 0;

$(function () {
    $.fn.loadCache = function (object, href, callback) {
        if (cache[$(object).attr('href')] != undefined) {
            $(this).html(cache[$(object).attr('href')]);
            if($('.productWindow').last().css('display') == 'none'){
                setTimeout(function(){
                    callback();
                }, 200);
            }else{
                callback();
            }
        } else {
            $(this).load(href, callback);
        }

        return this;
    };
    initClick();
    $("img.lazy").lazyload();
    $(window).bind('load', function(){
//        cacheProducts();
    });

    function after_insert_page_bottom($page_last){
        if($page_last.prev('.page').length > 0){
            var height = $page_last.prev('.page').height();
            pages[$page_last.prev('.page').attr('id')] = $page_last.prev('.page').html();
//            if($('.page').last().length > 0){
//                console.log($('.page').last().attr('id'));
//                pages[$('.page').last().attr('id')] = $('.page').last().html();
//            }
            $page_last.prev('.page').remove();
            $(window).scrollTop($(window).scrollTop() - height);
        }
        $("img.lazy").lazyload();
        initClick();
    }

    function after_insert_page_top($page_first){
        if($page_first.prev('.page').length > 0){
            var height = $page_first.prev('.page').height();
            pages[$page_first.prev('.page').attr('id')] = $page_first.prev('.page').html();
            $('.page').last().remove();
            $(window).scrollTop($(window).scrollTop() + height);
            var link = $(".indexByTaxonAjax").attr('href');
            var re = /[0-9]+/;
            var new_link = link.replace(re, link.match(re) - 1);
            $(".indexByTaxonAjax").attr('href', new_link);
            $(".indexByTaxonAjax.hold").removeClass('hold');
        }
        $("img.lazy").lazyload();
        initClick();
    }

    $(window).scroll(function () {
        if ($(".indexByTaxonAjax").length > 0) {
            var scrollTop = document.documentElement.scrollTop || jQuery(this).scrollTop();
//          Скроллим вверх
            if ((scrollTop < 1000) && lastScrollTop > scrollTop) {
                var $page_first = $(".catalog .page").first();
                var re = /[0-9]+/;
                var number_page = $page_first.attr('id').match(re) - 1;
                if(pages['page' + number_page] != undefined){
                    $page_first
                        .before('<div class="page" id="page'+number_page+'">'+pages['page'+number_page]+'</div>');
                    after_insert_page_top($page_first);
                }
            }
//          Скроллим вниз
            setTimeout(function(){
                if($(".indexByTaxonAjax.hold").length <= 0){
                    if ((scrollTop >= ($(document).height() - $(window).height()) - 1000)) {
                        var link = $(".indexByTaxonAjax").attr('href');
                        $(".indexByTaxonAjax").addClass('hold');
                        var $page_last = $(".catalog .page").last();
                        var re = /[0-9]+/;
                        var number_page = link.match(re);
                        if(pages['page'+number_page] != undefined){
                            $page_last
                                .append('<div class="page" id="page'+number_page+'">'+pages['page'+number_page]+'</div>');
                            after_insert_page_bottom($page_last);
                        }else{
                            $.get(link, {}, function (data) {
                                $page_last
                                    .after(data);
                                $(".indexByTaxonAjax.hold").remove();
                                after_insert_page_bottom($page_last);
                            }).fail(function(){
                                $(".indexByTaxonAjax").hide();
                            });
                        }
                    }
                }

                lastScrollTop = scrollTop;
            }, 500);

        }
    });
});

function cacheProducts(object) {
    object = object || 0;
    if(object != 0){
        var $prev = $(object).parent().parent().prev().find('.inner_box a');
        var $next = $(object).parent().parent().next().find('.inner_box a');
        if (cache[$prev.attr('href')] == undefined) {
            $.post($prev.attr('href'), {}, function (data) {
                cache[$prev.attr('href')] = data;
            });
        }
        if (cache[$next.attr('href')] == undefined) {
            $.post($next.attr('href'), {}, function (data) {
                cache[$next.attr('href')] = data;
            });
        }
    }
}

function initClick() {
    $('.inner_box a').unbind('click').click(function () {
        productShow(this);
    });
}

function initWindow(object, $clone_productWindow, $clone_overlay) {
    $clone_productWindow
        .animate({
            'top': 0
        }, 200)
        .find('.close a').click(function () {
            productClose($clone_productWindow, $clone_overlay);
        })
        .end()
        .find('.latestProducts a').click(function () {
            productShow(this, 200, 1);
        })
        .end();

    $clone_productWindow
        .find('.pictureProduct img')
//        .css({'opacity': 0})
        .one("load",function () {
            if ($clone_productWindow.find('.productWindow_content .buyBlock .inner_buyBlock').height() < $('body').height()) {
                $clone_productWindow
                    .find('.productWindow_content')
                    .css({
                        'max-height': $('body').height(),
                        'margin': 0
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
            $(this).css({'opacity': 1});
            smoothZoom($clone_productWindow.find('.pictureProduct')[0]);
            cacheProducts(object);
        }).each(function () {
            if (this.complete) $(this).load();
        });

    addCart();
    addCartRing();
}

function productShow(object, time, latest) {
    time = time || 200;
    latest = latest || 0;

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
        .loadCache(object, $(object).attr('href'), function () {
            var $navright = $clone_productWindow.find('.navright'),
                $navleft = $clone_productWindow.find('.navleft');

            if(latest == 1){
                $navleft.hide();
                $navright.hide();
            }

            $navleft
                .unbind('click')
                .click(function () {
                    var tmp = $(object)
                        .parent()
                        .parent()
                        .prev()
                        .find('.inner_box a')
                        .get(0);
                    if ($(tmp).length <= 0) {
                        tmp = $(object)
                            .parent()
                            .parent()
                            .parent()
                            .prev()
                            .find('.box3 .inner_box a')
                            .get(0);
                    }
                    if ($(tmp).length > 0) {
                        object = tmp;
                        $clone_productWindow
                            .find('.productWindow_content')
                            .css({
                                'height': $clone_productWindow.find('.productWindow_content').height()
                            })
                            .html('')
                            .loadCache(object, $(object).attr('href'), function () {
                                $clone_productWindow
                                    .find('.close a').click(function () {
                                        productClose($clone_productWindow, $clone_overlay);
                                    })
                                    .end()
                                    .find('.productWindow_content')
                                    .css({
                                        'height': 'auto'
                                    });
                                initWindow(object, $clone_productWindow, $clone_overlay);
                            });
                    }
                });

            $navright
                .unbind('click')
                .click(function () {
                    var tmp = $(object)
                        .parent()
                        .parent()
                        .next()
                        .find('.inner_box a')
                        .get(0);
                    if ($(tmp).length <= 0) {
                        tmp = $(object)
                            .parent()
                            .parent()
                            .parent()
                            .next()
                            .find('.box0 .inner_box a')
                            .get(0);
                    }
                    if ($(tmp).length > 0) {
                        object = tmp;
                        $clone_productWindow
                            .find('.productWindow_content')
                            .css({
                                'height': $clone_productWindow.find('.productWindow_content').height()
                            })
                            .html('')
                            .loadCache(object, $(object).attr('href'), function () {
                                $clone_productWindow
                                    .find('.productWindow_content')
                                    .css({
                                        'height': $clone_productWindow.find('.productWindow_content').height()
                                    })
                                    .end()
                                    .find('.close a').click(function () {
                                        productClose($clone_productWindow, $clone_overlay);
                                    })
                                    .end()
                                    .find('.productWindow_content')
                                    .css({
                                        'height': 'auto'
                                    });
                                    initWindow(object, $clone_productWindow, $clone_overlay);
//                                    .loadCache(object, $(object).attr('href'), function () {
//                                        $clone_productWindow
//
//                                    });
                            });
                    }
                });

            $(window)
                .unbind('keyup')
                .keyup(function (event) {
                    if (event.keyCode == 37) {
                        $navleft.click();
                    }
                    if (event.keyCode == 39) {
                        $navright.click();
                    }
                });

            initWindow(object, $clone_productWindow, $clone_overlay);
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