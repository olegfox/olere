$(function(){
    $('.video a').click(function(){
        $(this).parent().html('<iframe src="'+$(this).attr('href')+'" width="835" height="470"></iframe>');
        return false;
    });
});