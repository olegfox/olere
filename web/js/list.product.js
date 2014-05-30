$(function () {
    $(".character").click(function () {
        if ($(this).parent().parent().parent().find(".dop-character").css("display") == "none") {
            if(window.history.pushState){
                $(".dop-character").each(function(index, element){
                    if($(element).css("display") == "block"){
                        $(element).hide();
                        if(window.history.pushState){
                            href = window.location.href;
                            href = href.substr(0, href.lastIndexOf("/"));
                            window.history.pushState(null, "", href);
                        }
                    }
                });
                window.history.pushState(null, "", window.location.href + $(this).attr("href"));
            }
            $(this).parent().parent().parent().find(".dop-character").show();
        } else {
            $(this).parent().parent().parent().find(".dop-character").hide();
            if(window.history.pushState){
                href = window.location.href;
                href = href.substr(0, href.lastIndexOf("/"));
                window.history.pushState(null, "", href);
            }
        }
    });
    $("#sidebar ul span.nav-header").click(function(){
        if($(this).parent().find(".nav-list").height() == 0){
            $("#sidebar ul span.nav-header").parent().find(".nav-list").css({"height" : "0px", "margin-top" : "-4px"});
            $(this).parent().find(".nav-list").css({"height" : "auto", "margin-top" : "20px"});
        }else{
            $(this).parent().find(".nav-list").css({"height" : "0px", "margin-top" : "-4px"});
        }
    });
    $(window).resize(function(){
        if($("html").width() <= 1100){
            $(".wrap_catalog").css({
                "width" : "76%"
            });
        }else{
            $(".wrap_catalog").css({
                "width" : "80%"
            });
        }
    });
    if($("html").width() <= 1100){
        $(".wrap_catalog").css({
            "width" : "76%"
        });
    }else{
        $(".wrap_catalog").css({
            "width" : "80%"
        });
    }
});