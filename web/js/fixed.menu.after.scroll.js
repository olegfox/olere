var offset = 0,
    time_id;
$(function () {
    offset = $("#sidebar").offset();
});
function fixedMenuAfterScroll() {
    if ($(".flexslider").length <= 0) {
        if ($(this).scrollTop() > 0) {
            $('.wrap_top.clone').remove();
            var nav0 = $('.wrap_top').clone().appendTo("body").addClass('clone');
            if (nav0.hasClass("f-nav0") == false) {
                nav0.addClass("f-nav0");
            }
        } else {
            $('.wrap_top.clone').remove();
        }

        if ($(this).scrollTop() >= 38) {
            $('.wrap_menu.clone').remove();
            var nav = $('.wrap_menu').clone().appendTo("body").addClass('clone');
            if (nav.hasClass("f-nav") == false) {
                nav.addClass("f-nav");
            }
        } else {
            $('.wrap_menu.clone').remove();
        }

        if ($(this).scrollTop() >= 175) {
            $(".steakyTable").css({
                "width": $(".catalogTable").width() - 1
            });
            $(".steakyTable").show();
        } else {
            $(".steakyTable").hide();
        }

        $(window).resize(function () {
            $(".steakyTable").css({
                "width": $(".catalogTable").width() - 1
            });
        });
    }
    if ($("#sidebar").length > 0) {
        var topPadding = 96;
        clearTimeout(time_id);
        time_id = setTimeout(function () {
            if ($(window).scrollTop() >= 175) {
                $("#sidebar").stop().animate({
                    marginTop: $(window).scrollTop() - offset.top + topPadding
                });
                $("#sidebar ul span.nav-header").parent().find(".nav-list").css({"height": "0px", "margin-top": "-4px"});
            } else {
                $("#sidebar").stop().animate({
                    marginTop: 0
                });
            }
        }, 2000);
    }
}
jQuery("document").ready(function ($) {
    if (!document.addEventListener) {
        window.onscroll = fixedMenuAfterScroll;
    }
    else {
        document.addEventListener("touchstart", fixedMenuAfterScroll, false);
        document.addEventListener("touchmove", fixedMenuAfterScroll, false);
        document.addEventListener("touchend", fixedMenuAfterScroll, false);
        document.addEventListener("scroll", fixedMenuAfterScroll, false);
    }
});