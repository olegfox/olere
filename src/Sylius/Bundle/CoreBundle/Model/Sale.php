<?php

namespace Sylius\Bundle\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

class Sale
{
    protected $id;

    protected $date_begin;

    protected $date_end;

    protected $percent;

    protected $taxons;

    protected $type_price;


    public function __construct()
    {
        $this->taxons = new ArrayCollection();

    }

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
    public function getTaxons()
    {
        return $this->taxons;
    }


    public function addTaxon(\Sylius\Bundle\CoreBundle\Model\Taxon $taxon)
    {
        $this->taxons[] = $taxon;

        return $this;
    }


    public function removeTaxon(\Sylius\Bundle\CoreBundle\Model\Taxon $taxon)
    {
        $this->taxons->removeElement($taxon);
    }
}
