function smoothZoom(){
    $('.pictureProduct img').one('load', function() {
        $('.pictureProduct img').smoothZoom({
            width: $('.pictureProduct').width() - 40,
            height: $('.pictureProduct').height(),
            border_TRANSPARENCY: 20,
            pan_BUTTONS_SHOW: "NO",
            pan_LIMIT_BOUNDARY: "NO",
            /******************************************
             Enable Responsive settings below if needed.
             Max width and height values are optional.
             ******************************************/
            responsive: false,
            responsive_maintain_ratio: true,
            max_WIDTH: '',
            max_HEIGHT: ''
        });
        $(".smooth_zoom_preloader div").hide();
        $(".smooth_zoom_preloader div.noSel").show();
        $(window).resize(function(){
            $('.pictureProduct').html($('.pictureProduct img'));
            smoothZoom();
        });
    }).each(function() {
        if(this.complete) $(this).load();
    });
}
