<?php

namespace Sylius\Bundle\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

class Sale
{
    /**
     * Sale id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Sale dateBegin.
     *
     * @var \DateTime
     */
    protected $date_begin;

    /**
     * Sale dateEnd.
     *
     * @var \DateTime
     */
    protected $date_end;

    /**
     * Sale percent.
     *
     * @var integer
     */
    protected $percent;

    protected $taxon;

    protected $taxonId;

    /**
     * Sale typePrice.
     *
     * @var integer
     */
    protected $type_price;


    public function setId()
    {

        return $this;
    }

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

    public function getTypePriceName()
    {
        switch($this->type_price){
            case 0: return "Все цены";break;
            case 1: return "Только оптовые";break;
            case 2: return "Только розничные";break;
            default: return "Все цены";
        }
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

    /**
     * {@inheritdoc}
     */
    public function getTaxon()
    {
        return $this->taxon;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxon($taxon)
    {
        $this->taxon = $taxon;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getTaxonId()
    {
        return $this->taxonId;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxonId($taxonId)
    {
        $this->taxonId = $taxonId;

        return $this;
    }

}
