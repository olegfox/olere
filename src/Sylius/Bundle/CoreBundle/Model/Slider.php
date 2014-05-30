<?php

namespace Sylius\Bundle\CoreBundle\Model;

class Slider
{
    protected $id;

    protected $image;

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
}

