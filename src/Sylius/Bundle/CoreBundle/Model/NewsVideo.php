<?php

namespace Sylius\Bundle\CoreBundle\Model;

class NewsVideo
{
    private $id;

    private $title;

    private $link;

    private $content;

    private $thumbnail;

    private $news;

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

    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
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

    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    public function getEmbed(){
        $url = "http://www.youtube.com/oembed?url=" . urlencode($this->link) . "?fs=1&amp;autoplay=1";
        return $url;
    }
}
