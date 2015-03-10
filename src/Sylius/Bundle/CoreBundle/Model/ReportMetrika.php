<?php

namespace Sylius\Bundle\CoreBundle\Model;

class ReportMetrika
{
    const TYPE_DAY = 0;//отчет за день
    const TYPE_WEEK = 1;//отчет за неделю
    const TYPE_MONTH = 2;//отчет за месяц

    private $id;

    private $type;

    private $datetime;

    private $text;

    public function getId()
    {
        return $this->id;
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

    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getDatetime()
    {
        return $this->datetime;
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

}
