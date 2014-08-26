function orderSave(object) {
    $.post($(object).parent().attr("action"), $(object).parent().serialize(), function (data) {
        try {
            order = $.parseJSON(data);
            $(object).parent().parent().parent().find('.onHand').html(order.onHand);
            $(object).parent().parent().parent().find('.total').html(order.total);
            $('.amount').html(order.amount);
        } catch (e) {
            alert(data);
        }
    });
}

function orderItemDelete(object){
    $.post($(object).attr("action"), $(object).serialize(), function (data) {
        $(object).parent().parent().hide();
        if(data != 'no'){
            try {
                order = $.parseJSON(data);
                $(object).parent().parent().remove();
                $('.amount').html(order.amount);
                $('#confirmation-modal').modal('hide');
            } catch (e) {
                $(object).parent().parent().show();
                alert('Ошибка!');
            }
        }else{
            $(object).parent().parent().show();
            alert('Ошибка!');
        }
    });
}