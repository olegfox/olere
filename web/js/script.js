function addCart(){
    $(".addCart").click(function () {
        form = $(this).parent();
        $(form).unbind("submit").submit(function () {
            $.post($(form).attr("action"), $(form).serialize(), function (data) {
                try {
                    data = $.parseJSON(data);
                    $(".wrap_trash .trash span span").html("");
                    $(".wrap_trash .trash span span").eq(0).html(data['quantity']);
                    $(".wrap_trash .trash span span").eq(1).html(data['total']);
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
function addCartRing(){
    $(".addCartRing").click(function(){
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

    if($('.catalog .head.filter').length > 0){
        $(window).scroll(function(){
            if($(window).scrollTop() > 125){
                $('.borderHead.filter').addClass('fixed');
                $('.catalog .head.filter').addClass('fixed');
            }else{
                $('.borderHead.filter').removeClass('fixed');
                $('.catalog .head.filter').removeClass('fixed');
            }
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
    $(object).parent().parent().parent().parent().parent().find(".pictureProduct").html("<img src='" + image + "' alt='" + name + "' width='500px'>");
    smoothZoom($(object).parent().parent().parent().parent().parent().find(".pictureProduct")[0]);
}

function filter(category) {
    if (category != 'sale') {
        var cat = $(".filter #type").val();
    }
    var price = $(".filter #price").val();
    var material = $(".filter #material").val();
    var weight = $(".filter #weight").val();
    var color = $(".filter #color").val();
    var collection = $(".filter #collection").val();
//    var depth = $(".filter #depth").val();
    var box = $(".filter #box").val();
    var size = $(".filter #size").val();
    if (category != 'sale') {
        var href = "/" + category + "/1/" + cat + "?filter[price]=" + price + "&filter[material]=" + material + "&filter[weight]=" + weight;
    } else {
        var href = "/" + category + "/1?filter[price]=" + price + "&filter[material]=" + material + "&filter[weight]=" + weight;
    }
//    href = href + "&filter[depth]=" + depth;
    href = href + "&filter[box]=" + box;
    href = href + "&filter[size]=" + size;
    href = href + "&filter[color]=" + color;
    href = href + "&filter[collection]=" + collection;
    window.location.href = href;
}