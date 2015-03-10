<?php

namespace Sylius\Bundle\CoreBundle\Model;

class Metrika
{
    const TYPE_CATALOG = 0;//тип метрики, которая показывает, какие каталоги посетил пользователь
    const TYPE_NOT_ORDER = 1;//тип метрики, которая показывает, что пользователь добавил в корзину, но не оформил заказ

    private $id;

    private $user;

    private $taxon;

    private $type;

    private $datetime;

    public function getId()
    {
        return $this->id;
    }

    public function setUser(\Sylius\Bundle\CoreBundle\Model\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setTaxon(\Sylius\Bundle\CoreBundle\Model\Taxon $taxon = null)
    {
        $this->taxon = $taxon;

        return $this;
    }

    public function getTaxon()
    {
        return $this->taxon;
    }

    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getDatetime()
    {
        return $this->datetime;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

}
