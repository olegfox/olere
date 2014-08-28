<?php

namespace Sylius\Bundle\CoreBundle\Model;

class SliderText
{
    protected $id;

    protected $text;

    protected $enable;

    public function getId()
    {
        return $this->id;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getEnable()
    {
        return $this->enable;
    }

    public function setEnable($enable)
    {
        $this->enable = $enable;

        return $this;
    }
}

