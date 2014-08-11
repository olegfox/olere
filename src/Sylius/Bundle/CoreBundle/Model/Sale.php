<?php

namespace Sylius\Bundle\CoreBundle\Model;

class Sale
{
    protected $id;

    protected $date_begin;

    protected $date_end;

    protected $percent;

    protected $taxons;

    protected $type_price;


    /**
     * Set date_begin
     *
     * @param \DateTime $dateBegin
     * @return Sale
     */
    public function setDateBegin($dateBegin)
    {
        $this->date_begin = $dateBegin;

        return $this;
    }

    /**
     * Get date_begin
     *
     * @return \DateTime 
     */
    public function getDateBegin()
    {
        return $this->date_begin;
    }

    /**
     * Set date_end
     *
     * @param \DateTime $dateEnd
     * @return Sale
     */
    public function setDateEnd($dateEnd)
    {
        $this->date_end = $dateEnd;

        return $this;
    }

    /**
     * Get date_end
     *
     * @return \DateTime 
     */
    public function getDateEnd()
    {
        return $this->date_end;
    }

    /**
     * Set percent
     *
     * @param integer $percent
     * @return Sale
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * Get percent
     *
     * @return integer 
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * Set type_price
     *
     * @param integer $typePrice
     * @return Sale
     */
    public function setTypePrice($typePrice)
    {
        $this->type_price = $typePrice;

        return $this;
    }

    /**
     * Get type_price
     *
     * @return integer 
     */
    public function getTypePrice()
    {
        return $this->type_price;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
