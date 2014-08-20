<?php

namespace Sylius\Bundle\CoreBundle\Model;

class SliderText
{
    protected $id;

    protected $text;

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
}

