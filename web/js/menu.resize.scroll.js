$(function(){
    var scrollTop = 0, different = 0;
    $(window).scroll(function(){
        if(scrollTop > $(window).scrollTop()){
            different = scrollTop - $(window).scrollTop();
            scrollTop = $(window).scrollTop();
            height = $(".menu").height() + different;
            if(height > 85){
                height = 85;
            }
            $(".menu, .logoBox").css({
                "height" : height
            });
            $(".menu, .logoBox").css({
                "line-height" : $(".menu").height()+"px"
            });
        }else{
            different = $(window).scrollTop() - scrollTop;
            scrollTop = $(window).scrollTop();
            height = $(".menu").height() - different;
            if(height < 38){
                height = 38;
            }
            $(".menu, .logoBox").css({
                "height" : height
            });
            $(".menu, .logoBox").css({
                "line-height" : $(".menu").height()+"px"
            });
        }
    });
});