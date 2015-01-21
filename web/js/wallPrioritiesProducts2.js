$(function(){
    $(".table").tableDnD({
        onDrop: function(table, row) {
            drag = row.id;
            drop = $(".sylius-products-table #td"+($("#"+row.id).index()+1)).parent().attr("id");
            $('#' + drag).attr('id', drop+'x');
            $('#' + drop).attr('id', drag);
            $('#' + drop+'x').attr('id', drop);
            $.get("/administration/products/order_change/"+drag+"/"+drop+"/1");
        }
    });
});