$(function(){
    $(".table").tableDnD({
        onDrop: function(table, row) {
            drag = row.id;
            drop = $("#td"+($("#"+row.id).index()+1)).parent().attr("id");
            $.get("/administration/taxonomies/order_change/"+drag+"/"+drop);
        }
    });
});