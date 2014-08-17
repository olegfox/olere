function activeChild(object, href, parent, child, active){
    if(active == 1){
        $(object).attr("onclick", "activeChild(this, '/administration/products/children/active', "+parent+", "+child+", 0); return false;");
        $(object).appendTo(".selectedProducts ul");
    }else{
        $(object).remove();
    }
    $.post(href, {parent : parent, child : child, active : active}, function(data){
        console.log(data);
    });
}

function changeTaxon(object, parent){
    $.post('/administration/products/children/taxon/'+$(object).val()+'/'+parent, {}, function(data){
        $(".childrenProducts .products").html(data);
    });
}