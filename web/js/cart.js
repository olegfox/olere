function cartSave(object, onHand) {
    if (onHand >= $(object).val()) {
        $.post($(".cartForm").attr("action"), $(".cartForm").serialize(), function (data) {
            cart = $.parseJSON(data);
            for (i = 0; i < cart.items.length; i++) {
                $(".item_total").eq(i).html(cart.items[i].total);
            }
            $(".summary_grand_total span").html(cart.total);
        });
    }else{
        alert('На складе нет такого количества.');
    }
}

function changeSizeCart(object, url){
    $.post(url, {size: $(object).val()}, function (data) {
        if(data == 'ok'){
            window.location.reload();
        }else{
            alert(data);
        }
    });
}