function allRemove(object){
    $(".idx").prop('checked', $(object).prop('checked'));
    removeClick();
}

function removeClick(){
    if($('[name="idx[]"]:checked').length > 0){
        $(".groupEdit").show();
        var ids = [], j =0;
        $('[name="idx[]"]:checked').each(function(i, e){
            ids[j] = $(e).val();
            $(".groupEdit #ids").val(JSON.stringify(ids));
            j++;
        });
    }else{
        $(".groupEdit").css('display', 'none');
    }
}

function editProductsSubmit(){
    var form = $(".groupEdit form");
    $.post(form.attr('action'), form.serializeArray(), function(data){
       if(data == "ok"){
           alert("Изменения сохранены!");
       }else{
           alert("Ошибка!");
       }
    });
    return false;
}

function deleteAll(object){
    console.log("deleteAll");
    var idx = [], i = 0, count = $(".idx:checked").length;
    console.log(count);
    $(".idx").each(function(index, element){
        if($(element).prop('checked')){
            idx[i] = $(element).val();
            console.log(idx[i]);
            i++;
            if (!--count){
                $(object).parent().find('#idx_all').val(JSON.stringify(idx));
                console.log(JSON.stringify(idx));
                $(object).parent().submit();
            }
        }
    });
}

function editProduct(id, route){
    $.post(route, {
        id: id,
        name: $("tr#"+id).find("#name").val(),
        sku: $("tr#"+id).find("#sku").val(),
        price: $("tr#"+id).find("#price").val(),
        priceOpt: $("tr#"+id).find("#priceOpt").val()
    }, function(data){
        if(data != "ok"){
            alert("Ошибка!");
        }
    });
}