/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    config.filebrowserBrowseUrl = '/js/kcfinder/browse.php?opener=ckeditor&type=files';
    config.filebrowserImageBrowseUrl = '/js/kcfinder/browse.php?opener=ckeditor&type=images';
    config.filebrowserFlashBrowseUrl = '/js/kcfinder/browse.php?opener=ckeditor&type=flash';
    config.filebrowserUploadUrl = '/js/kcfinder/upload.php?opener=ckeditor&type=files';
    config.filebrowserImageUploadUrl = '/js/kcfinder/upload.php?opener=ckeditor&type=images';
    config.filebrowserFlashUploadUrl = '/js/kcfinder/upload.php?opener=ckeditor&type=flash';
};
