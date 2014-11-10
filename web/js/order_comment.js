function commentAdd() {
    $('.comments #add a, .comments #edit a').each(function (i, e) {
        $(e).click(function () {

            var link = $(this).attr('href');

            $('#modalComment').modal('show');

            $('#modalComment .modal-body').load(link);

            return false;
        });
    });
}

$(function () {
    commentAdd();
});