<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

function uploadFTP($name, $NewName, $login,$pass,$host,$path){

    $connect = ftp_connect($host);

    if(!$connect) { return false; }

    $result = ftp_login($connect, $login, $pass);

    if ($result==false) return false;

    if (ftp_chdir($connect, $path)){ ftp_put($connect, $NewName, $name, FTP_BINARY); }

    else { return false;  }

    ftp_quit($connect);

}

// Define a destination
$targetFolder = '/import/files/'; // Relative to the root

if (!empty($_FILES)) {
    $tempFile = $_FILES['Filedata']['tmp_name'];
    $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
    // Validate the file type
    $fileTypes = array('jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG'); // File extensions
    $fileParts = pathinfo($_FILES['Filedata']['name']);
    $newFileName = $_FILES['Filedata']['name'];
    $targetFile = rtrim($targetPath, '/') . '/' . $newFileName;

    if (in_array($fileParts['extension'], $fileTypes)) {
        uploadFTP($tempFile, $newFileName, 'anonymous', '', 'fotobank.olere.ru', '/');
//        move_uploaded_file($tempFile, $targetFile);
        echo $newFileName;
    } else {
        echo 'Invalid file type.';
    }
}
?>