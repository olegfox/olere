document.addEventListener('DOMContentLoaded', function(){
    $(".gallery").each(function(i, e){
        $(e).unbind('click').click(function(){
            Code.photoSwipe('a', this);
            Code.PhotoSwipe.Current.show(0);
            return false;
        });
    });
}, false);
		