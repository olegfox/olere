$(function(){
    var scrollTop = 0, different = 0;
    $(window).scroll(function(){
        if(scrollTop > $(window).scrollTop()){
            different = scrollTop - $(window).scrollTop();
            scrollTop = $(window).scrollTop();
            $(".menu, .logoBox").css({
                "height" : $(".menu").height() + different
            });
            $(".menu, .logoBox").css({
                "line-height" : $(".menu").height()+"px"
            });
        }else{
            different = $(window).scrollTop() - scrollTop;
            scrollTop = $(window).scrollTop();
            $(".menu, .logoBox").css({
                "height" : $(".menu").height() - different
            });
            $(".menu, .logoBox").css({
                "line-height" : $(".menu").height()+"px"
            });
        }
    });
});