/**
 * Created by oleg on 13.01.14.
 */
$(function(){
    redactor = $('.redactor').redactor({
        imageUpload: '/administration/admin/image_upload/',
        //toolbar: 'custom',
        //css: ['custom.css?1'],
        allowedTags: ["a", "p", "img"],
        convertVideoLinks: true
    });
});
