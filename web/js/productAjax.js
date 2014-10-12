$(function(){
    $('.productWindow_overlay').click(function(){
        productClose();
    });
    $('.inner_box a').click(function(){
        productShow(this);
    });
});

function productShow(object){
    $('.productWindow_overlay').fadeIn(200);
    $('.productWindow').on('click', function(e) {
        if( e.target !== this )
            return;
        productClose();
    });
    $('.productWindow .productWindow_content').html('');
    $('.productWindow').css({
        'top' : '100%'
    });
    $('.productWindow').show();
    $('body').css({
        'overflow' : 'hidden'
    });
    $('.productWindow .productWindow_content').load($(object).attr('href'), function(){
        $('.productWindow').animate({
            'top' : '0'
        }, 200);
        smoothZoom();
        addCart();
        $('.latestProducts a').click(function(){
            productShow(this);
        });
    });
}

function productClose(){
    $('.productWindow').animate({
        'top' : '100%'
    }, {
        duration: 200,
        complete : function(){
            $('.productWindow').hide();
            $('body').css({
                'overflow' : 'auto'
            });
            $('.productWindow_overlay').fadeOut(200);
        }
    });
}