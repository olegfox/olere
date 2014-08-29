$(function () {
    var uploadEnd = 0,
        image = [],
        i = 0;
    $('#sylius_import_image').parent().append("<input type='hidden' name='gallery' class='gallery' />");
    $('#sylius_import_image').uploadify({
        'auto': false,
        'fileTypeDesc' : 'Image Files',
        'fileTypeExts' : '*.gif; *.jpg; *.jpeg; *.png; *.GIF; *.JPG; *.JPEG; *.PNG',
        'swf': '/js/uploadify/uploadify.swf',
        'uploader': '/js/uploadify/uploadify_import_products.php',
        "buttonText" : "Выбрать файл",
        'onQueueComplete': function (queueData) {
            uploadEnd = 1;
            $(".gallery").val(JSON.stringify(image));
            $("form").submit();
        },
        'onUploadError': function (file, errorCode, errorMsg, errorString) {
            alert('Файл ' + file.name + ' не может быть загружен: ' + errorString);
        },
        'onUploadSuccess': function (file, data, response) {
            image[i] = data;
            i++;
        }
    });
    $('.uploadify').siblings(".file-overlay").hide();
        $("form").submit(function(){
            if (uploadEnd == 0 && $(".uploadify-queue *").length > 0) {
                $('.uploadify').uploadify('upload', '*');
                return false;
            }
        });
});