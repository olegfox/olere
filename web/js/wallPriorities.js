$(function(){
    $(".table").tableDnD({
        onDrop: function(table, row) {
            drag = row.id;
            drop = $("#td"+($("#"+row.id).index()+1)).parent().attr("id");
            $('#' + drag).attr('id', drop+'x');
            $('#' + drop).attr('id', drag);
            $('#' + drop+'x').attr('id', drop);
            $.get("/administration/taxonomies/order_change/"+drag+"/"+drop);
        }
    });
});