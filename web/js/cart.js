function cartSave(){
    $.post($(".content .row form").attr("action"), $(".content .row form").serialize(), function(data){
        cart = $.parseJSON(data);
        for(i = 0; i < cart.items.length; i++){
            $(".item_total").eq(i).html(cart.items[i].total);
        }
        $(".summary_grand_total span").html(cart.total);
    });
}