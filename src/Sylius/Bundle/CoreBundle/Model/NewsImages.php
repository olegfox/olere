<?php

namespace Sylius\Bundle\CoreBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class NewsImages
{
    private $id;

    private $name;

    private $extension;

    private $path;

    private $file;

    private $news;

    public function getId(){
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../..//uploads/news/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/news';
    }

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
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

        if (isset($this->path)) {
            unlink($this->getUploadDir().'/'.$this->path);
            $this->path = null;
        }

        $filename = sha1(uniqid(mt_rand(), true));
        $this->path = $filename.'.'.$this->getFile()->guessExtension();
        $this->extension = $this->getFile()->guessExtension();
        $this->name = $this->getFile()->getClientOriginalName();

        $this->getFile()->move(
            $this->getUploadDir(),
            $this->path
        );

        $this->file = null;
    }

    public function setNews(\Sylius\Bundle\CoreBundle\Model\News $news = null)
    {
        $this->news = $news;

        return $this;
    }

    public function getNews()
    {
        return $this->news;
    }

}
