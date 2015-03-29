<?php

namespace Sylius\Bundle\CoreBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Baner
{
    protected $id;

    protected $image;

    protected $link;

    protected $width;

    protected $position;

    /**
     * @Assert\File()
     */
    private $file;

    public function getId()
    {
        return $this->id;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    public function getAbsolutePath()
    {
        return null === $this->image
            ? null
            : $this->getUploadRootDir().'/'.$this->image;
    }

    public function getWebPath()
    {
        return null === $this->image
            ? null
            : $this->getUploadDir().'/'.$this->image;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/baner';
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        $this->upload();
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        if (isset($this->image)) {
            if(file_exists($this->getUploadDir().'/'.$this->image)){
                unlink($this->getUploadDir().'/'.$this->image);
            }
            $this->image = null;
        }

        $this->image = $this->getFile()->getClientOriginalName();

        $this->getFile()->move(
            $this->getUploadDir(),
            $this->image
        );

        $this->file = null;
    }
}

