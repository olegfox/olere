<?php

namespace Sylius\Bundle\CoreBundle\Misc;

class SluggableListener extends \Gedmo\Sluggable\SluggableListener
{

    public function __construct(){
        $this->setTransliterator(array('\Sylius\Bundle\CoreBundle\Misc\Transliterator', 'transliterate'));
    }

    protected function getNamespace()
    {
        return parent::getNamespace();
    }


}