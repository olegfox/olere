$(function () {
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
    if (window.location.href.indexOf('catalog') + 1) {
        $("a[href='/catalog']").parent().addClass("current");
    }
    $(".fancybox").fancybox();
    $(window).load(function () {
        footerBottom();
    });
    $(window).resize(function () {
        footerBottom();
    });

    $('input, textarea').placeholder();
});

function footerBottom() {
    if ($(".container").height() < $(window).height() - 50) {
        $(".footer").addClass("footer-bottom");
    } else {
        $(".footer").removeClass("footer-bottom");
    }
}

function preview(original, image, name) {
    $(".product .pictureProduct").html("<img src='" + image + "' alt='" + name + "' width='500px'>");
    smoothZoom();
}

function filter(category) {
    if (category != 'sale') {
        var cat = $(".filter #type").val();
    }
    var price = $(".filter #price").val();
    var material = $(".filter #material").val();
    var weight = $(".filter #weight").val();
    var depth = $(".filter #depth").val();
    var box = $(".filter #box").val();
    var size = $(".filter #size").val();
    if (category != 'sale') {
        var href = "/" + category + "/1/" + cat + "?filter[price]=" + price + "&filter[material]=" + material + "&filter[weight]=" + weight;
    } else {
        var href = "/" + category + "/1?filter[price]=" + price + "&filter[material]=" + material + "&filter[weight]=" + weight;
    }
    href = href + "&filter[depth]=" + depth;
    href = href + "&filter[box]=" + box;
    href = href + "&filter[size]=" + size;
    window.location.href = href;
}