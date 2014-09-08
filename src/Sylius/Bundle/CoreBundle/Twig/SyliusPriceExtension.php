<?php

namespace Sylius\Bundle\CoreBundle\Twig;

class SyliusPriceExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('price', array($this, 'priceFilter')),
        );
    }

    public function priceFilter($object)
    {
        if(count($object) > 0){
            foreach($object as $key => $o){
                if($key == 0){
                    $min = $o->getPriceOpt();
                    $max = $o->getPriceOpt();
                }
                if($min > $o->getPriceOpt()){
                    $min = $o->getPriceOpt();
                }
                if($max < $o->getPriceOpt()){
                    $max = $o->getPriceOpt();
                }
            }

            return 'Цена от '.($min/100).' до '.($max/100);
        }
        return 'любая';
    }

    public function getName()
    {
        return 'price_extension';
    }
}
