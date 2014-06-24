$(function(){
    $(".inner_box a").click(function(){
        if($(this).parent().hasClass("inner_box")){
            if(!$(this).parent().hasClass("inner_box_click")){
//                reset
                $(".inner_box_click").parent().css({
                    padding : "8px"
                });
                $(".inner_box_click .clickBox").css({
                    display : "none"
                });
//                $(".inner_box").css({
//                    "-o-transition":".5s",
//                    "-ms-transition":".5s",
//                    "-moz-transition":".5s",
//                    "-webkit-transition":".5s",
//                    "transition":".5s"
//                });
                $(".inner_box_click").removeClass("inner_box_click");


                $(this).parent().css({
                    "-o-transition":".0s",
                    "-ms-transition":".0s",
                    "-moz-transition":".0s",
                    "-webkit-transition":".0s",
                    "transition":".0s"
                });
                $(this).parent().parent().css({
                    padding : "5px"
                });
                $(this).parent().addClass("inner_box_click");
                $(this).parent().find(".clickBox").css({
                    display : "table"
                });
                $(this).parent().find(".descriptionBox").css({
                    display : "block"
                });
                return false;
            }
        }else{
            if(!$(this).parent().parent().parent().hasClass("inner_box_click")){
                $(this).parent().parent().parent().css({
                    "-o-transition":".0s",
                    "-ms-transition":".0s",
                    "-moz-transition":".0s",
                    "-webkit-transition":".0s",
                    "transition":".0s"
                });
                $(this).parent().parent().parent().parent().css({
                    padding : "5px"
                });
                $(this).parent().parent().parent().addClass("inner_box_click");
                $(this).parent().parent().parent().find(".clickBox").css({
                    display : "table"
                });
                return false;
            }
        }
    });
});