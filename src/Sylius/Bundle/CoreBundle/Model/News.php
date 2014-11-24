<?php

namespace Sylius\Bundle\CoreBundle\Model;

class News
{
    private $id;

    private $title;

    private $keyword = "";

    private $description = "";

    private $slug;

    private $text;

    private $images;

    private $video;

    private $created;

    private $updated;

    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
        $this->video = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(){
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function getKeyword()
    {
        return $this->keyword;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function addImage(\Sylius\Bundle\CoreBundle\Model\NewsImages $images)
    {
        $this->images[] = $images;

        return $this;
    }

    public function removeImage(\Sylius\Bundle\CoreBundle\Model\NewsImages $images)
    {
        $this->images->removeElement($images);
    }

    public function getImages()
    {
        return $this->images;
    }

    public function addVideo(\Sylius\Bundle\CoreBundle\Model\NewsVideo $video)
    {
        $this->video[] = $video;

        return $this;
    }

    public function removeVideo(\Sylius\Bundle\CoreBundle\Model\NewsVideo $video)
    {
        $this->images->removeElement($video);
    }

    public function getVideo()
    {
        return $this->video;
    }

    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

}
