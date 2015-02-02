function addCart() {
    $(".addCart").click(function () {
        form = $(this).parent();
        $(form).unbind("submit").submit(function () {
            $.post($(form).attr("action"), $(form).serialize(), function (data) {
                try {
                    data = $.parseJSON(data);
                    $(".wrap_trash .trash span span").html("");
                    $(".wrap_trash .trash span span").eq(0).html(data['quantity']);
                    $(".wrap_trash .trash span span").eq(1).html(data['total']);
                    yaCounter25824779.reachGoal('ADD_CART');
                }
                catch (e) {
//                    $(".wrap_trash").hide();
                    alert(data);
                }
            });
            $(".wrap_trash").show();
            return false;
        });
    });
}
function addCartRing() {
    $(".addCartRing").click(function () {
        form = $(this).parent();
        $(form).unbind("submit").submit(function () {
            $.get($(form).attr("action"), $(form).serialize(), function (data) {
                try {
                    $.modal(data, {
                        overlayClose: true,
                        zIndex: 100000
                    });
                    addCart();
                }
                catch (e) {
                    alert(data);
                }
            });
            return false;
        });
    });
}
$(function () {
    $('.advantages ul li')
        .click(function(){

            var text = $(this).find('img').attr('alt');

            $('.windowOverlay').remove();
            $('.window').remove();
            $('body').append('<div class="windowOverlay"></div>');
            $('.windowOverlay').fadeIn(500);
            $('body').append('<div class="window">'+text+'<div class="close"></div></div>');
            $('.window')
                .css({
                    'position' : 'fixed',
                    'height' : $('.window').height() + 40,
                    'top' : ($('body').height() + 40 - $('.window').height())/2
                });

            $('.window .close')
                .click(function(){
                    $('.window').animate({
                        'opacity' : 0
                    }, 500);
                    $('.windowOverlay').fadeOut(500, function(){
                        $('.windowOverlay').remove();
                        $('.window').remove();
                    });
                });

            $('.windowOverlay').click(function(){
                $('.window .close').click();
            });

        });

    addCartRing();
    addCart();
//    if (window.location.href.indexOf('catalog') + 1) {
//        $("a[href='/catalog']").parent().addClass("current");
//    }
    $(".fancybox").fancybox();
    $(window).load(function () {
        footerBottom();
    });
    $(window).resize(function () {
        footerBottom();
    });

    $('input, textarea').placeholder();

    if ($('.head.filter').length > 0) {
        $(".head.filter #price").ionRangeSlider({
            min: 1,
            max: 2000,
            type: "double",
            onFinish: function (data) {
                $(".head.filter #price_from").val(data.from);
                $(".head.filter #price_to").val(data.to);
            }
        });


        $(window).scroll(function () {
//            if ($(window).scrollTop() > 125) {
//                $('.borderHead.filter').addClass('fixed');
//                $('.wrap_filter').addClass('fixed');
//            } else {
//                $('.borderHead.filter').removeClass('fixed');
//                $('.wrap_filter').removeClass('fixed');
//            }
        });
    }

});

function footerBottom() {
    if ($(".container").height() < $(window).height() - 50) {
        $(".footer").addClass("footer-bottom");
    } else {
        $(".footer").removeClass("footer-bottom");
    }
}

function preview(object, original, image, name) {
    $(object).parent().parent().parent().parent().parent().find(".pictureProduct").html("<img data-src='"+original+"' src='" + image + "' alt='" + name + "' height='100%'>");
    smoothZoom($(object).parent().parent().parent().parent().parent().find(".pictureProduct")[0]);
}

function filter(category) {
    if (category != 'sale') {
        var cat = $(".filter #type").val();
        if(cat == undefined){
            cat = $(".filter #catalog").val();
        }
    }
    var price_from = $(".filter #price_from").val();
    var price_to = $(".filter #price_to").val();
    var priceSale = $(".filter #priceSale").val();
    var material = $(".filter #material").val();
    var weight = $(".filter #weight").val();
    var color = $(".filter #color").val();
    var collection = $(".filter #collection").val();
    var catalog = $(".filter #catalog").val();
//    var depth = $(".filter #depth").val();
    var box = $(".filter #box").val();
    var size = $(".filter #size").val();
    var created = $(".filter #created").val();
    if (category != 'sale') {
        var href = "/" + category + "/1/" + cat + "?filter[price_from]=" + price_from + "&filter[price_to]=" + price_to + "&filter[material]=" + material + "&filter[weight]=" + weight;
    } else {
        var href = "/" + category + "/1?filter[price_from]=" + price_from + "&filter[price_to]=" + price_to + "&filter[material]=" + material + "&filter[weight]=" + weight;
    }
//    href = href + "&filter[depth]=" + depth;
    href = href + "&filter[box]=" + box;
    href = href + "&filter[size]=" + size;
    href = href + "&filter[color]=" + color;
    href = href + "&filter[collection]=" + collection;
    href = href + "&filter[catalog]=" + catalog;
    href = href + "&filter[created]=" + created;
    href = href + "&filter[priceSale]=" + priceSale;
    window.location.href = href;
}