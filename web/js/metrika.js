$(function(){
    $.post('/metrika/', {url : window.location.href}).fail(function(){
        throw new Error('Ошибка метрики');
    });
});
