<?php

namespace Sylius\Bundle\WebBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Bundle\WebBundle\Image\ImageHandler;

class ImageRedactorController extends Controller {

    public function uploadAction() {
        $request = $this->getRequest()->request->all();
        $class = 'photo';

        if (isset($request["redactor_file_link"])) {
            $src = $request["redactor_file_link"];
            if ($src) {
                $content = file_get_contents($src);
                file_put_contents('uploads/img-tmp', $content);
                $ih = new ImageHandler('uploads/img-tmp');
                $class = $ih->getClass();
                $ret = "<img class='" . $class . "' src='" . $src . "' />";
                return new Response($ret);
            }
        }

        $web_dir_path = $this->container->getParameter('web_dir_path');
        $dir = 'uploads/images/';

        /*
         * From imperavi documentation http://imperavi.com
         */


        $_FILES['file']['type'] = strtolower($_FILES['file']['type']);

        if ($_FILES['file']['type'] == 'image/png' || $_FILES['file']['type'] == 'image/jpg' || $_FILES['file']['type'] == 'image/gif' || $_FILES['file']['type'] == 'image/jpeg' || $_FILES['file']['type'] == 'image/pjpeg') {
            $filename = md5(date('YmdHis')) . '.jpg';
            copy($_FILES['file']['tmp_name'], $dir . $filename);
            $ih = new ImageHandler($dir . $filename);
            $ih->resizeToWidth(720);
            $class = $ih->getClass();
            $ret = "<img class='" . $class . "' src='$web_dir_path" . $dir . $filename . "' />";
            $array = array(
                'filelink' => $web_dir_path . $dir . $filename
            );

            return new Response(stripslashes(json_encode($array)));
            //return new Response($ret);
        }

        return new Response("");
    }

    public function fileUploadAction(){
        $web_dir_path = $this->container->getParameter('web_dir_path');
        $dir = 'uploads/files/';
        move_uploaded_file($_FILES['file']['tmp_name'], $dir.$_FILES['file']['name']);

        $array = array(
            'filelink' => $web_dir_path . $dir .$_FILES['file']['name'],
            'filename' => $_FILES['file']['name']
        );

        return new Response(json_encode($array));
    }

}
