function smoothZoom(object){
    $(object)
        .find('img')
        .unbind('click')
        .click(function(){
            $('body').append("<div class='image-viewer'><img style='opacity: 0;' src='"+$(this).data('src')+"' /><div class='close'></div></div>");
            $(".image-viewer")
                .find('.close')
                .click(function(){
                    $(".image-viewer").remove();
                })
                .end()
                .find('img')
                .one('load', function() {
                    $(".image-viewer")
                        .find('img')
                        .css({
                            "opacity" : 1
                        })
                        .smoothZoom({
                            width: "100%",
                            height: "100%",
                            border_TRANSPARENCY: 20,
                            pan_BUTTONS_SHOW: "NO",
                            pan_LIMIT_BOUNDARY: "NO",
                            responsive: false,
                            responsive_maintain_ratio: true,
                            max_WIDTH: '',
                            max_HEIGHT: '',
                            zoom_MAX: 150,
                            animation_SPEED_ZOOM: 1,
                            animation_SMOOTHNESS: 10
                        });
                    $(".smooth_zoom_preloader div").hide();
                    $(".smooth_zoom_preloader div.noSel").show();
//                    $(window).resize(function(){
//                        $(object).html($(object).find('img').attr("width", "500px"));
//                        smoothZoom();
//                    });
                }).each(function() {
                    if(this.complete) $(this).load();
                });
        });
}
