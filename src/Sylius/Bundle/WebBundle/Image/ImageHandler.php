<?php

namespace Sylius\Bundle\WebBundle\Image;

class ImageHandler
{
	var $image_src;
	var $image;
	var $image_type;
 
	public function __construct($image_src) 
	{
		$this->image_src = $image_src;
		if(!file_exists($this->image_src))
			return;

		$image_info = getimagesize($this->image_src);
		$this->image_type = $image_info[2];
		if( $this->image_type == IMAGETYPE_JPEG ) {	
			$this->image = imagecreatefromjpeg($image_src);
		} elseif( $this->image_type == IMAGETYPE_GIF ) {
			$this->image = imagecreatefromgif($image_src);
		} elseif( $this->image_type == IMAGETYPE_PNG ) {
			$this->image = imagecreatefrompng($image_src);
		}
	}

	public function crop($x1, $y1, $w, $h, $div_width) 
	{
		if(!file_exists($this->image_src))
			return;

		list($width, $height, $type, $attr) = getimagesize($this->image_src);
        //$scale = $width / $div_width;
		$scale = 1;

        $this->resizeThumbnailImage($w, $h, $x1, $y1, $scale);
	}
	
    protected function resizeThumbnailImage($width, $height, $start_width, $start_height, $scale)
	{
		if(!$this->image)
			return;

		$newImageWidth = ceil($width * $scale);
    	$newImageHeight = ceil($height * $scale);
    	$newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
        imagecopyresampled($newImage, $this->image, 0, 0, $start_width * $scale, $start_height * $scale, $newImageWidth, $newImageHeight, $width * $scale, $height * $scale);
		$this->image = $newImage;
		$this->output();
    }

	public function resizeToWidth($width)
	{
		if(!$this->image)
			return;

		$ratio = $width / $this->getWidth();
		if ($ratio > 1) return;
		$height = $this->getHeight() * $ratio;
		$this->resize($width, $height);
	}

	public function resizeToHeight($height)
	{
		if(!$this->image)
			return;

		$ratio = $height / $this->getHeight();
		if ($ratio > 1) return;
		$width = $this->getWidth() * $ratio;
		$this->resize($width, $height);
	}
	
	protected function resize($width, $height) 
	{
		if(!$this->image)
			return;

		$new_image = imagecreatetruecolor($width, $height);
		imagealphablending($new_image, true);
		imagesavealpha($new_image, true);
		imagefill($new_image, 0, 0, 0x7fff0000);
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $new_image;
		$this->output();
		/*
		$new_image = imagecreatetruecolor($width, $height);
		$black = imagecolorallocate($new_image, 0, 0, 0);
		imagecolortransparent($new_image, $black);
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $new_image;
		$this->output();
		*/
	} 
	
	public function getWidth() 
	{
		if(!$this->image)
			return;

		return imagesx($this->image);
	}
	
	public function getHeight() 
	{
		if(!$this->image)
			return;

		return imagesy($this->image);
	}

	public function getClass()
	{
		if(!$this->image)
			return '';

		$class = 'photo';
		$w = $this->getWidth();
		$h = $this->getHeight();
		if ($w >= 500 || $h > 550) {
			if (800 / $h * $w > 720)
				$class = 'photo_big_width';
			else
				$class = 'photo_big_height';
		}

		return $class;
	}

	protected function output()
	{
		if(!$this->image)
			return;

		if( $this->image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image, $this->image_src);
		} elseif( $this->image_type == IMAGETYPE_GIF ) {
			imagegif($this->image, $this->image_src);
		} elseif( $this->image_type == IMAGETYPE_PNG ) {
			imagepng($this->image, $this->image_src);
		}
    	chmod($this->image_src, 0777);
	}

}

