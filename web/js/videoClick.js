$(function(){
    $('.video a').click(function(){
        $(this).parent().html($(this).attr('href'));
        return false;
    });
});