$(function(){
    $(".inner_box").hover(function(){
//        if($(this).hasClass("inner_box")){
            if(!$(this).hasClass("inner_box_click")){
//                reset
                if($(this).find('img').attr('data-image2') != ""){
                    $(this).find('img').attr('src', $(this).find('img').attr('data-image2'));
                }
                $(".inner_box_click").parent().css({
                    padding : "0px"
                });
                $(".inner_box_click .descriptionBox").css({
                    display : "none"
                });
////                $(".inner_box").css({
////                    "-o-transition":".5s",
////                    "-ms-transition":".5s",
////                    "-moz-transition":".5s",
////                    "-webkit-transition":".5s",
////                    "transition":".5s"
////                });
                $(".inner_box_click").removeClass("inner_box_click");


//                $(this).parent().css({
//                    "-o-transition":".0s",
//                    "-ms-transition":".0s",
//                    "-moz-transition":".0s",
//                    "-webkit-transition":".0s",
//                    "transition":".0s"
//                });
//                $(this).parent().parent().css({
//                    padding : "0px"
//                });
                $(this).addClass("inner_box_click");
                $(this).find(".clickBox").css({
                    display : "table"
                });
                $(this).find(".descriptionBox").css({
                    display : "block"
                });
                return false;
            }else{
                if($(this).find('img').attr('data-image1') != ""){
                    $(this).find('img').attr('src', $(this).find('img').attr('data-image1'));
                }
            }
//        }else{
//            if(!$(this).parent().parent().parent().hasClass("inner_box_click")){
////                $(this).parent().parent().parent().css({
////                    "-o-transition":".0s",
////                    "-ms-transition":".0s",
////                    "-moz-transition":".0s",
////                    "-webkit-transition":".0s",
////                    "transition":".0s"
////                });
//                $(this).parent().parent().parent().parent().css({
//                    padding : "0px"
//                });
//                $(this).parent().parent().parent().addClass("inner_box_click");
//                $(this).parent().parent().parent().find(".clickBox").css({
//                    display : "table"
//                });
//                return false;
//            }else{
//                if($(this).find('img').attr('data-image1') != ""){
//                    $(this).find('img').attr('src', $(this).find('img').attr('data-image1'));
//                }
//            }
//        }
    }, function(){
        $(".inner_box_click .descriptionBox").css({
            display : "none"
        });
        if($(this).find('img').attr('data-image1') != ""){
            $(this).find('img').attr('src', $(this).find('img').attr('data-image1'));
        }
        $(".inner_box_click").removeClass("inner_box_click");
    });
});