function allRemove(object){
    $(".idx").prop('checked', $(object).prop('checked'));
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