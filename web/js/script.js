$(function(){
   $(".addCart").click(function(){
       form = $(this).parent();
       $(form).unbind("submit").submit(function(){
           $(".wrap_trash .trash span span").html("");
           $.post($(form).attr("action"), $(form).serialize(), function(data){
               data = $.parseJSON(data);
               $(".wrap_trash .trash span span").eq(0).html(data['quantity']);
               $(".wrap_trash .trash span span").eq(1).html(data['total']);
           });
           $(".wrap_trash").show();
           return false;
       });
   });
    if(window.location.href.indexOf('catalog') + 1){
        $("a[href='/catalog']").parent().addClass("current");
    }
    $(".fancybox").fancybox();
    $(window).load(function(){
        footerBottom();
    });
    $(window).resize(function(){
        footerBottom();
    });
});

function footerBottom(){
    if($(".container").height() < $(window).height() - 50){
        $(".footer").addClass("footer-bottom");
    }else{
        $(".footer").removeClass("footer-bottom");
    }
}

function preview(original, image, name){
    $(".product .pictureProduct").html("<img src='"+image+"' alt='"+name+"' width='500px'>");
    smoothZoom();
}